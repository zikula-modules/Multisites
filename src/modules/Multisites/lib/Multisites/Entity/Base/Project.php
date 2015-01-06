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

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for project entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 *
 * @abstract
 */
abstract class Multisites_Entity_Base_Project extends Zikula_EntityAccess
{
    /**
     * @var string The tablename this object maps to.
     */
    protected $_objectType = 'project';
    
    /**
     * @var Multisites_Entity_Validator_Project The validator for this entity.
     */
    protected $_validator = null;
    
    /**
     * @var boolean Option to bypass validation if needed.
     */
    protected $_bypassValidation = false;
    
    /**
     * @var array List of available item actions.
     */
    protected $_actions = array();
    
    /**
     * @var array The current workflow data of this object.
     */
    protected $__WORKFLOW__ = array();
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id.
     */
    protected $id = 0;
    
    /**
     * @ORM\Column(length=20)
     * @var string $workflowState.
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(length=150)
     * @var string $name.
     */
    protected $name = '';
    
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="create")
     * @var integer $createdUserId.
     */
    protected $createdUserId;
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="update")
     * @var integer $updatedUserId.
     */
    protected $updatedUserId;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var datetime $createdDate.
     */
    protected $createdDate;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var datetime $updatedDate.
     */
    protected $updatedDate;
    
    /**
     * Bidirectional - One project [project] has many sites [sites] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="Multisites_Entity_Site", mappedBy="project", cascade={"remove"})
     * @ORM\JoinTable(name="multisites_projectsites")
     * @var Multisites_Entity_Site[] $sites.
     */
    protected $sites = null;
    
    /**
     * Bidirectional - Many projects [projects] have many templates [templates] (OWNING SIDE).
     *
     * @ORM\ManyToMany(targetEntity="Multisites_Entity_Template", inversedBy="projects")
     * @ORM\JoinTable(name="multisites_project_template")
     * @var Multisites_Entity_Template[] $templates.
     */
    protected $templates = null;
    
    /**
     * Constructor.
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     *
     * @param TODO
     */
    public function __construct()
    {
        $this->workflowState = 'initial';
        $this->initValidator();
        $this->initWorkflow();
        $this->sites = new ArrayCollection();
        $this->templates = new ArrayCollection();
    }
    
    /**
     * Get _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Set _object type.
     *
     * @param string $_objectType.
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        $this->_objectType = $_objectType;
    }
    
    /**
     * Get _validator.
     *
     * @return Multisites_Entity_Validator_Project
     */
    public function get_validator()
    {
        return $this->_validator;
    }
    
    /**
     * Set _validator.
     *
     * @param Multisites_Entity_Validator_Project $_validator.
     *
     * @return void
     */
    public function set_validator(Multisites_Entity_Validator_Project $_validator = null)
    {
        $this->_validator = $_validator;
    }
    
    /**
     * Get _bypass validation.
     *
     * @return boolean
     */
    public function get_bypassValidation()
    {
        return $this->_bypassValidation;
    }
    
    /**
     * Set _bypass validation.
     *
     * @param boolean $_bypassValidation.
     *
     * @return void
     */
    public function set_bypassValidation($_bypassValidation)
    {
        $this->_bypassValidation = $_bypassValidation;
    }
    
    /**
     * Get _actions.
     *
     * @return array
     */
    public function get_actions()
    {
        return $this->_actions;
    }
    
    /**
     * Set _actions.
     *
     * @param array $_actions.
     *
     * @return void
     */
    public function set_actions(array $_actions = Array())
    {
        $this->_actions = $_actions;
    }
    
    /**
     * Get __ w o r k f l o w__.
     *
     * @return array
     */
    public function get__WORKFLOW__()
    {
        return $this->__WORKFLOW__;
    }
    
    /**
     * Set __ w o r k f l o w__.
     *
     * @param array $__WORKFLOW__.
     *
     * @return void
     */
    public function set__WORKFLOW__(array $__WORKFLOW__ = Array())
    {
        $this->__WORKFLOW__ = $__WORKFLOW__;
    }
    
    
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id.
     *
     * @param integer $id.
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Get workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Set workflow state.
     *
     * @param string $workflowState.
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        $this->workflowState = $workflowState;
    }
    
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name.
     *
     * @param string $name.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Get created user id.
     *
     * @return integer
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }
    
    /**
     * Set created user id.
     *
     * @param integer $createdUserId.
     *
     * @return void
     */
    public function setCreatedUserId($createdUserId)
    {
        $this->createdUserId = $createdUserId;
    }
    
    /**
     * Get updated user id.
     *
     * @return integer
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }
    
    /**
     * Set updated user id.
     *
     * @param integer $updatedUserId.
     *
     * @return void
     */
    public function setUpdatedUserId($updatedUserId)
    {
        $this->updatedUserId = $updatedUserId;
    }
    
    /**
     * Get created date.
     *
     * @return datetime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    
    /**
     * Set created date.
     *
     * @param datetime $createdDate.
     *
     * @return void
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }
    
    /**
     * Get updated date.
     *
     * @return datetime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
    
    /**
     * Set updated date.
     *
     * @param datetime $updatedDate.
     *
     * @return void
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }
    
    
    /**
     * Get sites.
     *
     * @return Multisites_Entity_Site[]
     */
    public function getSites()
    {
        return $this->sites;
    }
    
    /**
     * Set sites.
     *
     * @param Multisites_Entity_Site[] $sites.
     *
     * @return void
     */
    public function setSites($sites)
    {
        foreach ($sites as $siteSingle) {
            $this->addSites($siteSingle);
        }
    }
    
    /**
     * Adds an instance of Multisites_Entity_Site to the list of sites.
     *
     * @param Multisites_Entity_Site $site The instance to be added to the collection.
     *
     * @return void
     */
    public function addSites(Multisites_Entity_Site $site)
    {
        $this->sites->add($site);
        $site->setProject($this);
    }
    
    /**
     * Removes an instance of Multisites_Entity_Site from the list of sites.
     *
     * @param Multisites_Entity_Site $site The instance to be removed from the collection.
     *
     * @return void
     */
    public function removeSites(Multisites_Entity_Site $site)
    {
        $this->sites->removeElement($site);
        $site->setProject(null);
    }
    
    /**
     * Get templates.
     *
     * @return Multisites_Entity_Template[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }
    
    /**
     * Set templates.
     *
     * @param Multisites_Entity_Template[] $templates.
     *
     * @return void
     */
    public function setTemplates($templates)
    {
        foreach ($templates as $templateSingle) {
            $this->addTemplates($templateSingle);
        }
    }
    
    /**
     * Adds an instance of Multisites_Entity_Template to the list of templates.
     *
     * @param Multisites_Entity_Template $template The instance to be added to the collection.
     *
     * @return void
     */
    public function addTemplates(Multisites_Entity_Template $template)
    {
        $this->templates->add($template);
    }
    
    /**
     * Removes an instance of Multisites_Entity_Template from the list of templates.
     *
     * @param Multisites_Entity_Template $template The instance to be removed from the collection.
     *
     * @return void
     */
    public function removeTemplates(Multisites_Entity_Template $template)
    {
        $this->templates->removeElement($template);
    }
    
    
    /**
     * Post-Process the data after the entity has been constructed by the entity manager.
     * The event happens after the entity has been loaded from database or after a refresh call.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - no access to associations (not initialised yet)
     *
     * @see Multisites_Entity_Project::postLoadCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostLoadCallback()
    {
        // echo 'loaded a record ...';
        $currentFunc = FormUtil::getPassedValue('func', 'main', 'GETPOST', FILTER_SANITIZE_STRING);
        $usesCsvOutput = FormUtil::getPassedValue('usecsvext', false, 'GETPOST', FILTER_VALIDATE_BOOLEAN);
        
        $this['id'] = (int) ((isset($this['id']) && !empty($this['id'])) ? DataUtil::formatForDisplay($this['id']) : 0);
        $this->formatTextualField('workflowState', $currentFunc, $usesCsvOutput, true);
        $this->formatTextualField('name', $currentFunc, $usesCsvOutput);
    
        $this->prepareItemActions();
    
        return true;
    }
    
    /**
     * Formats a given textual field depending on it's actual kind of content.
     *
     * @param string  $fieldName     Name of field to be formatted.
     * @param string  $currentFunc   Name of current controller action.
     * @param string  $usesCsvOutput Whether the output is CSV or not (defaults to false).
     * @param boolean $allowZero     Whether 0 values are allowed or not (defaults to false).
     */
    protected function formatTextualField($fieldName, $currentFunc, $usesCsvOutput = false, $allowZero = false)
    {
        if ($currentFunc == 'edit') {
            // apply no changes when editing the content
            return;
        }
    
        if ($usesCsvOutput == 1) {
            // apply no changes for CSV output
            return;
        }
    
        $string = '';
        if (isset($this[$fieldName])) {
            if (!empty($this[$fieldName]) || ($allowZero && $this[$fieldName] == 0)) {
                $string = $this[$fieldName];
                if ($this->containsHtml($string)) {
                    $string = DataUtil::formatForDisplayHTML($string);
                } else {
                    $string = DataUtil::formatForDisplay($string);
                    $string = nl2br($string);
                }
            }
        }
    
        $this[$fieldName] = $string;
    }
    
    /**
     * Checks whether any html tags are contained in the given string.
     * See http://stackoverflow.com/questions/10778035/how-to-check-if-string-contents-have-any-html-in-it for implementation details.
     *
     * @param $string string The given input string.
     *
     * @return boolean Whether any html tags are found or not.
     */
    protected function containsHtml($string)
    {
        return preg_match("/<[^<]+>/", $string, $m) != 0;
    }
    
    /**
     * Pre-Process the data prior to an insert operation.
     * The event happens before the entity managers persist operation is executed for this entity.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - no identifiers available if using an identity generator like sequences
     *     - Doctrine won't recognize changes on relations which are done here
     *       if this method is called by cascade persist
     *     - no creation of other entities allowed
     *
     * @see Multisites_Entity_Project::prePersistCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPrePersistCallback()
    {
        $this->validate();
    
        return true;
    }
    
    /**
     * Post-Process the data after an insert operation.
     * The event happens after the entity has been made persistant.
     * Will be called after the database insert operations.
     * The generated primary key values are available.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *
     * @see Multisites_Entity_Project::postPersistCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostPersistCallback()
    {
        return true;
    }
    
    /**
     * Pre-Process the data prior a delete operation.
     * The event happens before the entity managers remove operation is executed for this entity.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL DELETE statement
     *
     * @see Multisites_Entity_Project::preRemoveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPreRemoveCallback()
    {
        // delete workflow for this entity
        $workflow = $this['__WORKFLOW__'];
        if ($workflow['id'] > 0) {
            $result = (bool) DBUtil::deleteObjectByID('workflows', $workflow['id']);
            if ($result === false) {
                $dom = ZLanguage::getModuleDomain('Multisites');
                return LogUtil::registerError(__('Error! Could not remove stored workflow. Deletion has been aborted.', $dom));
            }
        }
    
        return true;
    }
    
    /**
     * Post-Process the data after a delete.
     * The event happens after the entity has been deleted.
     * Will be called after the database delete operations.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL DELETE statement
     *
     * @see Multisites_Entity_Project::postRemoveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostRemoveCallback()
    {
    
        return true;
    }
    
    /**
     * Pre-Process the data prior to an update operation.
     * The event happens before the database update operations for the entity data.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL UPDATE statement
     *     - changes on associations are not allowed and won't be recognized by flush
     *     - changes on properties won't be recognized by flush as well
     *     - no creation of other entities allowed
     *
     * @see Multisites_Entity_Project::preUpdateCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPreUpdateCallback()
    {
        $this->validate();
    
        return true;
    }
    
    /**
     * Post-Process the data after an update operation.
     * The event happens after the database update operations for the entity data.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL UPDATE statement
     *
     * @see Multisites_Entity_Project::postUpdateCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostUpdateCallback()
    {
        return true;
    }
    
    /**
     * Pre-Process the data prior to a save operation.
     * This combines the PrePersist and PreUpdate events.
     * For more information see corresponding callback handlers.
     *
     * @see Multisites_Entity_Project::preSaveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPreSaveCallback()
    {
        $this->validate();
    
        return true;
    }
    
    /**
     * Post-Process the data after a save operation.
     * This combines the PostPersist and PostUpdate events.
     * For more information see corresponding callback handlers.
     *
     * @see Multisites_Entity_Project::postSaveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostSaveCallback()
    {
        return true;
    }
    
    
    /**
     * Returns the formatted title conforming to the display pattern
     * specified for this entity.
     */
    public function getTitleFromDisplayPattern()
    {
        $serviceManager = ServiceUtil::getManager();
        $listHelper = new Multisites_Util_ListEntries(ServiceUtil::getManager());
    
        $formattedTitle = ''
                . $this->getName();
    
        return $formattedTitle;
    }
    
    /**
     * Initialises the validator and return it's instance.
     *
     * @return Multisites_Entity_Validator_Project The validator for this entity.
     */
    public function initValidator()
    {
        if (!is_null($this->_validator)) {
            return $this->_validator;
        }
        $this->_validator = new Multisites_Entity_Validator_Project($this);
    
        return $this->_validator;
    }
    
    /**
     * Sets/retrieves the workflow details.
     *
     * @param boolean $forceLoading load the workflow record.
     */
    public function initWorkflow($forceLoading = false)
    {
        $currentFunc = FormUtil::getPassedValue('func', 'main', 'GETPOST', FILTER_SANITIZE_STRING);
        $isReuse = FormUtil::getPassedValue('astemplate', '', 'GETPOST', FILTER_SANITIZE_STRING);
    
        // apply workflow with most important information
        $idColumn = 'id';
        
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = new Multisites_Util_Workflow($serviceManager);
        
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = array(
            'module' => 'Multisites',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => $idColumn,
            'obj_id' => $this[$idColumn],
            'schemaname' => $schemaName);
        
        // load the real workflow only when required (e. g. when func is edit or delete)
        if ((!in_array($currentFunc, array('main', 'view', 'display')) && empty($isReuse)) || $forceLoading) {
            $result = Zikula_Workflow_Util::getWorkflowForObject($this, $this['_objectType'], $idColumn, 'Multisites');
            if (!$result) {
                $dom = ZLanguage::getModuleDomain('Multisites');
                LogUtil::registerError(__('Error! Could not load the associated workflow.', $dom));
            }
        }
        
        if (!is_object($this['__WORKFLOW__']) && !isset($this['__WORKFLOW__']['schemaname'])) {
            $workflow = $this['__WORKFLOW__'];
            $workflow['schemaname'] = $schemaName;
            $this['__WORKFLOW__'] = $workflow;
        }
    }
    
    /**
     * Resets workflow data back to initial state.
     * To be used after cloning an entity object.
     */
    public function resetWorkflow()
    {
        $this->setWorkflowState('initial');
    
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = new Multisites_Util_Workflow($serviceManager);
    
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = array(
            'module' => 'Multisites',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => 'id',
            'obj_id' => 0,
            'schemaname' => $schemaName);
    }
    
    /**
     * Start validation and raise exception if invalid data is found.
     *
     * @return void.
     *
     * @throws Zikula_Exception Thrown if a validation error occurs
     */
    public function validate()
    {
        if ($this->_bypassValidation === true) {
            return;
        }
    
        $result = $this->initValidator()->validateAll();
        if (is_array($result)) {
            throw new Zikula_Exception($result['message'], $result['code'], $result['debugArray']);
        }
    }
    
    /**
     * Return entity data in JSON format.
     *
     * @return string JSON-encoded data.
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Collect available actions for this entity.
     */
    protected function prepareItemActions()
    {
        if (!empty($this->_actions)) {
            return;
        }
    
        $currentLegacyControllerType = FormUtil::getPassedValue('lct', 'user', 'GETPOST', FILTER_SANITIZE_STRING);
        $currentFunc = FormUtil::getPassedValue('func', 'main', 'GETPOST', FILTER_SANITIZE_STRING);
        $component = 'Multisites:Project:';
        $instance = $this->id . '::';
        $dom = ZLanguage::getModuleDomain('Multisites');
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, array('main', 'view'))) {
            }
            if (in_array($currentFunc, array('main', 'view', 'display'))) {
                if (SecurityUtil::checkPermission($component, $instance, ACCESS_EDIT)) {
                    $this->_actions[] = array(
                        'url' => array('type' => 'admin', 'func' => 'edit', 'arguments' => array('ot' => 'project', 'id' => $this['id'])),
                        'icon' => 'edit',
                        'linkTitle' => __('Edit', $dom),
                        'linkText' => __('Edit', $dom)
                    );
                    $this->_actions[] = array(
                        'url' => array('type' => 'admin', 'func' => 'edit', 'arguments' => array('ot' => 'project', 'astemplate' => $this['id'])),
                        'icon' => 'saveas',
                        'linkTitle' => __('Reuse for new item', $dom),
                        'linkText' => __('Reuse', $dom)
                    );
                }
            }
            
            // more actions for adding new related items
            $authAdmin = SecurityUtil::checkPermission($component, $instance, ACCESS_ADMIN);
            
            $uid = UserUtil::getVar('uid');
            if ($authAdmin || (isset($uid) && isset($this->createdUserId) && $this->createdUserId == $uid)) {
            
                $urlArgs = array('ot' => 'site',
                                 'project' => $this->id);
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewProject';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplayProject';
                }
                $this->_actions[] = array(
                    'url' => array('type' => 'admin', 'func' => 'edit', 'arguments' => $urlArgs),
                    'icon' => 'add',
                    'linkTitle' => __('Create site', $dom),
                    'linkText' => __('Create site', $dom)
                );
            
                $urlArgs = array('ot' => 'template',
                                 'projects' => $this->id);
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewProject';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplayProject';
                }
                $this->_actions[] = array(
                    'url' => array('type' => 'admin', 'func' => 'edit', 'arguments' => $urlArgs),
                    'icon' => 'add',
                    'linkTitle' => __('Create template', $dom),
                    'linkText' => __('Create template', $dom)
                );
            }
        }
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return Array The resulting arguments list.
     */
    public function createUrlArgs()
    {
        $args = array('ot' => $this['_objectType']);
    
        $args['id'] = $this['id'];
    
        if (isset($this['slug'])) {
            $args['slug'] = $this['slug'];
        }
    
        return $args;
    }
    
    /**
     * Create concatenated identifier string (for composite keys).
     *
     * @return String concatenated identifiers.
     */
    public function createCompositeIdentifier()
    {
        $itemId = $this['id'];
    
        return $itemId;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'multisites.ui_hooks.projects';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects The objects are added to this array. Default: array()
     * 
     * @return array of entity objects.
     */
    public function getRelatedObjectsToPersist(&$objects = array()) 
    {
        return array();
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     */
    public function __toString()
    {
        return $this->getId();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     * (4) http://www.pantovic.com/article/26/doctrine2-entity-cloning
     */
    public function __clone()
    {
        // If the entity has an identity, proceed as normal.
        if ($this->id) {
            // unset identifiers
            $this->setId(0);
    
            // init validator
            $this->initValidator();
    
            // reset Workflow
            $this->resetWorkflow();
    
            $this->setCreatedDate(null);
            $this->setCreatedUserId(null);
            $this->setUpdatedDate(null);
            $this->setUpdatedUserId(null);
    
            
        }
        // otherwise do nothing, do NOT throw an exception!
    }
}
