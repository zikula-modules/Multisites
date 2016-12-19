<?php
/**
 * Multisites.
 *
 * @copyright Albert P?rez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert P?rez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Controller\Base;

use Zikula\MultisitesModule\Entity\SiteExtensionEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FormUtil;
use ModUtil;
use RuntimeException;
use System;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;

/**
 * Site extension controller base class.
 */
abstract class AbstractSiteExtensionController extends AbstractController
{
    /**
     * This action provides an item list overview in the admin area.
     * @Cache(expires="+2 hours", public=false)
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field
     * @param string  $sortdir      Sorting direction
     * @param int     $pos          Current pager position
     * @param int     $num          Amount of entries to display
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * This action provides an item list overview.
     * @Cache(expires="+2 hours", public=false)
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field
     * @param string  $sortdir      Sorting direction
     * @param int     $pos          Current pager position
     * @param int     $num          Amount of entries to display
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, false);
    }
    
    /**
     * This method includes the common implementation code for adminView() and view().
     */
    protected function viewInternal(Request $request, $sort, $sortdir, $pos, $num, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'siteExtension';
        $utilArgs = ['controller' => 'siteExtension', 'action' => 'view'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $repository = $this->get('zikula_multisites_module.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);
        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $imageHelper = $this->get('zikula_multisites_module.image_helper');
        $selectionHelper = $this->get('zikula_multisites_module.selection_helper');
        
        // convenience vars to make code clearer
        $currentUrlArgs = [];
        $where = '';
        
        $showOwnEntries = $request->query->getInt('own', $this->getVar('showOnlyOwnEntries', 0));
        $showAllEntries = $request->query->getInt('all', 0);
        
        if (!$showAllEntries) {
            $csv = $request->getRequestFormat() == 'csv' ? 1 : 0;
            if ($csv == 1) {
                $showAllEntries = 1;
            }
        }
        
        $templateParameters['own'] = $showAllEntries;
        $templateParameters['all'] = $showOwnEntries;
        if ($showAllEntries == 1) {
            $currentUrlArgs['all'] = 1;
        }
        if ($showOwnEntries == 1) {
            $currentUrlArgs['own'] = 1;
        }
        
        $additionalParameters = $repository->getAdditionalTemplateParameters($imageHelper, 'controllerAction', $utilArgs);
        
        $resultsPerPage = 0;
        if ($showAllEntries != 1) {
            // the number of items displayed on a page for pagination
            $resultsPerPage = $num;
            if ($resultsPerPage == 0) {
                $resultsPerPage = $this->getVar($objectType . 'EntriesPerPage', 10);
            }
        }
        
        // parameter for used sorting field
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
            System::queryStringSetVar('sort', $sort);
            $request->query->set('sort', $sort);
            // set default sorting in route parameters (e.g. for the pager)
            $routeParams = $request->attributes->get('_route_params');
            $routeParams['sort'] = $sort;
            $request->attributes->set('_route_params', $routeParams);
        }
        
        // parameter for used sort order
        $sortdir = strtolower($sortdir);
        
        $sortableColumns = new SortableColumns($this->get('router'), 'zikulamultisitesmodule_siteextension_' . ($isAdmin ? 'admin' : '') . 'view', 'sort', 'sortdir');
        $sortableColumns->addColumns([
            new Column('name'),
            new Column('extensionVersion'),
            new Column('extensionType'),
            new Column('site'),
            new Column('createdUserId'),
            new Column('createdDate'),
            new Column('updatedUserId'),
            new Column('updatedDate'),
        ]);
        
        $additionalUrlParameters = [
            'all' => $showAllEntries,
            'own' => $showOwnEntries,
            'num' => $resultsPerPage
        ];
        foreach ($additionalParameters as $parameterName => $parameterValue) {
            if (false !== stripos($parameterName, 'thumbRuntimeOptions')) {
                continue;
            }
            $additionalUrlParameters[$parameterName] = $parameterValue;
        }
        
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['num'] = $resultsPerPage;
        
        $quickNavForm = $this->createForm('Zikula\MultisitesModule\Form\Type\QuickNavigation\\' . ucfirst($objectType) . 'QuickNavType', $templateParameters);
        if ($quickNavForm->handleRequest($request) && $quickNavForm->isSubmitted()) {
            $quickNavData = $quickNavForm->getData();
            foreach ($quickNavData as $fieldName => $fieldValue) {
                if ($fieldName == 'routeArea') {
                    continue;
                }
                if ($fieldName == 'all') {
                    $showAllEntries = $additionalUrlParameters['all'] = $templateParameters['all'] = $fieldValue;
                } elseif ($fieldName == 'own') {
                    $showOwnEntries = $additionalUrlParameters['own'] = $templateParameters['own'] = $fieldValue;
                } elseif ($fieldName == 'num') {
                    $resultsPerPage = $additionalUrlParameters['num'] = $fieldValue;
                } else {
                    // set filter as query argument, fetched inside repository
                    $request->query->set($fieldName, $fieldValue);
                }
            }
        }
        $sortableColumns->setOrderBy($sortableColumns->getColumn($sort), strtoupper($sortdir));
        $sortableColumns->setAdditionalUrlParameters($additionalUrlParameters);
        
        if ($showAllEntries == 1) {
            // retrieve item list without pagination
            $entities = $selectionHelper->getEntities($objectType, [], $where, $sort . ' ' . $sortdir);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = $pos;
        
            // retrieve item list with pagination
            list($entities, $objectCount) = $selectionHelper->getEntitiesPaginated($objectType, $where, $sort . ' ' . $sortdir, $currentPage, $resultsPerPage);
        
            $templateParameters['currentPage'] = $currentPage;
            $templateParameters['pager'] = ['numitems' => $objectCount, 'itemsperpage' => $resultsPerPage];
        }
        
        foreach ($entities as $k => $entity) {
            $entity->initWorkflow();
        }
        
        // build RouteUrl instance for display hooks
        $currentUrlArgs['_locale'] = $request->getLocale();
        $currentUrlObject = new RouteUrl('zikulamultisitesmodule_siteExtension_' . /*($isAdmin ? 'admin' : '') . */'view', $currentUrlArgs);
        
        $templateParameters['items'] = $entities;
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['num'] = $resultsPerPage;
        $templateParameters['currentUrlObject'] = $currentUrlObject;
        $templateParameters = array_merge($templateParameters, $additionalParameters);
        
        $templateParameters['sort'] = $sortableColumns->generateSortableColumns();
        $templateParameters['quickNavForm'] = $quickNavForm->createView();
        
        $templateParameters['showAllEntries'] = $templateParameters['all'];
        $templateParameters['showOwnEntries'] = $templateParameters['own'];
        
        $modelHelper = $this->get('zikula_multisites_module.model_helper');
        $templateParameters['canBeCreated'] = $modelHelper->canBeCreated($objectType);
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'view', $request, $templateParameters);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return bool true on sucess, false on failure
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, true);
    }
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return bool true on sucess, false on failure
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminHandleSelectedEntriesAction() and handleSelectedEntriesAction().
     */
    protected function handleSelectedEntriesActionInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'siteExtension';
        
        // Get parameters
        $action = $request->request->get('action', null);
        $items = $request->request->get('items', null);
        
        $action = strtolower($action);
        
        $workflowHelper = $this->get('zikula_multisites_module.workflow_helper');
        $hookHelper = $this->get('zikula_multisites_module.hook_helper');
        $logger = $this->get('logger');
        $userName = $this->get('zikula_users_module.current_user')->get('uname');
        
        // process each item
        foreach ($items as $itemid) {
            // check if item exists, and get record instance
            $selectionHelper = $this->get('zikula_multisites_module.selection_helper');
            $entity = $selectionHelper->getEntity($objectType, $itemid, false);
        
            $entity->initWorkflow();
        
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds)) {
                // action not allowed, skip this object
                continue;
            }
        
            // Let any hooks perform additional validation actions
            $hookType = $action == 'delete' ? 'validate_delete' : 'validate_edit';
            $validationHooksPassed = $hookHelper->callValidationHooks($entity, $hookType);
            if (!$validationHooksPassed) {
                continue;
            }
        
            $success = false;
            try {
                if (!$entity->validate()) {
                    continue;
                }
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch(\Exception $e) {
                $this->addFlash('error', $this->__f('Sorry, but an unknown error occured during the %s action. Please apply the changes again!', ['%s' => $action]));
                $logger->error('{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'action' => $action, 'entity' => 'site extension', 'id' => $itemid, 'errorMessage' => $e->getMessage()]);
            }
        
            if (!$success) {
                continue;
            }
        
            if ($action == 'delete') {
                $this->addFlash('status', $this->__('Done! Item deleted.'));
                $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'entity' => 'site extension', 'id' => $itemid]);
            } else {
                $this->addFlash('status', $this->__('Done! Item updated.'));
                $logger->notice('{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.', ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'action' => $action, 'entity' => 'site extension', 'id' => $itemid]);
            }
        
            // Let any hooks know that we have updated or deleted an item
            $hookType = $action == 'delete' ? 'process_delete' : 'process_edit';
            $url = null;
            if ($action != 'delete') {
                $urlArgs = $entity->createUrlArgs();
                $urlArgs['_locale'] = $request->getLocale();
                $url = new RouteUrl('zikulamultisitesmodule_siteExtension_' . /*($isAdmin ? 'admin' : '') . */'display', $urlArgs);
            }
            $hookHelper->callProcessHooks($entity, $hookType, $url);
        }
        
        return $this->redirectToRoute('zikulamultisitesmodule_siteextension_' . ($isAdmin ? 'admin' : '') . 'index');
    }
}
