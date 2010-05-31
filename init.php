<?php
/**
 * PostNuke Application Framework
 *
 * @copyright (c) 2002, PostNuke Development Team
 * @link http://www.postnuke.com
 * @version $Id: pninit.php 22139 2007-06-01 10:57:16Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Create module tables and module vars
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return bool true if successful, false otherwise
 */
function Multisites_init()
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // create the models folder
    // check if the sitesFilesFolder exists
    $path = (isset($GLOBALS['ZConfig']['Multisites']['filesRealPath']) ? $GLOBALS['ZConfig']['Multisites']['filesRealPath'] : '');
    if ($path == '') {
        LogUtil::registerError (__('The directory where the sites files have to be created is not defined. Check your configuration values.', $dom));
        return false;
    }
    if (!file_exists($path)) {
        LogUtil::registerError (__('The directory where the sites files have to be created does not exists.', $dom));
        return false;
    }
    // check if the sitesFilesFolder is writeable
	if (!is_writeable($path)) {
		LogUtil::registerError (__('The directory where the sites files have to be created is not writeable.', $dom));
		return false;
	}
	// create the main site folder
	$path .= '/' . FormUtil::getPassedValue(siteDNS, null, 'GET');
	if (!file_exists($path)) {
        if (!mkdir($path, 0777)) {
            LogUtil::registerError (__('Error creating the directory.', $dom) . ': ' . $path);
            return false;
        }
    }
    // create the data folder
    $path .= $GLOBALS['ZConfig']['Multisites']['siteFilesFolder'];
    if (!file_exists($path)) {
        if (!mkdir($path, 0777)) {
            LogUtil::registerError (__('Error creating the directory.', $dom) . ': ' . $path);
            return false;
        }
    }
    // create the models folder
    $path .= '/models';
    if (!file_exists($path)) {
        if (!mkdir($path, 0777)) {
            LogUtil::registerError (__('Error creating the directory.', $dom) . ': ' . $path);
            return false;
        }
    }
    // Create module table
    if (!DBUtil::createTable('Multisites_sites')) {
        return false;
    }
    if (!DBUtil::createTable('Multisites_access')) {
        return false;
    }
    if (!DBUtil::createTable('Multisites_models')) {
        return false;
    }
    if (!DBUtil::createTable('Multisites_sitesModules')) {
        return false;
    }
    //Create module vars
    ModUtil::setVar('Multisites', 'modelsFolder', $path);
    ModUtil::setVar('Multisites', 'tempAccessFileContent','SetEnvIf Request_URI "\.css$" object_is_css=css
SetEnvIf Request_URI "\.js$" object_is_js=js
Order deny,allow
Deny from all
Allow from env=object_is_css
Allow from env=object_is_js');
    ModUtil::setVar('Multisites', 'globalAdminName', '');
    ModUtil::setVar('Multisites', 'globalAdminPassword', '');
    ModUtil::setVar('Multisites', 'globalAdminemail', '');
    return true;
}

/**
 * Initialise the interactive install system for the Multisites module
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return If the file multisites_config.php is not created pnRedirect to the step0 otherwise pnRedirect to step 4
 */
