<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.1.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Handler\Site\Base;

use Zikula\MultisitesModule\Form\Handler\Common\EditHandler;
use Zikula\MultisitesModule\Form\Type\SiteType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the site object type.
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
    public function processForm(array $templateParameters = [])
    {
        $this->objectType = 'site';
        $this->objectTypeCapital = 'Site';
        $this->objectTypeLower = 'site';
        
        $this->hasPageLockSupport = true;
    
        $result = parent::processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
    
        if ($this->templateParameters['mode'] == 'create') {
            if (!$this->modelHelper->canBeCreated($this->objectType)) {
                $this->request->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the site yet as other items are required which must be created before!'));
                $logArgs = ['app' => 'ZikulaMultisitesModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType];
                $this->logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
    
        return $result;
    }
    
    /**
     * Initialises relationship presets.
     */
    protected function initRelationPresets()
    {
        $entity = $this->entityRef;
    
        
        // assign identifiers of predefined incoming relationships
        // editable relation, we store the id and assign it now to show it in UI
        $this->relationPresets['template'] = $this->request->get('template', '');
        if (!empty($this->relationPresets['template'])) {
            $relObj = $this->entityFactory->getRepository('template')->selectById($this->relationPresets['template']);
            if (null !== $relObj) {
                $relObj->addSites($entity);
            }
        }
        // editable relation, we store the id and assign it now to show it in UI
        $this->relationPresets['project'] = $this->request->get('project', '');
        if (!empty($this->relationPresets['project'])) {
            $relObj = $this->entityFactory->getRepository('project')->selectById($this->relationPresets['project']);
            if (null !== $relObj) {
                $relObj->addSites($entity);
            }
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        $options = [
            'entity' => $this->entityRef,
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionApi->hasPermission($this->permissionComponent, $this->idValue . '::', ACCESS_ADMIN),
            'filter_by_ownership' => !$this->permissionApi->hasPermission($this->permissionComponent, $this->idValue . '::', ACCESS_ADD),
            'inline_usage' => $this->templateParameters['inlineUsage']
        ];
    
        return $this->formFactory->create(SiteType::class, $this->entityRef, $options);
    }


    /**
     * Get list of allowed redirect codes.
     *
     * @return string[] list of possible redirect codes
     */
    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
        // user list of sites
        $codes[] = 'userView';
        // admin list of sites
        $codes[] = 'adminView';
        // user list of own sites
        $codes[] = 'userOwnView';
        // admin list of own sites
        $codes[] = 'adminOwnView';
    
        // user list of templates
        $codes[] = 'userViewTemplates';
        // admin list of templates
        $codes[] = 'adminViewTemplates';
        // user list of own templates
        $codes[] = 'userOwnViewTemplates';
        // admin list of own templates
        $codes[] = 'adminOwnViewTemplates';
        // user list of projects
        $codes[] = 'userViewProjects';
        // admin list of projects
        $codes[] = 'adminViewProjects';
        // user list of own projects
        $codes[] = 'userOwnViewProjects';
        // admin list of own projects
        $codes[] = 'adminOwnViewProjects';
    
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
    protected function getDefaultReturnUrl(array $args = [])
    {
        $objectIsPersisted = $args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel');
    
        if (null !== $this->returnTo) {
            $refererParts = explode('/', $this->returnTo);
            $isDisplayOrEditPage = $refererParts[count($refererParts)-1] == $this->idValue;
            if (!$isDisplayOrEditPage || $objectIsPersisted) {
                // return to referer
                return $this->returnTo;
            }
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
        $routePrefix = 'zikulamultisitesmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of sites
        $url = $this->router->generate($routePrefix . 'view');
    
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
    public function handleCommand(array $args = [])
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
        if ($this->templateParameters['mode'] == 'create' && $this->form->get('submitrepeat')->isClicked()) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
        if ($this->form->get('cancel')->isClicked()) {
            $args['commandName'] = 'cancel';
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    List of arguments from handleCommand method
     * @param boolean $success Becomes true if this is a success, false for default error
     *
     * @return String desired status or error message
     */
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        $message = '';
        switch ($args['commandName']) {
            case 'submit':
                if ($this->templateParameters['mode'] == 'create') {
                    $message = $this->__('Done! Site created.');
                } else {
                    $message = $this->__('Done! Site updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Site deleted.');
                break;
            default:
                $message = $this->__('Done! Site updated.');
                break;
        }
    
        return $message;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args List of arguments from handleCommand method
     *
     * @return boolean Whether everything worked well or not
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
        try {
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $exception->getMessage());
            $logArgs = ['app' => 'ZikulaMultisitesModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'site', 'id' => $entity->getKey(), 'errorMessage' => $exception->getMessage()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && $this->templateParameters['mode'] == 'create') {
            // store new identifier
            $this->idValue = $entity->getKey();
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
    protected function getRedirectUrl(array $args = [])
    {
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        if ($this->request->getSession()->has('zikulamultisitesmodule' . $this->objectTypeCapital . 'Referer')) {
            $this->request->getSession()->remove('zikulamultisitesmodule' . $this->objectTypeCapital . 'Referer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = substr($this->returnTo, 0, 5) == 'admin' ? 'admin' : '';
        $routePrefix = 'zikulamultisitesmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userView':
            case 'adminView':
                return $this->router->generate($routePrefix . 'view');
            case 'userOwnView':
            case 'adminOwnView':
                return $this->router->generate($routePrefix . 'view', [ 'own' => 1 ]);
            case 'userViewTemplates':
            case 'adminViewTemplates':
                return $this->router->generate('zikulamultisitesmodule_template_' . $routeArea . 'view');
            case 'userOwnViewTemplates':
            case 'adminOwnViewTemplates':
                return $this->router->generate('zikulamultisitesmodule_template_' . $routeArea . 'view', ['own' => 1]);
            case 'userViewProjects':
            case 'adminViewProjects':
                return $this->router->generate('zikulamultisitesmodule_project_' . $routeArea . 'view');
            case 'userOwnViewProjects':
            case 'adminOwnViewProjects':
                return $this->router->generate('zikulamultisitesmodule_project_' . $routeArea . 'view', ['own' => 1]);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
