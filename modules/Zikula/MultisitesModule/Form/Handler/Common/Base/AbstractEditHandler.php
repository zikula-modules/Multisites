<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Handler\Common\Base;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\RouteUrl;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\PageLockModule\Api\ApiInterface\LockingApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\MultisitesModule\Entity\Factory\EntityFactory;
use Zikula\MultisitesModule\Helper\ControllerHelper;
use Zikula\MultisitesModule\Helper\HookHelper;
use Zikula\MultisitesModule\Helper\ModelHelper;
use Zikula\MultisitesModule\Helper\PermissionHelper;
use Zikula\MultisitesModule\Helper\WorkflowHelper;

/**
 * This handler class handles the page events of editing forms.
 * It collects common functionality required by different object types.
 */
abstract class AbstractEditHandler
{
    use TranslatorTrait;

    /**
     * Name of treated object type.
     *
     * @var string
     */
    protected $objectType;

    /**
     * Name of treated object type starting with upper case.
     *
     * @var string
     */
    protected $objectTypeCapital;

    /**
     * Lower case version.
     *
     * @var string
     */
    protected $objectTypeLower;

    /**
     * Reference to treated entity instance.
     *
     * @var EntityAccess
     */
    protected $entityRef;

    /**
     * Name of primary identifier field.
     *
     * @var string
     */
    protected $idField;

    /**
     * Identifier of treated entity.
     *
     * @var integer
     */
    protected $idValue = 0;

    /**
     * Code defining the redirect goal after command handling.
     *
     * @var string
     */
    protected $returnTo;

    /**
     * Whether a create action is going to be repeated or not.
     *
     * @var boolean
     */
    protected $repeatCreateAction = false;

    /**
     * Url of current form with all parameters for multiple creations.
     *
     * @var string
     */
    protected $repeatReturnUrl;
    
    /**
     * List of identifiers for predefined relationships.
     *
     * @var mixed
     */
    protected $relationPresets = [];

    /**
     * Full prefix for related items.
     *
     * @var string
     */
    protected $idPrefix = '';

    /**
     * Whether the PageLock extension is used for this entity type or not.
     *
     * @var boolean
     */
    protected $hasPageLockSupport = false;

    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var ModelHelper
     */
    protected $modelHelper;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;

    /**
     * @var HookHelper
     */
    protected $hookHelper;

    /**
     * Reference to optional locking api.
     *
     * @var LockingApiInterface
     */
    protected $lockingApi;

    /**
     * The handled form type.
     *
     * @var Form
     */
    protected $form;

    /**
     * Template parameters.
     *
     * @var array
     */
    protected $templateParameters = [];

