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

namespace Zikula\MultisitesModule\Helper;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ExtensionVariablesTrait;
use Zikula\GroupsModule\Constant as GroupsConstant;
use Zikula\MultisitesModule\DatabaseInfo;
use Zikula\MultisitesModule\Entity\Factory\EntityFactory;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\ZAuthModule\Api\ApiInterface\PasswordApiInterface;

/**
 * Utility class for configuration related functionality.
 */
class SystemHelper
{
    use ExtensionVariablesTrait;
    use TranslatorTrait;

    /**
     * Subsites configuration file.
     *
     * @var string
     */
    private $subsitesConfigFile = 'var/multisites.json';

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var PasswordApiInterface
     */
    protected $passwordApi;

    /**
     * The entity factory.
     *
     * @var EntityFactory
     */
    protected $entityFactory = null;

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $logsDirectory;

    /**
     * @var string
     */
    private $dataDirectory;

    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param TranslatorInterface  $translator     Translator service instance
     * @param SessionInterface     $session        Session service instance
     * @param VariableApiInterface $variableApi    VariableApi service instance
     * @param PasswordApiInterface $passwordApi    PasswordApi service instance
     * @param EntityFactory        $entityFactory  EntityFactory service instance
     * @param string               $cacheDirectory Cache directory
     * @param string               $logsDirectory  Logs directory
     * @param string               $dataDirectory  Data directory
     */
    public function __construct(
        TranslatorInterface $translator,
        SessionInterface $session,
        VariableApiInterface $variableApi,
        PasswordApiInterface $passwordApi,
        EntityFactory $entityFactory,
        $cacheDirectory,
        $logsDirectory,
        $dataDirectory
    ) {
        $this->setTranslator($translator);
        $this->session = $session;
        $this->variableApi = $variableApi;
        $this->passwordApi = $passwordApi;
        $this->entityFactory = $entityFactory;
        $this->multisitesParameters = $multisitesParameters;
        $this->cacheDirectory = $cacheDirectory;
        $this->logsDirectory = $logsDirectory;
        $this->dataDirectory = $dataDirectory;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * Creates initial folders for a new site.
     *
     * @param SiteEntity $site The given site instance
     *
     * @return boolean True on success or false otherwise
     */
    public function createSiteFolders(SiteEntity $site)
    {
        $suffix = '/' . $site->getSiteAlias();
        $filesDirectory = $this->dataDirectory . $suffix;

        $directoryList = [
            $this->cacheDirectory . $suffix,
            $this->logsDirectory . $suffix,
            $filesDirectory
        ];

        // add additional template folders to the list of directories
        if (null !== $site['template'] && isset($site['template']['folders'])) {
            foreach ($site['template']['folders'] as $folder) {
                // check for empty value (just for BC)
                if ($folder == '') {
                    continue;
                }
                $directoryList[] = $filesDirectory . '/' . trim($folder);
            }
        }

        $fs = new Filesystem();
        $flashBag = $this->session->getFlashBag();

        // check and create the directories
        $result = true;
        foreach ($directoryList as $directory) {
            if (!$fs->exists($directory)) {
                $fs->mkdir($directory, 0777);
                if (!$fs->exists($directory)) {
                    $flashBag->add('error', $this->__f('Error! The <strong>%directory</strong> directory does not exist and could not be created automatically. Please create it and make it writeable.', ['%directory' => $directory]));

                    $result = false;
                }
            } elseif (!is_writeable($directory)) {
                $fs->chmod($directory, 0777);
                if (!is_writeable($directory)) {
                    $flashBag->add('error', $this->__f('Error! The <strong>%directory</strong> directory is not writeable. Please correct that.', ['%directory' => $directory]));

                    $result = false;
                }
            }
        }


        return $result;
    }

    /**
     * Connects to an external database.
     *
     * @param DatabaseInfo $dbInfo       Database information
     * @param boolean      $skipDatabase Whether to create a general connection (non-db-specific)
     *
     * @return Connection|boolean Connection object or false on errors
     */
    public function connectToExternalDatabase(DatabaseInfo $dbInfo, $skipDatabase = false)
    {
        $dbName = $dbInfo->getName();
        $dbUser = $dbInfo->getUserName();
        $dbPass = $dbInfo->getPassword();
        $dbHost = $dbInfo->getHost();
        $dbType = $dbInfo->getType();

        $config = new Configuration();

        $connectionParams = [
            'url' => $dbType . '://' . $dbUser . ':' . $dbPass . '@' . $dbHost
        ];
        if (!$skipDatabase) {
            $connectionParams['url'] .= '/' . $dbName;
        }

        try {
            $connect = DriverManager::getConnection($connectionParams, $config);
        } catch (\Exception $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());

            return false;
        }

        return $connect;
    }

