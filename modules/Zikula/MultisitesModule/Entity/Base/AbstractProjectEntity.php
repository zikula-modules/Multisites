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
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\MultisitesModule\Traits\StandardFieldsTrait;
use Zikula\MultisitesModule\Validator\Constraints as MultisitesAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for project entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractProjectEntity extends EntityAccess
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'project';
    
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
     * @MultisitesAssert\ListEntry(entityName="project", propertyName="workflowState", multiple=false)
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
     * Bidirectional - One project [project] has many sites [sites] (INVERSE SIDE).
     *
     * @ORM\OneToMany(
     *     targetEntity="Zikula\MultisitesModule\Entity\SiteEntity",
     *     mappedBy="project", cascade={"remove"})
     * )
     * @ORM\JoinTable(name="zikula_multisites_projectsites")
     * @var \Zikula\MultisitesModule\Entity\SiteEntity[] $sites
     */
    protected $sites = null;
    
    /**
     * Bidirectional - Many projects [projects] have many templates [templates] (OWNING SIDE).
     *
     * @ORM\ManyToMany(
     *     targetEntity="Zikula\MultisitesModule\Entity\TemplateEntity",
     *     inversedBy="projects"
     * )
     * @ORM\JoinTable(name="zikula_multisites_project_template")
     * @var \Zikula\MultisitesModule\Entity\TemplateEntity[] $templates
     */
    protected $templates = null;
    
    /**
     * ProjectEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
        $this->sites = new ArrayCollection();
        $this->templates = new ArrayCollection();
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
        $site->setProject($this);
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
        $site->setProject(null);
    }
    
    /**
     * Returns the templates.
     *
     * @return \Zikula\MultisitesModule\Entity\TemplateEntity[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }
    
    /**
     * Sets the templates.
     *
     * @param \Zikula\MultisitesModule\Entity\TemplateEntity[] $templates
     *
     * @return void
     */
    public function setTemplates($templates = null)
    {
        foreach ($this->templates as $templateSingle) {
            $this->removeTemplates($templateSingle);
        }
        foreach ($templates as $templateSingle) {
            $this->addTemplates($templateSingle);
        }
    }
    
    /**
     * Adds an instance of \Zikula\MultisitesModule\Entity\TemplateEntity to the list of templates.
     *
     * @param \Zikula\MultisitesModule\Entity\TemplateEntity $template The instance to be added to the collection
     *
     * @return void
     */
    public function addTemplates(\Zikula\MultisitesModule\Entity\TemplateEntity $template)
    {
        $this->templates->add($template);
    }
    
    /**
     * Removes an instance of \Zikula\MultisitesModule\Entity\TemplateEntity from the list of templates.
     *
     * @param \Zikula\MultisitesModule\Entity\TemplateEntity $template The instance to be removed from the collection
     *
     * @return void
     */
    public function removeTemplates(\Zikula\MultisitesModule\Entity\TemplateEntity $template)
    {
        $this->templates->removeElement($template);
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
        return 'zikulamultisitesmodule.ui_hooks.projects';
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
        return 'Project ' . $this->getKey() . ': ' . $this->getName();
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
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    }
}
