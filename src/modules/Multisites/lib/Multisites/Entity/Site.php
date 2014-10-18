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
 * This is the concrete entity class for site entities.
 * @ORM\Entity(repositoryClass="Multisites_Entity_Repository_Site")
 * @ORM\Table(name="multisites_site",
 *     indexes={
*         @ORM\Index(name="sitednsindex", columns={"siteDns"}),
*         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
* @ORM\HasLifecycleCallbacks
 */
class Multisites_Entity_Site extends Multisites_Entity_Base_Site
{
    // feel free to add your own methods here

    /**
     * Post-Process the data after the entity has been constructed by the entity manager.
     *
     * @ORM\PostLoad
     * @see Multisites_Entity_Site::performPostLoadCallback()
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
     * @see Multisites_Entity_Site::performPrePersistCallback()
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
     * @see Multisites_Entity_Site::performPostPersistCallback()
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
     * @see Multisites_Entity_Site::performPreRemoveCallback()
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
     * @see Multisites_Entity_Site::performPostRemoveCallback()
     * @return void
     */
    public function postRemoveCallback()
    {
        $this->performPostRemoveCallback();

        
    }
    
    /**
     * Pre-Process the data prior to an update operation.
     *
     * @ORM\PreUpdate
     * @see Multisites_Entity_Site::performPreUpdateCallback()
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
     * @see Multisites_Entity_Site::performPostUpdateCallback()
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
     * @see Multisites_Entity_Site::performPreSaveCallback()
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
     * @see Multisites_Entity_Site::performPostSaveCallback()
     * @return void.
     */
    public function postSaveCallback()
    {
        $this->performPostSaveCallback();
    }

    /**
     * Retrieves database information array.
     *
     * @return array List of database parameters.
     */
    public function getDatabaseData()
    {
        $dbInfo = array(
            'alias' => $this->getSiteAlias(),
            'dbname' => $this->getDatabaseName(),
            'dbhost' => $this->getDatabaseHost(),
            'dbtype' => $this->getDatabaseType(),
            'dbuname' => $this->getDatabaseUserName(),
            'dbpass' => $this->getDatabasePassword()
        );

        return $dbInfo;
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
     * @see Multisites_Entity_Site::postPersistCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostPersistCallback()
    {
        $dom = ZLanguage::getModuleDomain('Multisites');

        $serviceManager = ServiceUtil::getManager();
        $systemHelper = new Multisites_Util_System($serviceManager);

        // update db config adding the new database
        if (!$systemHelper->updateDatabaseConfigFile()) {
            return LogUtil::registerError(__('Error! Updating the database configuration file failed.', $dom));
        }

        // save the site module into the Multisites database
        $extensionHandler = new Multisites_Util_SiteExtensionHandler($serviceManager);
        if (!$extensionHandler->saveSiteModulesIntoOwnDb($this)) {
            return LogUtil::registerError(__('Error! Storing the site modules in the Multisites database failed.', $dom));
        }

        return parent::performPostPersistCallback();
    }

    /**
     * Post-Process the data after an update operation.
     * The event happens after the database update operations for the entity data.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL UPDATE statement
     *
     * @see Multisites_Entity_Site::postUpdateCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostUpdateCallback()
    {
        $serviceManager = ServiceUtil::getManager();
        $systemHelper = new Multisites_Util_System($serviceManager);

        // update db config adding the new database
        if (!$systemHelper->updateDatabaseConfigFile()) {
            $dom = ZLanguage::getModuleDomain('Multisites');
            return LogUtil::registerError(__('Error! Updating the database configuration file failed.', $dom));
        }

        return parent::performPostUpdateCallback();
    }

    /**
     * Pre-Process the data prior a delete operation.
     * The event happens before the entity managers remove operation is executed for this entity.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL DELETE statement
     *
     * @see Multisites_Entity_Site::preRemoveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPreRemoveCallback()
    {
        $dom = ZLanguage::getModuleDomain('Multisites');

        $deleteDatabase = FormUtil::getPassedValue('deleteDatabase', 0, 'POST', FILTER_VALIDATE_BOOLEAN);
        $deleteFiles = FormUtil::getPassedValue('deleteFiles', 0, 'POST', FILTER_VALIDATE_BOOLEAN);

        $serviceManager = ServiceUtil::getManager();
        $systemHelper = new Multisites_Util_System($serviceManager);

        if ($deleteDatabase == 1) {
            // delete the database
            if (!$systemHelper->deleteDatabase($this->getDatabaseData())) {
                return LogUtil::registerError(__('Error during deleting the database.', $dom));
            }
        }
        if ($deleteFiles == 1) {
            // delete the site files and directories
            $siteFolder = $this->serviceManager['multisites.files_real_path'] . '/' . $site['siteAlias'];
            if (!$systemHelper->deleteDir($siteFolder)) {
                return LogUtil::registerError(__('Error during deleting the site files directory.', $dom));
            }
        }

        return parent::performPreRemoveCallback();
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
     * @see Multisites_Entity_Site::postRemoveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostRemoveCallback()
    {
        $serviceManager = ServiceUtil::getManager();
        $systemHelper = new Multisites_Util_System($serviceManager);

        // update db config removing the database
        if (!$systemHelper->updateDatabaseConfigFile()) {
            $dom = ZLanguage::getModuleDomain('Multisites');
            LogUtil::registerError(__('Error! Updating the database configuration file failed.', $dom));
        }

        return parent::performPostRemoveCallback();
    }

    protected function isFunctionAllowed($func)
    {
        if (ini_get('safe_mode')) {
            return false;
        }
        $disabled = ini_get('disable_functions');
        if ($disabled) {
            $disabled = explode(',', $disabled);
            $disabled = array_map('trim', $disabled);

            return !in_array($func, $disabled);
        }

        return true;
    }

    /**
     * Collect available actions for this entity.
     */
    protected function prepareItemActions()
    {
        if (!empty($this->_actions)) {
            return;
        }

        parent::prepareItemActions();

        $dom = ZLanguage::getModuleDomain('Multisites');

        $deleteAction = null;

        foreach ($this->_actions as $k => $v) {
            if ($v['linkText'] == __('Create site extension', $dom)) {
                unset($this->_actions[$k]);
            } elseif ($v['linkText'] == __('Delete', $dom)) {
                $deleteAction = $this->_actions[$k];
                unset($this->_actions[$k]);
            }
        }

        $this->_actions[] = array(
            'url' => array('type' => 'admin', 'func' => 'manageExtensions', 'arguments' => array('id' => $this['id'])),
            'icon' => 'cubes',
            'linkTitle' => __('Manage the modules for this site', $dom),
            'linkText' => __('Allowed extensions', $dom)
        );

        $this->_actions[] = array(
            'url' => array('type' => 'admin', 'func' => 'manageThemes', 'arguments' => array('id' => $this['id'])),
            'icon' => 'display',
            'linkTitle' => __('Manage the themes for this site', $dom),
            'linkText' => __('Allowed layouts', $dom)
        );

        // check if system() is allowed
        if (in_array($this['databaseType'], array('mysql', 'mysqli')) && $this->isFunctionAllowed('system')) {
            $this->_actions[] = array(
                'url' => array('type' => 'admin', 'func' => 'exportDatabaseAsTemplate', 'arguments' => array('id' => $this['id'])),
                'icon' => 'export',
                'linkTitle' => __('Export the database as SQL file', $dom),
                'linkText' => __('Database SQL Export', $dom)
            );
        }

        $this->_actions[] = array(
            'url' => array('type' => 'admin', 'func' => 'viewTools', 'arguments' => array('id' => $this['id'])),
            'icon' => 'options',
            'linkTitle' => __('Site tools', $dom),
            'linkText' => __('Site tools', $dom)
        );

        // readd delete action
        if ($deleteAction !== null) {
            $this->_actions[] = $deleteAction;
        }
    }
}
