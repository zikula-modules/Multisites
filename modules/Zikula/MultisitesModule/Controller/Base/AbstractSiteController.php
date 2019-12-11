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

namespace Zikula\MultisitesModule\Controller\Base;

use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\FormExtensionBundle\Form\Type\DeletionType;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\MultisitesModule\Entity\SiteEntity;

/**
 * Site controller base class.
 */
abstract class AbstractSiteController extends AbstractController
{
    
    /**
     * This action provides an item list overview.
     *
     * @param Request $request
     * @param string $sort Sorting field
     * @param string $sortdir Sorting direction
     * @param int $pos Current pager position
     * @param int $num Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws Exception
     */
    protected function viewInternal(
        Request $request,
        $sort,
        $sortdir,
        $pos,
        $num,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        $request->query->set('pos', $pos);
        
        /** @var RouterInterface $router */
        $router = $this->get('router');
        $sortableColumns = new SortableColumns($router, 'zikulamultisitesmodule_site_' . ($isAdmin ? 'admin' : '') . 'view', 'sort', 'sortdir');
        
        $sortableColumns->addColumns([
            new Column('name'),
            new Column('description'),
            new Column('siteAlias'),
            new Column('siteName'),
            new Column('siteDescription'),
            new Column('siteAdminName'),
            new Column('siteAdminPassword'),
            new Column('siteAdminRealName'),
            new Column('siteAdminEmail'),
            new Column('siteCompany'),
            new Column('siteDns'),
            new Column('databaseName'),
            new Column('databaseUserName'),
            new Column('databasePassword'),
            new Column('databaseHost'),
            new Column('databaseType'),
            new Column('logo'),
            new Column('favIcon'),
            new Column('parametersCsvFile'),
            new Column('active'),
            new Column('template'),
            new Column('project'),
            new Column('createdBy'),
            new Column('createdDate'),
            new Column('updatedBy'),
            new Column('updatedDate'),
        ]);
        
        $templateParameters = $controllerHelper->processViewActionParameters($objectType, $sortableColumns, $templateParameters, true);
        
        // filter by permissions
        $templateParameters['items'] = $permissionHelper->filterCollection($objectType, $templateParameters['items'], $permLevel);
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($objectType, 'view', $templateParameters);
    }
    
    
    /**
     * This action provides a handling of edit requests.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws RuntimeException Thrown if another critical error occurs (e.g. workflow actions not available)
     * @throws Exception
     */
    protected function editInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
        $templateParameters = $controllerHelper->processEditActionParameters($objectType, $templateParameters);
        
