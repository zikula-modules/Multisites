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

namespace Zikula\MultisitesModule\Form\Handler\Template\Base;

use Zikula\MultisitesModule\Form\Handler\Common\EditHandler;
use Zikula\MultisitesModule\Form\Type\TemplateType;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Zikula\MultisitesModule\Entity\TemplateEntity;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the template object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    public function processForm(array $templateParameters = [])
    {
        $this->objectType = 'template';
        $this->objectTypeCapital = 'Template';
        $this->objectTypeLower = 'template';
        
        $this->hasPageLockSupport = true;
    
        $result = parent::processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
    
        if ('create' === $this->templateParameters['mode'] && !$this->modelHelper->canBeCreated($this->objectType)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add(
                'error',
                $this->__('Sorry, but you can not create the template yet as other items are required which must be created before!')
            );
            $logArgs = [
                'app' => 'ZikulaMultisitesModule',
                'user' => $this->currentUserApi->get('uname'),
                'entity' => $this->objectType
            ];
            $this->logger->notice(
                '{app}: User {user} tried to create a new {entity}, but failed'
                    . ' as other items are required which must be created before.',
                $logArgs
            );
    
            return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
        $this->templateParameters['supportsHookSubscribers'] = $this->entityRef->supportsHookSubscribers();
    
        return $result;
    }
    
    protected function initRelationPresets()
    {
        $entity = $this->entityRef;
        
        // assign identifiers of predefined incoming relationships
        // editable relation, we store the id and assign it now to show it in UI
        $this->relationPresets['projects'] = $this->requestStack->getCurrentRequest()->query->get('projects', '');
        if (!empty($this->relationPresets['projects'])) {
            $repository = $this->entityFactory->getRepository('project');
            $relObj = $repository->selectById($this->relationPresets['projects']);
            if (null !== $relObj) {
                $entity->addProjects($relObj);
            }
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    }
    
    protected function createForm()
    {
        return $this->formFactory->create(TemplateType::class, $this->entityRef, $this->getFormOptions());
    }
    
    protected function getFormOptions()
    {
        $options = [
            'entity' => $this->entityRef,
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADMIN),
            'allow_moderation_specific_creator' => (bool)$this->variableApi->get(
                'ZikulaMultisitesModule',
                'allowModerationSpecificCreatorFor' . $this->objectTypeCapital,
                false
            ),
            'allow_moderation_specific_creation_date' => (bool)$this->variableApi->get(
                'ZikulaMultisitesModule',
                'allowModerationSpecificCreationDateFor' . $this->objectTypeCapital,
                false
            ),
            'filter_by_ownership' => !$this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADD),
            'inline_usage' => $this->templateParameters['inlineUsage']
        ];
    
        return $options;
    }

    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
        // user list of templates
        $codes[] = 'userView';
        // admin list of templates
        $codes[] = 'adminView';
        // user list of own templates
        $codes[] = 'userOwnView';
        // admin list of own templates
        $codes[] = 'adminOwnView';
    
    
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
        $objectIsPersisted = 'delete' !== $args['commandName']
            && !('create' === $this->templateParameters['mode'] && 'cancel' === $args['commandName']
        );
        if (null !== $this->returnTo && $objectIsPersisted) {
            // return to referer
            return $this->returnTo;
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters)
            ? $this->templateParameters['routeArea']
            : ''
        ;
        $routePrefix = 'zikulamultisitesmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of templates
        $url = $this->router->generate($routePrefix . 'view');
    
        return $url;
    }

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
        if (
            'create' === $this->templateParameters['mode']
            && $this->form->has('submitrepeat')
            && $this->form->get('submitrepeat')->isClicked()
        ) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        switch ($args['commandName']) {
            case 'submit':
                if ('create' === $this->templateParameters['mode']) {
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
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        /** @var TemplateEntity $entity */
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
    
        $success = false;
        $flashBag = $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
        try {
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (Exception $exception) {
            $flashBag->add(
                'error',
                $this->__f(
                    'Sorry, but an error occured during the %action% action. Please apply the changes again!',
                    ['%action%' => $action]
                ) . ' ' . $exception->getMessage()
            );
            $logArgs = [
                'app' => 'ZikulaMultisitesModule',
                'user' => $this->currentUserApi->get('uname'),
                'entity' => 'template',
                'id' => $entity->getKey(),
                'errorMessage' => $exception->getMessage()
            ];
            $this->logger->error(
                '{app}: User {user} tried to edit the {entity} with id {id},'
                    . ' but failed. Error details: {errorMessage}.',
                $logArgs
            );
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && 'create' === $this->templateParameters['mode']) {
            // store new identifier
            $this->idValue = $entity->getKey();
        }
    
        return $success;
    }

    /**
     * Get URL to redirect to.
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
    
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session->has('zikulamultisitesmodule' . $this->objectTypeCapital . 'Referer')) {
            $this->returnTo = $session->get('zikulamultisitesmodule' . $this->objectTypeCapital . 'Referer');
            $session->remove('zikulamultisitesmodule' . $this->objectTypeCapital . 'Referer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes(), true)) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = 0 === strpos($this->returnTo, 'admin') ? 'admin' : '';
        $routePrefix = 'zikulamultisitesmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userView':
            case 'adminView':
                return $this->router->generate($routePrefix . 'view');
            case 'userOwnView':
            case 'adminOwnView':
                return $this->router->generate($routePrefix . 'view', [ 'own' => 1 ]);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
