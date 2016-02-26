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

namespace Zikula\MultisitesModule;

use Zikula\MultisitesModule\Base\MultisitesModuleInstaller as BaseMultisitesModuleInstaller;

use DBUtil;
use FileUtil;
use RuntimeException;
use System;
use Zikula\ExtensionsModule\Api\HookApi;

/**
 * Installer implementation class.
 */
class MultisitesModuleInstaller extends BaseMultisitesModuleInstaller
{
    /**
     * Upgrade the Multisites application from an older version.
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
        $logger = $this->container->get('logger');
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
            case '1.0.1':
                if (!$this->upgradeToV2()) {
                    return false;
                }
/*            case '2.0.0':
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
                }*/
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
        } */

        // update successful
        return true;
    }

    /**
     * Upgrades v1.x to 2.0.0. This includes migration from DBUtil to Doctrine 2 as well as
     * several changes in the filesystem.
     *
     * @return boolean True on success or false otherwise.
     */
    protected function upgradeToV2()
    {
        // first the access table can be removed, too, as it was actually never used
        $sql = 'DROP TABLE IF EXISTS `multisitesaccess`';
        DBUtil::executeSQL($sql);

        // rename the other tables to avoid any naming conflicts
        $oldTables = ['sites', 'models', 'sitemodules'];
        foreach ($oldTables as $tableName) {
            if (!DBUtil::renameTable($tableName, $tableName . 'Old')) {
                return false;
            }
        }

        // install the new stuff
        if (!$this->installComponentsNewInV2()) {
            return false;
        }

        // move template files into new location
        if (!$this->migrateOldTemplates()) {
            return false;
        }

        // transfer existing data
        if (!$this->migrateDatabaseDataToV2()) {
            return false;
        }

        // remove the old tables
        foreach ($oldTables as $tableName) {
            $sql = 'DROP TABLE IF EXISTS `' . $tableName . 'Old`';
            DBUtil::executeSQL($sql);
        }

        // update the primary configuration file
        if (!$this->migratePrimaryConfigFile()) {
            return false;
        }

        // finally update the database configuration file
        $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
        if (!$systemHelper->updateDatabaseConfigFile()) {
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__('Error! Updating the database configuration file failed.'));
            return false;
        }

        return true;
    }

    /**
     * Transfers existing data from v1.x to 2.0.0.
     *
     * @return boolean True on success or false otherwise.
     */
    protected function migrateDatabaseDataToV2()
    {
        // create a project
        $project = new \Zikula\MultisitesModule\Entity\ProjectEntity();
        $project->setName($this->__('Imported'));
        $this->entityManager->persist($project);

        $templatesByName = [];
        $sitesById = [];

        // transfer data from old template table
        $result = DBUtil::executeSQL('SELECT * FROM `multisitesmodelsOld`');
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);
        if ($data) {
            $uploadManager = new \Zikula\MultisitesModule\UploadHandler($this->container->get('translator.default'));
            $controllerHelper = $this->container->get('zikulamultisitesmodule.controller_helper');
            $basePath = $controllerHelper->getFileBaseFolder('template', 'sqlFile');

            foreach ($data as $k => $v) {
                $template = new \Zikula\MultisitesModule\Entity\TemplateEntity();
                $template->setId($v['modelid']);
                $template->setName($v['modelname']);
                $template->setDescription($v['description']);

                if (!empty($v['filename'])) {
                    $template->setSqlFile($v['filename']);

                    $fullPath = $basePath . $template->getSqlFile();
                    $template->setSqlFileMeta($uploadManager->readMetaDataForFile($template->getSqlFile(), $fullPath));
                }

                if (!empty($v['folders'])) {
                    $folders = [];
                    $oldFolders = explode(',', $v['folders']);
                    foreach ($oldFolders as $folder) {
                        if (!empty($folder)) {
                            $folders[] = $folder;
                        }
                    }
                    $template->setFolders($folders);
                }

                $template->addProjects($project);

                $this->entityManager->persist($template);

                $templatesByName[$template->getName()] = $template;
            }
        }

        // transfer data from old site table
        $result = DBUtil::executeSQL('SELECT * FROM `multisitessitesOld`');
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);
        if ($data) {
            foreach ($data as $k => $v) {
                $site = new \Zikula\MultisitesModule\Entity\SiteEntity();
                $site->setId($v['instanceid']);
                $site->setName($v['instancename']);
                $site->setDescription($v['description']);
                $site->setSiteAlias($v['alias']);
                $site->setSiteName($v['sitename']);
                $site->setSiteDescription($v['sitedescription']);
                $site->setSiteAdminName($v['siteadminname']);
                $site->setSiteAdminPassword($v['siteadminpwd']);
                $site->setSiteAdminRealName($v['siteadminrealname']);
                $site->setSiteAdminEmail($v['siteadminemail']);
                $site->setSiteCompany($v['sitecompany']);
                $site->setSiteDns($v['sitedns']);
                $site->setDatabaseName($v['sitedbname']);
                $site->setDatabaseUserName($v['sitedbuname']);
                $site->setDatabasePassword($v['sitedbpass']);
                $site->setDatabaseHost($v['sitedbhost']);
                $site->setDatabaseType($v['sitedbtype']);

                if ($v['sitedbprefix'] != '') {
                    /** TODO
                     * We could also do this automatically.
                     * Needs a refactoring of SystemHelper class, see readTables() and renameExcludedTables()
                     */
                    $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__f('The site "%1$s" does have a table prefix set which is not supported anymore. You need to rename tables in the "%2$s" database accordingly.', [$site->getName(), $site->getDatabaseName()]));
                }

                $site->setActive($v['active']);

                $this->entityManager->persist($site);

                $project->addSites($site);
                if (isset($templatesByName[$v['siteinitmodel']])) {
                    $templatesByName[$v['siteinitmodel']]->addSites($site);
                }

                $sitesById[$site->getId()] = $site;
            }
        }

        // transfer data from old site modules table
        $result = DBUtil::executeSQL('SELECT * FROM `multisitessitemodulesOld`');
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);
        if ($data) {
            foreach ($data as $k => $v) {
                $extension = new \Zikula\MultisitesModule\Entity\SiteExtensionEntity();
                $extension->setName($v['modulename']);
                $extension->setExtensionVersion($v['moduleversion']);
                $extension->setExtensionType('module');

                if (isset($sitesById[$v['instanceid']])) {
                    $sitesById[$v['instanceid']]->addExtensions($extension);
                }

                $this->entityManager->persist($extension);
            }
        }

        $this->entityManager->flush();

        // because we did not use the workflow manager, we need to add the workflow data in a separate step
        $sqlBase = 'INSERT INTO `workflows` (`metaid`, `module`, `schemaname`, `state`, `type`, `obj_table`, `obj_idcolumn`, `obj_id`, `busy`, `debug`) ';
        $sqlBase .= 'SELECT 0 AS `metaid`, \'Multisites\' AS `module`, \'none\' AS `schemaname`, \'approved\' AS `state`, 1 AS `type`, ';

        $tables = ['project', 'template', 'site'];
        foreach ($tables as $tableName) {
            $sql = $sqlBase . '\'' . $tableName . '\' AS `obj_table`, \'id\' AS `obj_idcolumn`, `id` AS `obj_id`, 0 AS `busy`, NULL AS `debug` FROM `multisites_' . $tableName . '` ';
            $sql .= 'WHERE `id` NOT IN (SELECT `obj_id` FROM `workflows` WHERE `module` = \'Multisites\' AND `obj_table` = \'' . $tableName . '\')';
            DBUtil::executeSQL($sql);
        }

        // add workflow data for the site extensions
        $sql = $sqlBase . '\'siteExtension\' AS `obj_table`, \'id\' AS `obj_idcolumn`, `id` AS `obj_id`, 0 AS `busy`, NULL AS `debug` FROM `multisites_site_extension` ';
        $sql .= 'WHERE `id` NOT IN (SELECT `obj_id` FROM `workflows` WHERE `module` = \'Multisites\' AND `obj_table` = \'siteExtension\')';
        DBUtil::executeSQL($sql);

        return true;
    }

    /**
     * Perform steps for new installation which are required for existing installations.
     *
     * @return boolean True on success or false otherwise.
     */
    protected function installComponentsNewInV2()
    {
        // Check if upload directories exist and if needed create them
        try {
            $controllerHelper = $this->container->get('zikulamultisitesmodule.controller_helper');
            $controllerHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $e) {
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $e->getMessage());
            return false;
        }

        // create all tables from according entity definitions
        try {
            $this->container->get('zikula.doctrine.schema_tool')->create($this->listEntityClasses());
        } catch (\Exception $e) {
            if (System::isDevelopmentMode()) {
                $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__('Doctrine Exception: ') . $e->getMessage());
                return false;
            }
            $returnMessage = $this->__f('An error was encountered while creating the tables for the %s extension.', [$this->name]);
            if (!System::isDevelopmentMode()) {
                $returnMessage .= ' ' . $this->__('Please enable the development mode by editing the /config/config.php file in order to reveal the error details.');
            }
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $returnMessage);

            return false;
        }

        // register hook subscriber bundles
        $subscriberHookContainer = $this->hookApi->getHookContainerInstance($this->bundle->getMetaData(), HookApi::SUBSCRIBER_TYPE);
        $this->hookApi->registerSubscriberBundles($subscriberHookContainer->getHookSubscriberBundles());

        return true;
    }

    /**
     * Migrates the old template files folder.
     *
     * @return boolean True on success or false otherwise.
     */
    protected function migrateOldTemplates()
    {
        // the modelsFolder modvar can be removed, because
        // 2.0.0 introduces new upload locations based on MOST
        $modelPath = $this->getVar('modelsFolder');
        $this->delVar('modelsFolder');
        if (is_dir($modelPath)) {
            // move existing template files into new folder
            $dh = opendir($modelPath);
            $filesArray = [];
            while ($file = readdir($dh)) {
                if (in_array($file, ['.', '..', '.git', '.svn', 'CVS', 'index.html', 'index.htm', '.htaccess'])) {
                    continue;
                }
                if (!is_file($modelPath . '/' . $file)) {
                    continue;
                }
                $filesArray[] = $file;
            }
            closedir($dh);

            $controllerHelper = $this->container->get('zikulamultisitesmodule.controller_helper');
            $destinationPath = $controllerHelper->getFileBaseFolder('template', 'sqlFile');
            $allMoved = true;
            foreach ($filesArray as $file) {
                if (!@rename($modelPath . '/' . $file, $destinationPath . $file)) {
                    $allMoved = false;
                }
            }

            if ($allMoved) {
                // delete the old directory
                $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
                if (!$systemHelper->deleteDir($modelPath)) {
                    // raise a message, but continue the process
                    $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__f('Could not delete the %s folder. Please remove it manually.', [$modelPath]));
                }
            }
        }

        return true;
    }

    /**
     * Migrates the Multisites configuration file.
     *
     * @return boolean True on success or false otherwise.
     */
    protected function migratePrimaryConfigFile()
    {
        $configFile = 'config/multisites_config.php';
        $configFileTemplate = 'modules/Multisites/Resources/config/multisites_config.php';
        $configUpdated = false;

        if (file_exists($configFile) && is_writeable($configFile)) {
            $configLinesTemplate = FileUtil::readFileLines($configFileTemplate);
            $configLines = FileUtil::readFileLines($configFile);
            if ($configLinesTemplate !== false && is_array($configLinesTemplate) && count($configLinesTemplate) > 0) {
                if ($configLines !== false && is_array($configLines) && count($configLines) > 0) {
                    $configLinesNew = [];
                    $configChangeLine = "//****** DON'T CHANGE AFTER THIS LINE *******";

                    foreach ($configLines as $line) {
                        if ($line == $configChangeLine) {
                            break;
                        }
                        $configLinesNew[] = $line;
                    }

                    $hasReachedChange = false;
                    foreach ($configLinesTemplate as $line) {
                        if ($hasReachedChange) {
                            $configLinesNew[] = $line;
                        }
                        if ($line == $configChangeLine) {
                            $hasReachedChange = true;
                        }
                    }

                    $result = FileUtil::writeFile(implode("\n", $configLinesNew));
                    if ($result) {
                        $configUpdated = true;
                    }
                }
            }
        }

        if (!$configUpdated) {
            $this->addFlash(\Zikula_Session::MESSAGE_ERROR, $this->__f('Could not update the %1$s file automatically. Please compare it with %2$s and update it manually.', [$configFile, $configFileTemplate]));
        }

        return true;
    }
}
