<?php
/**
 * Show the list of sites created
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	The list of sites
 */
function Multisites_admin_main($args)
{
    $letter = FormUtil::getPassedValue('letter', isset($args['letter']) ? $args['letter'] : null, 'GET');
    $startnum = FormUtil::getPassedValue('startnum', isset($args['startnum']) ? $args['startnum'] : 1, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $itemsperpage = 10;
    // get sites
    $sites = pnModAPIFunc('Multisites', 'user', 'getAllSites',
                           array('letter' => $letter,
                                 'itemsperpage' => $itemsperpage,
                                 'startnum' => $startnum));
    // get total sites
    if ($letter == null) {
        $numSites = count(pnModAPIFunc('Multisites', 'user', 'getAllSites'));
    } else {
        $numSites = count(pnModAPIFunc('Multisites', 'user', 'getAllSites',
                                        array('letter' => $letter)));
    }
    $pager = array('numitems' => $numSites,
                   'itemsperpage' => $itemsperpage);
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('sites', $sites);
    $pnRender->assign('pager', $pager);
    $pnRender->assign('wwwroot', $GLOBALS['ZConfig']['Multisites']['wwwroot']);
    $pnRender->assign('siteDNSEndText', $GLOBALS['ZConfig']['Multisites']['siteDNSEndText']);
    $pnRender->assign('basedOnDomains', $GLOBALS['ZConfig']['Multisites']['basedOnDomains']);
    return $pnRender->fetch('Multisites_admin_main.htm');
}

/**
 * Show the form needed to create a new instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	The form needed to create a new instance
 */
function Multisites_admin_newInstance()
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // get all the models for new instances
    $models = pnModAPIFunc('Multisites', 'user', 'getAllModels');
    if (!$models) {
        LogUtil::registerError(__('There is not any module defined', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('models', $models);
    return $pnRender->fetch('Multisites_admin_new.htm');
}

/**
 * Create a new instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance properties received from the creation form
 * @return:	Returns user to administrator main page
 */
function Multisites_admin_createInstance($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceName = FormUtil::getPassedValue('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null, 'POST');
    $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
    $siteName = FormUtil::getPassedValue('siteName', isset($args['siteName']) ? $args['siteName'] : null, 'POST');
    $siteAdminName = FormUtil::getPassedValue('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null, 'POST');
    $siteAdminPwd = FormUtil::getPassedValue('siteAdminPwd', isset($args['siteAdminPwd']) ? $args['siteAdminPwd'] : null, 'POST');
    $siteAdminRealName = FormUtil::getPassedValue('adminRealName', isset($args['adminRealName']) ? $args['adminRealName'] : null, 'POST');
    $siteAdminEmail = FormUtil::getPassedValue('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null, 'POST');
    $siteCompany = FormUtil::getPassedValue('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null, 'POST');
    $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
    $siteDBName = FormUtil::getPassedValue('siteDBName', isset($args['siteDBName']) ? $args['siteDBName'] : null, 'POST');
    $siteDBUname = FormUtil::getPassedValue('siteDBUname', isset($args['siteDBUname']) ? $args['siteDBUname'] : null, 'POST');
    $siteDBPass = FormUtil::getPassedValue('siteDBPass', isset($args['siteDBPass']) ? $args['siteDBPass'] : null, 'POST');
    $siteDBHost = FormUtil::getPassedValue('siteDBHost', isset($args['siteDBHost']) ? $args['siteDBHost'] : null, 'POST');
    $siteDBType = FormUtil::getPassedValue('siteDBType', isset($args['siteDBType']) ? $args['siteDBType'] : null, 'POST');
    $createDB = FormUtil::getPassedValue('createDB', isset($args['createDB']) ? $args['createDB'] : 0, 'POST');
    $siteInitModel = FormUtil::getPassedValue('siteInitModel', isset($args['siteInitModel']) ? $args['siteInitModel'] : null, 'POST');
    $active = FormUtil::getPassedValue('active', isset($args['active']) ? $args['active'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'main'));
    }
    // needed arguments
    if ($siteDNS == null || $siteDBName == null || $siteDBUname == null || $siteDBPass == null || $siteDBHost == null || $siteDBType == null || $siteInitModel == null || $siteInitModel == '') {
        LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // check that the siteDNS exists and if it exists return error
    if (pnModAPIFunc('Multisites', 'user', 'getSiteInfo',
                      array('site' => $siteDNS))) {
        LogUtil::registerError(__('This site just exists. The site DNS must be unique.', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // get model information
    $model = pnModAPIFunc('Multisites', 'user', 'getModel',
                           array('modelName' => $siteInitModel));
    if ($model == false) {
        LogUtil::registerError(__('Model note found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    if ($createDB == 1) {
        // create a new database if it doesn't exist
        if (!pnModAPIFunc('Multisites', 'admin', 'createDB',
                          array('siteDBName' => $siteDBName,
                                'siteDBUname' => $siteDBUname,
                                'siteDBPass' => $siteDBPass,
                                'siteDBType' => $siteDBType,
                                'siteDBHost' => $siteDBHost))) {
            LogUtil::registerError(__('The database creation has failed', $dom));
            return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
        }
    }
    // created the database tables based on the model file
    if (!pnModAPIFunc('Multisites', 'admin', 'createTables',
                       array('fileName' => $model['fileName'],
                             'siteDBName' => $siteDBName,
                             'siteDBPass' => $siteDBPass,
                             'siteDBUname' => $siteDBUname,
                             'siteDBHost' => $siteDBHost,
                             'siteDBType' => $siteDBType))) {
        LogUtil::registerError(__('The tables creation has failed', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }   
    // update instance values like admin name, admin password, cookie name, site name...
    if (!pnModAPIFunc('Multisites', 'admin', 'updateConfigValues',
                       array('siteAdminName' => $siteAdminName,
                             'siteAdminPwd' => $siteAdminPwd,
                             'siteAdminEmail' => $siteAdminEmail,
                             'siteName' => $siteName,
                             'siteDBName' => $siteDBName,
                             'siteDBPass' => $siteDBPass,
                             'siteDBUname' => $siteDBUname,
                             'siteDBHost' => $siteDBHost,
                             'siteDBType' => $siteDBType))) {
        LogUtil::registerError(__('The site configuration has failed.', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // modify multisites_dbconfig files
    if (!pnModAPIFunc('Multisites', 'admin', 'updateDBConfig',
                       array('siteDNS' => $siteDNS,
                             'siteDBName' => $siteDBName,
                             'siteDBPass' => $siteDBPass,
                             'siteDBUname' => $siteDBUname,
                             'siteDBHost' => $siteDBHost,
                             'siteDBType' => $siteDBType))) {
        LogUtil::registerError(__('Error updating the file multisites_dbconfig.php.', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // create the instance directories
    $initDir = $GLOBALS['ZConfig']['Multisites']['filesRealPath'] . '/' . $siteDBName;
    $initTemp = $initDir . $GLOBALS['ZConfig']['Multisites']['siteTempFilesFolder'];
    $dirArray = array($initDir,
				      $initDir . $GLOBALS['ZConfig']['Multisites']['siteFilesFolder'],
				      $initTemp,
				      $initTemp . '/error_logs',
				      $initTemp . '/idsTmp',
				      $initTemp . '/purifierCache',
			          $initTemp . '/Renderer_cache',
		              $initTemp . '/Renderer_compiled',
				      $initTemp . '/Theme_cache',
				      $initTemp . '/Theme_compiled',
                      $initTemp . '/Theme_Config');
				      $modelFoldersArray = explode(',', $model['folders']);
    foreach ($modelFoldersArray as $folder) {
        if ($folder != '') {
            $dirArray[] = $initDir . $GLOBALS['ZConfig']['Multisites']['siteFilesFolder'] . '/' . trim($folder);
        }
    }
    foreach ($dirArray as $dir) {
        if (!mkdir($dir, 0777)) {
            LogUtil::registerError(__('Error creating site directories', $dom) . ': ' . $dir);
            return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
        }
    }
    // create a .htaccess file in the temporal folder
    $tempAccessFileContent = pnModGetVar('Multisites', 'tempAccessFileContent');
    if ($tempAccessFileContent != '') {
        // create file
        $file = $initTemp . '/.htaccess';
        file_put_contents($file, $tempAccessFileContent);
    }
    // create the instance
    $created = pnModAPIFunc('Multisites', 'admin', 'createInstance',
                             array('instanceName' => $instanceName,
    						        'description' => $description,
    						        'siteName' => $siteName,
    						        'siteAdminName' => $siteAdminName,
    						        'siteAdminPwd' => $siteAdminPwd,
    						        'siteAdminRealName' => $siteAdminRealName,
    						        'siteAdminEmail' => $siteAdminEmail,
    						        'siteCompany' => $siteCompany,
    						        'siteDNS' => $siteDNS,
                                    'siteDBName' => $siteDBName,
                                    'siteDBUname' => $siteDBUname,
                                    'siteDBPass' => $siteDBPass,
                                    'siteDBHost' => $siteDBHost,
                                    'siteDBType' => $siteDBType,
    						        'siteInitModel' => $siteInitModel,
    						        'active' => $active));
    if ($created == false) {
        LogUtil::registerError(__('Creation instance error', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    //******* PNN *******
    // save the site module in database
    $siteModules = pnModAPIFunc('Multisites', 'admin', 'saveSiteModules',
                                 array('instanceId' => $created));
    //*******
    // success
    LogUtil::registerStatus(__('A new instance has been created', $dom));
    //  redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
}

/**
 * Delete an instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity
 * @return:	Returns true if success and false otherwise
 */
function Multisites_admin_deleteInstance($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GETPOST');
    $confirmation = FormUtil::getPassedValue('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null, 'POST');
    $deleteDB = FormUtil::getPassedValue('deleteDB', isset($args['deleteDB']) ? $args['deleteDB'] : 0, 'POST');
    $deleteFiles = FormUtil::getPassedValue('deleteFiles', isset($args['deleteFiles']) ? $args['deleteFiles'] : 0, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // get site information
    $site = pnModAPIFunc('Multisites', 'user', 'getSite',
                          array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    if ($confirmation == null) {
        // create output object
        $pnRender = pnRender::getInstance('Multisites', false);
        $pnRender->assign('instance', $site);
        return $pnRender->fetch('Multisites_admin_deleteInstance.htm');
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'main'));
    }
    if ($deleteDB == 1) {
        // delete the instance database
        if (!pnModAPIFunc('Multisites', 'admin', 'deleteDatabase',
                           array('siteDBName' => $site['siteDBName'],
                                 'siteDBHost' => $site['siteDBHost'],
                                 'siteDBType' => $site['siteDBType'],
                                 'siteDBUname' => $site['siteDBUname'],
                                 'siteDBPass' => $site['siteDBPass']))) {
            LogUtil::registerError(__('Error deleting database', $dom));
        }
    }
    if ($deleteFiles == 1) {
        // delete the instance files and directoris
        pnModAPIFunc('Multisites', 'admin', 'deleteDir',
                      array('dirName' => $GLOBALS['ZConfig']['Multisites']['filesRealPath'] . '/' . $site['siteDBName']));
    }
    // delete instance information
    if (!pnModAPIFunc('Multisites', 'admin', 'deleteInstance',
                       array('instanceId' => $site['instanceId']))) {
        LogUtil::registerError(__('The instance deletion has failed', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // modify multisites_dbconfig files
    if (!pnModAPIFunc('Multisites', 'admin', 'updateDBConfig',
                       array('siteDNS' => $siteDNS,
                             'siteDBName' => $siteDBName,
                             'siteDBPass' => $siteDBPass,
                             'siteDBUname' => $siteDBUname,
                             'siteDBHost' => $siteDBHost,
                             'siteDBType' => $siteDBType))) {
        LogUtil::registerError(__('Error updating the file multisites_dbconfig.php.', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // success
    LogUtil::registerStatus(__('The instance has been deleted', $dom));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
}































































































/**
 * Edit an instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: The instance identity
 * @return:	The form fields prepared to edit
 */
function Multisites_admin_edit($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // get site information
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('site', $site);
    return $pnRender->fetch('Multisites_admin_edit.htm');
}

/**
 * Update and instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: The instance information
 * @return:	Return to admin main page
 */
function Multisites_admin_update($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
    $instanceName = FormUtil::getPassedValue('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null, 'POST');
    $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
    $siteAdminRealName = FormUtil::getPassedValue('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null, 'POST');
    $siteAdminEmail = FormUtil::getPassedValue('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null, 'POST');
    $siteCompany = FormUtil::getPassedValue('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null, 'POST');
    $active = FormUtil::getPassedValue('active', isset($args['active']) ? $args['active'] : 0, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'main'));
    }
    // get site information
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    $edited = pnModAPIFunc('Multisites', 'admin', 'updateInstance', array(
        'instanceId' => $instanceId,
        'items' => array('instanceName' => $instanceName,
                            'description' => $description,
                            'siteAdminRealName' => $siteAdminRealName,
                            'siteAdminEmail' => $siteAdminEmail,
                            'siteCompany' => $siteCompany,
                            'active' => $active)));
    if (!$edited) {
        LogUtil::registerError(__('Error editing instance', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // success
    LogUtil::registerStatus(__('The site information has been edited', $dom));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
}

/**
 * Edit a model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: The model identity
 * @return:	The form fields prepared to edit
 */
function Multisites_admin_editModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // get model information
    $model = pnModAPIFunc('Multisites', 'user', 'getModelById', array('modelId' => $modelId));
    if ($model == false) {
        LogUtil::registerError(__('Model not found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('model', $model);
    return $pnRender->fetch('Multisites_admin_editModel.htm');
}

/**
 * Update and instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: The instance information
 * @return:	Return to admin main page
 */
function Multisites_admin_updateModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');
    $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'POST');
    $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
    $folders = FormUtil::getPassedValue('folders', isset($args['folders']) ? $args['folders'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // get model information
    $model = pnModAPIFunc('Multisites', 'user', 'getModelById', array('modelId' => $modelId));
    if ($model == false) {
        LogUtil::registerError(__('Model not found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    $edited = pnModAPIFunc('Multisites', 'admin', 'updateModel', array('instanceId' => $instanceId,
                                                                        'items' => array('modelName' => $modelName,
                                                                                            'description' => $description,
                                                                                            'folders' => $folders)));
    if (!$edited) {
        LogUtil::registerError(__('Error editing model', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // success
    LogUtil::registerStatus(__('Model edited', $dom));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
}

/**
 * Show the form with the configurable parameters for the module
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	The form fields
 */
function Multisites_admin_config()
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('modelsFolder', pnModGetVar('Multisites', 'modelsFolder'));
    $pnRender->assign('tempAccessFileContent', pnModGetVar('Multisites', 'tempAccessFileContent'));
    $pnRender->assign('globalAdminName', pnModGetVar('Multisites', 'globalAdminName'));
    $pnRender->assign('globalAdminPassword', pnModGetVar('Multisites', 'globalAdminPassword'));
    $pnRender->assign('globalAdminemail', pnModGetVar('Multisites', 'globalAdminemail'));
    return $pnRender->fetch('Multisites_admin_config.htm');
}

/**
 * Modify module configuration
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The module parameter values
 * @return:	return user to config page
 */
function Multisites_admin_updateConfig($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelsFolder = FormUtil::getPassedValue('modelsFolder', isset($args['modelsFolder']) ? $args['modelsFolder'] : null, 'POST');
    $tempAccessFileContent = FormUtil::getPassedValue('tempAccessFileContent', isset($args['tempAccessFileContent']) ? $args['tempAccessFileContent'] : null, 'POST');
    $globalAdminName = FormUtil::getPassedValue('globalAdminName', isset($args['globalAdminName']) ? $args['globalAdminName'] : null, 'POST');
    $globalAdminPassword = FormUtil::getPassedValue('globalAdminPassword', isset($args['globalAdminPassword']) ? $args['globalAdminPassword'] : null, 'POST');
    $globalAdminemail = FormUtil::getPassedValue('globalAdminemail', isset($args['globalAdminemail']) ? $args['globalAdminemail'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'main'));
    }
    pnModSetVar('Multisites', 'modelsFolder', $modelsFolder);
    pnModSetVar('Multisites', 'tempAccessFileContent', $tempAccessFileContent);
    pnModSetVar('Multisites', 'globalAdminName', $globalAdminName);
    pnModSetVar('Multisites', 'globalAdminPassword', $globalAdminPassword);
    pnModSetVar('Multisites', 'globalAdminemail', $globalAdminemail);
    // success
    LogUtil::registerStatus(__('The module configuration has been modified', $dom));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'config'));
}

/**
 * Show the models availables
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	The list of models
 */
function Multisites_admin_manageModels()
{
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $models = pnModAPIFunc('Multisites', 'user', 'getAllModels');
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('modelsArray', $models);
    return $pnRender->fetch('Multisites_admin_manageModels.htm');
}

/**
 * Show the form needed to create a new model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	Form fields
 */
function Multisites_admin_createNewModel()
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // check if the models folders exists and it is writeable
    $path = pnModGetVar('Multisites', 'modelsFolder');
    // check if models folders is exists
    if (!file_exists($path)) {
        LogUtil::registerError(__('The models folder does not exists', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // check if models folders is writeable
    if (!is_writeable($path)) {
        LogUtil::registerError(__('The models folder is not writeable', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    return $pnRender->fetch('Multisites_admin_newModel.htm');
}

/**
 * Create a new model and upload the model SQL file to the server
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model properties and the file with the SQL
 * @return:	Returns true if success and false otherwise
 */
function Multisites_admin_createModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'POST');
    $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
    $folders = FormUtil::getPassedValue('folders', isset($args['folders']) ? $args['folders'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'main'));
    }
    // gets the attached file array
    $file = $_FILES['modelFile'];
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
    // check if the models folders exists and it is writeable
    $path = pnModGetVar('Multisites', 'modelsFolder');
    if (!is_writeable($path)) {
        LogUtil::registerError(__('The models folder does not exists', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // check if the extension of the file is allowed
    $file_extension = strtolower(substr(strrchr($file['name'], "."), 1));
    if ($file_extension != 'txt' && $file_extension != 'sql') {
        LogUtil::registerError(__('The model file extension is not allowed. The only allowed extensions are txt and sql', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // prepare file name
    // replace spaces with _
    // check if file name exists into the folder. In this case change the name
    $fileName = str_replace(' ', '_', $file['name']);
    $fitxer = $fileName;
    $i = 1;
    while (file_exists($path . '/' . $fileName)) {
        $fileName = substr($fitxer, 0, strlen($fitxer) - strlen($file_extension) - (1)) . $i . '.' . $file_extension;
        $i++;
    }
    // update the file
    if (!move_uploaded_file($file['tmp_name'], $path . '/' . $fileName)) {
        LogUtil::registerError(__(' Error updating file', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    //Update model information
    $created = pnModAPIFunc('Multisites', 'admin', 'createModel', array('modelName' => $modelName,
                                                                        'description' => $description,
                                                                        'fileName' => $fileName,
                                                                        'folders' => $folders));
    if (!$created) {
        // delete the model file
        unlink($path . '/' . $fileName);
        LogUtil::registerError(__('Error creating model', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // success
    LogUtil::registerStatus(__('A new model has been created', $dom));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
}

/**
 * Show the modules available for a site and allow to manage this feature
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity
 * @return:	The list of modules and its state in the site
 */
function Multisites_admin_siteElements($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GETPOST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // get all the modules located in modules folder
    $modules = pnModAPIFunc('Modules', 'admin', 'getfilemodules');
    sort($modules);
    // get all the modules available in site
    $siteModules = pnModAPIFunc('Multisites', 'admin', 'getAllSiteModules', array('instanceId' => $instanceId));
    foreach ($modules as $mod) {
        if ($mod['type'] != 3) {
            // if module exists in instance database
            $available = (array_key_exists($mod['name'], $siteModules)) ? 1 : 0;
            $icons = pnModFunc('Multisites', 'admin', 'siteElementsIcons', array('instanceId' => $instanceId,
                                                                                    'name' => $mod['name'],
                                                                                    'available' => $available,
                                                                                    'siteModules' => $siteModules));
            $modulesArray[] = array('id' => $mod['id'],
                                    'name' => $mod['name'],
                                    'version' => $mod['version'],
                                    'description' => $mod['description'],
                                    'icons' => $icons);
        }
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('site', $site);
    $pnRender->assign('modules', $modulesArray);
    return $pnRender->fetch('Multisites_admin_siteElements.htm');
}

/**
 * Delete a model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model identity
 * @return:	Redirect user to the models page
 */
function Multisites_admin_deleteModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'GETPOST');
    $confirmation = FormUtil::getPassedValue('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $model = pnModAPIFunc('Multisites', 'user', 'getModelById', array('modelId' => $modelId));
    if ($model == false) {
        LogUtil::registerError(__('Model not found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    if ($confirmation == null) {
        // create output object
        $pnRender = pnRender::getInstance('Multisites', false);
        $pnRender->assign('model', $model);
        return $pnRender->fetch('Multisites_admin_deleteModel.htm');
    }
    // confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('Multisites', 'admin', 'main'));
    }
    // delete file
    $deleted = unlink(pnModGetVar('Multisites', 'modelsFolder') . '/' . $model['fileName']);
    // delete model information
    if (!pnModAPIFunc('Multisites', 'admin', 'deleteModel', array('modelId' => $model['modelId']))) {
        LogUtil::registerError(__('Error deleting the model', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
    }
    // success
    LogUtil::registerStatus(__('Model deleted', $dom));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'manageModels'));
}

/**
 * Load the icons that identify the modules availability for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the modules state
 * @return:	Returns the needed icons
 */
function Multisites_admin_siteElementsIcons($args)
{
    $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
    $available = FormUtil::getPassedValue('available', isset($args['available']) ? $args['available'] : null, 'POST');
    $siteModules = FormUtil::getPassedValue('siteModules', isset($args['siteModules']) ? $args['siteModules'] : null, 'POST');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('name', $name);
    $pnRender->assign('available', $available);
    $pnRender->assign('siteModules', $siteModules);
    $pnRender->assign('instanceId', $instanceId);
    return $pnRender->fetch('Multisites_admin_siteElementsIcons.htm');
}

/**
 * Show the themes available for a site and allow to manage this feature
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity
 * @return:	The list of themes and its state in the site
 */
function Multisites_admin_siteThemes($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GETPOST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // get all the themes available in themes directory
    $themes = pnModAPIFunc('Multisites', 'admin', 'getAllThemes');
    // get all the themes  inserted in site or instance database
    $siteThemes = pnModAPIFunc('Multisites', 'admin', 'getAllSiteThemes', array('instanceId' => $instanceId));
    $defaultTheme = pnModAPIFunc('Multisites', 'admin', 'getSiteDefaultTheme', array('instanceId' => $instanceId));
    $pos = strpos($defaultTheme, '"');
    $defaultTheme = substr($defaultTheme, $pos + 1, -2);
    foreach ($themes as $theme) {
        // if module exists in instance database
        $available = (array_key_exists($theme['name'], $siteThemes)) ? 1 : 0;
        $isDefaultTheme = (strtolower($theme['name']) == strtolower($defaultTheme)) ? 1 : 0;
        $icons = pnModFunc('Multisites', 'admin', 'siteThemesIcons', array('instanceId' => $instanceId,
                                                                            'name' => $theme['name'],
                                                                            'available' => $available,
                                                                            'isDefaultTheme' => $isDefaultTheme,
                                                                            'siteThemes' => $siteThemes));
        $themesArray[] = array('id' => $theme['id'],
                                'name' => $theme['name'],
                                'version' => $theme['version'],
                                'description' => $theme['description'],
                                'icons' => $icons);
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('site', $site);
    $pnRender->assign('themes', $themesArray);
    return $pnRender->fetch('Multisites_admin_siteThemes.htm');
}

/**
 * Load the icons that identify the themes availability for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the modules state
 * @return:	Returns the needed icons
 */
function Multisites_admin_siteThemesIcons($args)
{
    $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'POST');
    $available = FormUtil::getPassedValue('available', isset($args['available']) ? $args['available'] : null, 'POST');
    $siteThemes = FormUtil::getPassedValue('siteThemes', isset($args['siteThemes']) ? $args['siteThemes'] : null, 'POST');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $isDefaultTheme = FormUtil::getPassedValue('isDefaultTheme', isset($args['isDefaultTheme']) ? $args['isDefaultTheme'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('name', $name);
    $pnRender->assign('available', $available);
    $pnRender->assign('isDefaultTheme', $isDefaultTheme);
    $pnRender->assign('siteThemes', $siteThemes);
    $pnRender->assign('instanceId', $instanceId);
    return $pnRender->fetch('Multisites_admin_siteThemesIcons.htm');
}

/**
 * Set a theme as default
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the theme name
 * @return:	Change the default theme
 */
function Multisites_admin_setThemeAsDefault($args)
{
    $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'GET');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $defaultTheme = pnModAPIFunc('Multisites', 'admin', 'setAsDefaultTheme', array('instanceId' => $instanceId,
                                                                                    'name' => $name));
    // redirect to the admin main page
    return pnRedirect(pnModURL('Multisites', 'admin', 'siteThemes', array('instanceId' => $instanceId)));
}

/**
 * Give access to some tools
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: Instance identity
 * @return:	The list of available tools
 */
function Multisites_admin_siteTools($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('site', $site);
    return $pnRender->fetch('Multisites_admin_siteTools.htm');
}

/**
 * Execute some actions with administration tools
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: Instance identity, tool to use
 * @return:	The list of available tools
 */
function Multisites_admin_executeSiteTool($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
    $tool = FormUtil::getPassedValue('tool', isset($args['tool']) ? $args['tool'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    switch ($tool) {
        case 'createAdministrator':
            $createAdministrator = pnModAPIFunc('Multisites', 'admin', 'createAdministrator', array('instanceId' => $instanceId));
            if ($createAdministrator) {
                LogUtil::registerStatus(__('A global administrator has been created', $dom));
            }
            break;
        case 'adminSiteControl':
            $recoverAdminSiteControl = pnModAPIFunc('Multisites', 'admin', 'recoverAdminSiteControl', array('instanceId' => $instanceId));
            if ($recoverAdminSiteControl) {
                LogUtil::registerStatus(__('The administration control had been recovered', $dom));
            }        	
        	break;
        default:
            LogUtil::registerError(__('Not tool selected', $dom));
    }
    return pnRedirect(pnModURL('Multisites', 'admin', 'siteTools', array('instanceId' => $instanceId)));
}

/**
 * Show the list of modules than can be actualized
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @return: The list of available modules
 */
function Multisites_admin_actualizer(){
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    // get all the modules located in modules folder
    $modules = pnModAPIFunc('Modules', 'admin', 'getfilemodules');
    sort($modules);
    // checks if any module needs actualization for any site
    $i = 0;
    $upgradeNeeded = false;
    foreach($modules as $module){
        // get the number of sites which have an old version
        $numberOfSites = pnModAPIFunc('Multisites', 'admin', 'getNumberOfSites', array('moduleName' => $module['name'],
                                                                                        'currentVersion' => $module['version']));
        if($numberOfSites > 0){
        	$upgradeNeeded = true;
        }
        $modules[$i]['numberOfSites'] = $numberOfSites;
        $i++;
    }
    // create output object
    $pnRender = pnRender::getInstance('Multisites', false);
    $pnRender->assign('modules', $modules);
    $pnRender->assign('upgradeNeeded', $upgradeNeeded);
    return $pnRender->fetch('Multisites_admin_actualizer.htm');
}

/**
 * Actualize the selected module
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: an array with the modules that needs actualization
 * @return: The list of available modules
 */
 function Multisites_admin_actualizeModule($args){
    $dom = ZLanguage::getModuleDomain('Multisites');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
	    (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
	    ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
        return LogUtil::registerPermissionError();
    }
    if($moduleName == null){
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    // get all the modules located in modules folder
    $modules = pnModAPIFunc('Modules', 'admin', 'getfilemodules');
    // get the module current version
    foreach($modules as $module){
    	if($module['name'] == $moduleName){
    		$moduleSelected = $module;
    		break;
    	}
    }
    $currentVersion = $moduleSelected['version'];
    // get the sites that need upgrade
    $sites = pnModAPIFunc('Multisites', 'admin', 'getSitesThatNeedUpgrade', array('moduleName' => $moduleName,
                                                                                    'currentVersion' => $currentVersion));
    if(!$sites){
    	LogUtil::registerError(__f("Not sites found that needs upgrade in module <strong>%s</strong>", $moduleName, $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'actualizer'));
    }
    
    print_r($sites);die();
}