function Multisites_init_interactiveinit()
{
    // We start the interactive installation process now.
    // This function is called automatically if present.
    // In this case we simply show a welcome screen.
    // Check permissions
	if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	$pnRender = Renderer::getInstance('Multisites', false);
	if ($GLOBALS['ZConfig']['Multisites']['multi'] == 1) {
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
		$pnRender->assign('fileWriteable1', $fileWriteable1);
		$pnRender->assign('fileWriteable2', $fileWriteable2);
	}else{
		$step = 0;
	}
	$pnRender->assign('step', $step);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Step 1 - Check if the needed files exists and if they are writeable
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return if they exist and are writeable user can jump to step 2
 */
function Multisites_init_step1()
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
	if (is_writeable($path)) $fileWriteable1 = true;
	$path = 'config/multisites_dbconfig.php';
    if (file_exists($path)) $file2 = true;
	if (is_writeable($path)) $fileWriteable2 = true;
	ModUtil::load('Modules', 'admin');
	$pnRender = Renderer::getInstance('Multisites', false);
	$pnRender->assign('step', 1);
	$pnRender->assign('file1', $file1);
	$pnRender->assign('file2', $file2);
	$pnRender->assign('fileWriteable1', $fileWriteable1);
	$pnRender->assign('fileWriteable2', $fileWriteable2);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Step 2 - Check if the files folder exists and ask for the physical path
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @param  the physical folder name in case it does not exists and the user is pnRedirect to this step
 * @return post the pysical folder path
 */
function Multisites_init_step2($args)
{
	$filesRealPath = FormUtil::getPassedValue('filesRealPath', isset($args['filesRealPath']) ? $args['filesRealPath'] : null, 'GET');
	// Check permissions
	if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	if ($filesRealPath == null) $filesRealPath = substr($_SERVER['DOCUMENT_ROOT'], 0 ,  strrpos($_SERVER['DOCUMENT_ROOT'], '/')) . '/msdata';
	$scriptRealPath = substr($_SERVER['SCRIPT_FILENAME'], 0 ,  strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
	// ask for the correct location for the sites folder where the Temp folders will be created.
	ModUtil::load('Modules', 'admin');
	$pnRender = Renderer::getInstance('Multisites', false);
	$pnRender->assign('step', 2);
	$pnRender->assign('filesRealPath', $filesRealPath);
	$pnRender->assign('scriptRealPath', $scriptRealPath);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Get the physical folder path and write the value in the config/multisites_config.php file
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @param  the physical files folder
 * @return if the folder exists and it is writeable user is pnRedirected to the step 3 otherwise the user is pnRedirected to the step 2
 */
function Multisites_init_step21($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
	$filesRealPath = FormUtil::getPassedValue('filesRealPath', isset($args['filesRealPath']) ? $args['filesRealPath'] : null, 'POST');
	// Check permissions
	if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
    if ($filesRealPath == '') {
        LogUtil::registerError (__('The directory where the sites files have to be created is not defined. Please, define it.', $dom));
        return pnRedirect(ModUtil::url('Multisites', 'init', 'step2'));
    }
    if (!file_exists($filesRealPath)) {
        LogUtil::registerError (__('The directory where the sites files have to be created does not exists. Please, create it.', $dom));
        return System::redirect(ModUtil::url('Multisites', 'init', 'step2', array('filesRealPath' => $filesRealPath)));
    }
    // check if the sitesFilesFolder is writeable
	if (!is_writeable($filesRealPath)) {
		LogUtil::registerError (__('The directory where the sites files have to be created is not writeable. Please, set it as writeable.', $dom));
		return System::redirect(ModUtil::url('Multisites', 'init', 'step2', array('filesRealPath' => $filesRealPath)));
	}
	// the folder exists and it is writeable
	// write this parameter in the multisites_config.php file
	$file = "config/multisites_config.php";
	$fh = @fopen($file, 'r+');
	if ($fh == false) {
		fclose($fh);
        LogUtil::registerError(__('Error: File multisites_config.php not found', $dom));
		return System::redirect(ModUtil::url('Multisites', 'init', 'step1'));
	}
	$lines = file($file);
	$final_file = "";
	foreach ($lines as $line_num => $line) {
		if (strpos($line, "ZConfig['Multisites']['filesRealPath']")) $line =  str_replace('$filesRealPath',$filesRealPath,$line);
		$final_file .= $line;
	}
	// write the file with the parameter
	$fh = @fopen($file, 'w+');
	if (!fwrite($fh,$final_file)) {
		fclose($fh);
        LogUtil::registerError(__('Error: the multiple_config.php not writted', $dom));
        return System::redirect(ModUtil::url('Multisites', 'init', 'step1'));
	}
	fclose($fh);
	// pnRedirect user to step 3
	return System::redirect(ModUtil::url('Multisites', 'init', 'step3'));
}

/**
 * Step 3 - Ask for the main information of the multisites system
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return show the form with the needed fields
 */
function Multisites_init_step3()
{
	// Check permissions
	if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	// get server zikula folder installation
	$path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
	$basePath = substr($path, 0 ,  strrpos($path, '/'));
	$wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
	ModUtil::load('Modules', 'admin');
	$pnRender = pnRender::getInstance('Multisites', false);
	$pnRender->assign('step', 3);
	//$pnRender->assign('dbhost', $GLOBALS['ZConfig']['DBInfo']['default']['dbhost']);
	//$pnRender->assign('dbuname', $GLOBALS['ZConfig']['DBInfo']['default']['dbuname']);
	$pnRender->assign('siteTempFilesFolder', $GLOBALS['ZConfig']['System']['temp']);
    $pnRender->assign('mainHost', $_SERVER['HTTP_HOST']);
	$pnRender->assign('wwwroot', $wwwroot);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Get the multisites system parameters and write the value in the config/multisites_config.php file
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @param  the main multisites system parameters
 * @return pnRedirect the user to the new URL according with the multisites parameters
 */
function Multisites_init_step31($args)
{
	$mainSiteURL = FormUtil::getPassedValue('mainSiteURL', isset($args['mainSiteURL']) ? $args['mainSiteURL'] : null, 'POST');
	$siteDNSEndText = FormUtil::getPassedValue('siteDNSEndText', isset($args['siteDNSEndText']) ? $args['siteDNSEndText'] : null, 'POST');
	$siteTempFilesFolder = FormUtil::getPassedValue('siteTempFilesFolder', isset($args['siteTempFilesFolder']) ? $args['siteTempFilesFolder'] : null, 'POST');
	$siteFilesFolder = FormUtil::getPassedValue('siteFilesFolder', isset($args['siteFilesFolder']) ? $args['siteFilesFolder'] : null, 'POST');
    // Check permissions
	if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	// get server zikula folder installation
	$path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
	$basePath = substr($path, 0 ,  strrpos($path, '/'));
	$wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
	// read the file multisites_config.php
	$file = "config/multisites_config.php";
	$fh = @fopen($file, 'r+');
	if ($fh == false) {
		fclose($fh);
        LogUtil::registerError(__('Error: File multisites_config.php not found', $dom));
        return pnRedirect(ModUtil::url('Multisites', 'init', 'step1'));
	}
	$lines = file($file);
	$final_file = "";
	// Loop through our array, show HTML source as HTML source; and line numbers too.
	foreach ($lines as $line_num => $line) {
		if (strpos($line, "ZConfig['Multisites']['multi']") && !strpos($line, "ZConfig['Multisites']['mainSiteURL']")) {
			$line =  str_replace('= 0','= 1',$line);
		}else if (strpos($line, "ZConfig['Multisites']['mainSiteURL']")) {
			$line =  str_replace('$mainSiteURL',$mainSiteURL,$line);
		}else if (strpos($line, "ZConfig['Multisites']['siteTempFilesFolder']")) {
			$line = str_replace('$siteTempFilesFolder','/' . $siteTempFilesFolder,$line);
		}else if (strpos($line, "ZConfig['Multisites']['siteFilesFolder']")) {
			$line = str_replace('$siteFilesFolder','/' . $siteFilesFolder,$line);
		}else if (strpos($line, "ZConfig['Multisites']['wwwroot']")) {
			$line = str_replace('$wwwroot',$wwwroot,$line);
		}else if (strpos($line, "ZConfig['Multisites']['siteDNS']")) {
			$line = str_replace('$basePath',$basePath,$line);
		}
		//print $line . '<br />';
		$final_file .= $line;
	}
	$fh = @fopen($file, 'w');
	if (!fwrite($fh,$final_file)) {
		fclose($fh);
        LogUtil::registerError(__('Error: the file multisites_config.php has not been writen', $dom));
        return System::redirect(ModUtil::url('Multisites', 'init', 'step1'));
	}
	fclose($fh);
    //TODO: write rule to convert domains from www.foo.dom to foo.dom
	$path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
	$basePath = substr($path, 0 ,  strrpos($path, '/'));
	$wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
    return System::redirect(ModUtil::url('Multisites', 'init', 'step4'));
}

/**
 * Step 4 - Check if the needed files are writeable
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return if they exist and they are not writeable user can install the Multisites module
 */
function Multisites_init_step4()
{
	// Check permissions
	if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	// check if the files multisites_config.php and .htaccess are writeable
	$fileWriteable = false;
	$path = 'config/multisites_config.php';
	if (is_writeable($path)) $fileWriteable = true;
	ModUtil::load('Modules', 'admin');
	$pnRender = Renderer::getInstance('Multisites', false);
	$pnRender->assign('step', 4);
	$pnRender->assign('fileWriteable', $fileWriteable);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Delete the Multisites module
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return bool true if successful, false otherwise
 */
function Multisites_delete()
{
    // Delete module table
    DBUtil::dropTable('Multisites_sites');
    DBUtil::dropTable('Multisites_access');
    DBUtil::dropTable('Multisites_models');
    DBUtil::dropTable('Multisites_sitesModules');

    //Delete module vars
    ModUtil::delVar('Multisites', 'modelsFolder');
    ModUtil::delVar('Multisites', 'tempAccessFileContent');
    ModUtil::delVar('Multisites', 'globalAdminName');
    ModUtil::delVar('Multisites', 'globalAdminPassword');
    ModUtil::delVar('Multisites', 'globalAdminemail');

    //Deletion successfull
    return true;
}

/**
 * Update the Multisites module
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return bool true if successful, false otherwise
 */
function Multisites_upgrade($oldversion)
{
    if (!DBUtil::changeTable('Multisites_sites')) return false;
    if (!DBUtil::changeTable('Multisites_access')) return false;
    if (!DBUtil::changeTable('Multisites_models')) return false;
    if (!DBUtil::changeTable('Multisites_sitesModules')) return false;
    return true;
}
