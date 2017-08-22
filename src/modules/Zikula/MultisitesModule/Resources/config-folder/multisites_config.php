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
$ZConfig['Multisites']['enabled'] = false; // flag defining whether if multiple Zikula sites are enabled
$ZConfig['Multisites']['mainsiteurl'] = '$mainsiteurl'; // name used for the main site where the Multisites module is allowed
$ZConfig['Multisites']['files_real_path'] = '$files_real_path'; // path of the site files root in server
$ZConfig['Multisites']['site_temp_files_folder'] = '$site_temp_files_folder'; // name of temporary files folder
$ZConfig['Multisites']['site_files_folder'] = '$site_files_folder'; // name of files folder
$ZConfig['Multisites']['wwwroot'] = '$wwwroot'; // root path for the Multisites installation
$ZConfig['Multisites']['based_on_domains'] = 1;
$ZConfig['Multisites']['sitedns'] = '';
$ZConfig['Multisites']['sitealias'] = 'main';

//****** DON'T CHANGE AFTER THIS LINE *******
if ($sitedns == $ZConfig['Multisites']['mainsiteurl'] || true !== $ZConfig['Multisites']['enabled']) {
    // it is the main site or Multisites is disabled. No more information required.
    return;
}

// get database connection information for the current site
include_once 'multisites_dbconfig.php';
if (!isset($databaseArray[$sitedns]) ||
     !is_array($databaseArray[$sitedns]) ||
     !isset($databaseArray[$sitedns]['dbName']) || empty($databaseArray[$sitedns]['dbName']) ||
     !isset($databaseArray[$sitedns]['dbUser']) || empty($databaseArray[$sitedns]['dbUser']) ||
     !isset($databaseArray[$sitedns]['dbPass']) || empty($databaseArray[$sitedns]['dbPass']) ||
     !isset($databaseArray[$sitedns]['dbType']) || empty($databaseArray[$sitedns]['dbType']) ||
     !isset($databaseArray[$sitedns]['dbHost']) || empty($databaseArray[$sitedns]['dbHost'])
    ) {
    // if the site doesn't exist the user is sent to the main page
    header("HTTP/1.1 301 Moved Permanently");
    header('location: http://' . $ZConfig['Multisites']['mainsiteurl']);
    exit();
}

$siteDbData = $databaseArray[$sitedns];
$siteAlias = $siteDbData['alias'];

$tempFolder = $ZConfig['Multisites']['files_real_path'] . '/';
$tempFolder .= $siteAlias . '/';
$tempFolder .= $ZConfig['Multisites']['site_temp_files_folder'];

// set the correct connection values for this site
$ZConfig['DBInfo']['databases']['default']['host'] = $siteDbData['dbHost'];
$ZConfig['DBInfo']['databases']['default']['user'] = $siteDbData['dbUser'];
$ZConfig['DBInfo']['databases']['default']['password'] = $siteDbData['dbPass'];
$ZConfig['DBInfo']['databases']['default']['dbname'] = $siteDbData['dbName'];
$ZConfig['DBInfo']['databases']['default']['dbdriver'] = $siteDbData['dbType'];
$ZConfig['System']['prefix'] = '';
$ZConfig['System']['temp'] = $tempFolder;
$ZConfig['Multisites']['sitealias'] = $siteAlias;
