<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\MultisitesModule\Traits\StandardFieldsTrait;
use Zikula\MultisitesModule\Validator\Constraints as MultisitesAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for site entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractSiteEntity extends EntityAccess
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'site';
    
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
     * @MultisitesAssert\ListEntry(entityName="site", propertyName="workflowState", multiple=false)
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
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $description
     */
    protected $description = '';
    
    /**
     * @ORM\Column(length=80)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="80")
     * @var string $siteAlias
     */
    protected $siteAlias = '';
    
    /**
     * @ORM\Column(length=150)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="150")
     * @var string $siteName
     */
    protected $siteName = '';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $siteDescription
     */
    protected $siteDescription = '';
    
    /**
     * @ORM\Column(length=25)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="25")
     * @var string $siteAdminName
     */
    protected $siteAdminName = 'admin';
    
    /**
     * @ORM\Column(length=15)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="15")
     * @var string $siteAdminPassword
     */
    protected $siteAdminPassword = '';
    
    /**
     * @ORM\Column(length=70)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="70")
     * @var string $siteAdminRealName
     */
    protected $siteAdminRealName = '';
    
    /**
     * @ORM\Column(length=40)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="40")
     * @Assert\Email(checkMX=false, checkHost=false)
     * @var string $siteAdminEmail
     */
    protected $siteAdminEmail = '';
    
    /**
     * @ORM\Column(length=100)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="100")
     * @var string $siteCompany
     */
    protected $siteCompany = '';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $siteDns
     */
    protected $siteDns = '';
    
    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="50")
     * @var string $databaseName
     */
    protected $databaseName = '';
    
    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="50")
     * @var string $databaseUserName
     */
    protected $databaseUserName = '';
    
    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="50")
     * @var string $databasePassword
     */
    protected $databasePassword = '';
    
    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="50")
     * @var string $databaseHost
     */
    protected $databaseHost = 'localhost';
    
    /**
     * @ORM\Column(length=25)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="25")
     * @var string $databaseType
     */
    protected $databaseType = '';
    
    /**
     * Logo meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $logoMeta
     */
    protected $logoMeta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
     * )
     * @var string $logo
     */
    protected $logo = null;
    
    /**
     * Full logo path as url.
     *
     * @Assert\Type(type="string")
     * @var string $logoUrl
     */
    protected $logoUrl = '';
    
    /**
     * Fav icon meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $favIconMeta
     */
    protected $favIconMeta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    mimeTypes = {"image/*"}
     * )
     * @var string $favIcon
     */
    protected $favIcon = null;
    
    /**
     * Full fav icon path as url.
     *
     * @Assert\Type(type="string")
     * @var string $favIconUrl
     */
    protected $favIconUrl = '';
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $allowedLocales
     */
    protected $allowedLocales = [];
    
    /**
     * Parameters csv file meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $parametersCsvFileMeta
     */
    protected $parametersCsvFileMeta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    mimeTypes = {"text/csv"}
     * )
     * @var string $parametersCsvFile
     */
    protected $parametersCsvFile = null;
    
    /**
     * Full parameters csv file path as url.
     *
     * @Assert\Type(type="string")
     * @var string $parametersCsvFileUrl
     */
    protected $parametersCsvFileUrl = '';
    
    /**
     * @ORM\Column(type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     * @var array $parametersArray
     */
    protected $parametersArray = [];
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $active
     */
    protected $active = false;
    
    
    /**
     * Bidirectional - Many sites [sites] are linked by one template [template] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="Zikula\MultisitesModule\Entity\TemplateEntity", inversedBy="sites")
     * @ORM\JoinTable(name="zikula_multisites_template")
     * @Assert\Type(type="Zikula\MultisitesModule\Entity\TemplateEntity")
     * @var \Zikula\MultisitesModule\Entity\TemplateEntity $template
     */
    protected $template;
    
    /**
     * Bidirectional - Many sites [sites] are linked by one project [project] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="Zikula\MultisitesModule\Entity\ProjectEntity", inversedBy="sites")
     * @ORM\JoinTable(name="zikula_multisites_project")
     * @Assert\Type(type="Zikula\MultisitesModule\Entity\ProjectEntity")
     * @var \Zikula\MultisitesModule\Entity\ProjectEntity $project
     */
    protected $project;
    
    /**
     * Bidirectional - One site [site] has many extensions [site extensions] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="Zikula\MultisitesModule\Entity\SiteExtensionEntity", mappedBy="site", cascade={"remove"})
     * @ORM\JoinTable(name="zikula_multisites_siteextensions")
     * @ORM\OrderBy({"name" = "ASC"})
     * @var \Zikula\MultisitesModule\Entity\SiteExtensionEntity[] $extensions
     */
    protected $extensions = null;
    
    
    /**
     * SiteEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
        $this->extensions = new ArrayCollection();
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
        if ($this->_objectType != $_objectType) {
            $this->_objectType = $_objectType;
        }
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
        if (intval($this->id) !== intval($id)) {
            $this->id = intval($id);
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
     * Returns the site alias.
     *
     * @return string
     */
    public function getSiteAlias()
    {
        return $this->siteAlias;
    }
    
    /**
     * Sets the site alias.
     *
     * @param string $siteAlias
     *
     * @return void
     */
    public function setSiteAlias($siteAlias)
    {
        if ($this->siteAlias !== $siteAlias) {
            $this->siteAlias = isset($siteAlias) ? $siteAlias : '';
        }
    }
    
    /**
     * Returns the site name.
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }
    
    /**
     * Sets the site name.
     *
     * @param string $siteName
     *
     * @return void
     */
    public function setSiteName($siteName)
    {
        if ($this->siteName !== $siteName) {
            $this->siteName = isset($siteName) ? $siteName : '';
        }
    }
    
    /**
     * Returns the site description.
     *
     * @return string
     */
    public function getSiteDescription()
    {
        return $this->siteDescription;
    }
    
    /**
     * Sets the site description.
     *
     * @param string $siteDescription
     *
     * @return void
     */
    public function setSiteDescription($siteDescription)
    {
        if ($this->siteDescription !== $siteDescription) {
            $this->siteDescription = isset($siteDescription) ? $siteDescription : '';
        }
    }
    
    /**
     * Returns the site admin name.
     *
     * @return string
     */
    public function getSiteAdminName()
    {
        return $this->siteAdminName;
    }
    
    /**
     * Sets the site admin name.
     *
     * @param string $siteAdminName
     *
     * @return void
     */
    public function setSiteAdminName($siteAdminName)
    {
        if ($this->siteAdminName !== $siteAdminName) {
            $this->siteAdminName = isset($siteAdminName) ? $siteAdminName : '';
        }
    }
    
    /**
     * Returns the site admin password.
     *
     * @return string
     */
    public function getSiteAdminPassword()
    {
        return $this->siteAdminPassword;
    }
    
    /**
     * Sets the site admin password.
     *
     * @param string $siteAdminPassword
     *
     * @return void
     */
    public function setSiteAdminPassword($siteAdminPassword)
    {
        if ($this->siteAdminPassword !== $siteAdminPassword) {
            $this->siteAdminPassword = isset($siteAdminPassword) ? $siteAdminPassword : '';
        }
    }
    
    /**
     * Returns the site admin real name.
     *
     * @return string
     */
    public function getSiteAdminRealName()
    {
        return $this->siteAdminRealName;
    }
    
    /**
     * Sets the site admin real name.
     *
     * @param string $siteAdminRealName
     *
     * @return void
     */
    public function setSiteAdminRealName($siteAdminRealName)
    {
        if ($this->siteAdminRealName !== $siteAdminRealName) {
            $this->siteAdminRealName = isset($siteAdminRealName) ? $siteAdminRealName : '';
        }
    }
    
    /**
     * Returns the site admin email.
     *
     * @return string
     */
    public function getSiteAdminEmail()
    {
        return $this->siteAdminEmail;
    }
    
    /**
     * Sets the site admin email.
     *
     * @param string $siteAdminEmail
     *
     * @return void
     */
    public function setSiteAdminEmail($siteAdminEmail)
    {
        if ($this->siteAdminEmail !== $siteAdminEmail) {
            $this->siteAdminEmail = isset($siteAdminEmail) ? $siteAdminEmail : '';
        }
    }
    
    /**
     * Returns the site company.
     *
     * @return string
     */
    public function getSiteCompany()
    {
        return $this->siteCompany;
    }
    
    /**
     * Sets the site company.
     *
     * @param string $siteCompany
     *
     * @return void
     */
    public function setSiteCompany($siteCompany)
    {
        if ($this->siteCompany !== $siteCompany) {
            $this->siteCompany = isset($siteCompany) ? $siteCompany : '';
        }
    }
    
    /**
     * Returns the site dns.
     *
     * @return string
     */
    public function getSiteDns()
    {
        return $this->siteDns;
    }
    
    /**
     * Sets the site dns.
     *
     * @param string $siteDns
     *
     * @return void
     */
    public function setSiteDns($siteDns)
    {
        if ($this->siteDns !== $siteDns) {
            $this->siteDns = isset($siteDns) ? $siteDns : '';
        }
    }
    
    /**
     * Returns the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }
    
    /**
     * Sets the database name.
     *
     * @param string $databaseName
     *
     * @return void
     */
    public function setDatabaseName($databaseName)
    {
        if ($this->databaseName !== $databaseName) {
            $this->databaseName = isset($databaseName) ? $databaseName : '';
        }
    }
    
    /**
     * Returns the database user name.
     *
     * @return string
     */
    public function getDatabaseUserName()
    {
        return $this->databaseUserName;
    }
    
    /**
     * Sets the database user name.
     *
     * @param string $databaseUserName
     *
     * @return void
     */
    public function setDatabaseUserName($databaseUserName)
    {
        if ($this->databaseUserName !== $databaseUserName) {
            $this->databaseUserName = isset($databaseUserName) ? $databaseUserName : '';
        }
    }
    
    /**
     * Returns the database password.
     *
     * @return string
     */
    public function getDatabasePassword()
    {
        return $this->databasePassword;
    }
    
    /**
     * Sets the database password.
     *
     * @param string $databasePassword
     *
     * @return void
     */
    public function setDatabasePassword($databasePassword)
    {
        if ($this->databasePassword !== $databasePassword) {
            $this->databasePassword = isset($databasePassword) ? $databasePassword : '';
        }
    }
    
    /**
     * Returns the database host.
     *
     * @return string
     */
    public function getDatabaseHost()
    {
        return $this->databaseHost;
    }
    
    /**
     * Sets the database host.
     *
     * @param string $databaseHost
     *
     * @return void
     */
    public function setDatabaseHost($databaseHost)
    {
        if ($this->databaseHost !== $databaseHost) {
            $this->databaseHost = isset($databaseHost) ? $databaseHost : '';
        }
    }
    
    /**
     * Returns the database type.
     *
     * @return string
     */
    public function getDatabaseType()
    {
        return $this->databaseType;
    }
    
    /**
     * Sets the database type.
     *
     * @param string $databaseType
     *
     * @return void
     */
    public function setDatabaseType($databaseType)
    {
        if ($this->databaseType !== $databaseType) {
            $this->databaseType = isset($databaseType) ? $databaseType : '';
        }
    }
    
    /**
     * Returns the logo.
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * Sets the logo.
     *
     * @param string $logo
     *
     * @return void
     */
    public function setLogo($logo)
    {
        if ($this->logo !== $logo) {
            $this->logo = $logo;
        }
    }
    
    /**
     * Returns the logo url.
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }
    
    /**
     * Sets the logo url.
     *
     * @param string $logoUrl
     *
     * @return void
     */
    public function setLogoUrl($logoUrl)
    {
        if ($this->logoUrl !== $logoUrl) {
            $this->logoUrl = $logoUrl;
        }
    }
    
    /**
     * Returns the logo meta.
     *
     * @return array
     */
    public function getLogoMeta()
    {
        return $this->logoMeta;
    }
    
    /**
     * Sets the logo meta.
     *
     * @param array $logoMeta
     *
     * @return void
     */
    public function setLogoMeta($logoMeta = [])
    {
        if ($this->logoMeta !== $logoMeta) {
            $this->logoMeta = $logoMeta;
        }
    }
    
    /**
     * Returns the fav icon.
     *
     * @return string
     */
    public function getFavIcon()
    {
        return $this->favIcon;
    }
    
    /**
     * Sets the fav icon.
     *
     * @param string $favIcon
     *
     * @return void
     */
    public function setFavIcon($favIcon)
    {
        if ($this->favIcon !== $favIcon) {
            $this->favIcon = $favIcon;
        }
    }
    
    /**
     * Returns the fav icon url.
     *
     * @return string
     */
    public function getFavIconUrl()
    {
        return $this->favIconUrl;
    }
    
    /**
     * Sets the fav icon url.
     *
     * @param string $favIconUrl
     *
     * @return void
     */
    public function setFavIconUrl($favIconUrl)
    {
        if ($this->favIconUrl !== $favIconUrl) {
            $this->favIconUrl = $favIconUrl;
        }
    }
    
    /**
     * Returns the fav icon meta.
     *
     * @return array
     */
    public function getFavIconMeta()
    {
        return $this->favIconMeta;
    }
    
    /**
     * Sets the fav icon meta.
     *
     * @param array $favIconMeta
     *
     * @return void
     */
    public function setFavIconMeta($favIconMeta = [])
    {
        if ($this->favIconMeta !== $favIconMeta) {
            $this->favIconMeta = $favIconMeta;
        }
    }
    
    /**
     * Returns the allowed locales.
     *
     * @return array
     */
    public function getAllowedLocales()
    {
        return $this->allowedLocales;
    }
    
    /**
     * Sets the allowed locales.
     *
     * @param array $allowedLocales
     *
     * @return void
     */
    public function setAllowedLocales($allowedLocales)
    {
        if ($this->allowedLocales !== $allowedLocales) {
            $this->allowedLocales = isset($allowedLocales) ? $allowedLocales : '';
        }
    }
    
    /**
     * Returns the parameters csv file.
     *
     * @return string
     */
    public function getParametersCsvFile()
    {
        return $this->parametersCsvFile;
    }
    
    /**
     * Sets the parameters csv file.
     *
     * @param string $parametersCsvFile
     *
     * @return void
     */
    public function setParametersCsvFile($parametersCsvFile)
    {
        if ($this->parametersCsvFile !== $parametersCsvFile) {
            $this->parametersCsvFile = $parametersCsvFile;
        }
    }
    
    /**
     * Returns the parameters csv file url.
     *
     * @return string
     */
    public function getParametersCsvFileUrl()
    {
        return $this->parametersCsvFileUrl;
    }
    
    /**
     * Sets the parameters csv file url.
     *
     * @param string $parametersCsvFileUrl
     *
     * @return void
     */
    public function setParametersCsvFileUrl($parametersCsvFileUrl)
    {
        if ($this->parametersCsvFileUrl !== $parametersCsvFileUrl) {
            $this->parametersCsvFileUrl = $parametersCsvFileUrl;
        }
    }
    
    /**
     * Returns the parameters csv file meta.
     *
     * @return array
     */
    public function getParametersCsvFileMeta()
    {
        return $this->parametersCsvFileMeta;
    }
    
    /**
     * Sets the parameters csv file meta.
     *
     * @param array $parametersCsvFileMeta
     *
     * @return void
     */
    public function setParametersCsvFileMeta($parametersCsvFileMeta = [])
    {
        if ($this->parametersCsvFileMeta !== $parametersCsvFileMeta) {
            $this->parametersCsvFileMeta = $parametersCsvFileMeta;
        }
    }
    
    /**
     * Returns the parameters array.
     *
     * @return array
     */
    public function getParametersArray()
    {
        return $this->parametersArray;
    }
    
    /**
     * Sets the parameters array.
     *
     * @param array $parametersArray
     *
     * @return void
     */
    public function setParametersArray($parametersArray)
    {
        if ($this->parametersArray !== $parametersArray) {
            $this->parametersArray = isset($parametersArray) ? $parametersArray : '';
        }
    }
    
    /**
     * Returns the active.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
    /**
     * Sets the active.
     *
     * @param boolean $active
     *
     * @return void
     */
    public function setActive($active)
    {
        if (boolval($this->active) !== boolval($active)) {
            $this->active = boolval($active);
        }
    }
    
    
    /**
     * Returns the template.
     *
     * @return \Zikula\MultisitesModule\Entity\TemplateEntity
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Sets the template.
     *
     * @param \Zikula\MultisitesModule\Entity\TemplateEntity $template
     *
     * @return void
     */
    public function setTemplate($template = null)
    {
        $this->template = $template;
    }
    
    /**
     * Returns the project.
     *
     * @return \Zikula\MultisitesModule\Entity\ProjectEntity
     */
    public function getProject()
    {
        return $this->project;
    }
    
    /**
     * Sets the project.
     *
     * @param \Zikula\MultisitesModule\Entity\ProjectEntity $project
     *
     * @return void
     */
    public function setProject($project = null)
    {
        $this->project = $project;
    }
    
    /**
     * Returns the extensions.
     *
     * @return \Zikula\MultisitesModule\Entity\SiteExtensionEntity[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
    
    /**
     * Sets the extensions.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteExtensionEntity[] $extensions
     *
     * @return void
     */
    public function setExtensions($extensions)
    {
        foreach ($this->extensions as $siteExtensionSingle) {
            $this->removeExtensions($siteExtensionSingle);
        }
        foreach ($extensions as $siteExtensionSingle) {
            $this->addExtensions($siteExtensionSingle);
        }
    }
    
    /**
     * Adds an instance of \Zikula\MultisitesModule\Entity\SiteExtensionEntity to the list of extensions.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteExtensionEntity $siteExtension The instance to be added to the collection
     *
     * @return void
     */
    public function addExtensions(\Zikula\MultisitesModule\Entity\SiteExtensionEntity $siteExtension)
    {
        $this->extensions->add($siteExtension);
        $siteExtension->setSite($this);
    }
    
    /**
     * Removes an instance of \Zikula\MultisitesModule\Entity\SiteExtensionEntity from the list of extensions.
     *
     * @param \Zikula\MultisitesModule\Entity\SiteExtensionEntity $siteExtension The instance to be removed from the collection
     *
     * @return void
     */
    public function removeExtensions(\Zikula\MultisitesModule\Entity\SiteExtensionEntity $siteExtension)
    {
        $this->extensions->removeElement($siteExtension);
        $siteExtension->setSite(null);
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array The resulting arguments list
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
     * @return integer The identifier
     */
    public function getKey()
    {
        return $this->getId();
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
        return 'zikulamultisitesmodule.ui_hooks.sites';
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
        return 'Site ' . $this->getKey() . ': ' . $this->getName();
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
        $this->setLogo(null);
        $this->setLogoMeta([]);
        $this->setLogoUrl('');
        $this->setFavIcon(null);
        $this->setFavIconMeta([]);
        $this->setFavIconUrl('');
        $this->setParametersCsvFile(null);
        $this->setParametersCsvFileMeta([]);
        $this->setParametersCsvFileUrl('');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    
    }
}
