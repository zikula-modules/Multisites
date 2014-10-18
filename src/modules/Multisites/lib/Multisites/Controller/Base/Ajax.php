<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package Multisites
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

/**
 * Ajax controller class.
 */
class Multisites_Controller_Base_Ajax extends Zikula_Controller_AbstractAjax
{


    /**
     * This method is the default function handling the main area called without defining arguments.
     *
     *
     * @return mixed Output.
     */
    public function main()
    {
        // parameter specifying which type of objects we are treating
        $objectType = $this->request->query->filter('ot', 'site', FILTER_SANITIZE_STRING);
        
        $permLevel = ACCESS_OVERVIEW;
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', $permLevel), LogUtil::getErrorMsgPermission());
    }
    
    
    /**
     * Searches for entities for auto completion usage.
     *
     * @param string $ot       Treated object type.
     * @param string $fragment The fragment of the entered item name.
     * @param string $exclude  Comma separated list with ids of other items (to be excluded from search).
     *
     * @return Zikula_Response_Ajax_Plain
     */
    public function getItemListAutoCompletion()
    {
        if (!SecurityUtil::checkPermission($this->name . '::Ajax', '::', ACCESS_EDIT)) {
            return true;
        }
        
        $objectType = 'site';
        if ($this->request->isPost() && $this->request->request->has('ot')) {
            $objectType = $this->request->request->filter('ot', 'site', FILTER_SANITIZE_STRING);
        } elseif ($this->request->isGet() && $this->request->query->has('ot')) {
            $objectType = $this->request->query->filter('ot', 'site', FILTER_SANITIZE_STRING);
        }
        $controllerHelper = new Multisites_Util_Controller($this->serviceManager);
        $utilArgs = array('controller' => 'ajax', 'action' => 'getItemListAutoCompletion');
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        
        $entityClass = 'Multisites_Entity_' . ucfirst($objectType);
        $repository = $this->entityManager->getRepository($entityClass);
        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $objectType));
        
        $fragment = '';
        $exclude = '';
        if ($this->request->isPost() && $this->request->request->has('fragment')) {
            $fragment = $this->request->request->get('fragment', '');
            $exclude = $this->request->request->get('exclude', '');
        } elseif ($this->request->isGet() && $this->request->query->has('fragment')) {
            $fragment = $this->request->query->get('fragment', '');
            $exclude = $this->request->query->get('exclude', '');
        }
        $exclude = ((!empty($exclude)) ? array($exclude) : array());
        
        // parameter for used sorting field
        $sort = $this->request->query->get('sort', '');
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }
        $sortParam = $sort . ' asc';
        
        $currentPage = 1;
        $resultsPerPage = 20;
        
        // get objects from database
        list($entities, $objectCount) = $repository->selectSearch($fragment, $exclude, $sortParam, $currentPage, $resultsPerPage);
        
        $out = '<ul>';
        if ((is_array($entities) || is_object($entities)) && count($entities) > 0) {
            $descriptionFieldName = $repository->getDescriptionFieldName();
            $previewFieldName = $repository->getPreviewFieldName();
            if (!empty($previewFieldName)) {
                $imageHelper = new Multisites_Util_Image($this->serviceManager);
                $imagineManager = $imageHelper->getManager($objectType, $previewFieldName, 'controllerAction', $utilArgs);
            }
            foreach ($entities as $item) {
                // class="informal" --> show in dropdown, but do nots copy in the input field after selection
                $itemTitle = $item->getTitleFromDisplayPattern();
                $itemTitleStripped = str_replace('"', '', $itemTitle);
                $itemDescription = isset($item[$descriptionFieldName]) && !empty($item[$descriptionFieldName]) ? $item[$descriptionFieldName] : '';//$this->__('No description yet.');
                $itemId = $item->createCompositeIdentifier();
        
                $out .= '<li id="' . $itemId . '" title="' . $itemTitleStripped . '">';
                $out .= '<div class="itemtitle">' . $itemTitle . '</div>';
                if (!empty($itemDescription)) {
                    $out .= '<div class="itemdesc informal">' . substr($itemDescription, 0, 50) . '&hellip;</div>';
                }
        
                // check for preview image
                if (!empty($previewFieldName) && !empty($item[$previewFieldName]) && isset($item[$previewFieldName . 'FullPath'])) {
                    $fullObjectId = $objectType . '-' . $itemId;
                    $thumbImagePath = $imagineManager->getThumb($item[$previewFieldName], $fullObjectId);
                    $preview = '<img src="' . $thumbImagePath . '" width="50" height="50" alt="' . $itemTitleStripped . '" />';
                    $out .= '<div id="itemPreview' . $itemId . '" class="itempreview informal">' . $preview . '</div>';
                }
        
                $out .= '</li>';
            }
        }
        $out .= '</ul>';
        
        // return response
        return new Zikula_Response_Ajax_Plain($out);
    }
    
    /**
     * Changes a given flag (boolean field) by switching between true and false.
     *
     * @param string $ot    Treated object type.
     * @param string $field The field to be toggled.
     * @param int    $id    Identifier of treated entity.
     *
     * @return Zikula_Response_Ajax
     */
    public function toggleFlag()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::Ajax', '::', ACCESS_EDIT));
        
        $postData = $this->request->request;
        
        $objectType = $postData->filter('ot', '', FILTER_SANITIZE_STRING);
        $field = $postData->filter('field', '', FILTER_SANITIZE_STRING);
        $id = (int) $postData->filter('id', 0, FILTER_VALIDATE_INT);
        
        if ($id == 0
            || ($objectType != 'site')
        || ($objectType == 'site' && !in_array($field, array('active')))
        ) {
            return new Zikula_Response_Ajax_BadData($this->__('Error: invalid input.'));
        }
        
        // select data from data source
        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', array('ot' => $objectType, 'id' => $id));
        if ($entity == null) {
            return new Zikula_Response_Ajax_NotFound($this->__('No such item.'));
        }
        
        // toggle the flag
        $entity[$field] = !$entity[$field];
        
        // save entity back to database
        $this->entityManager->flush();
        
        // return response
        $result = array('id' => $id,
                        'state' => $entity[$field]);
        
        return new Zikula_Response_Ajax($result);
    }
}