    /**
     * Creates a new database for a new site.
     *
     * @param DatabaseInfo $dbInfo Database information
     *
     * @return boolean True on success or false otherwise
     */
    public function createDatabase(DatabaseInfo $dbInfo)
    {
        $dbName = $dbInfo->getName();
        $dbType = $dbInfo->getType();

        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase($dbInfo, true);
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $dbName]));

            return false;
        }

        try {
            $sql = '';
            $sqlStart = 'CREATE DATABASE :dbName ';
            switch ($dbType) {
                case 'mysql':
                case 'mysqli':
                    $sql = $sqlStart . 'DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci';
                    break;
                case 'pgsql':
                    $sql = $sqlStart . 'ENCODING = \'utf8\'';
                    break;
                case 'oci':
                    $sql = $sqlStart . 'national character SET utf8';
                    break;
            }
            if (!empty($sql)) {
                $stmt = $connect->executeQuery($sql, [':dbName' => $dbName]);
                if (!$stmt->execute()) {
                    $flashBag->add('error', $this->__('DB Query error.') . ':<br />' . $sql  . "\n");;

                    return false;
                }
            }
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Connection error: %message', ['%message' => $exception->getMessage()]));

            return false;
        }

        return true;
    }

    /**
     * Performs all steps required to setup a certain site in a given database.
     *
     * @param SiteEntity $site The currently treated site instance
     *
     * @return boolean True on success or false otherwise
     */
    public function setupDatabaseContent(SiteEntity $site)
    {
        // check if a template is present (could have been decoupled)
        if (!isset($site['template']) || !isset($site['template']['sqlFile']) || empty($site['template']['sqlFile'])) {
            // do nothing then
            return true;
        }

        // read out the existing tables
        $tables = $this->readTables($site);
        if (!is_array($tables)) {
            return false;
        }

        $flashBag = $this->session->getFlashBag();

        // delete old tables (except excluded ones)
        if (!$this->deleteTables($site, $tables['delete'])) {
            $flashBag->add('error', $this->__('Error! Deletion of old database tables failed.'));

            return false;
        }

        // rename/backup excluded tables
        if (!$this->renameExcludedTables($site, $tables['rename'])) {
            $flashBag->add('error', $this->__('Error! Renaming of excluded database tables failed.'));

            return false;
        }

        // recreate the database tables based on the template file
        if (!$this->createTablesFromTemplate($site)) {
            $flashBag->add('error', $this->__('Error! Creation of database tables failed.'));

            return false;
        }

        // rename/restore excluded tables
        if (!$this->renameExcludedTables($site, $tables['rename'], true)) {
            $flashBag->add('error', $this->__('Error! Renaming of excluded database tables failed.'));

            return false;
        }

        // update site parameters like admin name, admin password, cookie name, site name...
        if (!$this->updateConfigValues($site)) {
            $flashBag->add('error', $this->__('Error! Updating the site configuration failed.'));

            return false;
        }

        // handle parameters as modvars
        if (!$this->processParameters($site)) {
            $flashBag->add('error', $this->__('Error! Updating the site parameters failed.'));

            return false;
        }

        return true;
    }

    /**
     * Reads in all tables contained in a database.
     *
     * @param SiteEntity $site The currently treated site instance
     *
     * @return array|boolean Array with table names or false on errors
     */
    protected function readTables(SiteEntity $site)
    {
        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        $excludedTables = [];
        if (isset($site['template']) && isset($site['template']['excludedTables']) && is_array($site['template']['excludedTables'])) {
            $excludedTables = $site['template']['excludedTables'];
        }

        $droppedTables = [];
        $backupTables = [];

        try {
            $excludedTablesWithWildCards = [];
            $excludeAll = false;
            foreach ($excludedTables as $excludedTable) {
                if ($excludedTable == '*') {
                    $excludeAll = true;
                    break;
                }
                if (strpos($excludedTable, '*') === false) {
                    // no wildcard here
                    continue;
                }
                $excludedTablesWithWildCards[] = $excludedTable;
            }

            $sql = '
                SELECT `table_name` AS `tableName`
                FROM `information_schema`.`tables`
                WHERE `table_schema` = :dbName
            ';

            while ($row = $connect->fetchAssoc($sql, [':dbName' => $site->getDatabaseName()])) {
                $tableName = $row['tableName'];

                $excluded = false;
                if (true === $excludeAll) {
                    $excluded = true;
                } elseif (in_array($tableName, $excludedTables)) {
                    // table is excluded (e.g. content_content)
                    $excluded = true;
                } else {
                    // check if a wildcard affects $tableName
                    foreach ($excludedTablesWithWildCards as $excludedTable) {
                        $excludedTableParts = explode('*', $excludedTable);
                        $length = strlen($excludedTableParts[0]);
                        if (substr($tableName, 0, $length) === $excludedTableParts[0]) {
                            $excluded = true;
                            break;
                        }
                    }
                }

                if (true === $excluded) {
                    // rename
                    $backupTables[] = $tableName;
                } else {
                    // drop
                    $droppedTables[] = $tableName;
                }
            }
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Connection error: %message', ['%message' => $exception->getMessage()]));

            return false;
        }

        return [
            'delete' => $droppedTables,
            'rename' => $backupTables
        ];
    }

    /**
     * Deletes a given list of tables contained in a database.
     *
     * @param SiteEntity $site   The currently treated site instance
     * @param array      $tables List of table names
     *
     * @return boolean True on success or false otherwise
     */
    protected function deleteTables(SiteEntity $site, array $tables)
    {
        if (count($tables) < 1) {
            return true;
        }

        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        // drop tables
        $sql = '
            DROP TABLE IF EXISTS `' . implode('`, `', $tables) . '`
        ';
        $stmt = $connect->prepare($sql);
        try {
            $stmt->execute();
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Connection error: %message', ['%message' => $exception->getMessage()]));

            return false;
        }

        return true;
    }

    /**
     * Renames a given list of tables contained in a database.
     *
     * @param SiteEntity $site    The currently treated site instance
     * @param array      $tables  List of table names
     * @param boolean    $restore False for backup mode and true for recover mode
     *
     * @return boolean True on success or false otherwise
     */
    protected function renameExcludedTables(SiteEntity $site, array $tables, $restore = false)
    {
        if (count($tables) < 1) {
            return true;
        }

        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        $backupPrefix = 'zkms_backup_';
        $prefixFrom = '';
        $prefixTo = '';
        if (!$restore) {
            // backup
            $prefixTo = $backupPrefix;
        } else {
            // restore
            $prefixFrom = $backupPrefix;
        }

        try {
            // delete possible destination tables before
            $sql = '';
            foreach ($tables as $tableName) {
                if ($sql != '') {
                    $sql .= ', ';
                }
                $sql .= '`' . $prefixTo . $tableName . '`';
            }
            $sql = 'DROP TABLE IF EXISTS ' . $sql;
            $stmt = $connect->prepare($sql);
            $stmt->execute();

            // rename tables
            if ($site->getDatabaseType() == 'pgsql') {
                // postgres seems to need one command per table
                foreach ($tables as $tableName) {
                    $sql = 'ALTER TABLE `' . $prefixFrom . $tableName . '` RENAME TO `' . $prefixTo . $tableName . '`';
                    $stmt = $connect->prepare($sql);
                    $stmt->execute();
                }
            } else {
                // mysql and oracle can do it in one step
                $sql = '';
                foreach ($tables as $tableName) {
                    if ($sql != '') {
                        $sql .= ', ';
                    }
                    $sql .= '`' . $prefixFrom . $tableName . '` TO `' . $prefixTo . $tableName . '`';
                }
                $sql = 'RENAME TABLE ' . $sql;
                $stmt = $connect->prepare($sql);
                $stmt->execute();
            }
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Connection error: %message', ['%message' => $exception->getMessage()]));

            return false;
        }

        return true;
    }

    /**
     * Creates database tables based on a given template file.
     *
     * @param SiteEntity $site The currently treated site instance
     *
     * @return boolean True on success or false otherwise
     */
    protected function createTablesFromTemplate(SiteEntity $site)
    {
        $flashBag = $this->session->getFlashBag();

        // check if the sql exists and it is readable
        $sqlFile = $site['template']['sqlFileFullPath'];
        $fs = new Filesystem();
        if (!$fs->exists($sqlFile) || !is_readable($sqlFile)) {
            $flashBag->add('error', $this->__('Error! The template sql file could not be found.'));

            return false;
        }

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        // read in the sql file's content
        $lines = file($sqlFile);
        $sql = '';
        $done = false;
        $errorInfo = '';
        foreach ($lines as $line_num => $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '--') === 0) {
                continue;
            }
            $sql .= $line;
            if (strrpos($line, ';') === strlen($line) - 1) {
                if (!$connect->query($sql)) {
                    $errorInfo = $this->__('Error importing the database in line') . ' ' . $line_num . ':<br />' . $sql . '<br />' . $connect->errorInfo() . "\n";
                    break;
                }

                $done = true;
                $sql = '';
            }
        }

        if (!empty($errorInfo)) {
            $flashBag->add('error', $errorInfo);

            return false;
        }

        if (!$done) {
            $flashBag->add('error', $this->__('Error! Importing the database failed. Perhaps there is a problem with the template file.'));

            return false;
        }

        return true;
    }

    /**
     * Updates the module vars values for a newly created site.
     *
     * @param SiteEntity $site The given site instance
     *
     * @return boolean True on success or false otherwise
     */
    protected function updateConfigValues(SiteEntity $site)
    {
        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        // modify the site name
        $connect->update('module_vars', ['value' => serialize($site->getSiteName())], ['modname' => 'ZConfig', 'name' => 'sitename']);
        $connect->update('module_vars', ['value' => serialize($site->getSiteName())], ['modname' => 'ZConfig', 'name' => 'defaultpagetitle']);

        // modify the site description
        $connect->update('module_vars', ['value' => serialize($site->getSiteDescription())], ['modname' => 'ZConfig', 'name' => 'slogan']);
        $connect->update('module_vars', ['value' => serialize($site->getSiteDescription())], ['modname' => 'ZConfig', 'name' => 'defaultmetadescription']);

        // modify the adminmail
        $connect->update('module_vars', ['value' => serialize($site->getSiteAdminEmail())], ['modname' => 'ZConfig', 'name' => 'adminmail']);

        // modify the session cookie name
        $connect->update('module_vars', ['value' => serialize('ZKSID_' . strtoupper($site->getSiteAlias()))], ['modname' => 'ZConfig', 'name' => 'sessionname']);

        $this->processAdministrator($connect, $site->getSiteAdminName(), $site->getSiteAdminEmail(), $site->getSiteAdminPassword());

        return true;
    }

    /**
     * Returns a list of parameter names and values for a certain site.
     *
     * @param SiteEntity $site The given site instance
     *
     * @return array Built list of parameters
     */
    protected function determineParameters(SiteEntity $site)
    {
        $parameters = [];

        if (!is_array($site['template']['parameters']) || count($site['template']['parameters']) < 1) {
            return $parameters;
        }

        // init result array
        foreach ($site['template']['parameters'] as $parameterName) {
            $parameters[$parameterName] = '';
        }

        // read in csv values
        if (null !== $site['parametersCsvFile'] && $site['parametersCsvFile'] != '') {
            $row = 1;
            $csvFilePath = $site['parametersCsvFileFullPath'];
            $fs = new Filesystem();
            if ($fs->exists($csvFilePath) && false !== ($handle = fopen($csvFilePath, 'r'))) {
                $delimiter = ';';
                while (false !== ($paramParts = fgetcsv($handle, 1000, $delimiter))) {
                    if (count($paramParts) != 2) {
                        continue;
                    }

                    if (!in_array($paramParts[0], array_keys($parameters))) {
                        continue;
                    }

                    $parameters[$paramParts[0]] = $paramParts[1];
                }
                fclose($handle);
            }
        }

        // read in manually entered values
        if (is_array($site['parametersArray']) && count($site['parametersArray']) > 0) {
            foreach ($site['parametersArray'] as $siteParam) {
                $paramParts = explode(': ', $siteParam);
                if (count($paramParts) != 2) {
                    continue;
                }

                if (!in_array($paramParts[0], array_keys($parameters))) {
                    continue;
                }

                $parameters[$paramParts[0]] = $paramParts[1];
            }
        }

        return $parameters;
    }

    /**
     * Inserts parameters and parameter values as module vars.
     *
     * @param SiteEntity $site The given site instance
     *
     * @return boolean True on success or false otherwise
     */
    protected function processParameters(SiteEntity $site)
    {
        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        $parameterPrefix = 'parameterValue';

        // delete obsolete parameter modvars which could exist due to another (earlier) template
        $sql = '
            DELETE FROM `module_vars`
            WHERE `modname` = \'ZikulaSubsiteModule\'
            AND `name` LIKE \'' . $parameterPrefix . '%\'
        ';
        $stmt = $connect->prepare($sql);
        if (!$stmt->execute()) {
            $flashBag->add('error', $this->__('Error! Deleting old parameters failed.'));

            return false;
        }

        // insert new parameters
        $sql = '
            INSERT INTO `module_vars` (`modname`, `name`, `value`)
            VALUES (\'ZikulaSubsiteModule\', :name, :value)
        ';

        // determine new parameter names and values
        $parameters = $this->determineParameters($site);
        if (count($parameters) > 0) {
            foreach ($parameters as $parameterName => $parameterValue) {
                $stmt = $connect->prepare($sql);
                if (!$stmt->execute([':name' => $parameterPrefix . ucfirst($parameterName), ':value' => serialize($parameterValue)])) {
                    $flashBag->add('error', $this->__f('Error! Creating parameter "%s" failed.', ['%s' => ucfirst($parameterName)]));

                    return false;
                }
            }
        }

        $fs = new Filesystem();

        // add logo path as parameter
        $logo = (null !== $site['logo'] && $site['logo'] != '' && $fs->exists($site['logoFullPath'])) ? $site['logoFullPath'] : '';
        $stmt = $connect->prepare($sql);
        if (!$stmt->execute([':name' => $parameterPrefix . 'Logo', ':value' => serialize($logo)])) {
            $flashBag->add('error', $this->__f('Error! Creating parameter "%s" failed.', ['%s' => 'Logo']));

            return false;
        }

        // add favicon path as parameter
        $favIcon = (null !== $site['favIcon'] && $site['favIcon'] != '' && $fs->exists($site['favIconFullPath'])) ? $site['favIconFullPath'] : '';
        $stmt = $connect->prepare($sql);
        if (!$stmt->execute([':name' => $parameterPrefix . 'FavIcon', ':value' => serialize($favIcon)])) {
            $flashBag->add('error', $this->__f('Error! Creating parameter "%s" failed.', ['%s' => 'FavIcon']));

            return false;
        }

        return true;
    }

    /**
     * Updates the dynamic multisites configuration file.
     *
     * @return boolean True on success or false otherwise
     */
    public function updateSubsitesConfigFile()
    {
        $fs = new Filesystem();
        if (!$fs->exists($this->subsitesConfigFile)) {
            return false;
        }

        // get all active sites
        $repository = $this->entityFactory->getRepository('site');
        $where = 'tbl.active = 1';
        $sites = $repository->selectWhere($where, '', false);
        if (false === $sites) {
            return false;
        }

        $siteData = [];
        foreach ($sites as $site) {
            $dbInfo = new DatabaseInfo($site);
            $siteData[$site->getSiteDns()] = $dbInfo->getConfigData();
        }

        // write file
        $result = false;
        try {
            $fs->dumpFile($this->subsitesConfigFile, json_encode($siteData));
            $result = true;
        } catch (IOExceptionInterface $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Deletes a database.
     *
     * @param DatabaseInfo $dbInfo Database information
     *
     * @return Boolean True on success or false otherwise
     */
    public function deleteDatabase(DatabaseInfo $dbInfo)
    {
        $dbName = $dbInfo->getName();

        // check if database connection works
        $connect = $this->connectToExternalDatabase($dbInfo);
        if (!$connect) {
            $flashBag = $this->session->getFlashBag();
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $dbName]));

            return false;
        }

        // now try to delete the database
        try {
            $stmt = $connect->prepare('DROP DATABASE :dbName;');
            $stmt->execute([':dbName' => $dbName]);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Deletes a directory recursively.
     *
     * @param string $dirName Name of the directory to be deleted
     *
     * @return Boolean True on success or false otherwise
     */
    public function deleteDir($dirName)
    {
        $fs = new Filesystem();
        if (!$fs->exists($dirName)) {
            return true;
        }

        $flashBag = $this->session->getFlashBag();
        $finder = new Finder();
        $finder->in($dirName);

        foreach ($finder as $file) {
            if (in_array($file->getFilename(), ['.', '..'])) {
                continue;
            }
            if (is_dir($file->getRealPath())) {
                $this->deleteDir($file->getRealPath());
                continue;
            }

            try {
                $fs->remove($file->getRealPath());
            } catch (IOExceptionInterface $e) {
                $flashBag->add('error', $this->__f('Error deleting file <strong>%file</strong>.', ['%file' => $file->getRealPath()]));

                return false;
            }
        }

        try {
            $fs->remove($dirname);
        } catch (IOExceptionInterface $e) {
            $flashBag->add('error', $this->__f('Error deleting directory <strong>%directory</strong>.', ['%directory' => $dirName]));

            return false;
        }

        return true;
    }

    /**
     * Checks whether the operating system is Windows.
     *
     * @return boolean True if Windows is detected, false otherwise
     */
    protected function isOnWindows() 
    {
        return strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0;
    }

    /**
     * Returns complete file path to the mysql program.
     *
     * @return string Path to the mysql program
     */
    protected function getMySQLFilePath()
    {
        $candidates = [];
        if ($this->isOnWindows()) {
            $candidates = [
                '/xampp/mysql/bin/mysql.exe',
                '/mysql/bin/mysql.exe',
                '/Programme/mysql/bin/mysql.exe',
                '/Programme/xampp/mysql/bin/mysql.exe',
                '/Program Files/mysql/bin/mysql.exe',
                '/Program Files/xampp/mysql/bin/mysql.exe'
            ];
        } else {
            $candidates = [
                '/usr/bin/mysql',
                '/usr/sbin/mysql',
                '/usr/etc/mysql',
                '/etc/mysql',
                '/usr/ucblib/mysql',
                '/usr/lib/mysql'
            ];
        }

        $fs = new Filesystem();
        foreach ($candidates as $path) {
            if ($fs->exists($path)) {
                return realpath($path);
            }
        }

        return null;
    }

    /**
     * Creates a database dump into the given sql file.
     *
     * @param SiteEntity $site           The currently treated site instance
     * @param string     $outputFilePath Path of output file
     *
     * @return boolean True on success or false otherwise
     */
    public function dumpDatabase(SiteEntity $site, $outputFilePath)
    {
        ini_set('max_execution_time', 600);

        $flashBag = $this->session->getFlashBag();

        // find the "mysqldump" program
        $mysqlPath = $this->getMySQLFilePath(); // z.B. c:\Programme\xampp\mysql\bin\mysql.exe
        if (!$mysqlPath) {
            $flashBag->add('error', $this->__('Error! Could not find MySQL program directory.'));

            return false;
        }

        $fs = new Filesystem();

        $dumper = dirname($mysqlPath) . ($this->isOnWindows() ? '/mysqldump.exe' : '/mysqldump');
        if (!$fs->exists($dumper)) {
            $flashBag->add('error', $this->__('Error! The "mysqldump" program is not installed.'));

            return false;
        }

        if ($fs->exists($outputFilePath)) {
            $fs->remove($outputFilePath);
        }

        $cmd = $dumper;
        $cmd .= ' --user=' . $site['databaseUserName'];
        $cmd .= ' --password="' . $site['databasePassword'] . '"';
        $cmd .= ' --host=' . $site['databaseHost'] . '';
        $cmd .= ' --quote-names --opt --compress --default-character-set=utf8';
        $cmd .= ' ' . $site['databaseName'];
        $cmd .= ' > ' . $outputFilePath;

        system($cmd, $retval);
        if ($retval != 0) {
            $flashBag->add('error', $this->__f('Error! The database dump failed. Please ensure that the database user %1$s has the "LOCK_TABLES" permission and the web service may write into the %2$s folder.', ['%1$s' => $site['databaseUserName'], '%2$s' => dirname($outputFilePath)]));

            return false;
        }

        return true;
    }

    /**
     * Creates a global administrator for a given site.
     *
     * @param SiteEntity $site The currently treated site instance
     *
     * @return boolean True on success or false otherwise
     */
    public function createAdministrator(SiteEntity $site)
    {
        $flashBag = $this->session->getFlashBag();

        // get global administrator parameters
        $globalAdminName = $this->getVar('globalAdminName');
        $globalAdminPassword = $this->getVar('globalAdminPassword');
        $globalAdminEmail = $this->getVar('globalAdminEmail');
        // check if the global administrator name, password and email had been defined
        if ($globalAdminName == '' || $globalAdminPassword == '' || $globalAdminEmail == '') {
            $flashBag->add('error', $this->__('You have not defined the global administrator name or password. Check the module configuration.'));

            return false;
        }

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        $this->processAdministrator($connect, $globalAdminName, $globalAdminEmail, $globalAdminPassword);

        return true;
    }

    /**
     * Recover the first row in the permissions table for administrators.
     *
     * @param SiteEntity $site The currently treated site instance
     *
     * @return boolean True on success or false otherwise
     */
    public function recoverAdminSiteControl(SiteEntity $site)
    {
        $flashBag = $this->session->getFlashBag();

        // check if database connection works
        $connect = $this->connectToExternalDatabase(new DatabaseInfo($site));
        if (!$connect) {
            $flashBag->add('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $site->getDatabaseName()]));

            return false;
        }

        // delete the sequence in the first position
        $sql = '
            DELETE FROM `group_perms`
            WHERE `sequence` < 1
            OR `pid` = 1
        ';
        $stmt = $connect->prepare($sql);
        if (!$stmt->execute()) {
            $flashBag->add('error', $this->__('Error! Deleting the permission sequences having a value below 0 failed.'));

            return false;
        }

        // insert a new sequence
        $fields = [
            'gid' => GroupsConstant::GROUP_ID_ADMIN,
            'sequence' => 0,
            'component' => '.*',
            'instance' => '.*',
            'level' => '800',
            'pid' => 1
        ];
        $connect->insert('group_perms', $fields);

        return true;
    }

    /**
     * Creates or updates an admin user.
     *
     * @param Connection $connect
     * @param string     $userName
     * @param string     $emailAddress
     * @param string     $unhashedPassword
     */
    protected function processAdministrator(Connection $connect, $userName, $emailAddress, $unhashedPassword)
    {
        // checks if the given administrator user exists
        $sql = '
            SELECT `uid`
            FROM `users`
            WHERE `uname` = :uname
        ';
        $user = $connect->fetchAssoc($sql, [':uname' => $userName]);
        $userId = $user['uid'];

        // encrypt the password with the hash method
        $password = $this->passwordApi->getHashedPassword($password);

        if ($userId == '') {
            // insert new admin user
            $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
            $nowUTCStr = $nowUTC->format('Y-m-d H:i:s');
            $fields = [
                'uname' => $userName,
                'email' => $emailAddress,
                'approved_date' => $nowUTCStr,
                'user_regdate' => $nowUTCStr,
                'activated' => '1'
            ];
            $connect->insert('users', $fields);

            // insert new admin user
            $fields = [
                'method' => 'native_either',
                'uid' => $userId,
                'uname' => $userName,
                'email' => $emailAddress,
                'verifiedEmail' => true,
                'pass' => $password
            ];
            $connect->insert('zauth_authentication_mapping', $fields);

            // add user attribute for used authentication method
            $fields = [
                'user_id' => $userId,
                'name' => 'authenticationMethod',
                'value' => 'native_either'
            ];
            $connect->insert('users_attributes', $fields);

            $user = $connect->fetchAssoc($sql, [':uname' => $userName]);
            $userId = $user['uid'];
        } else {
            // modify administrator password and email
            $connect->update('users', ['email' => $emailAddress], ['uid' => $userId]);
            $connect->update('zauth_authentication_mapping', ['email' => $emailAddress, 'pass' => $password], ['uid' => $userId]);
        }

        // check if administrator is member of the admin group already
        $adminGroupId = GroupsConstant::GROUP_ID_ADMIN;
        $sql = '
            SELECT `uid`
            FROM `group_membership`
            WHERE `uid` = :uid
            AND `gid` = :gid
        ';
        $groupMembership = $connect->fetchAssoc($sql, [':uid' => $userId, ':gid' => $adminGroupId]);
        if ($groupMembership['gid'] == '') {
            // add admin to the admin group
            $connect->insert('group_membership', ['uid' => $userId, 'gid' => $adminGroupId]);
        }
    }
}
