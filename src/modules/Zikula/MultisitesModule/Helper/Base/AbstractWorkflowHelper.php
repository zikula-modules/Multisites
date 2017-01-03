<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.1 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper\Base;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula_Workflow_Util;

/**
 * Helper base class for workflow methods.
 */
abstract class AbstractWorkflowHelper
{
    /**
     * Name of the application.
     *
     * @var string
     */
    protected $name;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param ContainerBuilder    $container  ContainerBuilder service instance
     * @param TranslatorInterface $translator Translator service instance
     *
     * @return void
     */
    public function __construct(ContainerBuilder $container, TranslatorInterface $translator)
    {
        $this->name = 'ZikulaMultisitesModule';
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
      * This method returns a list of possible object states.
      *
      * @return array List of collected state information
      */
     public function getObjectStates()
     {
         $states = [];
         $states[] = [
             'value' => 'initial',
             'text' => $this->translator__('Initial'),
             'ui' => 'danger'
         ];
         $states[] = [
             'value' => 'approved',
             'text' => $this->translator__('Approved'),
             'ui' => 'success'
         ];
         $states[] = [
             'value' => 'deleted',
             'text' => $this->translator__('Deleted'),
             'ui' => 'danger'
         ];
    
         return $states;
     }
    
    /**
     * This method returns information about a certain state.
     *
     * @param string $state The given state value
     *
     * @return array|null The corresponding state information
     */
    public function getStateInfo($state = 'initial')
    {
        $result = null;
        $stateList = $this->getObjectStates();
        foreach ($stateList as $singleState) {
            if ($singleState['value'] != $state) {
                continue;
            }
            $result = $singleState;
            break;
        }
    
        return $result;
    }
    
    /**
     * This method returns the workflow name for a certain object type.
     *
     * @param string $objectType Name of treated object type
     *
     * @return string Name of the corresponding workflow
     */
    public function getWorkflowName($objectType = '')
    {
        $result = '';
        switch ($objectType) {
            case 'site':
                $result = 'none';
                break;
            case 'template':
                $result = 'none';
                break;
            case 'siteExtension':
                $result = 'none';
                break;
            case 'project':
                $result = 'none';
                break;
        }
    
        return $result;
    }
    
    /**
     * This method returns the workflow schema for a certain object type.
     *
     * @param string $objectType Name of treated object type
     *
     * @return array|null The resulting workflow schema
     */
    public function getWorkflowSchema($objectType = '')
    {
        $schema = null;
        $schemaName = $this->getWorkflowName($objectType);
        if ($schemaName != '') {
            $schema = Zikula_Workflow_Util::loadSchema($schemaName, $this->name);
        }
    
        return $schema;
    }
    
    /**
     * Retrieve the available actions for a given entity object.
     *
     * @param EntityAccess $entity The given entity instance
     *
     * @return array List of available workflow actions
     */
    public function getActionsForObject($entity)
    {
        // get possible actions for this object in it's current workflow state
        $objectType = $entity['_objectType'];
    
        $this->normaliseWorkflowData($entity);
    
        $idColumn = $entity['__WORKFLOW__']['obj_idcolumn'];
        $wfActions = Zikula_Workflow_Util::getActionsForObject($entity, $objectType, $idColumn, $this->name);
    
        // as we use the workflows for multiple object types we must maybe filter out some actions
        $listHelper = $this->container->get('zikula_multisites_module.listentries_helper');
        $states = $listHelper->getEntries($objectType, 'workflowState');
        $allowedStates = [];
        foreach ($states as $state) {
            $allowedStates[] = $state['value'];
        }
    
        $actions = [];
        foreach ($wfActions as $actionId => $action) {
            $nextState = (isset($action['nextState']) ? $action['nextState'] : '');
            if (!in_array($nextState, ['', 'deleted']) && !in_array($nextState, $allowedStates)) {
                continue;
            }
    
            $actions[$actionId] = $action;
            $actions[$actionId]['buttonClass'] = $this->getButtonClassForAction($actionId);
        }
    
        return $actions;
    }
    
    /**
     * Returns a button class for a certain action.
     *
     * @param string $actionId Id of the treated action
     *
     * @return string The button class
     */
    protected function getButtonClassForAction($actionId)
    {
        $buttonClass = '';
        switch ($actionId) {
            case 'submit':
                $buttonClass = 'success';
                break;
            case 'update':
                $buttonClass = 'success';
                break;
            case 'delete':
                $buttonClass = 'danger';
                break;
        }
    
        if (empty($buttonClass)) {
            $buttonClass = 'default';
        }
    
        return 'btn btn-' . $buttonClass;
    }
    
    /**
     * Executes a certain workflow action for a given entity object.
     *
     * @param EntityAccess $entity    The given entity instance
     * @param string       $actionId  Name of action to be executed
     * @param bool         $recursive True if the function called itself
     *
     * @return bool False on error or true if everything worked well
     */
    public function executeAction($entity, $actionId = '', $recursive = false)
    {
        $objectType = $entity['_objectType'];
        $schemaName = $this->getWorkflowName($objectType);
    
        $entity->initWorkflow(true);
        $idColumn = $entity['__WORKFLOW__']['obj_idcolumn'];
    
        $this->normaliseWorkflowData($entity);
    
        $result = Zikula_Workflow_Util::executeAction($schemaName, $entity, $actionId, $objectType, 'ZikulaMultisitesModule', $idColumn);
    
        if (false !== $result && !$recursive) {
            $entities = $entity->getRelatedObjectsToPersist();
            foreach ($entities as $rel) {
                if ($rel->getWorkflowState() == 'initial') {
                    $this->executeAction($rel, $actionId, true);
                }
            }
        }
    
        return (false !== $result);
    }
    /**
     * Performs a conversion of the workflow object back to an array.
     *
     * @param EntityAccess $entity The given entity instance (excplicitly assigned by reference as form handlers use arrays)
     *
     * @return bool False on error or true if everything worked well
     */
    public function normaliseWorkflowData(&$entity)
    {
        $workflow = $entity['__WORKFLOW__'];
        if (!isset($workflow[0]) && isset($workflow['module'])) {
            return true;
        }
    
        if (isset($workflow[0])) {
            $workflow = $workflow[0];
        }
    
        if (!is_object($workflow)) {
            $workflow['module'] = 'ZikulaMultisitesModule';
            $entity['__WORKFLOW__'] = $workflow;
    
            return true;
        }
    
        $entity['__WORKFLOW__'] = [
            'module'        => 'ZikulaMultisitesModule',
            'id'            => $workflow->getId(),
            'state'         => $workflow->getState(),
            'obj_table'     => $workflow->getObjTable(),
            'obj_idcolumn'  => $workflow->getObjIdcolumn(),
            'obj_id'        => $workflow->getObjId(),
            'schemaname'    => $workflow->getSchemaname()
        ];
    
        return true;
    }
    
    /**
     * Collects amount of moderation items foreach object type.
     *
     * @return array List of collected amounts
     */
    public function collectAmountOfModerationItems()
    {
        $amounts = [];
    
        // nothing required here as no entities use enhanced workflows including approval actions
    
        return $amounts;
    }
    
    /**
     * Retrieves the amount of moderation items for a given object type
     * and a certain workflow state.
     *
     * @param string $objectType Name of treated object type
     * @param string $state The given state value
     *
     * @return integer The affected amount of objects
     */
    public function getAmountOfModerationItems($objectType, $state)
    {
        $repository = $this->container->get('zikula_multisites_module.' . $objectType . '_factory')->getRepository();
    
        $where = 'tbl.workflowState:eq:' . $state;
        $parameters = ['workflowState' => $state];
        $useJoins = false;
    
        return $repository->selectCount($where, $useJoins, $parameters);
    }
}
