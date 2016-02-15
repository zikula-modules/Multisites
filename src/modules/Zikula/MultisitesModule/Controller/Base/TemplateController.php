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

namespace Zikula\MultisitesModule\Controller\Base;

use Zikula\MultisitesModule\Entity\TemplateEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FormUtil;
use JCSSUtil;
use ModUtil;
use System;
use UserUtil;
use ZLanguage;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\ModUrl;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Template controller base class.
 */
class TemplateController extends AbstractController
{
    /**
     * This action provides an item list overview in the admin area.
     * @Theme("admin")
     * @Cache(expires="+2 hours", public=false)
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field.
     * @param string  $sortdir      Sorting direction.
     * @param int     $pos          Current pager position.
     * @param int     $num          Amount of entries to display.
     * @param string  $tpl          Name of alternative template (to be used instead of the default template).
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * This action provides an item list overviewnull.
     * @Cache(expires="+2 hours", public=false)
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field.
     * @param string  $sortdir      Sorting direction.
     * @param int     $pos          Current pager position.
     * @param int     $num          Amount of entries to display.
     * @param string  $tpl          Name of alternative template (to be used instead of the default template).
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
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
        $controllerHelper = $this->get('zikulamultisitesmodule.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = 'template';
        $utilArgs = ['controller' => 'template', 'action' => 'view'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        // temporary workarounds
        // let repository know if we are in admin or user area
        $request->query->set('lct', $isAdmin ? 'admin' : 'user');
        // let entities know if we are in admin or user area
        System::queryStringSetVar('lct', $isAdmin ? 'admin' : 'user');
        
        $repository = $this->get('zikulamultisitesmodule.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);
        $viewHelper = $this->get('zikulamultisitesmodule.view_helper');
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
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
        
        $templateParameters['showOwnEntries'] = $showOwnEntries;
        $templateParameters['showAllEntries'] = $showAllEntries;
        if ($showOwnEntries == 1) {
            $currentUrlArgs['own'] = 1;
        }
        if ($showAllEntries == 1) {
            $currentUrlArgs['all'] = 1;
        }
        
        $additionalParameters = $repository->getAdditionalTemplateParameters('controllerAction', $utilArgs);
        
        $resultsPerPage = 0;
        if ($showAllEntries != 1) {
            // the number of items displayed on a page for pagination
            $resultsPerPage = $num;
            if ($resultsPerPage == 0) {
                $resultsPerPage = $this->getVar('pageSize', 10);
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
        
        $sortableColumns = new SortableColumns($this->get('router'), 'zikulamultisitesmodule_template_' . ($isAdmin ? 'admin' : '') . 'view', 'sort', 'sortdir');
        $sortableColumns->addColumns([
            new Column('name'),
            new Column('description'),
            new Column('sqlFile'),
            new Column('createdUserId'),
            new Column('createdDate'),
            new Column('updatedUserId'),
            new Column('updatedDate'),
        ]);
        $sortableColumns->setOrderBy($sortableColumns->getColumn($sort), strtoupper($sortdir));
        
        $additionalUrlParameters = [
            'all' => $showAllEntries,
            'own' => $showOwnEntries,
            'pageSize' => $resultsPerPage
        ];
        $additionalUrlParameters = array_merge($additionalUrlParameters, $additionalParameters);
        $sortableColumns->setAdditionalUrlParameters($additionalUrlParameters);
        
        $selectionArgs = [
            'ot' => $objectType,
            'where' => $where,
            'orderBy' => $sort . ' ' . $sortdir
        ];
        if ($showAllEntries == 1) {
            // retrieve item list without pagination
            $entities = ModUtil::apiFunc($this->name, 'selection', 'getEntities', $selectionArgs);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = $pos;
        
            // retrieve item list with pagination
            $selectionArgs['currentPage'] = $currentPage;
            $selectionArgs['resultsPerPage'] = $resultsPerPage;
            list($entities, $objectCount) = ModUtil::apiFunc($this->name, 'selection', 'getEntitiesPaginated', $selectionArgs);
        
            $templateParameters['currentPage'] = $currentPage;
            $templateParameters['pager'] = ['numitems' => $objectCount, 'itemsperpage' => $resultsPerPage];
        }
        
        foreach ($entities as $k => $entity) {
            $entity->initWorkflow();
        }
        
        // build ModUrl instance for display hooks
        $currentUrlObject = new ModUrl($this->name, 'template', 'view', ZLanguage::getLanguageCode(), $currentUrlArgs);
        
        $templateParameters['items'] = $entities;
        $templateParameters['sort'] = $sortableColumns->generateSortableColumns();
        $templateParameters['sdir'] = $sortdir;
        $templateParameters['pagesize'] = $resultsPerPage;
        $templateParameters['currentUrlObject'] = $currentUrlObject;
        $templateParameters = array_merge($templateParameters, $additionalParameters);
        
        $formOptions = [
            'all' => $templateParameters['showAllEntries'],
            'own' => $templateParameters['showOwnEntries']
        ];
        $form = $this->createForm('Zikula\MultisitesModule\Form\Type\QuickNavigation\\' . ucfirst($objectType) . 'QuickNavType', $templateParameters, $formOptions)
            ->setMethod('GET');
        
        $templateParameters['quickNavForm'] = $form;
        
        
        
        $modelHelper = $this->get('zikulamultisitesmodule.model_helper');
        $templateParameters['canBeCreated'] = $modelHelper->canBeCreated($objectType);
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'view', $request, $templateParameters);
    }
    /**
     * This action provides a handling of edit requests in the admin area.
     * @Theme("admin")
     * @Cache(lastModified="template.getUpdatedDate()", ETag="'Template' ~ template.getid() ~ template.getUpdatedDate().format('U')")
     *
     * @param Request  $request      Current request instance
     * @param string  $tpl          Name of alternative template (to be used instead of the default template).
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found.
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available).
     */
    public function adminEditAction(Request $request)
    {
        return $this->editInternal($request, true);
    }
    
