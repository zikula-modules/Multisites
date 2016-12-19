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

namespace Zikula\MultisitesModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;
use Symfony\Component\Validator\Constraints as Assert;

use DataUtil;
use FormUtil;
use RuntimeException;
use ServiceUtil;
use UserUtil;
use Zikula_Workflow_Util;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for site extension entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 *
 * @abstract
 */
abstract class AbstractSiteExtensionEntity extends EntityAccess
{
    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'siteExtension';
    
    /**
     * @Assert\Type(type="bool")
     * @var boolean Option to bypass validation if needed
     */
    protected $_bypassValidation = false;
    
    /**
     * @var array The current workflow data of this object
     */
    protected $__WORKFLOW__ = [];
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getWorkflowStateAllowedValues", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="50")
     * @var string $name
     */
    protected $name = '';
    
    /**
     * @ORM\Column(length=10)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="10")
     * @var string $extensionVersion
     */
    protected $extensionVersion = '';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getExtensionTypeAllowedValues", multiple=false)
     * @var string $extensionType
     */
    protected $extensionType = null;
    
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="create")
     * @var integer $createdUserId
     */
    protected $createdUserId;
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="update")
     * @var integer $updatedUserId
     */
    protected $updatedUserId;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Assert\DateTime()
     * @var \DateTime $createdDate
     */
    protected $createdDate;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Assert\DateTime()
     * @var \DateTime $updatedDate
     */
    protected $updatedDate;
    
    /**
     * Bidirectional - Many extensions [site extensions] are linked by one site [site] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="Zikula\MultisitesModule\Entity\SiteEntity", inversedBy="extensions")
     * @ORM\JoinTable(name="zikula_multisites_site")
     * @Assert\Type(type="Zikula\MultisitesModule\Entity\SiteEntity")
     * @Assert\Valid()
     * @var \Zikula\MultisitesModule\Entity\SiteEntity $site
     */
    protected $site;
    
    
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
        $serviceManager = ServiceUtil::getManager();
        
        $listHelper = $serviceManager->get('zikula_multisites_module.listentries_helper');
        
        $items = [];
        $listEntries = $listHelper->getExtensionTypeEntriesForSiteExtension();
        foreach ($listEntries as $listEntry) {
            if (true === $listEntry['default']) {
                $items[] = $listEntry['value'];
            }
        }
        $this->extensionType = implode('###', $items);
        
        $this->initWorkflow();
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        $this->_objectType = $_objectType;
    }
    
    /**
     * Returns the _bypass validation.
     *
     * @return boolean
     */
    public function get_bypassValidation()
    {
        return $this->_bypassValidation;
    }
    
    /**
     * Sets the _bypass validation.
     *
     * @param boolean $_bypassValidation
     *
     * @return void
     */
    public function set_bypassValidation($_bypassValidation)
    {
        $this->_bypassValidation = $_bypassValidation;
    }
    
    /**
     * Returns the __ w o r k f l o w__.
     *
     * @return array
     */
    public function get__WORKFLOW__()
    {
        return $this->__WORKFLOW__;
    }
    
    /**
     * Sets the __ w o r k f l o w__.
     *
     * @param array $__WORKFLOW__
     *
     * @return void
     */
    public function set__WORKFLOW__($__WORKFLOW__ = [])
    {
        $this->__WORKFLOW__ = $__WORKFLOW__;
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        $this->workflowState = isset($workflowState) ? $workflowState : '';
    }
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = isset($name) ? $name : '';
    }
    
    /**
     * Returns the extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return $this->extensionVersion;
    }
    
    /**
     * Sets the extension version.
     *
     * @param string $extensionVersion
     *
     * @return void
     */
    public function setExtensionVersion($extensionVersion)
    {
        $this->extensionVersion = isset($extensionVersion) ? $extensionVersion : '';
    }
    
    /**
     * Returns the extension type.
     *
     * @return string
     */
    public function getExtensionType()
    {
        return $this->extensionType;
    }
    
    /**
     * Sets the extension type.
     *
     * @param string $extensionType
     *
     * @return void
     */
    public function setExtensionType($extensionType)
    {
        $this->extensionType = isset($extensionType) ? $extensionType : '';
    }
    
    /**
     * Returns the created user id.
     *
     * @return string
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }
    
    /**
     * Sets the created user id.
     *
     * @param string $createdUserId
     *
     * @return void
     */
    public function setCreatedUserId($createdUserId)
    {
        $this->createdUserId = $createdUserId;
    }
    
    /**
     * Returns the updated user id.
     *
     * @return string
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }
    
    /**
     * Sets the updated user id.
     *
     * @param string $updatedUserId
     *
     * @return void
     */
    public function setUpdatedUserId($updatedUserId)
    {
        $this->updatedUserId = $updatedUserId;
    }
    
    /**
     * Returns the created date.
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    
    /**
     * Sets the created date.
     *
     * @param \DateTime $createdDate
     *
     * @return void
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }
    
    /**
     * Returns the updated date.
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
    
    /**
     * Sets the updated date.
     *
     * @param \DateTime $updatedDate
     *
     * @return void
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }
    
    
    /**
     * Returns the site.
     *
     * @return \Zikula\MultisitesModule\Entity\SiteEntity
     */
    public function getSite()
    {
        return $this->site;
    }
    
    /**
     * Sets the site.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteEntity $site
     *
     * @return void
     */
    public function setSite($site = null)
    {
        $this->site = $site;
    }
    
    
    
    /**
     * Returns the formatted title conforming to the display pattern
     * specified for this entity.
     *
     * @return string The display title
     */
    public function getTitleFromDisplayPattern()
    {
        $serviceManager = ServiceUtil::getManager();
        $listHelper = $serviceManager->get('zikula_multisites_module.listentries_helper');
    
        $formattedTitle = ''
                . $this->getName()
                . ' v'
                . $this->getExtensionVersion();
    
        return $formattedTitle;
    }
    
    
    /**
     * Returns a list of possible choices for the workflowState list field.
     * This method is used for validation.
     *
     * @return array List of allowed choices
     */
    public static function getWorkflowStateAllowedValues()
    {
        $serviceManager = ServiceUtil::getManager();
        $helper = $serviceManager->get('zikula_multisites_module.listentries_helper');
        $listEntries = $helper->getWorkflowStateEntriesForSiteExtension();
    
        $allowedValues = ['initial'];
        foreach ($listEntries as $entry) {
            $allowedValues[] = $entry['value'];
        }
    
        return $allowedValues;
    }
    
    /**
     * Returns a list of possible choices for the extensionType list field.
     * This method is used for validation.
     *
     * @return array List of allowed choices
     */
    public static function getExtensionTypeAllowedValues()
    {
        $serviceManager = ServiceUtil::getManager();
        $helper = $serviceManager->get('zikula_multisites_module.listentries_helper');
        $listEntries = $helper->getExtensionTypeEntriesForSiteExtension();
    
        $allowedValues = [];
        foreach ($listEntries as $entry) {
            $allowedValues[] = $entry['value'];
        }
    
        return $allowedValues;
    }
    
    /**
     * Sets/retrieves the workflow details.
     *
     * @param boolean $forceLoading load the workflow record
     *
     * @throws RuntimeException Thrown if retrieving the workflow object fails
     */
    public function initWorkflow($forceLoading = false)
    {
        $currentFunc = FormUtil::getPassedValue('func', 'index', 'GETPOST', FILTER_SANITIZE_STRING);
        $isReuse = FormUtil::getPassedValue('astemplate', '', 'GETPOST', FILTER_SANITIZE_STRING);
    
        // apply workflow with most important information
        $idColumn = 'id';
        
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = $serviceManager->get('zikula_multisites_module.workflow_helper');
        
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = [
            'module' => 'ZikulaMultisitesModule',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => $idColumn,
            'obj_id' => $this[$idColumn],
            'schemaname' => $schemaName
        ];
        
        // load the real workflow only when required (e. g. when func is edit or delete)
        if ((!in_array($currentFunc, ['index', 'view', 'display']) && empty($isReuse)) || $forceLoading) {
            $result = Zikula_Workflow_Util::getWorkflowForObject($this, $this['_objectType'], $idColumn, 'ZikulaMultisitesModule');
            if (!$result) {
                $flashBag = $serviceManager->get('session')->getFlashBag();
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Could not load the associated workflow.'));
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
        $workflowHelper = $serviceManager->get('zikula_multisites_module.workflow_helper');
    
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = [
            'module' => 'ZikulaMultisitesModule',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => 'id',
            'obj_id' => 0,
            'schemaname' => $schemaName
        ];
    }
    
    /**
     * Start validation and raise exception if invalid data is found.
     *
     * @return boolean Whether everything is valid or not
     */
    public function validate()
    {
        if (true === $this->_bypassValidation) {
            return true;
        }
    
        $serviceManager = ServiceUtil::getManager();
    
        $validator = $serviceManager->get('validator');
        $errors = $validator->validate($this);
    
        if (count($errors) > 0) {
            $flashBag = $serviceManager->get('session')->getFlashBag();
            foreach ($errors as $error) {
                $flashBag->add('error', $error->getMessage());
            }
    
            return false;
        }
    
        return true;
    }
    
    /**
     * Return entity data in JSON format.
     *
     * @return string JSON-encoded data
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array The resulting arguments list
     */
    public function createUrlArgs()
    {
        $args = [];
    
        $args['id'] = $this['id'];
    
        if (property_exists($this, 'slug')) {
            $args['slug'] = $this['slug'];
        }
    
        return $args;
    }
    
    /**
     * Create concatenated identifier string (for composite keys).
     *
     * @return String concatenated identifiers
     */
    public function createCompositeIdentifier()
    {
        $itemId = $this['id'];
    
        return $itemId;
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'zikulamultisitesmodule.ui_hooks.siteextensions';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects The objects are added to this array. Default: []
     * 
     * @return array of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = []) 
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Site extension ' . $this->createCompositeIdentifier();
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
     */
    public function __clone()
    {
        // If the entity has an identity, proceed as normal.
        if ($this->id) {
            // unset identifiers
            $this->setId(0);
    
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
