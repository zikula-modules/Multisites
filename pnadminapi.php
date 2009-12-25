<?php
/**
 * Connect with an external database
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	Database name
 * @return:	Connection object
 */
function Multisites_adminapi_connectExtDB($args)
{
    $database = FormUtil::getPassedValue('database', isset($args['database']) ? $args['database'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    global $PNConfig;
    $PNConfig['DBInfo']['Multisites']['dbname'] = $database;
    //connect to database
    $connect = DBConnectionStack::init('Multisites');
    if (!$connect) {
        return false;
    }
    DBConnectionStack::popConnection();
    return $connect;
}

/**
 * Create a new instance in database
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance properties received from the creation form
 * @return:	instanceId if success and false otherwise
 */
function Multisites_adminapi_createInstance($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceName = FormUtil::getPassedValue('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null, 'POST');
    $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
    $siteName = FormUtil::getPassedValue('siteName', isset($args['siteName']) ? $args['siteName'] : null, 'POST');
    $siteAdminName = FormUtil::getPassedValue('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null, 'POST');
    $siteAdminPwd = FormUtil::getPassedValue('siteAdminPwd', isset($args['siteAdminPwd']) ? $args['siteAdminPwd'] : null, 'POST');
    $siteAdminRealName = FormUtil::getPassedValue('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null, 'POST');
    $siteAdminEmail = FormUtil::getPassedValue('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null, 'POST');
    $siteCompany = FormUtil::getPassedValue('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null, 'POST');
    $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
    $siteInitModel = FormUtil::getPassedValue('siteInitModel', isset($args['siteInitModel']) ? $args['siteInitModel'] : null, 'POST');
    $active = FormUtil::getPassedValue('active', isset($args['active']) ? $args['active'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    //Needed arguments
    if ($siteDNS == null) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $activationDate = DateUtil::getDatetime(time());
    $item = array('instanceName' => $instanceName,
			        'description' => $description,
			        'siteName' => $siteName,
			        'siteAdminName' => $siteAdminName,
			        'siteAdminPwd' => $siteAdminPwd,
			        'siteAdminRealName' => $siteAdminRealName,
			        'siteAdminEmail' => $siteAdminEmail,
			        'siteCompany' => $siteCompany,
			        'siteDNS' => $siteDNS,
			        'siteInitModel' => $siteInitModel,
			        'activationDate' => $activationDate,
			        'active' => $active);
    if (!DBUtil::insertObject($item, 'Multisites_sites', 'instanceId')) {
        return LogUtil::registerError(__('Error! Creation attempt failed.', $dom));
    }
    // Let any hooks know that we have created a new item
    pnModCallHooks('item', 'create', $item['instanceId'], array('module' => 'Multisites'));
    return $item['instanceId'];
}

/**
 * Create a new model in database
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model properties received from the creation form
 * @return:	modelId if success and false otherwise
 */
function Multisites_adminapi_createModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'POST');
    $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
    $fileName = FormUtil::getPassedValue('fileName', isset($args['fileName']) ? $args['fileName'] : null, 'POST');
    $folders = FormUtil::getPassedValue('folders', isset($args['folders']) ? $args['folders'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    //Needed arguments
    if ($modelName == null) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $item = array('modelName' => $modelName,
                    'description' => $description,
                    'fileName' => $fileName,
                    'folders' => $folders);
    if (!DBUtil::insertObject($item, 'Multisites_models', 'modelId')) {
        return LogUtil::registerError(__('Error! Creation attempt failed.', $dom));
    }
    // Let any hooks know that we have created a new item
    pnModCallHooks('item', 'create', $item['modelId'], array('module' => 'Multisites'));
    return $item['modelId'];
}

/**
 * Create a new database for the new site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The database name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_createDB($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    if (!DBUtil::createDatabase($siteDNS)) {
        return LogUtil::registerError(__('Create Database failed'), $dom);
    }
    return true;
}

/**
 * Create database tables based on the model file
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model database that is the same as sitDNS and the model file name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_createTables($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $fileName = FormUtil::getPassedValue('fileName', isset($args['fileName']) ? $args['fileName'] : null, 'POST');
    $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    //check if the models folders exists and it is writeable
    $path = pnModGetVar('Multisites', 'modelsFolder');
    if (!is_dir($path)) {
        return LogUtil::registerError(__('The model do not exists', $dom));
    }
    //check if the file model exists and it is readable
    $file = $path . '/' . $fileName;
    if (!file_exists($file) || !is_readable($file)) {
        return LogUtil::registerError(__('The model file has not been found', $dom));
    }
    //read the file
    $fh = fopen($file, 'r+');
    if ($fh == false) {
        return LogUtil::registerError(__('Error opening the model file', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $siteDNS));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $lines = file($file);
    $exec = '';
    foreach ($lines as $line_num => $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '--') === 0)
            continue;
        $exec .= $line;
        if (strrpos($line, ';') === strlen($line) - 1) {
            if (!$connect->Execute($exec)) {
                return LogUtil::registerError(__('Error importing the database in line', $dom) . " " . $line_num . ":<br>" . $exec . "<br>" . mysql_error() . "\n");
            }
            $exec = '';
        }
    }
    fclose($fh);
    $connect->close();
    return true;
}

/**
 * Delete a database
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The database name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_deleteDatabase($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $siteDNS));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "DROP DATABASE " . $siteDNS . ";";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        $connect->close();
        return LogUtil::registerError(__('Error deleting database', $dom));
    }
    $connect->close();
    return true;
}

/**
 * Delete an instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identify
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_deleteInstance($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // Needed argument
    if ($instanceId == null || !is_numeric($instanceId)) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    //Get instance information
    $instance = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($instance == false) {
        LogUtil::registerError(__('Not site found', $dom));
        return pnRedirect(pnModURL('Multisites', 'admin', 'main'));
    }
    //delete instance information
    if (!DBUtil::deleteObjectByID('Multisites_sites', $instanceId, 'instanceId')) {
        return LogUtil::registerError(__('Error! Sorry! Deletion attempt failed.', $dom));
    }
    // Let any hooks know that we have created a new item
    pnModCallHooks('item', 'delete', $item['instanceId'], array('module' => 'Multisites'));
    return true;
}

/**
 * Update the module vars values for an instance that is being created
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The site main information
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_updateConfigValues($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $siteName = FormUtil::getPassedValue('siteName', isset($args['siteName']) ? $args['siteName'] : null, 'POST');
    $siteAdminName = FormUtil::getPassedValue('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null, 'POST');
    $siteAdminEmail = FormUtil::getPassedValue('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null, 'POST');
    $siteAdminPwd = FormUtil::getPassedValue('siteAdminPwd', isset($args['siteAdminPwd']) ? $args['siteAdminPwd'] : null, 'POST');
    $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $siteDNS));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $prefix = $GLOBALS['PNConfig']['System']['prefix'];
    // modify the site name
    $value = serialize($siteName);
    $sql = "UPDATE " . $prefix . "_module_vars set pn_value='$value' WHERE pn_modname='/PNConfig' AND pn_name='sitename'";
    if (!$connect->Execute($sql)) {
        return LogUtil::registerError(__('Error configurating value', $dom) . ":<br />" . $sql . "<br />" . mysql_error() . "\n");
    }
    // modify the adminmail
    $value = serialize($siteAdminEmail);
    $sql = "UPDATE " . $prefix . "_module_vars set pn_value='$value' WHERE pn_modname='/PNConfig' AND pn_name='adminmail'";
    if (!$connect->Execute($sql)) {
        return LogUtil::registerError(__('Error configurating value', $dom) . ":<br />" . $sql . "<br />" . mysql_error() . "\n");
    }
    // modify the sessionCookieName
    $value = serialize('ZKSID_' . $siteDNS);
    $sql = "UPDATE " . $prefix . "_module_vars set pn_value='$value' WHERE pn_modname='/PNConfig' AND pn_name='sessionname'";
    if (!$connect->Execute($sql)) {
        return LogUtil::registerError(__('Error configurating value', $dom) . ":<br />" . $sql . "<br />" . mysql_error() . "\n");
    }
    // modify the admin password
    // get the encript hash method
    $sql = "SELECT pn_hash_method FROM " . $prefix . "_users WHERE pn_uname='admin'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    $hash_method = $rs->fields[0];
    // encript the passed password with the method found
    $hashmethodsarray = pnModAPIFunc('Users', 'user', 'gethashmethods', array('reverse' => true));
    $password = DataUtil::hash($siteAdminPwd, $hashmethodsarray[$hash_method]);
    // change admin password
    $sql = "UPDATE " . $prefix . "_users set pn_pass='$password' WHERE pn_uname='admin'";
    if (!$connect->Execute($sql)) {
        return LogUtil::registerError(__('Error configurating value', $dom) . ":<br />" . $sql . "<br />" . mysql_error() . "\n");
    }
    $connect->close();
    return true;
}

/**
 * Get all the modules available
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	instance identity
 * @return:	An array with all the modules
 */
function Multisites_adminapi_getAllSiteModules($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $system = FormUtil::getPassedValue('system', isset($args['system']) ? $args['system'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    //$type = ($system == null) ? 3 : 1000;
    // Needed argument
    if ($instanceId == null || !is_numeric($instanceId)) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    //$sql = "SELECT pn_name, pn_state FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_modules WHERE pn_type<>$type";
	$sql = "SELECT pn_name, pn_state, pn_version FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_modules";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    $items = array();
    for (; !$rs->EOF; $rs->MoveNext()) {
        list ($name, $state, $version) = $rs->fields;
        $items[$name] = array('name' => $name,
                                'state' => $state,
                                'version' => $version);
    }
    $connect->close();
    return $items;
}

/**
 * Get a site modules information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	instance identity and module name
 * @return:	An array with the module needed information
 */
function Multisites_adminapi_getSiteModule($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // Needed argument
    if ($instanceId == null || !is_numeric($instanceId) || $moduleName == null || $moduleName == '') {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "SELECT pn_name, pn_state FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_modules WHERE pn_name='$moduleName'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    $item = array('name' => $rs->fields[0], 'state' => $rs->fields[1]);
    $connect->close();
    return $item;
}

/**
 * Get a site theme information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	instance identity and module name
 * @return:	An array with the theme needed information
 */
function Multisites_adminapi_getSiteTheme($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // Needed argument
    if ($instanceId == null || !is_numeric($instanceId) || $themeName == null || $themeName == '') {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "SELECT pn_name, pn_state FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_themes WHERE pn_name='$themeName'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    $item = array('name' => $rs->fields[0], 'state' => $rs->fields[1]);
    $connect->close();
    return $item;
}

/**
 * Delete a model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model identify
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_deleteModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // Needed argument
    if ($modelId == null || !is_numeric($modelId)) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    //Get instance information
    $model = pnModAPIFunc('Multisites', 'user', 'getModelById', array('modelId' => $modelId));
    if ($model == false) {
        return LogUtil::registerError(__('Model not found', $dom));
    }
    //delete instance information
    if (!DBUtil::deleteObjectByID('Multisites_models', $modelId, 'modelId')) {
        return LogUtil::registerError(__('Error! Sorry! Deletion attempt failed.', $dom));
    }
    // Let any hooks know that we have created a new item
    pnModCallHooks('item', 'delete', $item['modelId'], array('module' => 'Multisites'));
    return true;
}

/**
 * Delete a module form a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the module name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_deleteSiteModule($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    //get module information
    $siteModule = pnModAPIFunc('Multisites', 'admin', 'getSiteModule', array('moduleName' => $moduleName, 'instanceId' => $instanceId));
    if ($siteModule['state'] == 3) {
        pnModAPIFunc('Multisites', 'admin', 'modifyActivation', array('moduleName' => $moduleName, 'instanceId' => $instanceId, 'newState' => 2));
        return true;
    }
    if ($siteModule['state'] == 2) {
        return true;
    }
    $sql = "DELETE FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_modules WHERE pn_name='$moduleName'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Sorry! Deletion attempt failed.', $dom));
    }
    $connect->close();
    return true;
}

/**
 * Create a module for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the module name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_createSiteModule($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $filemodules = pnModAPIFunc('Modules', 'admin', 'getfilemodules');
    $module = $filemodules['modules/' . $moduleName];
    $textual = array('url','name', 'displayname', 'description', 'directory', 'version', 'author', 'contact', 'credits', 'help', 'changelog', 'license', 'securityschema');
    $exclude = array('oldnames', 'i18n', 'moddependencies');
    foreach ($module as $key => $value) {
        if (!in_array($key, $exclude)) {
            $fields .= 'pn_' . $key . ',';
            $apos = (in_array($key, $textual)) ? "'" : '';
            $valueString = ($value == '') ? "''" : $apos . DataUtil::formatForStore($value) . $apos;
            $values .= $valueString . ',';
        }
    }
    $fields = substr($fields, 0, -1);
    $values = substr($values, 0, -1);
    // set module state to 1
    $fields .= ',pn_state';
    $values .= ',1';
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    //create the module in the site
    $sql = "INSERT INTO " . $GLOBALS['PNConfig']['System']['prefix'] . "_modules
			($fields)
			VALUES
			($values)";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Creation attempt failed.', $dom));
    }
    $connect->close();
    return true;
}

/**
 * Modify the state of a module for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity, the module name and the new state
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_modifyActivation($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
    $newState = FormUtil::getPassedValue('newState', isset($args['newState']) ? $args['newState'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    //update the module state in the site
    $sql = "UPDATE " . $GLOBALS['PNConfig']['System']['prefix'] . "_modules set pn_state = " . $newState . " where pn_name = '" . $moduleName . "'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Update attempt failed.', $dom));
    }
    $connect->close();
    return true;
}

/**
 * delete a directory recursivily
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The directory name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_deleteDir($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $dirName = FormUtil::getPassedValue('dirName', isset($args['dirName']) ? $args['dirName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    if (file_exists($dirName)) {
        $dir = dir($dirName);
        while ($file = $dir->read()) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dirName . '/' . $file)) {
                    pnModAPIFunc('Multisites', 'admin', 'deleteDir', array('dirName' => $dirName . '/' . $file));
                } else {
                    if (!@unlink($dirName . '/' . $file)) {
                        return LogUtil::registerError(__('Error deleting file', $dom) . ': ' . $dirName . '/' . $file);
                    }
                }
            }
        }
        $dir->close();
        if (!@rmdir($dirName)) {
            return LogUtil::registerError(__('Error deleting file', $dom) . ': ' . $dirName);
        }
    }
}

/**
 * get all themes available in themes directori
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	An array with all the themes in themes folder
 */
function Multisites_adminapi_getAllThemes()
{
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // Get all themes on filesystem
    $filethemes = array();
    if (is_dir('themes')) {
        $dh = opendir('themes');
        $dirArray = array();
        while ($dir = readdir($dh)) {
            if ($dir != '.' && $dir != '..' && $dir != '.svn' && $dir != 'CVS' && $dir != 'index.html' && $dir != 'index.htm') {
                $dirArray[] = $dir;
            }
        }
        closedir($dh);
        foreach ($dirArray as $dir) {
            // Work out the theme type
            if (file_exists("themes/$dir/theme.cfg") && file_exists("themes/$dir/theme.php")) {
                $themetype = 4;
            } elseif (file_exists("themes/$dir/version.php") && !file_exists("themes/$dir/theme.php")) {
                $themetype = 3;
            } elseif (file_exists("themes/$dir/xaninit.php") && file_exists("themes/$dir/theme.php")) {
                // xanthia 2.0 themes will need upgrading so set the theme state to inactive
                $themeversion['state'] = PNTHEME_STATE_INACTIVE;
                $themetype = 2;
            } elseif (file_exists("themes/$dir/theme.php")) {
                $themetype = 1;
            } else {
                // anything else isn't a theme
                continue;
            }
            // include language file for ML displaynames and descriptions
            $defaultlang = pnConfigGetVar('language');
            if (empty($defaultlang)) {
                $defaultlang = 'eng';
            }
            $currentlang = DataUtil::formatForOS(pnUserGetLang());
            $possiblelanguagefiles = array("themes/$dir/lang/$currentlang/version.php", "themes/$dir/lang/$defaultlang/version.php", "themes/$dir/lang/eng/version.php");
            foreach ($possiblelanguagefiles as $languagefile) {
                if (file_exists($languagefile) && is_readable($languagefile)) {
                    include_once $languagefile;
                    break;
                }
            }
            // Get some defaults in case we don't have a theme version file
            $themeversion['name'] = preg_replace('/_/', ' ', $dir);
            $themeversion['displayname'] = preg_replace('/_/', ' ', $dir);
            $themeversion['version'] = '0';
            $themeversion['description'] = '';
            // include the correct version file based on theme type and
            // manipulate the theme version information
            if (file_exists($file = "themes/$dir/version.php")) {
                if (!include ($file)) {
                    LogUtil::registerError(pnML('_THEME_COULDNOTINCLUDE', array('file' => $file)));
                }
            } else if ($themetype == 4 && file_exists($file = "themes/$dir/theme.cfg")) {
                if (!include ($file)) {
                    LogUtil::registerError(pnML('_THEME_COULDNOTINCLUDE', array('file' => $file)));
                }
                if (!isset($themeversion['name'])) {
                    $themeversion['name'] = $dir;
                }
                $themeversion['displayname'] = $themeversion['name'];
            } else if ($themetype == 2 && file_exists($file = "themes/$dir/xaninfo.php")) {
                if (!include ($file)) {
                    LogUtil::registerError(pnML('_THEME_COULDNOTINCLUDE', array('file' => $file)));
                }
                $themeversion['author'] = $themeinfo['author'];
                $themeversion['contact'] = $themeinfo['download'];
                $themeversion['name'] = $themeinfo['name'];
                $themeversion['displayname'] = $themeinfo['name'];
                $themeversion['xhtml'] = $themeinfo['xhtmlsupport'];
            }
            $filethemes[$themeversion['name']] = array('directory' => $dir,
										                'name' => $themeversion['name'],
										                'type' => $themetype,
										                'displayname' => (isset($themeversion['displayname']) ? $themeversion['displayname'] : $themeversion['name']),
										                'version' => (isset($themeversion['version']) ? $themeversion['version'] : '1.0'),
										                'description' => (isset($themeversion['description']) ? $themeversion['description'] : $themeversion['displayname']),
										                'admin' => (isset($themeversion['admin']) ? (int) $themeversion['admin'] : '0'),
										                'user' => (isset($themeversion['user']) ? (int) $themeversion['user'] : '1'),
										                'system' => (isset($themeversion['system']) ? (int) $themeversion['system'] : '0'),
										                'state' => (isset($themeversion['state']) ? $themeversion['state'] : PNTHEME_STATE_ACTIVE),
										                'official' => (isset($themeversion['offical']) ? (int) $themeversion['offical'] : '0'),
										                'author' => (isset($themeversion['author']) ? $themeversion['author'] : ''),
										                'contact' => (isset($themeversion['contact']) ? $themeversion['contact'] : ''),
										                'credits' => (isset($themeversion['credits']) ? $themeversion['credits'] : ''),
										                'help' => (isset($themeversion['help']) ? $themeversion['help'] : ''),
										                'changelog' => (isset($themeversion['changelog']) ? $themeversion['changelog'] : ''),
										                'license' => (isset($themeversion['license']) ? $themeversion['license'] : ''),
										                'xhtml' => (isset($themeversion['xhtml']) ? (int) $themeversion['xhtml'] : 1));
            // important: unset themeversion otherwise all following themes will have
            // at least the same regid or other values not defined in
            // the next version.php files to be read
            unset($themeversion);
            unset($themetype);
        }
    }
    return $filethemes;
}

/**
 * Get all the themes available for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity
 * @return:	An array with all the themes for a site
 */
function Multisites_adminapi_getAllSiteThemes($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // Needed argument
    if ($instanceId == null || !is_numeric($instanceId)) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "SELECT pn_name, pn_state FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_themes";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    $items = array();
    for (; !$rs->EOF; $rs->MoveNext()) {
        list ($name, $state) = $rs->fields;
        $items[$name] = $state;
    }
    $connect->close();
    return $items;
}

/**
 * Delete a theme form a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the theme name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_deleteSiteTheme($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "DELETE FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_themes WHERE pn_name='$themeName'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Sorry! Deletion attempt failed.', $dom));
    }
    $connect->close();
    return true;
}

/**
 * Create a theme for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the theme name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_createSiteTheme($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $themes = pnModAPIFunc('Multisites', 'admin', 'getAllThemes');
    $theme = $themes[$themeName];
    $textual = array('name', 'displayname', 'description', 'directory', 'version', 'author', 'contact', 'credits', 'help', 'changelog', 'license');
    foreach ($theme as $key => $value) {
        $fields .= 'pn_' . $key . ',';
        $apos = (in_array($key, $textual)) ? "'" : '';
        $valueString = ($value == '') ? "''" : $apos . DataUtil::formatForStore($value) . $apos;
        $values .= $valueString . ',';
    }
    $fields = substr($fields, 0, -1);
    $values = substr($values, 0, -1);
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    //create the module in the site
    $sql = "INSERT INTO " . $GLOBALS['PNConfig']['System']['prefix'] . "_themes
			($fields)
			VALUES
			($values)";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Creation attempt failed.', $dom));
    }
    $connect->close();
    return true;
}

/**
 * Get the default theme for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity
 * @return:	The site default theme name
 */
function Multisites_adminapi_getSiteDefaultTheme($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "SELECT pn_value FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_module_vars WHERE pn_modname='/PNConfig' AND pn_name='Default_Theme'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    list ($defaultTheme) = $rs->fields;
    $connect->close();
    return $defaultTheme;
}

/**
 * Update the site default theme
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the theme name
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_setAsDefaultTheme($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'GET');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $value = serialize($name);
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    $sql = "UPDATE " . $GLOBALS['PNConfig']['System']['prefix'] . "_module_vars SET pn_value = '$value' WHERE pn_modname='/PNConfig' AND pn_name='Default_Theme'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
        return LogUtil::registerError(__('Error! Update attempt failed.', $dom));
    }
    $connect->close();
    return true;
}

/**
 * Update the site main information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance values
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_updateInstance($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // get site information
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_sites_column'];
    $where = "$c[instanceId] = $instanceId";
    if (!DBUTil::updateObject($items, 'Multisites_sites', $where)) {
        return LogUtil::registerError(__('Error! Update attempt failed.', $dom));
    }
    return true;
}

/**
 * Update the model main information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model values
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_updateModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');
    $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // get model information
    $model = pnModAPIFunc('Multisites', 'user', 'getModelById', array('modelId' => $modelId));
    if ($model == false) {
        return LogUtil::registerError(__('Model not found', $dom));
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_models_column'];
    $where = "$c[modelId] = $modelId";
    if (!DBUTil::updateObject($items, 'Multisites_models', $where)) {
        return LogUtil::registerError(__('Error! Update attempt failed.', $dom));
    }
    return true;
}

/**
 * Create a global administrator for a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance values
 * @return:	true if success or false otherwise
 */
function Multisites_adminapi_createAdministrator($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // get global administrator parameters
    $globalAdminName = pnModGetVar('Multisites', 'globalAdminName');
    $globalAdminPassword = pnModGetVar('Multisites', 'globalAdminPassword');
    $globalAdminemail = pnModGetVar('Multisites', 'globalAdminemail');
    // check if the global administrator name, password and email had been defined
    if ($globalAdminName == '' || $globalAdminPassword == '' || $globalAdminemail == '') {
        return LogUtil::registerError(__('You have not defined the global administrator name or password. Check the module configuration', $dom));
    }
    // get site information
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }
    // check if the super administrator exists
    $sql = "SELECT pn_uid FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_users WHERE `pn_uname`='" . $globalAdminName  . "'";
    $rs = $connect->Execute($sql);
    if (!$rs) {
    	$connect->close();
        return LogUtil::registerError(__('Error! Getting global administrator values.', $dom));
    }
    list ($uid) = $rs->fields;
    if($uid == ''){
        // the user doesn't exists and create it
        // get hash method and encript the password with the hash method
        $method = pnModGetVar('Users', 'hash_method');
        $methodNumberArray = pnModAPIFunc('Users','user','gethashmethods', array('reverse' => false));
        $methodNumber = $methodNumberArray[$method];
        $password = DataUtil::hash($globalAdminPassword, $method);
        $sql = "INSERT INTO " . $GLOBALS['PNConfig']['System']['prefix'] . "_users (pn_uname, pn_pass, pn_email, pn_hash_method, pn_activated)
                VALUES ('$globalAdminName','$password','$globalAdminemail',$methodNumber,1)";
        $rs = $connect->Execute($sql);
        if(!$rs){
        	$connect->close();
            return LogUtil::registerError(__('Error! Creating global administrator.', $dom));
        }
        $sql = "SELECT pn_uid FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_users WHERE `pn_uname`='" . $globalAdminName  . "'";
        $rs = $connect->Execute($sql);
        if (!$rs) {
            $connect->close();
            return LogUtil::registerError(__('Error! Getting global administrator values.', $dom));
        }
        list ($uid) = $rs->fields;
        if($uid != ''){
            // insert the user into administrators group
            $sql = "INSERT INTO " . $GLOBALS['PNConfig']['System']['prefix'] . "_group_membership (pn_uid, pn_gid) VALUES ($uid,2)";
            $rs = $connect->Execute($sql);
            if(!$rs){
            	$connect->close();
                return LogUtil::registerError(__('Error! Adding global administrator as administrators group membership.', $dom));
            }
        }
    } else {
    	// check if the user is administrator
        $sql = "SELECT pn_gid FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_group_membership
                WHERE `pn_uid`=$uid AND pn_gid=2";
        $rs = $connect->Execute($sql);
        if (!$rs) {
            $connect->close();
            return LogUtil::registerError(__('Error! Getting global administrator group.', $dom));
        }
        list ($gid) = $rs->fields;
        if($gid == ''){
        	// the user is not administrator and insert the user into administrators group
            $sql = "INSERT INTO " . $GLOBALS['PNConfig']['System']['prefix'] . "_group_membership (pn_uid, pn_gid) VALUES ($uid,2)";
            $rs = $connect->Execute($sql);
            if(!$rs){
                $connect->close();
                return LogUtil::registerError(__('Error! Adding global administrator as administrators group membership.', $dom));
            }
        }
    } 
    $connect->close();
    return true;
}

/**
 * Recover the first row in the permissions table for administrators
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:  The instance values
 * @return: true if success or false otherwise
 */
function Multisites_adminapi_recoverAdminSiteControl($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // get site information
    $site = pnModAPIFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
    if ($site == false) {
        return LogUtil::registerError(__('Not site found', $dom));
    }
    $connect = pnModAPIFunc('Multisites', 'admin', 'connectExtDB', array('database' => $site['siteDNS']));
    if (!$connect) {
        return LogUtil::registerError(__('Error connecting to database', $dom));
    }  
    //delete the sequence in the first position
    $sql = "DELETE FROM " . $GLOBALS['PNConfig']['System']['prefix'] . "_group_perms WHERE `pn_sequence` < 1 OR `pn_pid` = 1";
    $rs = $connect->Execute($sql);
    if(!$rs){
    	$connect->close();
    	return LogUtil::registerError(__('Error! Deleting the sequences with value under 0.', $dom));
    }
    //insert a new sequence
    $sql = "INSERT INTO " . $GLOBALS['PNConfig']['System']['prefix'] . "_group_perms (pn_gid, pn_sequence, pn_component, pn_instance, pn_level, pn_pid)
            VALUES (2,0,'.*','.*',800,1)";
    $rs = $connect->Execute($sql);
    if(!$rs){
    	$connect->close();
        return LogUtil::registerError(__('Error! Creating the sequence.', $dom));
    }
    mysql_close($con);
    return true;
}

/**
 * Save the site modules and versions
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:  The instance values
 * @return: true if success or false otherwise
 */
function Multisites_adminapi_saveSiteModules($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // get all the modules available in site
    $siteModules = pnModAPIFunc('Multisites', 'admin', 'getAllSiteModules', array('instanceId' => $instanceId));
    // save all modules in database
    foreach($siteModules as $module){
        $item = array('instanceId' => $instanceId,
                        'moduleName' => $module['name'],
                        'moduleVersion' => $module['version']);
        if (!DBUtil::insertObject($item, 'Multisites_sitesModules', 'smId')) {
            return LogUtil::registerError(__('Error! Creation attempt failed.', $dom));
        }
    }
    return true;    
}

/**
 * Delete the site modules information
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:  The instance values
 * @return: true if success or false otherwise
 */
function Multisites_adminapi_deleteSiteModules($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    //delete instance information
    if (!DBUtil::deleteObjectByID('Multisites_sitesModules', $instanceId, 'instanceId')) {
        return LogUtil::registerError(__('Error! Sorry! Deletion attempt failed.', $dom));
    }
    return true;
}

/**
 * Update the site modules information
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:  The instance values
 * @return: true if success or false otherwise
 */
function Multisites_adminapi_updateSiteModules($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    // get site available modules
    // get all modules available
    
    /*
    //delete instance information
    if (!DBUtil::deleteObjectByID('Multisites_sitesModules', $instanceId, 'instanceId')) {
        return LogUtil::registerError(__('Error! Sorry! Deletion attempt failed.', $dom));
    }
    */
    return true;
}

/**
 * get the number of sites that require actualization for a given module
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:  Module name
 * @return: The number of sites
 */
function Multisites_adminapi_getNumberOfSites($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
    $currentVersion = FormUtil::getPassedValue('currentVersion', isset($args['currentVersion']) ? $args['currentVersion'] : null, 'POST');
    // security check
    if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) || FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['PNConfig']['Multisites']['mainSiteURL']) {
        return LogUtil::registerPermissionError();
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_sitesModules_column'];
    $where = "$c[moduleName] = '$moduleName' AND $c[moduleVersion] < $currentVersion";
    $numberOfItems = DBUtil::selectObjectCount('Multisites_sitesModules', $where);
    if ($numberOfItems === false) {
        return LogUtil::registerError(__f('Error! Getting number of sites where the module <strong>%s</strong> need to be upgraded.', $moduleName ,$dom));
    }
    return $numberOfItems;
}

/**
 * get the sites ids where upgrading is needed
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:  Module name
 * @return: The number of sites
 */
function Multisites_adminapi_getSitesThatNeedUpgrade($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
    $currentVersion = FormUtil::getPassedValue('currentVersion', isset($args['currentVersion']) ? $args['currentVersion'] : null, 'POST');
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_sitesModules_column'];
    $where = "$c[moduleName] = '$moduleName' AND $c[moduleVersion] < $currentVersion";
    $sites = DBUtil::selectObjectArray('Multisites_sitesModules', $where);
    if ($sites === false) {
        return LogUtil::registerError(__f('Error! Getting sites where the module <strong>%s</strong> need to be upgraded.', $moduleName ,$dom));
    }
    return $sites;
}