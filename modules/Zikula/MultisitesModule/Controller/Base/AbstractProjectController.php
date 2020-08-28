<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 *
 * @see https://modulestudio.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Controller\Base;

use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\MultisitesModule\Entity\ProjectEntity;

/**
 * Project controller base class.
 */
abstract class AbstractProjectController extends AbstractController
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
        $objectType = 'project';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
        ];
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        $request->query->set('pos', $pos);
        
        /** @var RouterInterface $router */
        $router = $this->get('router');
        $routeName = 'zikulamultisitesmodule_project_' . ($isAdmin ? 'admin' : '') . 'view';
        $sortableColumns = new SortableColumns($router, $routeName, 'sort', 'sortdir');
        
        $sortableColumns->addColumns([
            new Column('name'),
            new Column('createdBy'),
            new Column('createdDate'),
            new Column('updatedBy'),
            new Column('updatedDate'),
        ]);
        
        $templateParameters = $controllerHelper->processViewActionParameters(
            $objectType,
            $sortableColumns,
            $templateParameters,
            true
        );
        
        // filter by permissions
        $templateParameters['items'] = $permissionHelper->filterCollection(
            $objectType,
            $templateParameters['items'],
            $permLevel
        );
        
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
        $objectType = 'project';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        $permissionHelper = $this->get('zikula_multisites_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
        ];
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
        
        
        // delegate form processing to the form handler
        $formHandler = $this->get('zikula_multisites_module.form.handler.project');
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $templateParameters = $formHandler->getTemplateParameters();
        
        $templateParameters = $controllerHelper->processEditActionParameters(
            $objectType,
            $templateParameters,
            $templateParameters['project']->supportsHookSubscribers()
        );
        
        // fetch and return the appropriate template
        return $this->get('zikula_multisites_module.view_helper')->processTemplate($objectType, 'edit', $templateParameters);
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
        $objectType = 'project';
        
        // get parameters
        $action = $request->request->get('action');
        $items = $request->request->get('items');
        if (!is_array($items) || !count($items)) {
            return $this->redirectToRoute('zikulamultisitesmodule_project_' . ($isAdmin ? 'admin' : '') . 'view');
        }
        
        $action = mb_strtolower($action);
        
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
                // let any ui hooks perform additional validation actions
                $hookType = 'delete' === $action
                    ? UiHooksCategory::TYPE_VALIDATE_DELETE
                    : UiHooksCategory::TYPE_VALIDATE_EDIT
                ;
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
                $this->addFlash(
                    'error',
                    $this->__f(
                        'Sorry, but an error occured during the %action% action.',
                        ['%action%' => $action]
                    ) . '  ' . $exception->getMessage()
                );
                $logger->error(
                    '{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id},'
                        . ' but failed. Error details: {errorMessage}.',
                    [
                        'app' => 'ZikulaMultisitesModule',
                        'user' => $userName,
                        'action' => $action,
                        'entity' => 'project',
                        'id' => $itemId,
                        'errorMessage' => $exception->getMessage(),
                    ]
                );
            }
        
            if (!$success) {
                continue;
            }
        
            if ('delete' === $action) {
                $this->addFlash(
                    'status',
                    $this->__(
                        'Done! Project deleted.'
                    )
                );
                $logger->notice(
                    '{app}: User {user} deleted the {entity} with id {id}.',
                    [
                        'app' => 'ZikulaMultisitesModule',
                        'user' => $userName,
                        'entity' => 'project',
                        'id' => $itemId,
                    ]
                );
            } else {
                $this->addFlash(
                    'status',
                    $this->__(
                        'Done! Project updated.'
                    )
                );
                $logger->notice(
                    '{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.',
                    [
                        'app' => 'ZikulaMultisitesModule',
                        'user' => $userName,
                        'action' => $action,
                        'entity' => 'project',
                        'id' => $itemId,
                    ]
                );
            }
        
            if ($entity->supportsHookSubscribers()) {
                // let any ui hooks know that we have updated or deleted an item
                $hookType = 'delete' === $action
                    ? UiHooksCategory::TYPE_PROCESS_DELETE
                    : UiHooksCategory::TYPE_PROCESS_EDIT
                ;
                $url = null;
                $hookHelper->callProcessHooks($entity, $hookType, $url);
            }
        }
        
        return $this->redirectToRoute('zikulamultisitesmodule_project_' . ($isAdmin ? 'admin' : '') . 'view');
    }
    
}
