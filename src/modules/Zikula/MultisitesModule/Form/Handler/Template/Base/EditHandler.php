<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Handler\Template\Base;

use Zikula\MultisitesModule\Form\Handler\Common\EditHandler as BaseEditHandler;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ModUtil;
use RuntimeException;
use System;
use UserUtil;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the template object type.
 */
class EditHandler extends BaseEditHandler
{
    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables.
     *
     * @return boolean False in case of initialisation errors, otherwise true.
     */
    public function processForm(array $templateParameters)
    {
        $this->objectType = 'template';
        $this->objectTypeCapital = 'Template';
        $this->objectTypeLower = 'template';
        
        $this->hasPageLockSupport = true;
        // array with upload fields and mandatory flags
        $this->uploadFields = ['sqlFile' => true];
    
        $result = parent::processForm($templateParameters);
        if (false === $result) {
            return $result;
        }
    
        if ($this->templateParameters['mode'] == 'create') {
            $modelHelper = $this->container->get('zikulamultisitesmodule.model_helper');
            if (!$modelHelper->canBeCreated($this->objectType)) {
                $this->request->getSession()->getFlashBag()->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Sorry, but you can not create the template yet as other items are required which must be created before!'));
                $logger = $this->container->get('logger');
                $logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'entity' => $this->objectType]);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
    
        $entity = $this->entityRef;
        
        // assign identifiers of predefined incoming relationships
        // editable relation, we store the id and assign it now to show it in UI
        $this->relationPresets['projects'] = $this->request->get('projects', '');
        if (!empty($this->relationPresets['projects'])) {
            $relObj = ModUtil::apiFunc('ZikulaMultisitesModule', 'selection', 'getEntity', ['ot' => 'project', 'id' => $this->relationPresets['projects']]);
            if ($relObj != null) {
                $entity->addProjects($relObj);
            }
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    
        $entityData = $entity->toArray();
    
        // assign data to template as array (makes translatable support easier)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
    
        if ($this->templateParameters['mode'] == 'edit') {
            // assign formatted title (used for image thumbnails)
            $this->templateParameters['formattedEntityTitle'] = $entity->getTitleFromDisplayPattern();
        }
    
        // everything okay, no initialisation errors occured
        return true;
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
    
        return $this->container->get('form.factory')->create('Zikula\MultisitesModule\Form\Type\TemplateType', $this->entityRef, $options);
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
     * @param array $args List of arguments.
     *
     * @return string The default redirect url.
     */
    protected function getDefaultReturnUrl($args)
    {
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
    
        // redirect to the list of templates
        $viewArgs = [];
        $url = $this->router->generate('zikulamultisitesmodule_' . strtolower($this->objectType) . '_' . $routeArea . 'view', $viewArgs);
    
        return $url;
    }

    /**
     * Command event handler.
     *
     * This event handler is called when a command is issued by the user.
     *
     * @param array $args List of arguments.
     *
     * @return mixed Redirect or false on errors.
     */
    public function handleCommand($args)
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    Arguments from handleCommand method.
     * @param Boolean $success Becomes true if this is a success, false for default error.
     *
     * @return String desired status or error message.
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
                    $message = $this->__('Done! Template created.');
                } else {
                    $message = $this->__('Done! Template updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Template deleted.');
                break;
            default:
                $message = $this->__('Done! Template updated.');
                break;
        }
    
        return $message;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args Arguments from handleCommand method.
     *
     * @return bool Whether everything worked well or not.
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
            $workflowHelper = $this->container->get('zikulamultisitesmodule.workflow_helper');
            $success = $workflowHelper->executeAction($entity, $action);
        } catch(\Exception $e) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__f('Sorry, but an unknown error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]));
            $logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'entity' => 'template', 'id' => $entity->createCompositeIdentifier(), 'errorMessage' => $e->getMessage()]);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && $this->templateParameters['mode'] == 'create') {
            // store new identifier
            foreach ($this->idFields as $idField) {
                $this->idValues[$idField] = $entity[$idField];
            }
        }
    
        return $success;
    }

    /**
     * Get url to redirect to.
     *
     * @param array $args List of arguments.
     *
     * @return string The redirect url.
     */
    protected function getRedirectUrl($args)
    {
        if ($this->templateParameters['inlineUsage'] == true) {
            $urlArgs = [
                'idPrefix' => $this->idPrefix,
                'commandName' => $args['commandName']
            ];
            foreach ($this->idFields as $idField) {
                $urlArgs[$idField] = $this->idValues[$idField];
            }
    
            // inline usage, return to special function for closing the modal window instance
            return $this->router->generate('zikulamultisitesmodule_' . strtolower($this->objectType) . '_handleinlineredirect', $urlArgs);
        }
    
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'admin':
                return $this->router->generate('zikulamultisitesmodule_' . strtolower($this->objectType) . '_adminindex');
            case 'adminView':
                return $this->router->generate('zikulamultisitesmodule_' . strtolower($this->objectType) . '_adminview');
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
