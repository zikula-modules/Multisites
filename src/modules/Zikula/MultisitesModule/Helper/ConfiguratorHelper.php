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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Bundle\CoreBundle\CacheClearer;
use Zikula\Bundle\CoreBundle\DynamicConfigDumper;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ExtensionVariablesTrait;

/**
 * Utility class for configuration related functionality.
 */
class ConfiguratorHelper
{
    use ExtensionVariablesTrait;
    use TranslatorTrait;

    /**
     * The current request.
     *
     * @var Request
     */
    protected $request = null;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Config dumper.
     *
     * @var DynamicConfigDumper
     */
    protected $configDumper = null;

    /**
     * Cache clearer.
     *
     * @var CacheClearer
     */
    protected $cacheClearer = null;

    /**
     * @var array
     */
    private $multisitesParameters;

    /**
     * @var string
     */
    private $dataDirectory = '';

    /**
     * @var string
     */
    private $tempDirectory = '';

    /**
     * Primary configuration file path.
     *
     * @var string
     */
    private $configFile;

    /**
     * Primary configuration template file path.
     *
     * @var string
     */
    private $configTemplateFile;

    /**
     * Subsites configuration file.
     *
     * @var string
     */
    private $subsitesConfigFile;

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
     * @param TranslatorInterface  $translator           Translator service instance
     * @param RequestStack         $requestStack         RequestStack service instance
     * @param SessionInterface     $session              Session service instance
     * @param VariableApiInterface $variableApi          VariableApi service instance
     * @param DynamicConfigDumper  $configDumper         DynamicConfigDumper service instance
     * @param CacheClearer         $cacheClearer         CacheClearer service instance
     * @param array                $multisitesParameters Multisites parameters array
     * @param string               $dataDirectory        Data directory
     * @param string               $tempDirectory        Temp directory
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        SessionInterface $session,
        VariableApiInterface $variableApi,
        DynamicConfigDumper $configDumper,
        CacheClearer $cacheClearer,
        array $multisitesParameters,
        $dataDirectory,
        $tempDirectory
    ) {
        $this->setTranslator($translator);
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $session;
        $this->variableApi = $variableApi;
        $this->configDumper = $configDumper;
        $this->cacheClearer = $cacheClearer;
        $this->multisitesParameters = $multisitesParameters;
        $this->dataDirectory = $dataDirectory;
        $this->tempDirectory = $tempDirectory;

        $this->configFile = 'config/multisites_config.php';
        $this->configTemplateFile = 'modules/Zikula/MultisitesModule/Resources/' . str_replace('config/', 'config-folder/', $this->configFile);
        $this->subsitesConfigFile = 'var/multisites.json';
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
     * Checks whether Multisites configuration is existing and well-formed.
     *
     * @return boolean indicating if everything is okay
     */
    public function verify()
    {
        $this->templateParameters = [];
        $fs = new Filesystem();

        if (!$this->isMultisitesEnabled()) {
            // step 1 - check if required files are located in the correct place and whether they are writeable
            if (!$this->checkConfigurationFiles()) {
                $this->templateParameters = [
                    'step' => 1
                ];
                return false;
            }

            $postData = $this->request->request;

            // step 2 - check if the files folder exists and ask for the physical path
            $filesRealPath = $this->getVar('files_real_path', '');
            if (empty($filesRealPath) || !$fs->exists($filesRealPath)) {
                $paramsValid = false;
                $filesRealPath = $postData->get('files_real_path', null);
                if (null !== $filesRealPath) {
                    // value is sent via POST, try to save it
                    if ($this->writeFilesPathToConfig($filesRealPath)) {
                        // save in modvar temporarily
                        $this->setVar('files_real_path', $filesRealPath);
                        $paramsValid = true;
                    }
                }
                if (true !== $paramsValid) {
                    // ask for the correct location for the sites folder where the Temp folders will be created.
                    $pathToThisFile = $this->request->server->get('SCRIPT_FILENAME');
                    $scriptRealPath = substr($pathToThisFile, 0, strrpos($pathToThisFile, '/'));
                    $filesRealPath = $scriptRealPath . '/' . $this->dataDirectory . '/msData';

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
                $mainSiteUrl = $postData->get('mainsiteurl', null);
                $siteTempFilesFolder = $postData->get('site_temp_files_folder', null);
                $siteFilesFolder = $postData->get('site_files_folder', null);
                if (null !== $mainSiteUrl && null !== $siteTempFilesFolder && null !== $siteFilesFolder) {
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

                if (true !== $paramsValid) {
                    // ask for multisites system parameters

                    $this->templateParameters = [
                        'step' => 3,
                        'configFile' => $this->configFile,
                        'mainSiteUrl' => $_SERVER['HTTP_HOST'],
                        'siteTempFilesFolder' => $this->tempDirectory,
                        'siteFilesFolder' => 'data'
                    ];

                    return false;
                }
            }

            // Step 4 - Check if config file is still writeable
            $configFileWriteable = $fs->exists($this->configFile) && is_writeable($this->configFile);
            if ($configFileWriteable) {
                $this->templateParameters = [
                    'step' => 4,
                    'configFile' => $this->configFile,
                    'configFileWriteable' => true
                ];

                return false;
            }

            $this->session->getFlashBag()->add('error', $this->__('Error: it seems everything is configured correctly, but Multisites is not running. Please check your configuration file!'));

            return false;
        }

        // Multisites is enabled

        // cleanup
        $this->delVar('files_real_path');
        $this->delVar('mainsiteurl');
        $this->delVar('site_temp_files_folder');
        $this->delVar('site_files_folder');

        // check if the multisites_config.php file is writeable (it should not)
        $configFileWriteable = $fs->exists($this->configFile) && is_writeable($this->configFile);
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
     * @return array List of template parameters
     */
    public function getTemplateParameters()
    {
        return $this->templateParameters;
    }

    /**
     * Checks whether the Multisites system is enabled or not.
     *
     * @return boolean True if Multisites is active, false otherwise
     */
    private function isMultisitesEnabled()
    {
        return true == $this->multisitesParameters['enabled'];
    }

    /**
     * Checks whether the Multisites configuration files exist and are writable.
     * Tries to setup the files automatically if possible.
     *
     * @return boolean True if all files are setup well, false otherwise
     */
    private function checkConfigurationFiles()
    {
        $fs = new Filesystem();
        $configFileExists = $fs->exists($this->configFile);
        if (!$configFileExists && $fs->copy($this->configTemplateFile, $this->configFile)) {
            $configFileExists = $fs->exists($this->configFile);
        }

        $configFileWriteable = $configFileExists && is_writeable($this->configFile);
        if ($configFileExists && !$configFileWriteable && @chmod($this->configFile, 0755)) {
            $configFileWriteable = is_writeable($this->configFile);
        }

        $result = $configFileWriteable;

        $mainSiteUrl = $this->getVar('mainsiteurl', '');
        if ($mainSiteUrl != '') {
            // configuration has been done almost completely
            // primary config file does not need to be writeable anymore
            $result = true;
        }

        $this->templateParameters = [
            'configFile' => $this->configFile,
            'configTemplateFile' => $this->configTemplateFile,
            'configFileExists' => $configFileExists,
            'configFileWriteable' => $configFileWriteable
        ];

        return $result;
    }

    /**
     * Checks whether the given files directory is existing and writeable or not.
     * Tries to create the directory and/or make it writeable if required.
     *
     * @param string $filesPath Path to the physical files folder
     *
     * @return boolean True if directory exists and is writeable, false otherwise
     */
    private function checkWriteableDirectory($filesPath)
    {
        $flashBag = $this->session->getFlashBag();

        if ($filesPath == '') {
            $flashBag->add('error', $this->__('The directory where the sites files have to be created is not defined. Please, define it.'));

            return false;
        }

        $fs = new Filesystem();
        if (!$fs->exists($filesPath)) {
            $fs->mkdir($filesPath, 0777);
            if (!$fs->exists($filesPath)) {
                $flashBag->add('error', $this->__('The directory where the sites files have to be created does not exist. Please, create it.'));

                return false;
            }
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($filesPath)) {
            $flashBag->add('error', $this->__('The directory where the sites files have to be created is not writeable. Please, set it as writeable.'));

            return false;
        }

        return true;
    }

    /**
     * Writes physical folder path into the config/multisites_config.php file.
     *
     * @param string $filesPath Path to the physical files folder
     *
     * @return boolean True if everything worked, false otherwise
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
        if (false === $fh) {
            fclose($fh);
            $flashBag->add('error', $this->__f('Error: File config/%s could not be found.', ['%s' => 'multisites_config.php']));

            return false;
        }

        $lines = file($this->configFile);
        $newFileContent = '';

        foreach ($lines as $line_num => $line) {
            if (false !== strpos($line, "ZConfig['Multisites']['files_real_path']")) {
                $line = str_replace('$files_real_path', $filesPath, $line);
            }
            $newFileContent .= $line;
        }

        // write new content into the file
        $fh = @fopen($this->configFile, 'w+');
        if (!fwrite($fh, $newFileContent)) {
            fclose($fh);
            $flashBag->add('error', $this->__f('Error: Could not write into the config/%s file.', ['%s' => 'multisites_config.php']));

            return false;
        }
        fclose($fh);

        // write stuff also into app/config/dynamic/generated.yml
        // TODO deprecate the old config file
        $parameters = $this->configDumper->getParameters();
        $parameters['multisites']['files_real_path'] = $filesPath;
        $this->configDumper->setParameters($parameters);
        $this->cacheClearer->clear('symfony');

        return true;
    }

    /**
     * Writes multisites system parameters into the config/multisites_config.php file.
     *
     * @param string $mainSiteUrl         Domain for the main site
     * @param string $siteTempFilesFolder Path to folder for temporary sites files
     * @param string $siteFilesFolder     Path to folder for sites files
     *
     * @return boolean True if everything worked, false otherwise
     */
    private function writeSystemParametersToConfig($mainSiteUrl, $siteTempFilesFolder, $siteFilesFolder)
    {
        // get server zikula folder installation
        /** TODO: write rule to convert domains from www.foo.dom to foo.dom */
        $pathToThisFile = $this->request->server->get('SCRIPT_FILENAME');
        $scriptRealPath = substr($pathToThisFile, 0, strrpos($pathToThisFile, '/'));
        $basePath = substr($scriptRealPath, 0, strrpos($scriptRealPath, '/'));
        $wwwroot = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath();

        $flashBag = $this->session->getFlashBag();

        // write parameter into the multisites_config.php file
        $fh = @fopen($this->configFile, 'r+');
        if (false === $fh) {
            fclose($fh);
            $flashBag->add('error', $this->__f('Error: File config/%s could not be found.', ['%s' => 'multisites_config.php']));

            return false;
        }

        $lines = file($this->configFile);
        $newFileContent = '';
        $configPrefix = "ZConfig['Multisites']";

        foreach ($lines as $line_num => $line) {
            if (false !== strpos($line, $configPrefix . "['enabled']") && false === strpos($line, $configPrefix . "['mainsiteurl']")) {
                $line = str_replace('= false', '= true', $line);
            } elseif (false !== strpos($line, $configPrefix . "['mainsiteurl']")) {
                $line = str_replace('$mainsiteurl', $mainSiteUrl, $line);
            } elseif (false !== strpos($line, $configPrefix . "['site_temp_files_folder']")) {
                $line = str_replace('$site_temp_files_folder', '/' . $siteTempFilesFolder, $line);
            } elseif (false !== strpos($line, $configPrefix . "['site_files_folder']")) {
                $line = str_replace('$site_files_folder', '/' . $siteFilesFolder, $line);
            } elseif (false !== strpos($line, $configPrefix . "['wwwroot']")) {
                $line = str_replace('$wwwroot', $wwwroot, $line);
            } elseif (false !== strpos($line, $configPrefix . "['sitedns']")) {
                $line = str_replace('$basePath', $basePath, $line);
            }
            $newFileContent .= $line;
        }

        // write new content into the file
        $fh = @fopen($this->configFile, 'w+');
        if (!fwrite($fh, $newFileContent)) {
            fclose($fh);
            $flashBag->add('error', $this->__f('Error: Could not write into the config/%s file.', ['%s' => 'multisites_config.php']));

            return false;
        }
        fclose($fh);

        // write stuff also into app/config/dynamic/generated.yml
        // TODO deprecate the old config file
        $parameters = $this->configDumper->getParameters();
        $parameters['multisites']['enabled'] = true;
        $parameters['multisites']['mainsiteurl'] = $mainSiteUrl;
        $parameters['multisites']['site_temp_files_folder'] = $siteTempFilesFolder;
        $parameters['multisites']['site_files_folder'] = $siteFilesFolder;
        $parameters['multisites']['wwwroot'] = $wwwroot;
        $parameters['multisites']['sitedns'] = $basePath;
        $this->configDumper->setParameters($parameters);
        $this->cacheClearer->clear('symfony');

        return true;
    }

    /**
     * Writes multisites system parameters into the config/multisites_config.php file.
     *
     * @return boolean True if everything worked, false otherwise
     */
    private function createAdditionalDirectories()
    {
        $flashBag = $this->session->getFlashBag();

        // check if the sitesFilesFolder exists
        $path = $this->getVar('files_real_path', '');
        if ($path == '') {
            $flashBag->add('error', $this->__('The directory for storing the sites files is not defined. Check your configuration values.'));

            return false;
        }

        $fs = new Filesystem();
        if (!$fs->exists($path)) {
            $flashBag->add('error', $this->__('The directory for storing the sites files does not exist.'));

            return false;
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($path)) {
            $flashBag->add('error', $this->__('The directory for storing the sites files is not writeable.'));

            return false;
        }

        // create the main site folder
        /*
        $path .= '/' . $this->request->query->get('sitedns', null);
        if (!$fs->exists($path)) {
            $fs->mkdir($path, 0777);
            if (!$fs->exists($path)) {
                $flashBag->add('error', $this->__('Error creating the directory:') . ' ' . $path);

                return false;
            }
        }*/

        // create the data folder
        $path .= '/' . $this->getVar('site_files_folder', '');
        if (!$fs->exists($path)) {
            $fs->mkdir($path, 0777);
            if (!$fs->exists($path)) {
                $flashBag->add('error', $this->__('Error creating the directory:') . ' ' . $path);

                return false;
            }
        }

        return true;
    }
}