    public function __construct(
        ZikulaHttpKernelInterface $kernel,
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        RouterInterface $router,
        LoggerInterface $logger,
        VariableApiInterface $variableApi,
        CurrentUserApiInterface $currentUserApi,
        EntityFactory $entityFactory,
        ControllerHelper $controllerHelper,
        ModelHelper $modelHelper,
        PermissionHelper $permissionHelper,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper
    ) {
        $this->kernel = $kernel;
        $this->setTranslator($translator);
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->logger = $logger;
        $this->variableApi = $variableApi;
        $this->currentUserApi = $currentUserApi;
        $this->entityFactory = $entityFactory;
        $this->controllerHelper = $controllerHelper;
        $this->modelHelper = $modelHelper;
        $this->permissionHelper = $permissionHelper;
        $this->workflowHelper = $workflowHelper;
        $this->hookHelper = $hookHelper;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables
     *
     * @return bool|RedirectResponse Redirect or false on errors
     *
     * @throws AccessDeniedException Thrown if user has not the required permissions
     * @throws RuntimeException Thrown if the workflow actions can not be determined
     */
    public function processForm(array $templateParameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->templateParameters = $templateParameters;
        $this->templateParameters['inlineUsage'] = $request->query->getBoolean('raw');
        $this->idPrefix = $request->query->get('idp', '');
        $session = $request->hasSession() ? $request->getSession() : null;
    
        // initialise redirect goal
        $this->returnTo = $request->query->get('returnTo');
        if (null !== $session) {
            // default to referer
            $refererSessionVar = 'zikulamultisitesmodule' . $this->objectTypeCapital . 'Referer';
            if (null === $this->returnTo && $request->headers->has('referer')) {
                $currentReferer = $request->headers->get('referer');
                if ($currentReferer !== urldecode($request->getUri())) {
                    $this->returnTo = $currentReferer;
                    $session->set($refererSessionVar, $this->returnTo);
                }
            }
            if (null === $this->returnTo && $session->has($refererSessionVar)) {
                $this->returnTo = $session->get($refererSessionVar);
            }
        }
        // store current uri for repeated creations
        $this->repeatReturnUrl = $request->getUri();
    
        $this->idField = $this->entityFactory->getIdField($this->objectType);
    
        // retrieve identifier of the object we wish to edit
        $routeParams = $request->get('_route_params', []);
        if (array_key_exists($this->idField, $routeParams)) {
            $this->idValue = (int) !empty($routeParams[$this->idField]) ? $routeParams[$this->idField] : 0;
        }
        if (0 === $this->idValue) {
            $this->idValue = $request->query->getInt($this->idField);
        }
        if (0 === $this->idValue && 'id' !== $this->idField) {
            $this->idValue = $request->query->getInt('id');
        }
    
        $entity = null;
        $this->templateParameters['mode'] = !empty($this->idValue) ? 'edit' : 'create';
    
        if ('edit' === $this->templateParameters['mode']) {
            $entity = $this->initEntityForEditing();
            if (null !== $entity) {
                if (
                    true === $this->hasPageLockSupport
                    && null !== $this->lockingApi
                    && $this->kernel->isBundle('ZikulaPageLockModule')
                ) {
                    // save entity reference for later reuse
                    $this->entityRef = $entity;
                
                    // try to guarantee that only one person at a time can be editing this entity
                    $lockName = 'ZikulaMultisitesModule' . $this->objectTypeCapital . $entity->getKey();
                    $this->lockingApi->addLock($lockName, $this->getRedirectUrl(['commandName' => '']));
                }
                if (!$this->permissionHelper->mayEdit($entity)) {
                    throw new AccessDeniedException();
                }
            }
        } else {
            $permissionLevel = ACCESS_EDIT;
            if (!$this->permissionHelper->hasComponentPermission($this->objectType, $permissionLevel)) {
                throw new AccessDeniedException();
            }
    
            $entity = $this->initEntityForCreation();
    
            // set default values from request parameters
            foreach ($request->query->all() as $key => $value) {
                if (5 > strlen($key) || 0 !== strpos($key, 'set_')) {
                    continue;
                }
                $fieldName = str_replace('set_', '', $key);
                $setterName = 'set' . ucfirst($fieldName);
                if (!method_exists($entity, $setterName)) {
                    continue;
                }
                $entity[$fieldName] = $value;
            }
        }
    
        if (null === $entity) {
            if (null !== $session) {
                $session->getFlashBag()->add('error', $this->__('No such item found.'));
            }
    
            return new RedirectResponse($this->getRedirectUrl(['commandName' => 'cancel']), 302);
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    
        
        $this->initRelationPresets();
    
        $actions = $this->workflowHelper->getActionsForObject($entity);
        if (false === $actions || !is_array($actions)) {
            if (null !== $session) {
                $session->getFlashBag()->add(
                    'error',
                    $this->__('Error! Could not determine workflow actions.')
                );
            }
            $logArgs = [
                'app' => 'ZikulaMultisitesModule',
                'user' => $this->currentUserApi->get('uname'),
                'entity' => $this->objectType,
                'id' => $entity->getKey()
            ];
            $this->logger->error(
                '{app}: User {user} tried to edit the {entity} with id {id},'
                    . ' but failed to determine available workflow actions.',
                $logArgs
            );
            throw new RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
    
        $this->templateParameters['actions'] = $actions;
    
        $this->form = $this->createForm();
        if (!is_object($this->form)) {
            return false;
        }
    
        if (method_exists($entity, 'supportsHookSubscribers') && $entity->supportsHookSubscribers()) {
            // Call form aware display hooks
            $formHook = $this->hookHelper->callFormDisplayHooks($this->form, $entity, FormAwareCategory::TYPE_EDIT);
            $this->templateParameters['formHookTemplates'] = $formHook->getTemplates();
        }
    
        // handle form request and check validity constraints of edited entity
        $this->form->handleRequest($request);
        if ($this->form->isSubmitted()) {
            if ($this->form->has('cancel') && $this->form->get('cancel')->isClicked()) {
                if (
                    true === $this->hasPageLockSupport
                    && null !== $this->lockingApi
                    && 'edit' === $this->templateParameters['mode']
                    && $this->kernel->isBundle('ZikulaPageLockModule')
                ) {
                    $lockName = 'ZikulaMultisitesModule' . $this->objectTypeCapital . $this->entityRef->getKey();
                    $this->lockingApi->releaseLock($lockName);
                }
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => 'cancel']), 302);
            }
            if ($this->form->isValid()) {
                $result = $this->handleCommand();
                if (false === $result) {
                    $this->templateParameters['form'] = $this->form->createView();
                }
    
                return $result;
            }
        }
    
        $this->templateParameters['form'] = $this->form->createView();
    
        // everything okay, no initialisation errors occured
        return true;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        // to be customised in sub classes
        return null;
    }
    
