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

namespace Zikula\MultisitesModule\Entity;

use Zikula\MultisitesModule\Entity\Base\AbstractTemplateEntity as BaseAbstractTemplateEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;
use Symfony\Component\Validator\Constraints as Assert;
use ModUtil;
use ServiceUtil;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for template entities.
 * @ORM\Entity(repositoryClass="\Zikula\MultisitesModule\Entity\Repository\Template")
 * @ORM\Table(name="zikula_multisites_template",
 *     indexes={
*         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
* @ORM\HasLifecycleCallbacks
 */
class TemplateEntity extends BaseAbstractTemplateEntity
{
    // feel free to add your own methods here

    /**
     * Post-Process the data after the entity has been constructed by the entity manager.
     *
     * @ORM\PostLoad
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPostLoadCallback()
     * @return void.
     */
    public function postLoadCallback()
    {
        $this->performPostLoadCallback();
    }
    
    /**
     * Pre-Process the data prior to an insert operation.
     *
     * @ORM\PrePersist
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPrePersistCallback()
     * @return void.
     */
    public function prePersistCallback()
    {
        $this->performPrePersistCallback();
    }
    
    /**
     * Post-Process the data after an insert operation.
     *
     * @ORM\PostPersist
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPostPersistCallback()
     * @return void.
     */
    public function postPersistCallback()
    {
        $this->performPostPersistCallback();
    }
    
    /**
     * Pre-Process the data prior a delete operation.
     *
     * @ORM\PreRemove
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPreRemoveCallback()
     * @return void.
     */
    public function preRemoveCallback()
    {
        $this->performPreRemoveCallback();
    }
    
    /**
     * Post-Process the data after a delete.
     *
     * @ORM\PostRemove
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPostRemoveCallback()
     * @return void
     */
    public function postRemoveCallback()
    {
        // delete sql file only if it is not referenced by any other template
        $sqlFileIsRequired = $this->isSqlFileReferencedByOtherTemplates();
        if ($sqlFileIsRequired) {
            return;
        }

        // proceed with deleting the upload file
        $this->performPostRemoveCallback();
    }
    
    /**
     * Pre-Process the data prior to an update operation.
     *
     * @ORM\PreUpdate
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPreUpdateCallback()
     * @return void.
     */
    public function preUpdateCallback()
    {
        $this->performPreUpdateCallback();
    }
    
    /**
     * Post-Process the data after an update operation.
     *
     * @ORM\PostUpdate
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPostUpdateCallback()
     * @return void.
     */
    public function postUpdateCallback()
    {
        $this->performPostUpdateCallback();
    }
    
    /**
     * Pre-Process the data prior to a save operation.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPreSaveCallback()
     * @return void.
     */
    public function preSaveCallback()
    {
        $this->performPreSaveCallback();
    }
    
    /**
     * Post-Process the data after a save operation.
     *
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::performPostSaveCallback()
     * @return void.
     */
    public function postSaveCallback()
    {
        $this->performPostSaveCallback();
    }

    /**
     * Collect available actions for this entity.
     */
    protected function prepareItemActions()
    {
        if (!empty($this->_actions)) {
            return;
        }

        $serviceManager = ServiceUtil::getManager();
        $translator = $serviceManager->get('translator.default');

        parent::prepareItemActions();

        foreach ($this->_actions as $k => $v) {
            if ($v['linkText'] != $translator->__('Create project')) {
                continue;
            }
            unset($this->_actions[$k]);
        }

        $this->_actions[] = [
            'url' => ['type' => 'template', 'func' => 'createParametersCsvTemplate', 'arguments' => ['id' => $this['id']]],
            'icon' => 'file-o',
            'linkTitle' => $translator->__('Create a CSV file for the defined parameters'),
            'linkText' => $translator->__('Parameters CSV')
        ];

        $this->_actions[] = [
            'url' => ['type' => 'template', 'func' => 'reapply', 'arguments' => ['ot' => 'template', 'id' => $this['id']]],
            'icon' => 'refresh',
            'linkTitle' => $translator->__('Reapply template to all assigned sites'),
            'linkText' => $translator->__('Reapply template')
        ];
    }

    /**
     * Checks whether the sql file is referenced by any other template.
     *
     * @return boolean True if a reference has been found, false otherwise.
     */
    public function isSqlFileReferencedByOtherTemplates()
    {
        $fileNeeded = false;
        $templates = ModUtil::apiFunc('ZikulaMultisitesModule', 'selection', 'getEntities', ['ot' => 'template']);
        foreach ($templates as $template) {
            if ($this->id == $template['id']) {
                continue;
            }
            if ($this->sqlFile != $template['sqlFile']) {
                continue;
            }
            $fileNeeded = true;
            break;
        }

        return $fileNeeded;
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
            //$this->setSqlFile('');
            //$this->setSqlFileMeta(array());
    
            $this->setCreatedDate(null);
            $this->setCreatedUserId(null);
            $this->setUpdatedDate(null);
            $this->setUpdatedUserId(null);
        }
        // otherwise do nothing, do NOT throw an exception!
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
     * @see Zikula\MultisitesModule\Entity\TemplateEntity::postRemoveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostRemoveCallback()
    {
        $serviceManager = ServiceUtil::getManager();
        $systemHelper = $serviceManager->get('zikula_multisites_module.system_helper');

        // update db config removing all obsolete databases
        if (!$systemHelper->updateDatabaseConfigFile()) {
            $session = $serviceManager->get('session');
            $session->getFlashBag()->add(\Zikula_Session::MESSAGE_ERROR, $serviceManager->get('translator.default')->__('Error! Updating the database configuration file failed.'));
        }

        return parent::performPostRemoveCallback();
    }
}
