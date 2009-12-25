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
    $path = (isset($GLOBALS['PNConfig']['Multisites']['filesRealPath']) ? $GLOBALS['PNConfig']['Multisites']['filesRealPath'] : '');
    if ($path == ''){
        LogUtil::registerError (__('The directory where the sites files have to be created is not defined. Check your configuration values.', $dom));
        return false;
    }
    if (!file_exists($path)){
        LogUtil::registerError (__('The directory where the sites files have to be created does not exists.', $dom));
        return false;
    }
    // check if the sitesFilesFolder is writeable
	if (!is_writeable($path)){
		LogUtil::registerError (__('The directory where the sites files have to be created is not writeable.', $dom));
		return false;
	}
	// create the main site folder
	$path .= '/' . FormUtil::getPassedValue(siteDNS, null, 'GET');
	if (!file_exists($path)){
        if(!mkdir($path, 0777)){
            LogUtil::registerError (__('Error creating the directory.', $dom) . ': ' . $path);
            return false;
        }
    }
    // create the data folder
    $path .= $GLOBALS['PNConfig']['Multisites']['siteFilesFolder'];
    if (!file_exists($path)){
        if(!mkdir($path, 0777)){
            LogUtil::registerError (__('Error creating the directory.', $dom) . ': ' . $path);
            return false;
        }
    }
    // create the models folder
    $path .= '/models';
    if (!file_exists($path)){
        if(!mkdir($path, 0777)){
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
    pnModSetVar('Multisites', 'modelsFolder', $path);
    pnModSetVar('Multisites', 'tempAccessFileContent','SetEnvIf Request_URI "\.css$" object_is_css=css
SetEnvIf Request_URI "\.js$" object_is_js=js
Order deny,allow
Deny from all
Allow from env=object_is_css
Allow from env=object_is_js');
    pnModSetVar('Multisites', 'globalAdminName', '');
    pnModSetVar('Multisites', 'globalAdminPassword', '');
    pnModSetVar('Multisites', 'globalAdminemail', '');
    return true;
}

/**
 * Initialise the interactive install system for the Multisites module
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return If the file multisites_config.php is not created redirect to the step0 otherwise redirect to step 4
 */
function Multisites_init_interactiveinit()
{
    // We start the interactive installation process now.
    // This function is called automatically if present.
    // In this case we simply show a welcome screen.
    // Check permissions
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
	$pnRender = pnRender::getInstance('Multisites', false);
	if($GLOBALS['PNConfig']['Multisites']['multi'] == 1){
		// check if the files multisites_config.php and .htaccess are writeable
		$fileWriteable1 = false;
		$fileWriteable2 = false;
		$path = 'config/multisites_config.php';
		if (is_writeable($path)){
			$fileWriteable1 = true;
		}
		$path = '../.htaccess';
		if (is_writeable($path)){
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
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
	// check if the needed files are located in the correct places and they are writeable
	$file1 = false;
	$file2 = false;
	$fileWriteable1 = false;
	$fileWriteable2 = false;
	$path = 'config/multisites_config.php';
    if (file_exists($path)){
		$file1 = true;
    }
	if (is_writeable($path)){
		$fileWriteable1 = true;
	}
	$path = '../.htaccess';
    if (file_exists($path)){
		$file2 = true;
    }
	if (is_writeable($path)){
		$fileWriteable2 = true;
	}
	pnModLoad('Modules', 'admin');
	$pnRender = pnRender::getInstance('Multisites', false);
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
 * @param  the physical folder name in case it does not exists and the user is redirect to this step
 * @return post the pysical folder path
 */
function Multisites_init_step2($args)
{
	$filesRealPath = FormUtil::getPassedValue('filesRealPath', isset($args['filesRealPath']) ? $args['filesRealPath'] : null, 'GET');
	// Check permissions
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
	if($filesRealPath == null){
		$filesRealPath = substr($_SERVER['DOCUMENT_ROOT'], 0 ,  strrpos($_SERVER['DOCUMENT_ROOT'], '/')) . '/msdata';
	}
	$scriptRealPath = substr($_SERVER['SCRIPT_FILENAME'], 0 ,  strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
	// ask for the correct location for the sites folder where the pnTemp folders will be created.
	pnModLoad('Modules', 'admin');
	$pnRender = pnRender::getInstance('Multisites', false);
	$pnRender->assign('step', 2);
	$pnRender->assign('filesRealPath', $filesRealPath);
	$pnRender->assign('scriptRealPath', $scriptRealPath);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Get the physical folder path and write the value in the config/multisites_config.php file
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @param  the physical files folder
 * @return if the folder exists and it is writeable user is redirected to the step 3 otherwise the user is redirected to the step 2
 */
function Multisites_init_step21($args)
{
	$filesRealPath = FormUtil::getPassedValue('filesRealPath', isset($args['filesRealPath']) ? $args['filesRealPath'] : null, 'POST');
	// Check permissions
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
    if ($filesRealPath == ''){
        LogUtil::registerError (__('The directory where the sites files have to be created is not defined. Please, define it.', $dom));
        return pnRedirect(pnModURL('Multisites', 'init', 'step2'));
    }
    if (!file_exists($filesRealPath)){
        LogUtil::registerError (__('The directory where the sites files have to be created does not exists. Please, create it.', $dom));
        return pnRedirect(pnModURL('Multisites', 'init', 'step2', array('filesRealPath' => $filesRealPath)));
    }
    // check if the sitesFilesFolder is writeable
	if (!is_writeable($filesRealPath)){
		LogUtil::registerError (__('The directory where the sites files have to be created is not writeable. Please, set it as writeable.', $dom));
		return pnRedirect(pnModURL('Multisites', 'init', 'step2', array('filesRealPath' => $filesRealPath)));
	}
	// the folder exists and it is writeable
	// write this parameter in the multisites_config.php file
	$file = "config/multisites_config.php";
	$fh = @fopen($file, 'r+');
	if($fh == false){
		fclose($fh);
        LogUtil::registerError(__('Error: File multisites_config.php not found', $dom));
		return pnRedirect(pnModURL('Multisites', 'init', 'step1'));
	}
	$lines = file($file);
	$final_file = "";
	foreach ($lines as $line_num => $line) {
		if(strpos($line, "PNConfig['Multisites']['filesRealPath']")){
			$line =  str_replace('$filesRealPath',$filesRealPath,$line);
		}
		$final_file .= $line;
	}
	// write the file with the parameter
	$fh = @fopen($file, 'w+');
	if(!fwrite($fh,$final_file)){
		fclose($fh);
        LogUtil::registerError(__('Error: the multiple_config.php not writted', $dom));
        return pnRedirect(pnModURL('Multisites', 'init', 'step1'));
	}
	fclose($fh);
	// redirect user to step 3
	return pnRedirect(pnModURL('Multisites', 'init', 'step3'));
}

/**
 * Step 3 - Ask for the main information of the multisites system
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return show the form with the needed fields
 */
function Multisites_init_step3()
{
	// Check permissions
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
	// get server zikula folder installation
	$path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
	$basePath = substr($path, 0 ,  strrpos($path, '/'));
	$wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
	pnModLoad('Modules', 'admin');
	$pnRender = pnRender::getInstance('Multisites', false);
	$pnRender->assign('step', 3);
	$pnRender->assign('dbhost', $GLOBALS['PNConfig']['DBInfo']['default']['dbhost']);
	$pnRender->assign('dbuname', $GLOBALS['PNConfig']['DBInfo']['default']['dbuname']);
	$pnRender->assign('siteTempFilesFolder', $GLOBALS['PNConfig']['System']['temp']);
	$pnRender->assign('wwwroot', $wwwroot);
    return $pnRender->fetch('Multisites_admin_init.htm');
}

/**
 * Get the multisites system parameters and write the value in the config/multisites_config.php file
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @param  the main multisites system parameters
 * @return redirect the user to the new URL according with the multisites parameters
 */
function Multisites_init_step31($args)
{
	$dbhost = FormUtil::getPassedValue('dbhost', isset($args['dbhost']) ? $args['dbhost'] : null, 'POST');
	$dbuname = FormUtil::getPassedValue('dbuname', isset($args['dbuname']) ? $args['dbuname'] : null, 'POST');
	$dbpass = FormUtil::getPassedValue('dbpass', isset($args['dbpass']) ? $args['dbpass'] : null, 'POST');
	$mainSiteURL = FormUtil::getPassedValue('mainSiteURL', isset($args['mainSiteURL']) ? $args['mainSiteURL'] : null, 'POST');
	$siteDNSEndText = FormUtil::getPassedValue('siteDNSEndText', isset($args['siteDNSEndText']) ? $args['siteDNSEndText'] : null, 'POST');
	$siteTempFilesFolder = FormUtil::getPassedValue('siteTempFilesFolder', isset($args['siteTempFilesFolder']) ? $args['siteTempFilesFolder'] : null, 'POST');
	$siteFilesFolder = FormUtil::getPassedValue('siteFilesFolder', isset($args['siteFilesFolder']) ? $args['siteFilesFolder'] : null, 'POST');
    // Check permissions
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
	// get server zikula folder installation
	$path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
	$basePath = substr($path, 0 ,  strrpos($path, '/'));
	$wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
	// read the file multisites_config.php
	$file = "config/multisites_config.php";
	$fh = @fopen($file, 'r+');
	if($fh == false){
		fclose($fh);
        LogUtil::registerError(__('Error: File multisites_config.php not found', $dom));
        return pnRedirect(pnModURL('Multisites', 'init', 'step1'));
	}
	$lines = file($file);
	$final_file = "";
	// Loop through our array, show HTML source as HTML source; and line numbers too.
	foreach ($lines as $line_num => $line) {
		if(strpos($line, "PNConfig['DBInfo']['Multisites']['dbhost']")){
			$line =  str_replace('$dbhost',$dbhost,$line);
		}else
		if(strpos($line, "PNConfig['DBInfo']['Multisites']['dbuname']")){
			$line =  str_replace('$dbuname',base64_encode($dbuname),$line);
		}else
		if(strpos($line, "PNConfig['DBInfo']['Multisites']['dbpass']")){
			$line =  str_replace('$dbpass',base64_encode($dbpass),$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['multi']") && !strpos($line, "PNConfig['Multisites']['mainSiteURL']")){
			$line =  str_replace('= 0','= 1',$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['mainSiteURL']")){
			$line =  str_replace('$mainSiteURL',$mainSiteURL,$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['siteDNSEndText']") && !strpos($line, "PNConfig['Multisites']['siteDNS']")){
			$line = str_replace('$siteDNSEndText',$siteDNSEndText,$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['siteTempFilesFolder']")){
			$line = str_replace('$siteTempFilesFolder','/' . $siteTempFilesFolder,$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['siteFilesFolder']")){
			$line = str_replace('$siteFilesFolder','/' . $siteFilesFolder,$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['wwwroot']")){
			$line = str_replace('$wwwroot',$wwwroot,$line);
		}else
		if(strpos($line, "PNConfig['Multisites']['siteDNS']")){
			$line = str_replace('$basePath',$basePath,$line);
		}
		//print $line . '<br />';
		$final_file .= $line;
	}
	$fh = @fopen($file, 'w');
	if(!fwrite($fh,$final_file)){
		fclose($fh);
        LogUtil::registerError(__('Error: the file multisites_config.php has not been writted', $dom));
        return pnRedirect(pnModURL('Multisites', 'init', 'step1'));
	}
	fclose($fh);
	// write the file .htaccess
	$path = str_replace($basePath . '/','',$path);
	$file = "../.htaccess";
	$final_file = 'RewriteEngine on' . "\n";
	$final_file .= 'RewriteBase '. $basePath .'/' . "\n";
	$final_file .= 'RewriteRule ^([^/]*)/' . $siteDNSEndText . '$ $1/' . $siteDNSEndText . "/ [QSA,R=permanent,L]\n";
	$final_file .= 'RewriteRule ^([^/]*)/' . $siteDNSEndText . '/(.*)$ ' . $path . "/$2?siteDNS=$1 [QSA,L]\n";
	$fh = @fopen($file, 'w+');
	if(!fwrite($fh,$final_file)){
		fclose($fh);
        LogUtil::registerError(__('Error: the file .htaccess hs not been writted', $dom));
        return pnRedirect(pnModURL('Multisites', 'init', 'step1'));
	}
	fclose($fh);
	$path = substr($_SERVER['PHP_SELF'], 0 ,  strrpos($_SERVER['PHP_SELF'], '/'));
	$basePath = substr($path, 0 ,  strrpos($path, '/'));
	$wwwroot = 'http://' . $_SERVER['HTTP_HOST'] . $basePath;
	return pnRedirect($wwwroot. '/' . $mainSiteURL . '/' . $siteDNSEndText);
}

/**
 * Step 4 - Check if the needed files are writeable
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return if they exist and they are not writeable user can install the Multisites module
 */
function Multisites_init_step4()
{
	// Check permissions
	if (!pnSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)){
		return LogUtil::registerPermissionError();
	}
	// check if the files multisites_config.php and .htaccess are writeable
	$fileWriteable1 = false;
	$fileWriteable2 = false;
	$path = 'config/multisites_config.php';
	if (is_writeable($path)){
		$fileWriteable1 = true;
	}
	$path = '../.htaccess';
	if (is_writeable($path)){
		$fileWriteable2 = true;
	}

	pnModLoad('Modules', 'admin');
	$pnRender = pnRender::getInstance('Multisites', false);
	$pnRender->assign('step', 4);
	$pnRender->assign('fileWriteable1', $fileWriteable1);
	$pnRender->assign('fileWriteable2', $fileWriteable2);
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
    pnModDelVar('Multisites', 'modelsFolder');
    pnModDelVar('Multisites', 'tempAccessFileContent');
    pnModDelVar('Multisites', 'globalAdminName');
    pnModDelVar('Multisites', 'globalAdminPassword');
    pnModDelVar('Multisites', 'globalAdminemail');

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
    if (!DBUtil::changeTable('Multisites_sites')) {
        return false;
    }
    if (!DBUtil::changeTable('Multisites_access')) {
        return false;
    }
    if (!DBUtil::changeTable('Multisites_models')) {
        return false;
    }
    if (!DBUtil::changeTable('Multisites_sitesModules')) {
        return false;
    }
    return true;
}
