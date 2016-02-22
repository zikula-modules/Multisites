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

namespace Zikula\MultisitesModule\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\ExtensionVariablesTrait;

/**
 * Utility class for configuration related functionality.
 */
class ConfiguratorHelper
{
    use ExtensionVariablesTrait;
    use TranslatorTrait;

    /**
     * @var Session
     */
    protected $session;

    /**
     * The current request.
     *
     * @var Request
     */
    protected $request = null;

    /**
     * Primary configuration file path.
     *
     * @var string
     */
    private $configFile;

    /**
     * Database configuration file path.
     *
     * @var string
     */
    private $dbConfigFile;

    /**
     * Primary configuration template file path.
     *
     * @var string
     */
    private $configTemplateFile;

    /**
     * Database configuration template file path.
     *
     * @var string
     */
    private $dbConfigTemplateFile;

    /**
     * List of template parameters.
     *
     * @var array
     */
    private $templateParameters = [];

    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param Session             $session     Session service instance.
     * @param TranslatorInterface $translator  Translator service instance.
     * @param VariableApi         $variableApi VariableApi service instance.
     *
     * @return void
     */
    public function __construct(Session $session, TranslatorInterface $translator, VariableApi $variableApi, RequestStack $requestStack)
    {
        $this->session = $session;
        $this->setTranslator($translator);
        $this->variableApi = $variableApi;
        $this->extensionName = 'ZikulaMultisitesModule';
        $this->request = $requestStack->getCurrentRequest();

        $this->configFile = 'config/multisites_config.php';
        $this->dbConfigFile = 'config/multisites_dbconfig.php';
        $this->configTemplateFile = 'modules/Multisites/Resources/' . $this->configFile;
        $this->dbConfigTemplateFile = 'modules/Multisites/Resources/' . $this->dbConfigFile;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance.
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Checks whether Multisites configuration is existing and well-formed.
     *
     * @return boolean indicating if everything is okay
     */
    public function verify()
    {
        $this->templateParameters = [];

        if (!$this->isMultisitesEnabled()) {
            // step 1 - check if required files are located in the correct place and whether they are writeable
            if (!$this->checkConfigurationFiles()) {
                $this->templateParameters = [
                    'step' => 1
                ];
                return false;
            }

            // step 2 - check if the files folder exists and ask for the physical path
            $filesRealPath = $this->getVar('files_real_path', '');
            if (empty($filesRealPath) || !file_exists($filesRealPath)) {
                $paramsValid = false;
                $filesRealPath = $this->request->request->get('files_real_path', null);
                if ($filesRealPath !== null) {
                    // value is sent via POST, try to save it
                    if ($this->writeFilesPathToConfig($filesRealPath)) {
                        // save in modvar temporarily
                        $this->setVar('files_real_path', $filesRealPath);
                        $paramsValid = true;
                    }
                }
                if ($paramsValid !== true) {
                    // ask for the correct location for the sites folder where the Temp folders will be created.
                    $scriptRealPath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
                    $filesRealPath = $scriptRealPath . '/' . $GLOBALS['ZConfig']['System']['datadir'] . '/msData';

                    $this->templateParameters = [
                        'step' => 2,
                        'files_real_path' => $filesRealPath,
                        'scriptRealPath' => $scriptRealPath
                    ];
                    return false;
                }
            }

            // step 3 - define multisites system parameters
            $mainSiteUrl = $this->getVar('mainsiteurl', '');
            $siteTempFilesFolder = $this->getVar('site_temp_files_folder', '');
            $siteFilesFolder = $this->getVar('site_files_folder', '');
            if (empty($mainSiteUrl) || empty($siteTempFilesFolder) || empty($siteFilesFolder)) {
                $paramsValid = false;
                $mainSiteUrl = $this->request->request->get('mainsiteurl', null);
                $siteTempFilesFolder = $this->request->request->get('site_temp_files_folder', null);
                $siteFilesFolder = $this->request->request->get('site_files_folder', null);
                if ($mainSiteUrl !== null && $siteTempFilesFolder !== null && $siteFilesFolder !== null) {
                    // values are sent via POST, try to save them
                    if ($this->writeSystemParametersToConfig($mainSiteUrl, $siteTempFilesFolder, $siteFilesFolder)) {
                        if ($this->createAdditionalDirectories()) {
                            // save parameters in modvars temporarily
                            $this->setVar('mainsiteurl', $mainSiteUrl);
                            $this->setVar('site_temp_files_folder', $siteTempFilesFolder);
                            $this->setVar('site_files_folder', $siteFilesFolder);
                            $paramsValid = true;

                            $htAccessContent = 'SetEnvIf Request_URI "\.css$" object_is_css=css' . "\n";
                            $htAccessContent .= 'SetEnvIf Request_URI "\.js$" object_is_js=js' . "\n";
                            $htAccessContent .= 'Order deny, allow' . "\n";
                            $htAccessContent .= 'Deny from all' . "\n";
                            $htAccessContent .= 'Allow from env=object_is_css' . "\n";
                            $htAccessContent .= 'Allow from env=object_is_js' . "\n";

                            $this->setVar('tempAccessFileContent', $htAccessContent);
                        }
                    }
                }

                if ($paramsValid !== true) {
                    // ask for multisites system parameters

                    // get server zikula folder installation
                    $path = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
                    $basePath = substr($path, 0, strrpos($path, '/'));

                    $this->templateParameters = [
                        'step' => 3,
                        'configFile' => $this->configFile,
                        'dbConfigFile' => $this->dbConfigFile,
                        'mainSiteUrl' => $_SERVER['HTTP_HOST'],
                        'siteTempFilesFolder' => $GLOBALS['ZConfig']['System']['temp'],
                        'siteFilesFolder' => 'data'
                    ];
                    return false;
                }
            }

            // Step 4 - Check if config file is still writeable
            $configFileWriteable = file_exists($this->configFile) && is_writeable($this->configFile);
            if ($configFileWriteable) {
                $this->templateParameters = [
                    'step' => 4,
                    'configFile' => $this->configFile,
                    'configFileWriteable' => true
                ];
                return false;
            }

            $this->session->getFlashBag()->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error: it seems everything is configured correctly, but Multisites is not running. Please check your configuration file!'));

            return false;
        }

        // Multisites is enabled

        // cleanup
        $this->delVar('files_real_path');
        $this->delVar('mainsiteurl');
        $this->delVar('site_temp_files_folder');
        $this->delVar('site_files_folder');

        // check if the multisites_config.php file is writeable (it should not)
        $configFileWriteable = file_exists($this->configFile) && is_writeable($this->configFile);
        if ($configFileWriteable) {
            $this->templateParameters = [
                'step' => 4,
                'configFile' => $this->configFile,
                'configFileWriteable' => true
            ];
            return false;
        }

        return true;
    }

    /**
     * Returns the collected template parameters.
     *
     * @return array List of template parameters.
     */
    public function getTemplateParameters()
    {
        return $this->templateParameters;
    }

    /**
     * Checks whether the Multisites system is enabled or not.
     *
     * @return boolean True if Multisites is active, false otherwise.
     */
    private function isMultisitesEnabled()
    {
        global $ZConfig;

        return isset($ZConfig['Multisites']['multisites.enabled']) && $ZConfig['Multisites']['multisites.enabled'] == 1;
    }

    /**
     * Checks whether the Multisites configuration files exist and are writable.
     * Tries to setup the files automatically if possible.
     *
     * @return boolean True if all files are setup well, false otherwise.
     */
    private function checkConfigurationFiles()
    {
        $configFileExists = file_exists($this->configFile);
        if (!$configFileExists && @copy($this->configTemplateFile, $this->configFile)) {
            $configFileExists = file_exists($this->configFile);
        }

        $dbConfigFileExists = file_exists($this->dbConfigFile);
        if (!$dbConfigFileExists && @copy($this->dbConfigTemplateFile, $this->dbConfigFile)) {
            $dbConfigFileExists = file_exists($this->dbConfigFile);
        }

        $configFileWriteable = $configFileExists && is_writeable($this->configFile);
        if ($configFileExists && !$configFileWriteable && @chmod($this->configFile, 0755)) {
            $configFileWriteable = is_writeable($this->configFile);
        }

        $dbConfigFileWriteable = $dbConfigFileExists && is_writeable($this->dbConfigFile);
        if ($dbConfigFileExists && !$dbConfigFileWriteable && @chmod($this->dbConfigFile, 0755)) {
            $dbConfigFileWriteable = is_writeable($this->dbConfigFile);
        }

        $result = ($configFileWriteable && $dbConfigFileWriteable);

        $mainSiteUrl = $this->getVar('mainsiteurl', '');
        if ($mainSiteUrl != '') {
            // configuration has been done almost completely
            // primary config file does not need to be writeable anymore
            $result = $dbConfigFileWriteable;
        }

        $this->templateParameters = [
            'configFile' => $this->configFile,
            'dbConfigFile' => $this->dbConfigFile,
            'configTemplateFile' => $this->configTemplateFile,
            'dbConfigTemplateFile' => $this->dbConfigTemplateFile,
            'configFileExists' => $configFileExists,
            'dbConfigFileExists' => $dbConfigFileExists,
            'configFileWriteable' => $configFileWriteable,
            'dbConfigFileWriteable' => $dbConfigFileWriteable
        ];

        return $result;
    }

    /**
     * Checks whether the given files directory is existing and writeable or not.
     * Tries to create the directory and/or make it writeable if required.
     *
     * @param string Path to the physical files folder.
     *
     * @return boolean True if directory exists and is writeable, false otherwise.
     */
    private function checkWriteableDirectory($filesPath)
    {
        $flashBag = $this->session->getFlashBag();

        if ($filesPath == '') {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('The directory where the sites files have to be created is not defined. Please, define it.'));
            return false;
        }
        if (!file_exists($filesPath)) {
            if (!@mkdir($filesPath, 0777, true) || !file_exists($filesPath)) {
                $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('The directory where the sites files have to be created does not exist. Please, create it.'));
                return false;
            }
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($filesPath)) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('The directory where the sites files have to be created is not writeable. Please, set it as writeable.'));
            return false;
        }

        return true;
    }

    /**
     * Writes physical folder path into the config/multisites_config.php file.
     *
     * @param string Path to the physical files folder.
     *
     * @return boolean True if everything worked, false otherwise.
     */
    private function writeFilesPathToConfig($filesPath)
    {
        // check if the folder exists and is writeable
        if (!$this->checkWriteableDirectory($filesPath)) {
            return false;
        }

        $flashBag = $this->session->getFlashBag();

        // write parameter into the multisites_config.php file
        $fh = @fopen($this->configFile, 'r+');
        if ($fh == false) {
            fclose($fh);
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error: File config/multisites_config.php could not be found.'));
            return false;
        }

        $lines = file($this->configFile);
        $newFileContent = '';

        foreach ($lines as $line_num => $line) {
            if (strpos($line, "ZConfig['Multisites']['multisites.files_real_path']") !== false) {
                $line = str_replace('$files_real_path', $filesPath, $line);
            }
            $newFileContent .= $line;
        }

        // write new content into the file
        $fh = @fopen($this->configFile, 'w+');
        if (!fwrite($fh, $newFileContent)) {
            fclose($fh);
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error: Could not write into the config/multiple_config.php file.'));
            return false;
        }
        fclose($fh);

        return true;
    }

    /**
     * Writes multisites system parameters into the config/multisites_config.php file.
     *
     * @param string Domain for the main site.
     * @param string Path to folder for temporary sites files.
     * @param string Path to folder for sites files.
     *
     * @return boolean True if everything worked, false otherwise.
     */
    private function writeSystemParametersToConfig($mainSiteUrl, $siteTempFilesFolder, $siteFilesFolder)
    {
        // get server zikula folder installation
        /** TODO: write rule to convert domains from www.foo.dom to foo.dom */
        $path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
        $basePath = substr($path, 0 ,  strrpos($path, '/'));
        $wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;

        $flashBag = $this->session->getFlashBag();

        // write parameter into the multisites_config.php file
        $fh = @fopen($this->configFile, 'r+');
        if ($fh == false) {
            fclose($fh);
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error: File config/multisites_config.php could not be found.'));
            return false;
        }

        $lines = file($this->configFile);
        $newFileContent = '';
        $configPrefix = "ZConfig['Multisites']";

        foreach ($lines as $line_num => $line) {
            if (strpos($line, $configPrefix . "['multisites.enabled']") !== false && strpos($line, "ZConfig['Multisites']['multisites.mainsiteurl']") === false) {
                $line = str_replace('= 0', '= 1', $line);
            } elseif (strpos($line, $configPrefix . "['multisites.mainsiteurl']") !== false) {
                $line = str_replace('$mainsiteurl', $mainSiteUrl, $line);
            } elseif (strpos($line, $configPrefix . "['multisites.site_temp_files_folder']") !== false) {
                $line = str_replace('$site_temp_files_folder', '/' . $siteTempFilesFolder, $line);
            } elseif (strpos($line, $configPrefix . "['multisites.site_files_folder']") !== false) {
                $line = str_replace('$site_files_folder', '/' . $siteFilesFolder, $line);
            } elseif (strpos($line, $configPrefix . "['multisites.wwwroot']") !== false) {
                $line = str_replace('$wwwroot', $wwwroot, $line);
            } elseif (strpos($line, $configPrefix . "['multisites.sitedns']") !== false) {
                $line = str_replace('$basePath', $basePath, $line);
            }
            $newFileContent .= $line;
        }

        // write new content into the file
        $fh = @fopen($this->configFile, 'w+');
        if (!fwrite($fh, $newFileContent)) {
            fclose($fh);
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error: Could not write into the config/multiple_config.php file.'));
            return false;
        }
        fclose($fh);

        return true;
    }

    /**
     * Writes multisites system parameters into the config/multisites_config.php file.
     *
     * @return boolean True if everything worked, false otherwise.
     */
    private function createAdditionalDirectories()
    {
        $flashBag = $this->session->getFlashBag();

        // check if the sitesFilesFolder exists
        $path = $this->getVar('files_real_path', '');
        if ($path == '') {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('The directory for storing the sites files is not defined. Check your configuration values.'));
            return false;
        }
        if (!file_exists($path)) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('The directory for storing the sites files does not exist.'));
            return false;
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($path)) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('The directory for storing the sites files is not writeable.'));
            return false;
        }

        // create the main site folder
//         $path .= '/' . FormUtil::getPassedValue('sitedns', null, 'GET');
//         if (!file_exists($path) && !mkdir($path, 0777, true)) {
//             $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error creating the directory:') . ' ' . $path);
//             return false;
//         }

        // create the data folder
        $path .= '/' . $this->getVar('site_files_folder', '');
        if (!file_exists($path) && !@mkdir($path, 0777, true)) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error creating the directory:') . ' ' . $path);
            return false;
        }

        return true;
    }
}
