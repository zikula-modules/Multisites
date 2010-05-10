<?php
// DON'T CHANGE - start
$siteDNS = $_SERVER['HTTP_HOST'];
// DON'T CHANGE - end

$ZConfig['DBInfo']['Multisites']['dbtabletype'] = 'myisam';
$ZConfig['DBInfo']['Multisites']['dbcharset'] = 'utf8';
$ZConfig['DBInfo']['Multisites']['dbcollate'] = 'utf8_general_ci';

// multi Zikula information
$ZConfig['Multisites']['multi'] = 0; // inform about if it is a multiple Zikula installation
$ZConfig['Multisites']['mainSiteURL'] = '$mainSiteURL'; // name used for the main site where the module Multisites is allowed
$ZConfig['Multisites']['filesRealPath'] = '$filesRealPath'; // path of the site files root in server
$ZConfig['Multisites']['siteTempFilesFolder'] = '$siteTempFilesFolder'; // name for the temporal files' folder
$ZConfig['Multisites']['siteFilesFolder'] = '$siteFilesFolder'; // name for the files' folder
$ZConfig['Multisites']['wwwroot'] = '$wwwroot'; // set the root for the multizikula installation
$ZConfig['Multisites']['basedOnDomains'] = 1;

//****** DON'T CHANGE AFTER THIS LINE *******
if ($siteDNS == $ZConfig['Multisites']['mainSiteURL'] || $ZConfig['Multisites']['multi'] == 0) {
    // it is the main site or it is not a multiple installation. Any more information it is needed
    return;
}
// get site database connection information
include_once('multisites_dbconfig.php');
if (!$databaseArray[$siteDNS] ||
     $databaseArray[$siteDNS]['siteDBName'] == '' ||
     $databaseArray[$siteDNS]['siteDBUname'] == '' ||
     $databaseArray[$siteDNS]['siteDBPass'] == '' ||
     $databaseArray[$siteDNS]['siteDBType'] == '' ||
     $databaseArray[$siteDNS]['siteDBHost'] == '' ||
     $databaseArray[$siteDNS]['siteDBPrefix'] == '') {
    // if the site doesn't exists user is sended to an error page
    header('location: ' . $ZConfig['Multisites']['wwwroot'] . '/' . 'error.php?s=' . $ZConfig['Multisites']['siteDNSEndText'] . '&dns=' . $siteDNS);
    exit();
}
$siteDBType = $databaseArray[$siteDNS]['siteDBType'];
$siteDBUname = $databaseArray[$siteDNS]['siteDBUname'];
$siteDBPass = $databaseArray[$siteDNS]['siteDBPass'];
$siteDBHost = $databaseArray[$siteDNS]['siteDBHost'];
$siteDBName = $databaseArray[$siteDNS]['siteDBName'];
$siteDBPrefix = $databaseArray[$siteDNS]['siteDBPrefix'];
// set the correct connection values to site 
$ZConfig['DBInfo']['default']['dsn'] = "$siteDBType://$siteDBUname:$siteDBPass@$siteDBHost/$siteDBName";
$ZConfig['System']['prefix'] = $siteDBPrefix;
$ZConfig['System']['temp'] = $ZConfig['Multisites']['filesRealPath'] . '/' . $siteDBName . '/' . $ZConfig['Multisites']['siteTempFilesFolder'];