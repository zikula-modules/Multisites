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
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;
use Symfony\Component\HttpFoundation\File\File;
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
 * This is the base entity class for template entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 *
 * @abstract
 */
abstract class AbstractTemplateEntity extends EntityAccess
{
    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'template';
    
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
     * @ORM\Column(length=150)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="150")
     * @var string $name
     */
    protected $name = '';
    
    /**
     * @ORM\Column(length=250)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="250")
     * @var string $description
     */
    protected $description = '';
    
    /**
     * Sql file meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $sqlFileMeta
     */
    protected $sqlFileMeta = [];
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
        mimeTypes = {"image/*"}
     * )
     * @var string $sqlFile
     */
    protected $sqlFile = null;
    
    /**
     * Full sql file path as url.
     *
     * @Assert\Type(type="string")
     * @Assert\Url()
     * @var string $sqlFileUrl
     */
    protected $sqlFileUrl = '';
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $parameters
     */
    protected $parameters = [];
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $folders
     */
    protected $folders = [];
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $excludedTables
     */
    protected $excludedTables = [];
    
    
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
     * Bidirectional - Many templates [templates] are linked by many projects [projects] (INVERSE SIDE).
     *
     * @ORM\ManyToMany(targetEntity="Zikula\MultisitesModule\Entity\ProjectEntity", mappedBy="templates")
     * @var \Zikula\MultisitesModule\Entity\ProjectEntity[] $projects
     */
    protected $projects = null;
    /**
     * Bidirectional - One template [template] has many sites [sites] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="Zikula\MultisitesModule\Entity\SiteEntity", mappedBy="template", cascade={"remove"})
     * @ORM\JoinTable(name="zikula_multisites_templatesites")
     * @ORM\OrderBy({"name" = "ASC"})
     * @var \Zikula\MultisitesModule\Entity\SiteEntity[] $sites
     */
    protected $sites = null;
    
    
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
        $this->initWorkflow();
        $this->sites = new ArrayCollection();
        $this->projects = new ArrayCollection();
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
     * Returns the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets the description.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = isset($description) ? $description : '';
    }
    
    /**
     * Returns the sql file.
     *
     * @return string
     */
    public function getSqlFile()
    {
        return $this->sqlFile;
    }
    
    /**
     * Sets the sql file.
     *
     * @param string $sqlFile
     *
     * @return void
     */
    public function setSqlFile($sqlFile)
    {
        $this->sqlFile = isset($sqlFile) ? $sqlFile : '';
    }
    
    /**
     * Returns the sql file url.
     *
     * @return string
     */
    public function getSqlFileUrl()
    {
        return $this->sqlFileUrl;
    }
    
    /**
     * Sets the sql file url.
     *
     * @param string $sqlFileUrl
     *
     * @return void
     */
    public function setSqlFileUrl($sqlFileUrl)
    {
        $this->sqlFileUrl = isset($sqlFileUrl) ? $sqlFileUrl : '';
    }
    
    /**
     * Returns the sql file meta.
     *
     * @return array
     */
    public function getSqlFileMeta()
    {
        return $this->sqlFileMeta;
    }
    
    /**
     * Sets the sql file meta.
     *
     * @param array $sqlFileMeta
     *
     * @return void
     */
    public function setSqlFileMeta($sqlFileMeta = [])
    {
        $this->sqlFileMeta = isset($sqlFileMeta) ? $sqlFileMeta : '';
    }
    
    /**
     * Returns the parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    
    /**
     * Sets the parameters.
     *
     * @param array $parameters
     *
     * @return void
     */
    public function setParameters($parameters)
    {
        $this->parameters = isset($parameters) ? $parameters : '';
    }
    
    /**
     * Returns the folders.
     *
     * @return array
     */
    public function getFolders()
    {
        return $this->folders;
    }
    
    /**
     * Sets the folders.
     *
     * @param array $folders
     *
     * @return void
     */
    public function setFolders($folders)
    {
        $this->folders = isset($folders) ? $folders : '';
    }
    
    /**
     * Returns the excluded tables.
     *
     * @return array
     */
    public function getExcludedTables()
    {
        return $this->excludedTables;
    }
    
    /**
     * Sets the excluded tables.
     *
     * @param array $excludedTables
     *
     * @return void
     */
    public function setExcludedTables($excludedTables)
    {
        $this->excludedTables = isset($excludedTables) ? $excludedTables : '';
    }
    
    /**
     * Returns the created user id.
     *
     * @return integer
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }
    
    /**
     * Sets the created user id.
     *
     * @param integer $createdUserId
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
     * @return integer
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }
    
    /**
     * Sets the updated user id.
     *
     * @param integer $updatedUserId
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
     * Returns the projects.
     *
     * @return \Zikula\MultisitesModule\Entity\ProjectEntity[]
     */
    public function getProjects()
    {
        return $this->projects;
    }
    
    /**
     * Sets the projects.
     *
     * @param \Zikula\MultisitesModule\Entity\ProjectEntity[] $projects
     *
     * @return void
     */
    public function setProjects($projects)
    {
        foreach ($projects as $projectSingle) {
            $this->addProjects($projectSingle);
        }
    }
    
    /**
     * Adds an instance of \Zikula\MultisitesModule\Entity\ProjectEntity to the list of projects.
     *
     * @param \Zikula\MultisitesModule\Entity\ProjectEntity $project The instance to be added to the collection
     *
     * @return void
     */
    public function addProjects(\Zikula\MultisitesModule\Entity\ProjectEntity $project)
    {
        $this->projects->add($project);
        $project->addTemplates($this);
    }
    
    /**
     * Removes an instance of \Zikula\MultisitesModule\Entity\ProjectEntity from the list of projects.
     *
     * @param \Zikula\MultisitesModule\Entity\ProjectEntity $project The instance to be removed from the collection
     *
     * @return void
     */
    public function removeProjects(\Zikula\MultisitesModule\Entity\ProjectEntity $project)
    {
        $this->projects->removeElement($project);
        $project->removeTemplates($this);
    }
    
    /**
     * Returns the sites.
     *
     * @return \Zikula\MultisitesModule\Entity\SiteEntity[]
     */
    public function getSites()
    {
        return $this->sites;
    }
    
    /**
     * Sets the sites.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteEntity[] $sites
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
     * Adds an instance of \Zikula\MultisitesModule\Entity\SiteEntity to the list of sites.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteEntity $site The instance to be added to the collection
     *
     * @return void
     */
    public function addSites(\Zikula\MultisitesModule\Entity\SiteEntity $site)
    {
        $this->sites->add($site);
        $site->setTemplate($this);
    }
    
    /**
     * Removes an instance of \Zikula\MultisitesModule\Entity\SiteEntity from the list of sites.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteEntity $site The instance to be removed from the collection
     *
     * @return void
     */
    public function removeSites(\Zikula\MultisitesModule\Entity\SiteEntity $site)
    {
        $this->sites->removeElement($site);
        $site->setTemplate(null);
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
                . $this->getName();
    
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
        $listEntries = $helper->getWorkflowStateEntriesForTemplate();
    
        $allowedValues = ['initial'];
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
        return 'zikulamultisitesmodule.ui_hooks.templates';
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
        return 'Template ' . $this->createCompositeIdentifier();
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
    
            // reset upload fields
            $this->setSqlFile('');
            $this->setSqlFileMeta([]);
    
            $this->setCreatedDate(null);
            $this->setCreatedUserId(null);
            $this->setUpdatedDate(null);
            $this->setUpdatedUserId(null);
    
        }
        // otherwise do nothing, do NOT throw an exception!
    }
}