    /**
     * This action provides a handling of edit requestsnull.
     * @Cache(lastModified="template.getUpdatedDate()", ETag="'Template' ~ template.getid() ~ template.getUpdatedDate().format('U')")
     *
     * @param Request  $request      Current request instance
     * @param string  $tpl          Name of alternative template (to be used instead of the default template).
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found.
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available).
     */
    public function editAction(Request $request)
    {
        return $this->editInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminEdit() and edit().
     */
    protected function editInternal(Request $request, $isAdmin = false)
    {
        $controllerHelper = $this->get('zikulamultisitesmodule.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = 'template';
        $utilArgs = ['controller' => 'template', 'action' => 'edit'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        // temporary workarounds
        // let repository know if we are in admin or user area
        $request->query->set('lct', $isAdmin ? 'admin' : 'user');
        // let entities know if we are in admin or user area
        System::queryStringSetVar('lct', $isAdmin ? 'admin' : 'user');
        
        $repository = $this->get('zikulamultisitesmodule.' . $objectType . '_factory')->getRepository();
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $templateParameters = array_merge($templateParameters, $repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
        
        // delegate form processing to the form handler
        $formHandler = $this->get('zikulamultisitesmodule.form.handler.template');
        $formHandler->processForm($templateParameters);
        
        $viewHelper = $this->get('zikulamultisitesmodule.view_helper');
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'edit', $request, $templateParameters);
    }
    /**
     * This is a custom action in the admin area.
     * @Theme("admin")
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminCreateParametersCsvTemplateAction(Request $request)
    {
        return $this->createParametersCsvTemplateInternal($request, true);
    }
    
    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function createParametersCsvTemplateAction(Request $request)
    {
        return $this->createParametersCsvTemplateInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminCreateParametersCsvTemplate() and createParametersCsvTemplate().
     */
    protected function createParametersCsvTemplateInternal(Request $request, $isAdmin = false)
    {
        $controllerHelper = $this->get('zikulamultisitesmodule.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = 'template';
        $utilArgs = ['controller' => 'template', 'action' => 'createParametersCsvTemplate'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Template/createParametersCsvTemplate.html.twig', $templateParameters);
    }
    /**
     * This is a custom action in the admin area.
     * @Theme("admin")
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminReapplyAction(Request $request)
    {
        return $this->reapplyInternal($request, true);
    }
    
    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function reapplyAction(Request $request)
    {
        return $this->reapplyInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminReapply() and reapply().
     */
    protected function reapplyInternal(Request $request, $isAdmin = false)
    {
        $controllerHelper = $this->get('zikulamultisitesmodule.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = 'template';
        $utilArgs = ['controller' => 'template', 'action' => 'reapply'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Template/reapply.html.twig', $templateParameters);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param string $action The action to be executed.
     * @param array  $items  Identifier list of the items to be processed.
     *
     * @return bool true on sucess, false on failure.
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        $objectType = 'template';
        
        // Get parameters
        $action = $request->request->get('action', null);
        $items = $request->request->get('items', null);
        
        $action = strtolower($action);
        
        $workflowHelper = $this->get('zikulamultisitesmodule.workflow_helper');
        $hookHelper = $this->get('zikulamultisitesmodule.hook_helper');
        $flashBag = $this->request->getSession()->getFlashBag();
        $logger = $this->get('logger');
        
        // process each item
        foreach ($items as $itemid) {
            // check if item exists, and get record instance
            $selectionArgs = [
                'ot' => $objectType,
                'id' => $itemid,
                'useJoins' => false
            ];
            $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
        
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
                $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__f('Sorry, but an unknown error occured during the %s action. Please apply the changes again!', [$action]));
                $logger->error('{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'action' => $action, 'entity' => 'template', 'id' => $itemid, 'errorMessage' => $e->getMessage()]);
            }
        
            if (!$success) {
                continue;
            }
        
            if ($action == 'delete') {
                $flashBag->add(\Zikula_Session::MESSAGE_STATUS, $this->__('Done! Item deleted.'));
                $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'entity' => 'template', 'id' => $itemid]);
            } else {
                $flashBag->add(\Zikula_Session::MESSAGE_STATUS, $this->__('Done! Item updated.'));
                $logger->notice('{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'action' => $action, 'entity' => 'template', 'id' => $itemid]);
            }
        
            // Let any hooks know that we have updated or deleted an item
            $hookType = $action == 'delete' ? 'process_delete' : 'process_edit';
            $url = null;
            if ($action != 'delete') {
                $urlArgs = $entity->createUrlArgs();
                $url = new RouteUrl('zikulamultisitesmodule_template_' . ($isAdmin ? 'admin' : '') . 'display', $urlArgs);
            }
            $hookHelper->callProcessHooks($entity, $hookType, $url);
        }
        
        return $this->redirectToRoute('zikulamultisitesmodule_template_adminindex');
    }
}
