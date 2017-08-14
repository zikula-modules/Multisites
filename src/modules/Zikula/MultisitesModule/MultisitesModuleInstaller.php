<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.1 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule;

use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Zikula\MultisitesModule\Base\AbstractMultisitesModuleInstaller;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\SiteExtensionEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;

/**
 * Installer implementation class.
 */
class MultisitesModuleInstaller extends AbstractMultisitesModuleInstaller
{
    /**
     * {@inheritdoc}
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
            case '2.0.0':
/*            case '2.1.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->container->get('zikula.doctrine.schema_tool')->update($this->listEntityClasses());
                } catch (\Exception $e) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'ZikulaMultisitesModule', 'errorMessage' => $e->getMessage()]);

                    return false;
                }*/
        }

        // update successful
        return true;
    }

    /**
     * Upgrades v1.x to 2.0.0. This includes migration to Doctrine 2 as well as
     * several changes in the filesystem.
     *
     * @return boolean True on success or false otherwise.
     */
    protected function upgradeToV2()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();

        // first the access table can be removed, too, as it was actually never used
        $conn->executeQuery('DROP TABLE IF EXISTS `multisitesaccess`');

        // rename the other tables to avoid any naming conflicts
        $oldTables = ['sites', 'models', 'sitemodules'];
        foreach ($oldTables as $tableName) {
            $conn->executeQuery('
                RENAME TABLE ' . $dbName . '.' . $tableName . '
                TO ' . $dbName . '.' . $newTableName . 'Old
            ');
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
            $conn->executeQuery('DROP TABLE IF EXISTS `' . $tableName . 'Old`');
        }

        // update the primary configuration file
        if (!$this->migratePrimaryConfigFile()) {
            return false;
        }

        // finally update the database configuration file
        $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
        if (!$systemHelper->updateDatabaseConfigFile()) {
            $this->addFlash('error', $this->__('Error! Updating the database configuration file failed.'));

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
        $project = new ProjectEntity();
        $project->setName($this->__('Imported'));
        $this->entityManager->persist($project);

        $templatesByName = [];
        $sitesById = [];

        // transfer data from old template table
        $conn = $this->getConnection();

        $uploadHelper = $this->container->get('zikula_multisites_module.upload_helper');
        $basePath = $uploadHelper->getFileBaseFolder('template', 'sqlFile');

        $stmt = $conn->executeQuery('SELECT * FROM `multisitesmodelsOld`');
        while ($row = $stmt->fetch()) {
            $template = new TemplateEntity();
            $template->setId($row['modelid']);
            $template->setName($row['modelname']);
            $template->setDescription($row['description']);

            if (!empty($row['filename'])) {
                $template->setSqlFile($row['filename']);

                $fullPath = $basePath . $template->getSqlFile();
                $template->setSqlFileMeta($uploadHelper->readMetaDataForFile($template->getSqlFile(), $fullPath));
            }

            if (!empty($row['folders'])) {
                $folders = [];
                $oldFolders = explode(',', $row['folders']);
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

        // transfer data from old site table
        $stmt = $conn->executeQuery('SELECT * FROM `multisitessitesOld`');
        while ($row = $stmt->fetch()) {
            $site = new SiteEntity();
            $site->setId($row['instanceid']);
            $site->setName($row['instancename']);
            $site->setDescription($row['description']);
            $site->setSiteAlias($row['alias']);
            $site->setSiteName($row['sitename']);
            $site->setSiteDescription($row['sitedescription']);
            $site->setSiteAdminName($row['siteadminname']);
            $site->setSiteAdminPassword($row['siteadminpwd']);
            $site->setSiteAdminRealName($row['siteadminrealname']);
            $site->setSiteAdminEmail($row['siteadminemail']);
            $site->setSiteCompany($row['sitecompany']);
            $site->setSiteDns($row['sitedns']);
            $site->setDatabaseName($row['sitedbname']);
            $site->setDatabaseUserName($row['sitedbuname']);
            $site->setDatabasePassword($row['sitedbpass']);
            $site->setDatabaseHost($row['sitedbhost']);
            $site->setDatabaseType($row['sitedbtype']);

            if ($row['sitedbprefix'] != '') {
                /** TODO
                 * We could also do this automatically.
                 * Needs a refactoring of SystemHelper class, see readTables() and renameExcludedTables()
                 */
                $this->addFlash('error', $this->__f('The site "%site%" does have a table prefix set which is not supported anymore. You need to rename tables in the "%database%" database accordingly.', ['%site%' => $site->getName(), '%database%' => $site->getDatabaseName()]));
            }

            $site->setActive($row['active']);

            $this->entityManager->persist($site);

            $project->addSites($site);
            if (isset($templatesByName[$row['siteinitmodel']])) {
                $templatesByName[$row['siteinitmodel']]->addSites($site);
            }

            $sitesById[$site->getId()] = $site;
        }

        // transfer data from old site modules table
        $stmt = $conn->executeQuery('SELECT * FROM `multisitessitemodulesOld`');
        while ($row = $stmt->fetch()) {
            $extension = new SiteExtensionEntity();
            $extension->setName($row['modulename']);
            $extension->setExtensionVersion($row['moduleversion']);
            $extension->setExtensionType('module');

            if (isset($sitesById[$row['instanceid']])) {
                $sitesById[$row['instanceid']]->addExtensions($extension);
            }

            $this->entityManager->persist($extension);
        }

        $this->entityManager->flush();

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
            $uploadHelper = $this->container->get('zikula_multisites_module.upload_helper');
            $uploadHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        // create all tables from according entity definitions
        try {
            $this->container->get('zikula.doctrine.schema_tool')->create($this->listEntityClasses());
        } catch (\Exception $e) {
            $this->addFlash('error', $this->__('Doctrine Exception: ') . $e->getMessage());

            return false;
        }

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
        if (!is_dir($modelPath)) {
            return true;
        }

        // move existing template files into new folder
        $finder = new Finder();
        $finder->files()->in($modelPath);
        $filesArray = [];
        foreach ($finder as $file) {
            if (in_array($file->getFilename(), ['.', '..', '.git', '.svn', 'CVS', 'index.html', 'index.htm', '.htaccess'])) {
                continue;
            }
            $filesArray[] = $file;
        }

        $fs = new Filesystem();
        $uploadHelper = $this->container->get('zikula_multisites_module.controller_helper');
        $destinationPath = $uploadHelper->getFileBaseFolder('template', 'sqlFile');
        foreach ($filesArray as $file) {
            $fs->rename($file->getRealPath(), $destinationPath . $file->getFilename());
        }

        if ($allMoved) {
            // delete the old directory
            $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
            if (!$systemHelper->deleteDir($modelPath)) {
                // raise a message, but continue the process
                $this->addFlash('error', $this->__f('Could not delete the %s folder. Please remove it manually.', ['%s' => $modelPath]));
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
        $configFileTemplate = 'modules/Zikula/MultisitesModule/Resources/config-folder/multisites_config.php';
        $configUpdated = false;

        $fs = new Filesystem();
        if ($fs->exists($configFile) && is_writeable($configFile)) {
            $configLinesTemplate = explode("\n". file_get_contents($configFileTemplate));
            $configLines = explode("\n". file_get_contents($configFile));
            if (false !== $configLinesTemplate && is_array($configLinesTemplate) && count($configLinesTemplate) > 0) {
                if (false !== $configLines && is_array($configLines) && count($configLines) > 0) {
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

                    $fs->dumpFile($configFile. implode("\n", $configLinesNew));
                    $configUpdated = true;
                }
            }
        }

        if (!$configUpdated) {
            $this->addFlash('error', $this->__f('Could not update the %file% file automatically. Please compare it with %template% and update it manually.', ['%file%' => $configFile, '%template%' => $configFileTemplate]));
        }

        return true;
    }
}
