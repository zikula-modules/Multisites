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

namespace Zikula\MultisitesModule\Base;

use EventUtil;
use FileUtil;
use HookUtil;
use ModUtil;
use System;
use UserUtil;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula_Workflow_Util;
use Zikula\ExtensionsModule\Api\HookApi;

/**
 * Installer base class.
 */
class MultisitesModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * Install the ZikulaMultisitesModule application.
     *
     * @return boolean True on success, or false.
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        // Check if upload directories exist and if needed create them
        try {
            $controllerHelper = $this->container->get('zikulamultisitesmodule.controller_helper')
            $controllerHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $e) {
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $e->getMessage());
            $logger->error('{app}: User {user} could not create upload folders during installation. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
        
            return false;
        }
        $logger = $this->container->get('logger');
        // create all tables from according entity definitions
        try {
            $this->container->get('zikula.doctrine.schema_tool')->create($this->listEntityClasses());
        } catch (\Exception $e) {
            if (System::isDevelopmentMode()) {
                $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__('Doctrine Exception') . ': ' . $e->getMessage());
                $logger->error('{app}: User {user} could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
    
                return false;
            }
            $returnMessage = $this->__f('An error was encountered while creating the tables for the %s extension.', ['ZikulaMultisitesModule']);
            if (!System::isDevelopmentMode()) {
                $returnMessage .= ' ' . $this->__('Please enable the development mode by editing the /app/config/parameters.yml file (change the env variable to dev) in order to reveal the error details (or look into the log files at /app/logs/).');
            }
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $returnMessage);
            $logger->error('{app}: User {user} could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('tempAccessFileContent', '');
        $this->setVar('globalAdminName', '');
        $this->setVar('globalAdminPassword', '');
        $this->setVar('globalAdminEmail', '');
    
        // create the default data
        $this->createDefaultData();
    
        // register hook subscriber bundles
        $subscriberHookContainer = $this->hookApi->getHookContainerInstance($this->bundle->getMetaData(), HookApi::SUBSCRIBER_TYPE);
        HookUtil::registerSubscriberBundles($subscriberHookContainer->getHookSubscriberBundles());
        
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the ZikulaMultisitesModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from.
     *
     * @return boolean True on success, false otherwise.
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->container->get('zikula.doctrine.schema_tool')->update($this->listEntityClasses());
                } catch (\Exception $e) {
                    if (System::isDevelopmentMode()) {
                        $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__('Doctrine Exception') . ': ' . $e->getMessage());
                        $logger->error('{app}: User {user} could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
    
                        return false;
                    }
                    $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__f('An error was encountered while updating tables for the %s extension.', ['ZikulaMultisitesModule']));
                    $logger->error('{app}: User {user} could not update the database tables during the ugprade. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
    
                    return false;
                }
        }
    
        // Note there are several helpers available for making migration of your extension easier.
        // The following convenience methods are each responsible for a single aspect of upgrading to Zikula 1.4.0.
    
        // here is a possible usage example
        // of course 1.2.3 should match the number you used for the last stable 1.3.x module version.
        /* if ($oldVersion = '1.2.3') {
            // rename module for all modvars
            $this->updateModVarsTo140();
            
            // update extension information about this app
            $this->updateExtensionInfoFor140();
            
            // rename existing permission rules
            $this->renamePermissionsFor140();
            
            // rename all tables
            $this->renameTablesFor140();
            
            // remove event handler definitions from database
            $this->dropEventHandlersFromDatabase();
            
            // update module name in the hook tables
            $this->updateHookNamesFor140();
            
            // update module name in the workflows table
            $this->updateWorkflowsFor140();
        } * /
    */
    
        // update successful
        return true;
    }
    
    /**
     * Renames the module name for variables in the module_vars table.
     */
    protected function updateModVarsTo140()
    {
        $dbName = $this->getDbName();
        $conn = $this->getConnection();
    
        $conn->executeQuery("UPDATE $dbName.module_vars
                             SET modname = 'ZikulaMultisitesModule'
                             WHERE modname = 'Multisites';
        ");
    }
    
    /**
     * Renames this application in the core's extensions table.
     */
    protected function updateExtensionInfoFor140()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("UPDATE $dbName.modules
                             SET name = 'ZikulaMultisitesModule',
                                 directory = 'Zikula/MultisitesModule'
                             WHERE name = 'Multisites';
        ");
    }
    
    /**
     * Renames all permission rules stored for this app.
     */
    protected function renamePermissionsFor140()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $componentLength = strlen('Multisites') + 1;
    
        $conn->executeQuery("UPDATE $dbName.group_perms
                             SET component = CONCAT('ZikulaMultisitesModule', SUBSTRING(component, $componentLength))
                             WHERE component LIKE 'Multisites%';
        ");
    }
    
    /**
     * Renames all (existing) tables of this app.
     */
    protected function renameTablesFor140()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $oldPrefix = 'multisites_';
        $oldPrefixLength = strlen($oldPrefix);
        $newPrefix = 'zikula_multisites_';
    
        $sm = $conn->getSchemaManager();
        $tables = $sm->listTables();
        foreach ($tables as $table) {
            $tableName = $table->getName();
            if (substr($tableName, 0, $oldPrefixLength) != $oldPrefix) {
                continue;
            }
    
            $newTableName = str_replace($oldPrefix, $newPrefix, $tableName);
    
            $conn->executeQuery("RENAME TABLE $dbName.$tableName
                                 TO $dbName.$newTableName;
            ");
        }
    }
    
    /**
     * Removes event handlers from database as they are now described by service definitions and managed by dependency injection.
     */
    protected function dropEventHandlersFromDatabase()
    {
        EventUtil::unregisterPersistentModuleHandlers('Multisites');
    }
    
    /**
     * Updates the module name in the hook tables.
     */
    protected function updateHookNamesFor140()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("UPDATE $dbName.hook_area
                             SET owner = 'ZikulaMultisitesModule'
                             WHERE owner = 'Multisites';
        ");
    
        $componentLength = strlen('subscriber.multisites') + 1;
        $conn->executeQuery("UPDATE $dbName.hook_area
                             SET areaname = CONCAT('subscriber.zikulamultisitesmodule', SUBSTRING(areaname, $componentLength))
                             WHERE areaname LIKE 'subscriber.multisites%';
        ");
    
        $conn->executeQuery("UPDATE $dbName.hook_binding
                             SET sowner = 'ZikulaMultisitesModule'
                             WHERE sowner = 'Multisites';
        ");
    
        $conn->executeQuery("UPDATE $dbName.hook_runtime
                             SET sowner = 'ZikulaMultisitesModule'
                             WHERE sowner = 'Multisites';
        ");
    
        $componentLength = strlen('multisites') + 1;
        $conn->executeQuery("UPDATE $dbName.hook_runtime
                             SET eventname = CONCAT('zikulamultisitesmodule', SUBSTRING(eventname, $componentLength))
                             WHERE eventname LIKE 'multisites%';
        ");
    
        $conn->executeQuery("UPDATE $dbName.hook_subscriber
                             SET owner = 'ZikulaMultisitesModule'
                             WHERE owner = 'Multisites';
        ");
    
        $componentLength = strlen('multisites') + 1;
        $conn->executeQuery("UPDATE $dbName.hook_subscriber
                             SET eventname = CONCAT('zikulamultisitesmodule', SUBSTRING(eventname, $componentLength))
                             WHERE eventname LIKE 'multisites%';
        ");
    }
    
    /**
     * Updates the module name in the workflows table.
     */
    protected function updateWorkflowsFor140()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("UPDATE $dbName.workflows
                             SET module = 'ZikulaMultisitesModule'
                             WHERE module = 'Multisites';
        ");
    }
    
    /**
     * Returns connection to the database.
     *
     * @return Connection the current connection.
     */
    protected function getConnection()
    {
        $entityManager = $this->container->get('doctrine.entitymanager');
        $connection = $entityManager->getConnection();
    
        return $connection;
    }
    
    /**
     * Returns the name of the default system database.
     *
     * @return string the database name.
     */
    protected function getDbName()
    {
        return $this->container->getParameter('database_name');
    }
    
    /**
     * Uninstall ZikulaMultisitesModule.
     *
     * @return boolean True on success, false otherwise.
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        $logger = $this->container->get('logger');
        // delete stored object workflows
        $result = Zikula_Workflow_Util::deleteWorkflowsForModule('ZikulaMultisitesModule');
        if ($result === false) {
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__f('An error was encountered while removing stored object workflows for the %s extension.', ['ZikulaMultisitesModule']));
            $logger->error('{app}: User {user} could not remove stored object workflows during uninstallation.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname')]);
    
            return false;
        }
    
        try {
            $this->container->get('zikula.doctrine.schema_tool')->drop($this->listEntityClasses());
        } catch (\Exception $e) {
            if (System::isDevelopmentMode()) {
                $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__('Doctrine Exception') . ': ' . $e->getMessage());
                $logger->error('{app}: User {user} could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
    
                return false;
            }
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__f('An error was encountered while dropping tables for the %s extension.', ['ZikulaMultisitesModule']));
            $logger->error('{app}: User {user} could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'user' => UserUtil::getVar('uname'), 'errorMessage' => $e->getMessage()]);
    
            return false;
        }
    
        // unregister hook subscriber bundles
        $subscriberHookContainer = $this->hookApi->getHookContainerInstance($this->bundle->getMetaData(), HookApi::SUBSCRIBER_TYPE);
        HookUtil::unregisterSubscriberBundles($subscriberHookContainer->getHookSubscriberBundles());
        
    
        // remove all module vars
        $this->delVars();
    
        // remove all thumbnails
        $manager = $this->container->get('systemplugin.imagine.manager');
        $manager->setModule('ZikulaMultisitesModule');
        $manager->cleanupModuleThumbs();
    
        // remind user about upload folders not being deleted
        $uploadPath = $this->container->getParameter('datadir') . '/ZikulaMultisitesModule/';
        $this->addFlash(\Zikula_Session::MESSAGE_STATUS, $this->__f('The upload directories at [%s] can be removed manually.', $uploadPath));
    
        // uninstallation successful
        return true;
    }
    
    /**
     * Build array with all entity classes for ZikulaMultisitesModule.
     *
     * @return array list of class names.
     */
    protected function listEntityClasses()
    {
        $classNames = [];
        $classNames[] = 'Zikula\MultisitesModule\Entity\SiteEntity';
        $classNames[] = 'Zikula\MultisitesModule\Entity\TemplateEntity';
        $classNames[] = 'Zikula\MultisitesModule\Entity\SiteExtensionEntity';
        $classNames[] = 'Zikula\MultisitesModule\Entity\ProjectEntity';
    
        return $classNames;
    }
    
    /**
     * Create the default data for ZikulaMultisitesModule.
     *
     * @return void
     */
    protected function createDefaultData()
    {
        $entityClass = 'Zikula\MultisitesModule\Entity\SiteEntity';
        $entityManager = $this->container->get('doctrine.entitymanager');
        $entityManager->getRepository($entityClass)->truncateTable();
        $entityClass = 'Zikula\MultisitesModule\Entity\TemplateEntity';
        $entityManager = $this->container->get('doctrine.entitymanager');
        $entityManager->getRepository($entityClass)->truncateTable();
        $entityClass = 'Zikula\MultisitesModule\Entity\SiteExtensionEntity';
        $entityManager = $this->container->get('doctrine.entitymanager');
        $entityManager->getRepository($entityClass)->truncateTable();
        $entityClass = 'Zikula\MultisitesModule\Entity\ProjectEntity';
        $entityManager = $this->container->get('doctrine.entitymanager');
        $entityManager->getRepository($entityClass)->truncateTable();
    }
}
