<?php
// DON'T CHANGE - start
$sitedns = $_SERVER['HTTP_HOST'];
// DON'T CHANGE - end

$ZConfig['DBInfo']['Multisites']['dbtabletype'] = 'myisam';
$ZConfig['DBInfo']['Multisites']['dbcharset'] = 'utf8';
$ZConfig['DBInfo']['Multisites']['dbcollate'] = 'utf8_general_ci';

// multi Zikula information
$ZConfig['Multisites']['multisites.enabled'] = 0; // inform about if it is a multiple Zikula installation
$ZConfig['Multisites']['multisites.mainsiteurl'] = '$mainsiteurl'; // name used for the main site where the module Multisites is allowed
$ZConfig['Multisites']['multisites.files_real_path'] = '$files_real_path'; // path of the site files root in server
$ZConfig['Multisites']['multisites.site_temp_files_folder'] = '$site_temp_files_folder'; // name for the temporal files' folder
$ZConfig['Multisites']['multisites.site_files_folder'] = '$site_files_folder'; // name for the files' folder
$ZConfig['Multisites']['multisites.wwwroot'] = '$wwwroot'; // set the root for the multizikula installation
$ZConfig['Multisites']['multisites.based_on_domains'] = 1;
$ZConfig['Multisites']['multisites.sitedns'] = '';

//****** DON'T CHANGE AFTER THIS LINE *******
if ($sitedns == $ZConfig['Multisites']['multisites.mainsiteurl'] || $ZConfig['Multisites']['multisites.enabled'] == 0) {
    // it is the main site or it is not a multiple installation. Any more information it is needed
    return;
}
// get site database connection information
include_once('multisites_dbconfig.php');
if (!$databaseArray[$sitedns] ||
     $databaseArray[$sitedns]['siteDBName'] == '' ||
     $databaseArray[$sitedns]['siteDBUname'] == '' ||
     $databaseArray[$sitedns]['siteDBPass'] == '' ||
     $databaseArray[$sitedns]['siteDBType'] == '' ||
     $databaseArray[$sitedns]['siteDBHost'] == '' ||
     $databaseArray[$sitedns]['siteDBPrefix'] == '') {
    // if the site doesn't exists user is sended to an error page
    header('location: ' . $ZConfig['Multisites']['multisites.wwwroot'] . '/' . 'error.php?s=' . $ZConfig['Multisites']['multisites.sitednsEndText'] . '&dns=' . $sitedns);
    exit();
}
$siteDBType = $databaseArray[$sitedns]['siteDBType'];
$siteDBUname = $databaseArray[$sitedns]['siteDBUname'];
$siteDBPass = $databaseArray[$sitedns]['siteDBPass'];
$siteDBHost = $databaseArray[$sitedns]['siteDBHost'];
$siteDBName = $databaseArray[$sitedns]['siteDBName'];
$siteDBPrefix = $databaseArray[$sitedns]['siteDBPrefix'];

// set the correct connection values to site
$ZConfig['DBInfo']['databases']['default']['host'] = $siteDBHost;
$ZConfig['DBInfo']['databases']['default']['user'] = $siteDBUname;
$ZConfig['DBInfo']['databases']['default']['password'] = $siteDBPass;
$ZConfig['DBInfo']['databases']['default']['dbname'] = $siteDBName;
$ZConfig['DBInfo']['databases']['default']['dbdriver'] = $siteDBType;
$ZConfig['System']['prefix'] = $siteDBPrefix;
$ZConfig['System']['temp'] = $ZConfig['Multisites']['multisites.files_real_path'] . '/' . $siteDBName . '/' . $ZConfig['Multisites']['multisites.site_temp_files_folder'];
