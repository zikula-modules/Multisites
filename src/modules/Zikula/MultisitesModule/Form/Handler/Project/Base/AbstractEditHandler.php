<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.1 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Handler\Project\Base;

use Zikula\MultisitesModule\Form\Handler\Common\EditHandler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ModUtil;
use RuntimeException;
use System;
use UserUtil;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the project object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables
     *
     * @return boolean False in case of initialisation errors, otherwise true
     */
    public function processForm(array $templateParameters)
    {
        $this->objectType = 'project';
        $this->objectTypeCapital = 'Project';
        $this->objectTypeLower = 'project';
        
        $this->hasPageLockSupport = true;
    
        $result = parent::processForm($templateParameters);
    
        if ($this->templateParameters['mode'] == 'create') {
            $modelHelper = $this->container->get('zikula_multisites_module.model_helper');
            if (!$modelHelper->canBeCreated($this->objectType)) {
                $this->request->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the project yet as other items are required which must be created before!'));
                $logger = $this->container->get('logger');
                $logArgs = ['app' => 'ZikulaMultisitesModule', 'user' => $this->container->get('zikula_users_module.current_user')->get('uname'), 'entity' => $this->objectType];
                $logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
    
        $entity = $this->entityRef;
        $selectionHelper = $this->container->get('zikula_multisites_module.selection_helper');
        
        // assign identifiers of predefined outgoing many to many relationships
        // non-editable relation, we store the id and assign it in handleCommand
        $this->relationPresets['templates'] = $this->request->get('templates', '');
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    
        $entityData = $entity->toArray();
    
        // assign data to template as array (makes translatable support easier)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
    
        return $result;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        $options = [
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'inlineUsage' => $this->templateParameters['inlineUsage']
        ];
    
        return $this->container->get('form.factory')->create('Zikula\MultisitesModule\Form\Type\ProjectType', $this->entityRef, $options);
    }


    /**
     * Get list of allowed redirect codes.
     *
     * @return array list of possible redirect codes
     */
    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
    
        return $codes;
    }

    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     *
     * @param array $args List of arguments
     *
     * @return string The default redirect url
     */
    protected function getDefaultReturnUrl($args)
    {
        $objectIsPersisted = $args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel');
    
        if (null !== $this->returnTo) {
            
            $isDisplayOrEditPage = substr($this->returnTo, -7) == 'display' || substr($this->returnTo, -4) == 'edit';
            if (!$isDisplayOrEditPage || $objectIsPersisted) {
                // return to referer
                return $this->returnTo;
            }
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
    
        // redirect to the list of projects
        $viewArgs = [];
        $url = $this->router->generate('zikulamultisitesmodule_' . $this->objectTypeLower . '_' . $routeArea . 'view', $viewArgs);
    
        return $url;
    }

    /**
     * Command event handler.
     *
     * This event handler is called when a command is issued by the user.
     *
     * @param array $args List of arguments
     *
     * @return mixed Redirect or false on errors
     */
    public function handleCommand($args = [])
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ($this->form->get('cancel')->isClicked()) {
            $args['commandName'] = 'cancel';
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    Arguments from handleCommand method
     * @param Boolean $success Becomes true if this is a success, false for default error
     *
     * @return String desired status or error message
     */
    protected function getDefaultMessage($args, $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        $message = '';
        switch ($args['commandName']) {
            case 'submit':
                if ($this->templateParameters['mode'] == 'create') {
                    $message = $this->__('Done! Project created.');
                } else {
                    $message = $this->__('Done! Project updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Project deleted.');
                break;
            default:
                $message = $this->__('Done! Project updated.');
                break;
        }
    
        return $message;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args Arguments from handleCommand method
     *
     * @return bool Whether everything worked well or not
     *
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
    
        $success = false;
        $flashBag = $this->request->getSession()->getFlashBag();
        $logger = $this->container->get('logger');
        try {
            // execute the workflow action
            $workflowHelper = $this->container->get('zikula_multisites_module.workflow_helper');
            $success = $workflowHelper->executeAction($entity, $action);
        } catch(\Exception $e) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $e->getMessage());
            $logArgs = ['app' => 'ZikulaMultisitesModule', 'user' => $this->container->get('zikula_users_module.current_user')->get('uname'), 'entity' => 'project', 'id' => $entity->createCompositeIdentifier(), 'errorMessage' => $e->getMessage()];
            $logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && $this->templateParameters['mode'] == 'create') {
            // store new identifier
            foreach ($this->idFields as $idField) {
                $this->idValues[$idField] = $entity[$idField];
            }
        }
        
        if ($args['commandName'] == 'create') {
            $selectionHelper = $this->container->get('zikula_multisites_module.selection_helper');
            // save predefined outgoing relationship to child entity
            if (!empty($this->relationPresets['templates'])) {
                $relObj = $selectionHelper->getEntity('template', $this->relationPresets['templates']);
                if (null !== $relObj) {
                    $relObj->addProjects($entity);
                }
            }
            $this->container->get('doctrine.orm.entity_manager')->flush();
        }
    
        return $success;
    }

    /**
     * Get url to redirect to.
     *
     * @param array $args List of arguments
     *
     * @return string The redirect url
     */
    protected function getRedirectUrl($args)
    {
        if (true === $this->templateParameters['inlineUsage']) {
            $urlArgs = [
                'idPrefix' => $this->idPrefix,
                'commandName' => $args['commandName']
            ];
            foreach ($this->idFields as $idField) {
                $urlArgs[$idField] = $this->idValues[$idField];
            }
    
            // inline usage, return to special function for closing the modal window instance
            return $this->router->generate('zikulamultisitesmodule_' . $this->objectTypeLower . '_handleinlineredirect', $urlArgs);
        }
    
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        if ($this->request->getSession()->has('referer')) {
            $this->request->getSession()->del('referer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'admin':
                return $this->router->generate('zikulamultisitesmodule_' . $this->objectTypeLower . '_adminindex');
            case 'adminView':
                return $this->router->generate('zikulamultisitesmodule_' . $this->objectTypeLower . '_adminview');
            case 'user':
                return $this->router->generate('zikulamultisitesmodule_' . $this->objectTypeLower . '_index');
            case 'userView':
                return $this->router->generate('zikulamultisitesmodule_' . $this->objectTypeLower . '_view');
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
