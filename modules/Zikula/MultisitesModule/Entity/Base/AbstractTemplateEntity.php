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

namespace Zikula\MultisitesModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\MultisitesModule\Traits\StandardFieldsTrait;
use Zikula\MultisitesModule\Validator\Constraints as MultisitesAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for template entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractTemplateEntity extends EntityAccess
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'template';
    
    /**
     * @var string Path to upload base folder
     */
    protected $_uploadBasePath = '';
    
    /**
     * @var string Base URL to upload files
     */
    protected $_uploadBaseUrl = '';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var int $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank
     * @MultisitesAssert\ListEntry(entityName="template", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(length=150)
     * @Assert\NotBlank
     * @Assert\Length(min="0", max="150")
     * @var string $name
     */
    protected $name = '';
    
    /**
     * @ORM\Column(length=250)
     * @Assert\NotNull
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
     * @ORM\Column(name="sqlFile", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min="0", max="255")
     * @var string $sqlFileFileName
     */
    protected $sqlFileFileName = null;
    
    /**
     * Full sql file path as url.
     *
     * @Assert\Type(type="string")
     * @var string $sqlFileUrl
     */
    protected $sqlFileUrl = '';
    
    /**
     * Sql file file object.
     *
     * @Assert\File(
     *    mimeTypes = {"text/*"}
     * )
     * @var File $sqlFile
     */
    protected $sqlFile = null;
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull
     * @Assert\Type(type="array")
     * @var array $parameters
     */
    protected $parameters = [];
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull
     * @Assert\Type(type="array")
     * @var array $folders
     */
    protected $folders = [];
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull
     * @Assert\Type(type="array")
     * @var array $excludedTables
     */
    protected $excludedTables = [];
    
    
    /**
     * Bidirectional - Many templates [templates] are linked by many projects [projects] (INVERSE SIDE).
     *
     * @ORM\ManyToMany(
     *     targetEntity="Zikula\MultisitesModule\Entity\ProjectEntity",
     *     mappedBy="templates"
     * )
     * @var \Zikula\MultisitesModule\Entity\ProjectEntity[] $projects
     */
    protected $projects = null;
    /**
     * Bidirectional - One template [template] has many sites [sites] (INVERSE SIDE).
     *
     * @ORM\OneToMany(
     *     targetEntity="Zikula\MultisitesModule\Entity\SiteEntity",
     *     mappedBy="template", cascade={"remove"})
     * )
     * @ORM\JoinTable(name="zikula_multisites_templatesites")
     * @ORM\OrderBy({"name" = "ASC"})
     * @var \Zikula\MultisitesModule\Entity\SiteEntity[] $sites
     */
    protected $sites = null;
    
    
    /**
     * TemplateEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
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
        if ($this->_objectType !== $_objectType) {
            $this->_objectType = isset($_objectType) ? $_objectType : '';
        }
    }
    
    /**
     * Returns the _upload base path.
     *
     * @return string
     */
    public function get_uploadBasePath()
    {
        return $this->_uploadBasePath;
    }
    
    /**
     * Sets the _upload base path.
     *
     * @param string $_uploadBasePath
     *
     * @return void
     */
    public function set_uploadBasePath($_uploadBasePath)
    {
        if ($this->_uploadBasePath !== $_uploadBasePath) {
            $this->_uploadBasePath = isset($_uploadBasePath) ? $_uploadBasePath : '';
        }
    }
    
    /**
     * Returns the _upload base url.
     *
     * @return string
     */
    public function get_uploadBaseUrl()
    {
        return $this->_uploadBaseUrl;
    }
    
    /**
     * Sets the _upload base url.
     *
     * @param string $_uploadBaseUrl
     *
     * @return void
     */
    public function set_uploadBaseUrl($_uploadBaseUrl)
    {
        if ($this->_uploadBaseUrl !== $_uploadBaseUrl) {
            $this->_uploadBaseUrl = isset($_uploadBaseUrl) ? $_uploadBaseUrl : '';
        }
    }
    
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id = null)
    {
        if ((int)$this->id !== (int)$id) {
            $this->id = (int)$id;
        }
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
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = isset($workflowState) ? $workflowState : '';
        }
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
        if ($this->name !== $name) {
            $this->name = isset($name) ? $name : '';
        }
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
        if ($this->description !== $description) {
            $this->description = isset($description) ? $description : '';
        }
    }
    /**
     * Returns the sql file.
     *
     * @return File
     */
    public function getSqlFile()
    {
        if (null !== $this->sqlFile) {
            return $this->sqlFile;
        }
    
        $fileName = $this->sqlFileFileName;
        if (!empty($fileName) && !$this->_uploadBasePath) {
            throw new RuntimeException('Invalid upload base path in ' . get_class($this) . '#getSqlFile().');
        }
    
        $filePath = $this->_uploadBasePath . 'sqlfile/' . $fileName;
        if (!empty($fileName) && file_exists($filePath)) {
            $this->sqlFile = new File($filePath);
            $this->setSqlFileUrl($this->_uploadBaseUrl . '/' . $filePath);
        } else {
            $this->setSqlFileFileName('');
            $this->setSqlFileUrl('');
        }
    
        return $this->sqlFile;
    }
    
    /**
     * Sets the sql file.
     *
     * @return void
     */
    public function setSqlFile(File $sqlFile = null)
    {
        if (null === $this->sqlFile && null === $sqlFile) {
            return;
        }
        if (
            null !== $this->sqlFile
            && null !== $sqlFile
            && $this->sqlFile instanceof File
            && $this->sqlFile->getRealPath() === $sqlFile->getRealPath()
        ) {
            return;
        }
        $this->sqlFile = isset($sqlFile) ? $sqlFile : '';
    
        if (null === $this->sqlFile || '' === $this->sqlFile) {
            $this->setSqlFileFileName('');
            $this->setSqlFileUrl('');
            $this->setSqlFileMeta([]);
        } else {
            $this->setSqlFileFileName($this->sqlFile->getFilename());
        }
    }
    
    
    /**
     * Returns the sql file file name.
     *
     * @return string
     */
    public function getSqlFileFileName()
    {
        return $this->sqlFileFileName;
    }
    
    /**
     * Sets the sql file file name.
     *
     * @param string $sqlFileFileName
     *
     * @return void
     */
    public function setSqlFileFileName($sqlFileFileName = null)
    {
        if ($this->sqlFileFileName !== $sqlFileFileName) {
            $this->sqlFileFileName = isset($sqlFileFileName) ? $sqlFileFileName : '';
        }
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
    public function setSqlFileUrl($sqlFileUrl = null)
    {
        if ($this->sqlFileUrl !== $sqlFileUrl) {
            $this->sqlFileUrl = isset($sqlFileUrl) ? $sqlFileUrl : '';
        }
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
    public function setSqlFileMeta(array $sqlFileMeta = [])
    {
        if ($this->sqlFileMeta !== $sqlFileMeta) {
            $this->sqlFileMeta = isset($sqlFileMeta) ? $sqlFileMeta : '';
        }
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
        if ($this->parameters !== $parameters) {
            $this->parameters = isset($parameters) ? $parameters : [];
        }
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
        if ($this->folders !== $folders) {
            $this->folders = isset($folders) ? $folders : [];
        }
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
        if ($this->excludedTables !== $excludedTables) {
            $this->excludedTables = isset($excludedTables) ? $excludedTables : [];
        }
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
    public function setProjects($projects = null)
    {
        foreach ($this->projects as $projectSingle) {
            $this->removeProjects($projectSingle);
        }
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
    public function setSites($sites = null)
    {
        foreach ($this->sites as $siteSingle) {
            $this->removeSites($siteSingle);
        }
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
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array List of resulting arguments
     */
    public function createUrlArgs()
    {
        return [
            'id' => $this->getId()
        ];
    }
    
    /**
     * Returns the primary key.
     *
     * @return int The identifier
     */
    public function getKey()
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return bool
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
     * @param array $objects Objects that are added to this array
     *
     * @return array List of entity objects
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
        return 'Template ' . $this->getKey() . ': ' . $this->getName();
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
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        // reset upload fields
        $this->setSqlFile(null);
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    }
}