    /**
     * Returns the form options.
     *
     * @return array
     */
    protected function getFormOptions()
    {
        // to be customised in sub classes
        return [];
    }
    
    
    /**
     * Initialises relationship presets.
     */
    protected function initRelationPresets()
    {
        // to be customised in sub classes
    }
    
    /**
     * Returns the template parameters.
     *
     * @return array
     */
    public function getTemplateParameters()
    {
        return $this->templateParameters;
    }
    
    /**
     * Initialise existing entity for editing.
     *
     * @return EntityAccess|null Desired entity instance or null
     */
    protected function initEntityForEditing()
    {
        return $this->entityFactory->getRepository($this->objectType)->selectById($this->idValue);
    }
    
    /**
     * Initialise new entity for creation.
     *
     * @return EntityAccess|null Desired entity instance or null
     */
    protected function initEntityForCreation()
    {
        $request = $this->requestStack->getCurrentRequest();
        $templateId = $request->query->getInt('astemplate');
        $entity = null;
    
        if (0 < $templateId) {
            // reuse existing entity
            $entityT = $this->entityFactory->getRepository($this->objectType)->selectById($templateId);
            if (null === $entityT) {
                return null;
            }
            $entity = clone $entityT;
        }
    
        if (null === $entity) {
            $createMethod = 'create' . ucfirst($this->objectType);
            $entity = $this->entityFactory->$createMethod();
        }
    
        return $entity;
    }

    /**
     * Returns a list of allowed redirect codes.
     *
     * @return string[] list of possible redirect codes
     */
    protected function getRedirectCodes()
    {
        $codes = [];
    
        // to be filled by subclasses
    
        return $codes;
    }

