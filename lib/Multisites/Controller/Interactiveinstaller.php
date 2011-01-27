<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv2.1 (or at your option, any later version).
 * @package Multisites
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Multisites_Controller_Interactiveinstaller extends Zikula_InteractiveInstaller
{
    /**
     * Initialise the interactive install system for the Multisites module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return If the file multisites_config.php is not created System::redirect to the step0 otherwise System::redirect to step 4
     */
    public function install()
    {
        // We start the interactive installation process now.
        // This function is called automatically if present.
        // In this case we simply show a welcome screen.
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        if (isset($this->serviceManager['multisites.enabled']) && $this->serviceManager['multisites.enabled'] == 1) {
            // check if the files multisites_config.php and .htaccess are writeable
            $fileWriteable1 = false;
            $fileWriteable2 = false;
            $path = 'config/multisites_config.php';
            if (is_writeable($path)) {
                $fileWriteable1 = true;
            }
            $path = 'config/multisites_dbconfig.php';
            if (is_writeable($path)) {
                $fileWriteable2 = true;
            }
            $step = 4;
            $this->view->assign('fileWriteable1', $fileWriteable1);
            $this->view->assign('fileWriteable2', $fileWriteable2);
        } else {
            $step = 0;
        }
        $this->view->assign('step', $step);
        return $this->view->fetch('Multisites_admin_init.htm');
    }

    /**
     * Step 1 - Check if the needed files exists and if they are writeable
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return if they exist and are writeable user can jump to step 2
     */
    public function step1()
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        // check if the needed files are located in the correct places and they are writeable
        $file1 = false;
        $file2 = false;
        $fileWriteable1 = false;
        $fileWriteable2 = false;
        $path = 'config/multisites_config.php';
        if (file_exists($path)) $file1 = true;
        if ($file1 === true && is_writeable($path)) $fileWriteable1 = true;
        $path = 'config/multisites_dbconfig.php';
        if (file_exists($path)) $file2 = true;
        if ($file2 === true && is_writeable($path)) $fileWriteable2 = true;

        ModUtil::load('Extensions', 'admin');
        $this->view->assign('step', 1)
                   ->assign('file1', $file1)
                   ->assign('file2', $file2)
                   ->assign('fileWriteable1', $fileWriteable1)
                   ->assign('fileWriteable2', $fileWriteable2);
        return $this->view->fetch('Multisites_admin_init.htm');
    }

    /**
     * Step 2 - Check if the files folder exists and ask for the physical path
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @param  the physical folder name in case it does not exists and the user is System::redirect to this step
     * @return post the pysical folder path
     */
    public function step2($args)
    {
        $files_real_path = FormUtil::getPassedValue('files_real_path', isset($args['files_real_path']) ? $args['files_real_path'] : null, 'GET');
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        if ($files_real_path == null) $files_real_path = substr($_SERVER['SCRIPT_FILENAME'], 0 ,  strrpos($_SERVER['SCRIPT_FILENAME'], '/')) . '/' . $GLOBALS['ZConfig']['System']['datadir'] . '/msdata';
        $scriptRealPath = substr($_SERVER['SCRIPT_FILENAME'], 0 ,  strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
        // ask for the correct location for the sites folder where the Temp folders will be created.
        ModUtil::load('Extensions', 'admin');
        $this->view->assign('step', 2)
                   ->assign('files_real_path', $files_real_path)
                   ->assign('scriptRealPath', $scriptRealPath);
        return $this->view->fetch('Multisites_admin_init.htm');
    }

    /**
     * Get the physical folder path and write the value in the config/multisites_config.php file
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @param  the physical files folder
     * @return if the folder exists and it is writeable user is System::redirected to the step 3 otherwise the user is System::redirected to the step 2
     */
    public function step21($args)
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $files_real_path = FormUtil::getPassedValue('files_real_path', isset($args['files_real_path']) ? $args['files_real_path'] : null, 'POST');
        if ($files_real_path == '') {
            LogUtil::registerError (__('The directory where the sites files have to be created is not defined. Please, define it.'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step2'));
        }
        if (!file_exists($files_real_path)) {
            LogUtil::registerError (__('The directory where the sites files have to be created does not exists. Please, create it.'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step2', array('files_real_path' => $files_real_path)));
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($files_real_path)) {
            LogUtil::registerError (__('The directory where the sites files have to be created is not writeable. Please, set it as writeable.'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step2', array('files_real_path' => $files_real_path)));
        }
        // the folder exists and it is writeable
        // write this parameter in the multisites_config.php file
        $file = "config/multisites_config.php";
        $fh = @fopen($file, 'r+');
        if ($fh == false) {
            fclose($fh);
            LogUtil::registerError(__('Error: File multisites_config.php not found'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step1'));
        }
        $lines = file($file);
        $final_file = "";
        foreach ($lines as $line_num => $line) {
            if (strpos($line, "ZConfig['Multisites']['multisites.files_real_path']")) $line =  str_replace('$files_real_path',$files_real_path,$line);
            $final_file .= $line;
        }
        // write the file with the parameter
        $fh = @fopen($file, 'w+');
        if (!fwrite($fh,$final_file)) {
            fclose($fh);
            LogUtil::registerError(__('Error: the multiple_config.php not writted'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step1'));
        }
        fclose($fh);
        // System::redirect user to step 3
        return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step3'));
    }

    /**
     * Step 3 - Ask for the main information of the multisites system
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return show the form with the needed fields
     */
    public function step3()
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // get server zikula folder installation
        $path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
        $basePath = substr($path, 0 ,  strrpos($path, '/'));
        $wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
        ModUtil::load('Extensions', 'admin');
        $this->view->assign('step', 3)
        //      ->assign('dbhost', $GLOBALS['ZConfig']['DBInfo']['default']['dbhost']);
        //      ->assign('dbuname', $GLOBALS['ZConfig']['DBInfo']['default']['dbuname']);
                ->assign('site_temp_files_folder', $GLOBALS['ZConfig']['System']['temp'])
                ->assign('mainHost', $_SERVER['HTTP_HOST'])
                ->assign('wwwroot', $wwwroot);
        return $this->view->fetch('Multisites_admin_init.htm');
    }

    /**
     * Get the multisites system parameters and write the value in the config/multisites_config.php file
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @param  the main multisites system parameters
     * @return System::redirect the user to the new URL according with the multisites parameters
     */
    public function step31($args)
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $mainsiteurl = FormUtil::getPassedValue('mainsiteurl', isset($args['mainsiteurl']) ? $args['mainsiteurl'] : null, 'POST');
        $sitednsEndText = FormUtil::getPassedValue('sitednsEndText', isset($args['sitednsEndText']) ? $args['sitednsEndText'] : null, 'POST');
        $site_temp_files_folder = FormUtil::getPassedValue('site_temp_files_folder', isset($args['site_temp_files_folder']) ? $args['site_temp_files_folder'] : null, 'POST');
        $site_files_folder = FormUtil::getPassedValue('site_files_folder', isset($args['site_files_folder']) ? $args['site_files_folder'] : null, 'POST');

        // get server zikula folder installation
        $path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
        $basePath = substr($path, 0 ,  strrpos($path, '/'));
        $wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
        // read the file multisites_config.php
        $file = "config/multisites_config.php";
        $fh = @fopen($file, 'r+');
        if ($fh == false) {
            fclose($fh);
            LogUtil::registerError(__('Error: File multisites_config.php not found'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step1'));
        }
        $lines = file($file);
        $final_file = "";
        $configPrefix = "ZConfig['Multisites']";
        // Loop through our array, show HTML source as HTML source; and line numbers too.
        foreach ($lines as $line_num => $line) {
            if (strpos($line, $configPrefix . "['multisites.enabled']") && !strpos($line, "ZConfig['Multisites']['multisites.mainsiteurl']")) {
                $line =  str_replace('= 0','= 1',$line);
            }else if (strpos($line, $configPrefix . "['multisites.mainsiteurl']")) {
                $line =  str_replace('$mainsiteurl',$mainsiteurl,$line);
            }else if (strpos($line, $configPrefix . "['multisites.site_temp_files_folder']")) {
                $line = str_replace('$site_temp_files_folder','/' . $site_temp_files_folder,$line);
            }else if (strpos($line, $configPrefix . "['multisites.site_files_folder']")) {
                $line = str_replace('$site_files_folder','/' . $site_files_folder,$line);
            }else if (strpos($line, $configPrefix . "['multisites.wwwroot']")) {
                $line = str_replace('$wwwroot',$wwwroot,$line);
            }else if (strpos($line, $configPrefix . "['multisites.sitedns']")) {
                $line = str_replace('$basePath',$basePath,$line);
            }
            //print $line . '<br />';
            $final_file .= $line;
        }
        $fh = @fopen($file, 'w');
        if (!fwrite($fh,$final_file)) {
            fclose($fh);
            LogUtil::registerError(__('Error: the file multisites_config.php has not been written'));
            return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step1'));
        }
        fclose($fh);

        /** TODO: write rule to convert domains from www.foo.dom to foo.dom */
        $path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
        $basePath = substr($path, 0 ,  strrpos($path, '/'));
        $wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;

        return System::redirect(ModUtil::url('Multisites', 'interactiveinstaller', 'step4'));
    }

    /**
     * Step 4 - Check if the needed files are writeable
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return if they exist and they are not writeable user can install the Multisites module
     */
    public function step4()
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        // check if the file multisites_config.php is writeable
        $fileWriteable = false;
        $path = 'config/multisites_config.php';
        if (is_writeable($path)) $fileWriteable = true;
        ModUtil::load('Extensions', 'admin');
        $this->view->assign('step', 4)
                   ->assign('fileWriteable', $fileWriteable);
        return $this->view->fetch('Multisites_admin_init.htm');
    }
}