        // delegate form processing to the form handler
        $formHandler = $this->get('zikula_multisites_module.form.handler.site');
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $this->get('zikula_multisites_module.view_helper')->processTemplate($objectType, 'edit', $templateParameters);
    }
    
    
    /**
     * This action provides a handling of simple delete requests.
     *
     * @param Request $request
     * @param int $id Identifier of treated site instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if site to be deleted isn't found
     * @throws RuntimeException Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    protected function deleteInternal(
        Request $request,
        $id,
        $isAdmin = false
    ) {
        if (null === $site) {
            $site = $this->get('zikula_multisites_module.entity_factory')->getRepository('site')->selectById($id);
        }
        if (null === $site) {
            throw new NotFoundHttpException($this->__('No such site found.'));
        }
        
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_DELETE;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasEntityPermission($site, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $logger = $this->get('logger');
        $logArgs = ['app' => 'ZikulaMultisitesModule', 'user' => $this->get('zikula_users_module.current_user')->get('uname'), 'entity' => 'site', 'id' => $site->getKey()];
        
        // determine available workflow actions
        $workflowHelper = $this->get('zikula_multisites_module.workflow_helper');
        $actions = $workflowHelper->getActionsForObject($site);
        if (false === $actions || !is_array($actions)) {
            $this->addFlash('error', $this->__('Error! Could not determine workflow actions.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but failed to determine available workflow actions.', $logArgs);
            throw new RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
        
        // redirect to the list of sites
        $redirectRoute = 'zikulamultisitesmodule_site_' . ($isAdmin ? 'admin' : '') . 'view';
        
        // check whether deletion is allowed
        $deleteActionId = 'delete';
        $deleteAllowed = false;
        foreach ($actions as $actionId => $action) {
            if ($actionId != $deleteActionId) {
                continue;
            }
            $deleteAllowed = true;
            break;
        }
        if (!$deleteAllowed) {
            $this->addFlash('error', $this->__('Error! It is not allowed to delete this site.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but this action was not allowed.', $logArgs);
        
            return $this->redirectToRoute($redirectRoute);
        }
        
        $form = $this->createForm(DeletionType::class, $site);
        if ($site->supportsHookSubscribers()) {
            $hookHelper = $this->get('zikula_multisites_module.hook_helper');
        
            // Call form aware display hooks
            $formHook = $hookHelper->callFormDisplayHooks($form, $site, FormAwareCategory::TYPE_DELETE);
        }
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                if ($site->supportsHookSubscribers()) {
                    // Let any ui hooks perform additional validation actions
                    $validationErrors = $hookHelper->callValidationHooks($site, UiHooksCategory::TYPE_VALIDATE_DELETE);
                    if (0 < count($validationErrors)) {
                        foreach ($validationErrors as $message) {
                            $this->addFlash('error', $message);
                        }
                    } else {
                        // execute the workflow action
                        $success = $workflowHelper->executeAction($site, $deleteActionId);
                        if ($success) {
                            $this->addFlash('status', $this->__('Done! Item deleted.'));
                            $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', $logArgs);
                        }
                        
                        if ($site->supportsHookSubscribers()) {
                            // Call form aware processing hooks
                            $hookHelper->callFormProcessHooks($form, $site, FormAwareCategory::TYPE_PROCESS_DELETE);
                        
                            // Let any ui hooks know that we have deleted the site
                            $hookHelper->callProcessHooks($site, UiHooksCategory::TYPE_PROCESS_DELETE);
                        }
                        
                        return $this->redirectToRoute($redirectRoute);
                    }
                } else {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($site, $deleteActionId);
                    if ($success) {
                        $this->addFlash('status', $this->__('Done! Item deleted.'));
                        $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', $logArgs);
                    }
                    
                    if ($site->supportsHookSubscribers()) {
                        // Call form aware processing hooks
                        $hookHelper->callFormProcessHooks($form, $site, FormAwareCategory::TYPE_PROCESS_DELETE);
                    
                        // Let any ui hooks know that we have deleted the site
                        $hookHelper->callProcessHooks($site, UiHooksCategory::TYPE_PROCESS_DELETE);
                    }
                    
                    return $this->redirectToRoute($redirectRoute);
                }
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
        
                return $this->redirectToRoute($redirectRoute);
            }
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            'deleteForm' => $form->createView(),
            $objectType => $site
        ];
        if ($site->supportsHookSubscribers()) {
            $templateParameters['formHookTemplates'] = $formHook->getTemplates();
        }
        
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
        $templateParameters = $controllerHelper->processDeleteActionParameters($objectType, $templateParameters, true);
        
        // fetch and return the appropriate template
        return $this->get('zikula_multisites_module.view_helper')->processTemplate($objectType, 'delete', $templateParameters);
    }
    
    
    /**
     * This is a custom action.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function manageExtensionsInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Site/manageExtensions.html.twig', $templateParameters);
    }
    
    
    /**
     * This is a custom action.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function manageThemesInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Site/manageThemes.html.twig', $templateParameters);
    }
    
    
    /**
     * This is a custom action.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function setThemeAsDefaultInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Site/setThemeAsDefault.html.twig', $templateParameters);
    }
    
    
    /**
     * This is a custom action.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function viewToolsInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Site/viewTools.html.twig', $templateParameters);
    }
    
    
    /**
     * This is a custom action.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function executeToolInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Site/executeTool.html.twig', $templateParameters);
    }
    
    
    /**
     * This is a custom action.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function exportDatabaseAsTemplateInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Site/exportDatabaseAsTemplate.html.twig', $templateParameters);
    }
    
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request
     * @param boolean $isAdmin Whether the admin area is used or not
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    protected function handleSelectedEntriesActionInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'site';
        
        // Get parameters
        $action = $request->request->get('action');
        $items = $request->request->get('items');
        if (!is_array($items) || !count($items)) {
            return $this->redirectToRoute('zikulamultisitesmodule_site_' . ($isAdmin ? 'admin' : '') . 'view');
        }
        
        $action = strtolower($action);
        
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository($objectType);
        $workflowHelper = $this->get('zikula_multisites_module.workflow_helper');
        $hookHelper = $this->get('zikula_multisites_module.hook_helper');
        $logger = $this->get('logger');
        $userName = $this->get('zikula_users_module.current_user')->get('uname');
        
        // process each item
        foreach ($items as $itemId) {
            // check if item exists, and get record instance
            $entity = $repository->selectById($itemId, false);
            if (null === $entity) {
                continue;
            }
        
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds, true)) {
                // action not allowed, skip this object
                continue;
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks perform additional validation actions
                $hookType = 'delete' === $action ? UiHooksCategory::TYPE_VALIDATE_DELETE : UiHooksCategory::TYPE_VALIDATE_EDIT;
                $validationErrors = $hookHelper->callValidationHooks($entity, $hookType);
                if (count($validationErrors) > 0) {
                    foreach ($validationErrors as $message) {
                        $this->addFlash('error', $message);
                    }
                    continue;
                }
            }
        
            $success = false;
            try {
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch (Exception $exception) {
                $this->addFlash('error', $this->__f('Sorry, but an error occured during the %action% action.', ['%action%' => $action]) . '  ' . $exception->getMessage());
                $logger->error('{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'action' => $action, 'entity' => 'site', 'id' => $itemId, 'errorMessage' => $exception->getMessage()]);
            }
        
            if (!$success) {
                continue;
            }
        
            if ('delete' === $action) {
                $this->addFlash('status', $this->__('Done! Item deleted.'));
                $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'entity' => 'site', 'id' => $itemId]);
            } else {
                $this->addFlash('status', $this->__('Done! Item updated.'));
                $logger->notice('{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.', ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'action' => $action, 'entity' => 'site', 'id' => $itemId]);
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated or deleted an item
                $hookType = 'delete' === $action ? UiHooksCategory::TYPE_PROCESS_DELETE : UiHooksCategory::TYPE_PROCESS_EDIT;
                $url = null;
                $hookHelper->callProcessHooks($entity, $hookType, $url);
            }
        }
        
        return $this->redirectToRoute('zikulamultisitesmodule_site_' . ($isAdmin ? 'admin' : '') . 'view');
    }
    
}