    /**
     * Command event handler.
     * This event handler is called when a command is issued by the user.
     *
     * @param array $args List of arguments
     *
     * @return bool|RedirectResponse Redirect or false on errors
     */
    public function handleCommand(array $args = [])
    {
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if (
            'create' === $this->templateParameters['mode']
            && $this->form->has('submitrepeat')
            && $this->form->get('submitrepeat')->isClicked()
        ) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        $action = $args['commandName'];
    
        $this->fetchInputData();
    
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        if (method_exists($entity, 'supportsHookSubscribers') && $entity->supportsHookSubscribers()) {
            // let any ui hooks perform additional validation actions
            $hookType = 'delete' === $action
                ? UiHooksCategory::TYPE_VALIDATE_DELETE
                : UiHooksCategory::TYPE_VALIDATE_EDIT
            ;
            $validationErrors = $this->hookHelper->callValidationHooks($entity, $hookType);
            if (0 < count($validationErrors)) {
                $request = $this->requestStack->getCurrentRequest();
                if ($request->hasSession() && ($session = $request->getSession())) {
                    foreach ($validationErrors as $message) {
                        $session->getFlashBag()->add('error', $message);
                    }
                }
    
                return false;
            }
        }
    
        $success = $this->applyAction($args);
        if (!$success) {
            // the workflow operation failed
            return false;
        }
    
        if (method_exists($entity, 'supportsHookSubscribers') && $entity->supportsHookSubscribers()) {
            $entitiesWithDisplayAction = [''];
            $hasDisplayAction = in_array($this->objectType, $entitiesWithDisplayAction, true);
    
            $routeUrl = null;
            if ($hasDisplayAction && 'delete' !== $action) {
                $urlArgs = $entity->createUrlArgs();
                $urlArgs['_locale'] = $this->requestStack->getCurrentRequest()->getLocale();
                $routeUrl = new RouteUrl('zikulamultisitesmodule_' . $this->objectTypeLower . '_display', $urlArgs);
            }
    
            // call form aware processing hooks
            $hookType = 'delete' === $action
                ? FormAwareCategory::TYPE_PROCESS_DELETE
                : FormAwareCategory::TYPE_PROCESS_EDIT
            ;
            $this->hookHelper->callFormProcessHooks($this->form, $entity, $hookType, $routeUrl);
    
            // let any ui hooks know that we have created, updated or deleted an item
            $hookType = 'delete' === $action
                ? UiHooksCategory::TYPE_PROCESS_DELETE
                : UiHooksCategory::TYPE_PROCESS_EDIT
            ;
            $this->hookHelper->callProcessHooks($entity, $hookType, $routeUrl);
        }
    
        if (
            true === $this->hasPageLockSupport
            && null !== $this->lockingApi
            && 'edit' === $this->templateParameters['mode']
            && $this->kernel->isBundle('ZikulaPageLockModule')
        ) {
            $lockName = 'ZikulaMultisitesModule' . $this->objectTypeCapital . $this->entityRef->getKey();
            $this->lockingApi->releaseLock($lockName);
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array $args List of arguments from handleCommand method
     * @param bool $success Becomes true if this is a success, false for default error
     *
     * @return string desired status or error message
     */
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        $message = '';
        switch ($args['commandName']) {
            case 'create':
                if (true === $success) {
                    $message = $this->__('Done! Item created.');
                } else {
                    $message = $this->__('Error! Creation attempt failed.');
                }
                break;
            case 'update':
                if (true === $success) {
                    $message = $this->__('Done! Item updated.');
                } else {
                    $message = $this->__('Error! Update attempt failed.');
                }
                break;
            case 'delete':
                if (true === $success) {
                    $message = $this->__('Done! Item deleted.');
                } else {
                    $message = $this->__('Error! Deletion attempt failed.');
                }
                break;
        }
    
        return $message;
    }
    
    /**
     * Add success or error message to session.
     *
     * @param array $args List of arguments from handleCommand method
     * @param bool $success Becomes true if this is a success, false for default error
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    protected function addDefaultMessage(array $args = [], $success = false)
    {
        $message = $this->getDefaultMessage($args, $success);
        if (empty($message)) {
            return;
        }
    
        $flashType = true === $success ? 'status' : 'error';
        $request = $this->requestStack->getCurrentRequest();
        if ($request->hasSession() && ($session = $request->getSession())) {
            $session->getFlashBag()->add($flashType, $message);
        }
        $logArgs = [
            'app' => 'ZikulaMultisitesModule',
            'user' => $this->currentUserApi->get('uname'),
            'entity' => $this->objectType,
            'id' => $this->entityRef->getKey()
        ];
        if (true === $success) {
            $this->logger->notice('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        } else {
            $this->logger->error('{app}: User {user} tried to update the {entity} with id {id}, but failed.', $logArgs);
        }
    }

    /**
     * Input data processing called by handleCommand method.
     *
     * @return mixed
     */
    public function fetchInputData()
    {
        // fetch posted data input values as an associative array
        $formData = $this->form->getData();
    
        if (method_exists($this->entityRef, 'getCreatedBy')) {
            if (
                isset($this->form['moderationSpecificCreator'])
                && null !== $this->form['moderationSpecificCreator']->getData()
            ) {
                $this->entityRef->setCreatedBy($this->form['moderationSpecificCreator']->getData());
            }
            if (
                isset($this->form['moderationSpecificCreationDate'])
                && '' !== $this->form['moderationSpecificCreationDate']->getData()
            ) {
                $this->entityRef->setCreatedDate($this->form['moderationSpecificCreationDate']->getData());
            }
        }
    
        // return remaining form data
        return $formData;
    }

    /**
     * Executes a certain workflow action.
     *
     * @param array $args List of arguments from handleCommand method
     *
     * @return bool Whether everything worked well or not
     */
    public function applyAction(array $args = [])
    {
        // stub for subclasses
        return false;
    }

    /**
     * Sets optional locking api reference.
     *
     * @param LockingApiInterface $lockingApi
     */
    public function setLockingApi(LockingApiInterface $lockingApi)
    {
        $this->lockingApi = $lockingApi;
    }
}
