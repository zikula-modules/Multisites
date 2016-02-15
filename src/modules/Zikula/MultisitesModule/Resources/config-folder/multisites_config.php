<?php
// DON'T CHANGE - start
$sitedns = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
if (is_null($sitedns)) {
    return;
}
// DON'T CHANGE - end

$ZConfig['DBInfo']['Multisites']['dbtabletype'] = 'myisam';
$ZConfig['DBInfo']['Multisites']['dbcharset'] = 'utf8';
$ZConfig['DBInfo']['Multisites']['dbcollate'] = 'utf8_general_ci';

// Zikula Multisites information
$ZConfig['Multisites']['multisites.enabled'] = 0; // flag defining whether if multiple Zikula sites are enabled
$ZConfig['Multisites']['multisites.mainsiteurl'] = '$mainsiteurl'; // name used for the main site where the Multisites module is allowed
$ZConfig['Multisites']['multisites.files_real_path'] = '$files_real_path'; // path of the site files root in server
$ZConfig['Multisites']['multisites.site_temp_files_folder'] = '$site_temp_files_folder'; // name of temporary files folder
$ZConfig['Multisites']['multisites.site_files_folder'] = '$site_files_folder'; // name of files folder
$ZConfig['Multisites']['multisites.wwwroot'] = '$wwwroot'; // root path for the Multisites installation
$ZConfig['Multisites']['multisites.based_on_domains'] = 1;
$ZConfig['Multisites']['multisites.sitedns'] = '';
$ZConfig['Multisites']['multisites.sitealias'] = 'main';

//****** DON'T CHANGE AFTER THIS LINE *******
if ($sitedns == $ZConfig['Multisites']['multisites.mainsiteurl'] || $ZConfig['Multisites']['multisites.enabled'] == 0) {
    // it is the main site or Multisites is disabled. No more information required.
    return;
}
// get database connection information for the current site
include_once('multisites_dbconfig.php');
if (!isset($databaseArray[$sitedns]) ||
     !is_array($databaseArray[$sitedns]) ||
     !isset($databaseArray[$sitedns]['dbname']) || empty($databaseArray[$sitedns]['dbname']) ||
     !isset($databaseArray[$sitedns]['dbuname']) || empty($databaseArray[$sitedns]['dbuname']) ||
     !isset($databaseArray[$sitedns]['dbpass']) || empty($databaseArray[$sitedns]['dbpass']) ||
     !isset($databaseArray[$sitedns]['dbtype']) || empty($databaseArray[$sitedns]['dbtype']) ||
     !isset($databaseArray[$sitedns]['dbhost']) || empty($databaseArray[$sitedns]['dbhost'])
    ) {
    // if the site doesn't exist the user is sent to the main page
    header("HTTP/1.1 301 Moved Permanently");
    header('location: http://' . $ZConfig['Multisites']['multisites.mainsiteurl']);
    exit();
}

$siteDbData = $databaseArray[$sitedns];
$siteAlias = $siteDbData['alias'];

$tempFolder = $ZConfig['Multisites']['multisites.files_real_path'] . '/';
$tempFolder .= $siteAlias . '/';
$tempFolder .= $ZConfig['Multisites']['multisites.site_temp_files_folder'];

// set the correct connection values for this site
$ZConfig['DBInfo']['databases']['default']['host'] = $siteDbData['dbhost'];
$ZConfig['DBInfo']['databases']['default']['user'] = $siteDbData['dbuname'];
$ZConfig['DBInfo']['databases']['default']['password'] = $siteDbData['dbpass'];
$ZConfig['DBInfo']['databases']['default']['dbname'] = $siteDbData['dbname'];
$ZConfig['DBInfo']['databases']['default']['dbdriver'] = $siteDbData['dbtype'];
$ZConfig['System']['prefix'] = '';
$ZConfig['System']['temp'] = $tempFolder;
$ZConfig['Multisites']['multisites.sitealias'] = $siteAlias;
