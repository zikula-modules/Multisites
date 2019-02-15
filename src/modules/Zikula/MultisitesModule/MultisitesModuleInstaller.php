<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule;

use RuntimeException;
use Symfony\Component\Finder\Finder;
use Zikula\MultisitesModule\Base\AbstractMultisitesModuleInstaller;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Entity\SiteEntity;
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
                // drop site extensions table
                $conn = $this->entityManager->getConnection();
                $conn->executeQuery('DROP TABLE IF EXISTS `multisites_siteextension`');
                // remove obsolete modvar
                $this->delVar('tempAccessFileContent');
            case '2.1.0':
                // current version
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
        $conn = $this->entityManager->getConnection();

        // first the access table can be removed, too, as it was actually never used
        $conn->executeQuery('DROP TABLE IF EXISTS `multisitesaccess`');

        // rename the other tables to avoid any naming conflicts
        $oldTables = ['sites', 'models', 'sitemodules'];
        foreach ($oldTables as $tableName) {
            $conn->executeQuery('
                RENAME TABLE `' . $tableName . '`
                TO `' . $tableName . 'Old`
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
        if (!$this->migratePrimaryConfigFileToV2()) {
            return false;
        }

        // finally update the subsites configuration file
        $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
        if (!$systemHelper->updateSubsitesConfigFile()) {
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
        $conn = $this->entityManager->getConnection();

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
        $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
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
                $connect = $systemHelper->connectToExternalDatabase(new DatabaseInfo($site));
                if (!$connect) {
                    $this->addFlash('error', $this->__f('The site "%site%" does have a table prefix set which is not supported anymore. You need to rename tables in the "%database%" database accordingly.', ['%site%' => $site->getName(), '%database%' => $site->getDatabaseName()]));
                } else {
                    // remove legacy prefix in all tables
                    $tableNames = [];
                    $sql = '
                        SELECT `table_name` AS `tableName`
                        FROM `information_schema`.`tables`
                        WHERE `table_schema` = :dbName
                    ';

                    while ($row = $connect->fetchAssoc($sql, [':dbName' => $site->getDatabaseName()])) {
                        $tableNames[] = $row['tableName'];
                    }

                    // rename tables removing the old prefix
                    $prefix = $row['sitedbprefix'] . '_';
                    $prefixLength = strlen($prefix);
                    foreach ($tableNames as $tableName) {
                        if (substr($tableName, 0, $prefixLength) != $prefix) {
                            continue;
                        }

                        $sql = 'ALTER TABLE `' . $tableName . '` RENAME TO `' . str_replace($prefix, '', $tableName) . '`';
                        $stmt = $connect->prepare($sql);
                        $stmt->execute();
                    }
                }
            }

            $site->setActive($row['active']);

            $this->entityManager->persist($site);

            $project->addSites($site);
            if (isset($templatesByName[$row['siteinitmodel']])) {
                $templatesByName[$row['siteinitmodel']]->addSites($site);
            }

            $sitesById[$site->getId()] = $site;
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
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());

            return false;
        }

        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->listEntityClasses());
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());

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

        $fs = $this->container->get('filesystem');
        $uploadHelper = $this->container->get('zikula_multisites_module.upload_helper');
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
    protected function migratePrimaryConfigFileToV2()
    {
        $configFile = 'config/multisites_config.php';
        $configFileTemplate = 'https://github.com/zikula-modules/Multisites/blob/9d2e66e72a17757b36c26c6889c700d88868b345/src/modules/Multisites/Resources/config/multisites_config.php';

        $this->addFlash('error', $this->__f('Could not update the %file% file automatically. Please compare it with %template% and update it manually.', ['%file%' => $configFile, '%template%' => $configFileTemplate]));

        return true;
    }
}
