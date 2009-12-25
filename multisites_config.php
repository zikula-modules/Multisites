<?php
// DON'T CHANGE - start
$siteDNS = (isset($_GET['siteDNS']) ? DataUtil::formatForOS($_GET['siteDNS']) : null);
// DON'T CHANGE - end


// sites databases connection. The dbname is a variable that depends on the site that it being consulted
$PNConfig['DBInfo']['Multisites']['dbhost'] = '$dbhost'; // sample value
$PNConfig['DBInfo']['Multisites']['dbuname'] = '$dbuname';
$PNConfig['DBInfo']['Multisites']['dbpass'] = '$dbpass';
$PNConfig['DBInfo']['Multisites']['dbname'] = ''; // sample value
$PNConfig['DBInfo']['Multisites']['encoded'] = 1;
$PNConfig['DBInfo']['Multisites']['pconnect'] = 0;

// multi Zikula information
$PNConfig['Multisites']['multi'] = 0; // inform about if it is a multiple Zikula installation
$PNConfig['Multisites']['mainSiteURL'] = '$mainSiteURL'; // name used for the main site where the module Multisites is allowed
$PNConfig['Multisites']['siteDNSEndText'] = '$siteDNSEndText'; // Internet address http://host.domain/siteDNS/this_parameter


// site information
$PNConfig['Multisites']['filesRealPath'] = '$filesRealPath'; // path of the site files root in server
$PNConfig['Multisites']['siteTempFilesFolder'] = '$siteTempFilesFolder'; // name for the temporal files' folder
$PNConfig['Multisites']['siteFilesFolder'] = '$siteFilesFolder'; // name for the files' folder
$PNConfig['Multisites']['wwwroot'] = '$wwwroot'; // set the root for the multizikula installation
$PNConfig['Multisites']['siteDNS'] = '$basePath' . '/' . $siteDNS . '/' . $PNConfig['Multisites']['siteDNSEndText'];

//****** DON'T CHANGE AFTER THIS LINE *******
if ($siteDNS == $PNConfig['Multisites']['mainSiteURL'] || $PNConfig['Multisites']['multi'] == 0) {
    // it is the main site or it is not a multiple installation. Any more information it is needed
    return;
}
$siteInfo = array();
// check if site dns is received via the url
if (isset($siteDNS) && $siteDNS != null) {
    // the site dns has been received
    // check if site cookie exists
    if (isset($_COOKIE['site'])) {
        $siteArray = $_COOKIE['site'];
        $signingKey = md5($GLOBALS['PNConfig']['DBInfo']['default']['dbhost'] . $GLOBALS['PNConfig']['DBInfo']['default']['dbuname'] . $GLOBALS['PNConfig']['DBInfo']['default']['dbpass']);
        $signedData = unserialize($siteArray);
        // check for a signed cookie
        if (md5($signedData['content'] . $signingKey) != $signedData['signature']) {
            header('HTTP/1.0 404 Not Found');
            die('ERROR: File has been altered.');
        }
        if ($siteDNS == $signedData['content']) {
            $siteInfo = $signedData['content'];
        } else {
            // site cookie doesn't exists
            // get site information from database
            $siteInfo = getSiteInfo($siteDNS);
        }
    } else {
        // site cookie doesn't exists
        // get site information from database
        $siteInfo = getSiteInfo($siteDNS);
    }
} else {
    header('location: ' . $PNConfig['Multisites']['wwwroot'] . '/' . $PNConfig['Multisites']['mainSiteURL'] . '/' . $PNConfig['Multisites']['siteDNSEndText']);
    exit();
}

if ($siteInfo == '') {
    // if the site doesn't exists user is sended to an error page
    header('location: ' . $PNConfig['Multisites']['wwwroot'] . '/' . 'error.php?s=' . $PNConfig['Multisites']['siteDNSEndText'] . '&dns=' . $siteDNS);
    exit();
}

//switch the site dbname and the site temporal folder
$PNConfig['DBInfo']['default']['dbname'] = $siteInfo;
$PNConfig['System']['temp'] = $PNConfig['Multisites']['filesRealPath'] . '/' . $siteInfo . '/' . $PNConfig['Multisites']['siteTempFilesFolder'];

/**
 * Gets site informaction. This function is used once for each site connection
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	the site DNS that have to be returned
 * @return:	The siteDNS and the some config parameters (because it is necessary a connection to the main site and some parameters had loosed)
 */
function getSiteInfo($site)
{
    $url = $GLOBALS['PNConfig']['Multisites']['wwwroot'] . '/' . $GLOBALS['PNConfig']['Multisites']['mainSiteURL'] . '/' . $GLOBALS['PNConfig']['Multisites']['siteDNSEndText'] . '/index.php?module=Multisites&func=getSiteAvailability&site=' . $site;
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    $siteDB = '';
    // protect in case some result is returned but it is to long to be a siteDNS
    if (strlen($buffer) > 50) {
        return $siteDB;
    }
    if (!empty($buffer)) {
        $siteDB = $buffer;
        $signingKey = md5($GLOBALS['PNConfig']['DBInfo']['default']['dbhost'] . $GLOBALS['PNConfig']['DBInfo']['default']['dbuname'] . $GLOBALS['PNConfig']['DBInfo']['default']['dbpass']);
        $signature = md5($siteDB . $signingKey);
        $data = array('content' => $siteDB, 'signature' => $signature);
        $data = serialize($data);
        setcookie('site', $data, 0, '/');
        //NOT IMPLEMENTED YET. HERE WE CAN GET SITE ACCESS INFORMATION FOR STADISTICAL PROPOSALS
    //registerNewAccess($siteInfo['siteDNS'], time(), $_SERVER['REMOTE_ADDR']);
    }
    return $siteDB;
}

