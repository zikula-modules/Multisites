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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DataUtil;
use ModUtil;
use RuntimeException;
use System;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\Ajax\AjaxResponse;
use Zikula\Core\Response\Ajax\BadDataResponse;
use Zikula\Core\Response\Ajax\FatalResponse;
use Zikula\Core\Response\Ajax\NotFoundResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zikula\Core\Response\PlainResponse;

/**
 * Ajax controller class.
 */
abstract class AbstractAjaxController extends AbstractController
{


    /**
     * This is the default action handling the main area called without defining arguments.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'site');
        
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . '::', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
    }

    
    /**
     * Searches for entities for auto completion usage.
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function getItemListAutoCompletionAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::Ajax', '::', ACCESS_EDIT)) {
            return true;
        }
        
        $objectType = 'site';
        if ($request->isMethod('POST') && $request->request->has('ot')) {
            $objectType = $request->request->getAlnum('ot', 'site');
        } elseif ($request->isMethod('GET') && $request->query->has('ot')) {
            $objectType = $request->query->getAlnum('ot', 'site');
        }
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
        $utilArgs = ['controller' => 'ajax', 'action' => 'getItemListAutoCompletion'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        
        $repository = $this->get('zikula_multisites_module.' . $objectType . '_factory')->getRepository();
        $selectionHelper = $this->get('zikula_multisites_module.selection_helper');
        $idFields = $selectionHelper->getIdFields($objectType);
        
        $fragment = '';
        $exclude = '';
        if ($request->isMethod('POST') && $request->request->has('fragment')) {
            $fragment = $request->request->get('fragment', '');
            $exclude = $request->request->get('exclude', '');
        } elseif ($request->isMethod('GET') && $request->query->has('fragment')) {
            $fragment = $request->query->get('fragment', '');
            $exclude = $request->query->get('exclude', '');
        }
        $exclude = !empty($exclude) ? explode(',', $exclude) : [];
        
        // parameter for used sorting field
        $sort = $request->query->get('sort', '');
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
            System::queryStringSetVar('sort', $sort);
            $request->query->set('sort', $sort);
            // set default sorting in route parameters (e.g. for the pager)
            $routeParams = $request->attributes->get('_route_params');
            $routeParams['sort'] = $sort;
            $request->attributes->set('_route_params', $routeParams);
        }
        $sortParam = $sort . ' asc';
        
        $currentPage = 1;
        $resultsPerPage = 20;
        
        // get objects from database
        list($entities, $objectCount) = $repository->selectSearch($fragment, $exclude, $sortParam, $currentPage, $resultsPerPage);
        
        $resultItems = [];
        
        if ((is_array($entities) || is_object($entities)) && count($entities) > 0) {
            $descriptionFieldName = $repository->getDescriptionFieldName();
            $previewFieldName = $repository->getPreviewFieldName();
            
            //$imageHelper = $this->get('zikula_multisites_module.image_helper');
            //$imagineManager = $imageHelper->getManager($objectType, $previewFieldName, 'controllerAction', $utilArgs);
            $imagineManager = $this->get('systemplugin.imagine.manager');
            foreach ($entities as $item) {
                $itemTitle = $item->getTitleFromDisplayPattern();
                $itemTitleStripped = str_replace('"', '', $itemTitle);
                $itemDescription = isset($item[$descriptionFieldName]) && !empty($item[$descriptionFieldName]) ? $item[$descriptionFieldName] : '';//$this->__('No description yet.')
                if (!empty($itemDescription)) {
                    $itemDescription = substr($itemDescription, 0, 50) . '&hellip;';
                }
        
                $resultItem = [
                    'id' => $item->createCompositeIdentifier(),
                    'title' => $item->getTitleFromDisplayPattern(),
                    'description' => $itemDescription,
                    'image' => ''
                ];
        
                // check for preview image
                if (!empty($previewFieldName) && !empty($item[$previewFieldName])) {
                    $fullObjectId = $objectType . '-' . $resultItem['id'];
                    $thumbImagePath = $imagineManager->getThumb($item[$previewFieldName], $fullObjectId);
                    $preview = '<img src="' . $thumbImagePath . '" width="50" height="50" alt="' . $itemTitleStripped . '" />';
                    $resultItem['image'] = $preview;
                }
        
                $resultItems[] = $resultItem;
            }
        }
        
        return new JsonResponse($resultItems);
    }
    
    /**
     * Changes a given flag (boolean field) by switching between true and false.
     *
     * @param Request $request Current request instance
     *
     * @return AjaxResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function toggleFlagAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::Ajax', '::', ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        
        $postData = $request->request;
        
        $objectType = $postData->getAlnum('ot', 'site');
        $field = $postData->getAlnum('field', '');
        $id = $postData->getInt('id', 0);
        
        if ($id == 0
            || ($objectType != 'site')
        || ($objectType == 'site' && !in_array($field, ['active']))
        ) {
            return new BadDataResponse($this->__('Error: invalid input.'));
        }
        
        // select data from data source
        $selectionHelper = $this->get('zikula_multisites_module.selection_helper');
        $entity = $selectionHelper->getEntity($objectType, $id);
        if (null === $entity) {
            return new NotFoundResponse($this->__('No such item.'));
        }
        
        // toggle the flag
        $entity[$field] = !$entity[$field];
        
        // save entity back to database
        $entityManager = $this->get('doctrine.orm.default_entity_manager');
        $entityManager->flush();
        
        // return response
        $result = [
            'id' => $id,
            'state' => $entity[$field]
        ];
        
        $logger = $this->get('logger');
        $logArgs = ['app' => 'ZikulaMultisitesModule', 'user' => $this->get('zikula_users_module.current_user')->get('uname'), 'field' => $field, 'entity' => $objectType, 'id' => $id];
        $logger->notice('{app}: User {user} toggled the {field} flag the {entity} with id {id}.', $logArgs);
        
        return new AjaxResponse($result);
    }
}
