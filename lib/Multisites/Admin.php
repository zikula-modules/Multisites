<?php

class Multisites_Admin extends AbstractController
{
    /**
     * Show the list of sites created
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The list of sites
     */
    public function main($args)
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
        $sites = ModUtil::apiFunc('Multisites', 'user', 'getAllSites',
                array('letter' => $letter,
                'itemsperpage' => $itemsperpage,
                'startnum' => $startnum));
        // get total sites
        if ($letter == null) {
            $numSites = count(ModUtil::apiFunc('Multisites', 'user', 'getAllSites'));
        } else {
            $numSites = count(ModUtil::apiFunc('Multisites', 'user', 'getAllSites',
                    array('letter' => $letter)));
        }
        $pager = array('numitems' => $numSites,
                'itemsperpage' => $itemsperpage);
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('sites', $sites);
        $render->assign('pager', $pager);
        $render->assign('wwwroot', $GLOBALS['ZConfig']['Multisites']['wwwroot']);
        $render->assign('siteDNSEndText', $GLOBALS['ZConfig']['Multisites']['siteDNSEndText']);
        $render->assign('basedOnDomains', $GLOBALS['ZConfig']['Multisites']['basedOnDomains']);
        return $render->fetch('Multisites_admin_main.htm');
    }

    /**
     * Show the form needed to create a new instance
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The form needed to create a new instance
     */
    public function newInstance($args)
    {
        $instanceName = FormUtil::getPassedValue('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null, 'GET');
        $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'GET');
        $siteName = FormUtil::getPassedValue('siteName', isset($args['siteName']) ? $args['siteName'] : null, 'GET');
        $siteAdminName = FormUtil::getPassedValue('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null, 'GET');
        $siteAdminRealName = FormUtil::getPassedValue('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null, 'GET');
        $siteAdminEmail = FormUtil::getPassedValue('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null, 'GET');
        $siteCompany = FormUtil::getPassedValue('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null, 'GET');
        $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'GET');
        $siteDBName = FormUtil::getPassedValue('siteDBName', isset($args['siteDBName']) ? $args['siteDBName'] : null, 'GET');
        $siteDBUname = FormUtil::getPassedValue('siteDBUname', isset($args['siteDBUname']) ? $args['siteDBUname'] : null, 'GET');
        $siteDBHost = FormUtil::getPassedValue('siteDBHost', isset($args['siteDBHost']) ? $args['siteDBHost'] : null, 'GET');
        $siteDBType = FormUtil::getPassedValue('siteDBType', isset($args['siteDBType']) ? $args['siteDBType'] : null, 'GET');
        $siteDBPrefix = FormUtil::getPassedValue('siteDBPrefix', isset($args['siteDBPrefix']) ? $args['siteDBPrefix'] : null, 'GET');
        $createDB = FormUtil::getPassedValue('createDB', isset($args['createDB']) ? $args['createDB'] : 0, 'GET');
        $siteInitModel = FormUtil::getPassedValue('siteInitModel', isset($args['siteInitModel']) ? $args['siteInitModel'] : null, 'GET');
        $active = FormUtil::getPassedValue('active', isset($args['active']) ? $args['active'] : 0, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get all the models for new instances
        $models = ModUtil::apiFunc('Multisites', 'user', 'getAllModels');
        if (!$models) {
            LogUtil::registerError($this->__('There is not any module defined'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // create output object
        $render = Renderer::getInstance('Multisites', false);

        $render->assign('models', $models);
        $render->assign('instanceName', $instanceName);
        $render->assign('description', $description);
        $render->assign('siteName', $siteName);
        $render->assign('siteAdminName', $siteAdminName);
        $render->assign('siteAdminRealName', $siteAdminRealName);
        $render->assign('siteAdminEmail', $siteAdminEmail);
        $render->assign('siteCompany', $siteCompany);
        $render->assign('siteDNS', $siteDNS);
        $render->assign('siteDBName', $siteDBName);
        $render->assign('siteDBUname', $siteDBUname);
        $render->assign('siteDBHost', $siteDBHost);
        $render->assign('siteDBType', $siteDBType);
        $render->assign('siteDBPrefix', $siteDBPrefix);
        $render->assign('createDB', $createDB);
        $render->assign('siteInitModel', $siteInitModel);
        $render->assign('active', $active);
        return $render->fetch('Multisites_admin_new.htm');
    }

    /**
     * Create a new instance
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance properties received from the creation form
     * @return:	Returns user to administrator main page
     */
    public function createInstance($args)
    {
        $instanceName = FormUtil::getPassedValue('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null, 'POST');
        $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
        $siteName = FormUtil::getPassedValue('siteName', isset($args['siteName']) ? $args['siteName'] : null, 'POST');
        $siteAdminName = FormUtil::getPassedValue('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null, 'POST');
        $siteAdminPwd = FormUtil::getPassedValue('siteAdminPwd', isset($args['siteAdminPwd']) ? $args['siteAdminPwd'] : null, 'POST');
        $siteAdminRealName = FormUtil::getPassedValue('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null, 'POST');
        $siteAdminEmail = FormUtil::getPassedValue('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null, 'POST');
        $siteCompany = FormUtil::getPassedValue('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null, 'POST');
        $siteDNS = FormUtil::getPassedValue('siteDNS', isset($args['siteDNS']) ? $args['siteDNS'] : null, 'POST');
        $siteDBName = FormUtil::getPassedValue('siteDBName', isset($args['siteDBName']) ? $args['siteDBName'] : null, 'POST');
        $siteDBUname = FormUtil::getPassedValue('siteDBUname', isset($args['siteDBUname']) ? $args['siteDBUname'] : null, 'POST');
        $siteDBPass = FormUtil::getPassedValue('siteDBPass', isset($args['siteDBPass']) ? $args['siteDBPass'] : null, 'POST');
        $siteDBHost = FormUtil::getPassedValue('siteDBHost', isset($args['siteDBHost']) ? $args['siteDBHost'] : null, 'POST');
        $siteDBType = FormUtil::getPassedValue('siteDBType', isset($args['siteDBType']) ? $args['siteDBType'] : null, 'POST');
        $siteDBPrefix = FormUtil::getPassedValue('siteDBPrefix', isset($args['siteDBPrefix']) ? $args['siteDBPrefix'] : null, 'POST');
        $createDB = FormUtil::getPassedValue('createDB', isset($args['createDB']) ? $args['createDB'] : 0, 'POST');
        $siteInitModel = FormUtil::getPassedValue('siteInitModel', isset($args['siteInitModel']) ? $args['siteInitModel'] : null, 'POST');
        $active = FormUtil::getPassedValue('active', isset($args['active']) ? $args['active'] : 0, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'main'));
        }
        $errorMsg = '';
        if ($instanceName == null || $instanceName == '') {
            $errorMsg = $this->__('Error! Please provide an instance name. It is a mandatory field.<br />');
        }
        if ($siteAdminName == null || $siteAdminName == '') {
            $errorMsg .= $this->__('Error! Please provide an admin\'s site name. It is a mandatory field.<br />');
        }
        if ($siteAdminPwd == null || $siteAdminPwd == '') {
            $errorMsg .= $this->__('Error! Please provide an admin\'s site password. It is a mandatory field.<br />');
        }
        if ($siteAdminEmail == null || $siteAdminEmail == '') {
            $errorMsg .= $this->__('Error! Please provide an admin\'s site email. It is a mandatory field. <br />');
        }
        if ($siteDNS == null || $siteDNS == '') {
            $errorMsg .= $this->__('Error! Please provide the site domain. It is a mandatory field.<br />');
        }
        if ($siteDBHost == null || $siteDBHost == '') {
            $errorMsg .= $this->__('Error! Please provide the site database host. It is a mandatory field.<br />');
        }
        if ($siteDBHost == null || $siteDBHost == '') {
            $errorMsg .= $this->__('Error! Please provide the site database host. It is a mandatory field.<br />');
        }
        if ($siteDBName == null || $siteDBName == '') {
            $errorMsg .= $this->__('Error! Please provide the site database name. It is a mandatory field.<br />');
        }
        if ($siteDBUname == null || $siteDBUname == '') {
            $errorMsg .= $this->__('Error! Please provide the site database user name. It is a mandatory field.<br />');
        }
        if ($siteDBPass == null || $siteDBPass == '') {
            $errorMsg .= $this->__('Error! Please provide the site database user password. It is a mandatory field.<br />');
        }
        if ($siteDBPrefix == null || $siteDBPrefix == '') {
            $errorMsg .= $this->__('Error! Please provide the site database prefix. It is a mandatory field.<br />');
        }
        if ($siteInitModel == null || $siteInitModel == '') {
            $errorMsg .= $this->__('Error! Please provide the model on the site will be based. It is a mandatory field.<br />');
        }
        // check that the siteDNS exists and if it exists return error
        if (ModUtil::apiFunc('Multisites', 'user', 'getSiteInfo',
        array('site' => $siteDNS))) {
            $errorMsg .= $this->__('This site just exists. The site DNS must be unique.');
        }
        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModel',
                array('modelName' => $siteInitModel));
        if ($model == false) {
            $errorMsg .= $this->__('Model note found');
        }
        if ($errorMsg == '') {
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
                if (!file_exists($dir)) {
                    if (!mkdir($dir, 0777)) {
                        $errorMsg = $this->__('Error creating site directories') . ': ' . $dir;
                    }
                } else if (!is_writeable($dir)) $errorMsg = $this->__f('Error with the folder <strong>%s</strong> because it is not writeable.', array($dir));
            }
        }
        if ($createDB == 1 && $errorMsg == '') {
            // create a new database if it doesn't exist
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createDB',
            array('siteDBName' => $siteDBName,
            'siteDBUname' => $siteDBUname,
            'siteDBPass' => $siteDBPass,
            'siteDBType' => $siteDBType,
            'siteDBHost' => $siteDBHost))) {
                $errorMsg = $this->__('The database creation has failed');
            }
        }
        if ($errorMsg == '') {
            // created the database tables based on the model file
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createTables',
            array('fileName' => $model['fileName'],
            'modelDBTablesPrefix' => $model['modelDBTablesPrefix'],
            'siteDBName' => $siteDBName,
            'siteDBPass' => $siteDBPass,
            'siteDBUname' => $siteDBUname,
            'siteDBHost' => $siteDBHost,
            'siteDBType' => $siteDBType,
            'siteDBPrefix' => $siteDBPrefix))) {
                $errorMsg = $this->__('The tables creation has failed');
            }
        }
        if ($errorMsg == '') {
            // update instance values like admin name, admin password, cookie name, site name...
            if (!ModUtil::apiFunc('Multisites', 'admin', 'updateConfigValues',
            array('siteAdminName' => $siteAdminName,
            'siteAdminPwd' => $siteAdminPwd,
            'siteAdminEmail' => $siteAdminEmail,
            'siteName' => $siteName,
            'siteDBName' => $siteDBName,
            'siteDBPass' => $siteDBPass,
            'siteDBUname' => $siteDBUname,
            'siteDBHost' => $siteDBHost,
            'siteDBType' => $siteDBType,
            'siteDBPrefix' => $siteDBPrefix))) {
                $errorMsg = $this->__('The site configuration has failed.');
            }
        }
        if ($errorMsg == '') {
            // modify multisites_dbconfig file
            if (!ModUtil::apiFunc('Multisites', 'admin', 'updateDBConfig',
            array('siteDNS' => $siteDNS,
            'siteDBName' => $siteDBName,
            'siteDBPass' => $siteDBPass,
            'siteDBUname' => $siteDBUname,
            'siteDBHost' => $siteDBHost,
            'siteDBType' => $siteDBType,
            'siteDBPrefix' => $siteDBPrefix))) {
                $errorMsg = $this->__('Error updating the file multisites_dbconfig.php.');
            }
        }
        if ($errorMsg == '') {
            // create a .htaccess file in the temporal folder
            $tempAccessFileContent = ModUtil::getVar('Multisites', 'tempAccessFileContent');
            if ($tempAccessFileContent != '') {
                // create file
                $file = $initTemp . '/.htaccess';
                file_put_contents($file, $tempAccessFileContent);
            }
            // create the instance
            $created = ModUtil::apiFunc('Multisites', 'admin', 'createInstance',
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
                    'siteDBPrefix' => $siteDBPrefix,
                    'siteInitModel' => $siteInitModel,
                    'active' => $active));
            if ($created == false) {
                $errorMsg = $this->__('Creation instance error');
            }
        }
        if ($errorMsg != '') {
            LogUtil::registerError($errorMsg);
            return System::redirect(ModUtil::url('Multisites', 'admin', 'newInstance',
                    array('instanceName' => $instanceName,
                    'description' => $description,
                    'siteName' => $siteName,
                    'siteAdminName' => $siteAdminName,
                    'siteAdminRealName' => $siteAdminRealName,
                    'siteAdminEmail' => $siteAdminEmail,
                    'siteCompany' => $siteCompany,
                    'siteDNS' => $siteDNS,
                    'siteDBType' => $siteDBType,
                    'siteDBHost' => $siteDBHost,
                    'siteDBName' => $siteDBName,
                    'siteDBUname' => $siteDBUname,
                    'siteDBPrefix' => $siteDBPrefix,
                    'createDB' => $createDB,
                    'siteInitModel' => $siteInitModel,
                    'active' => $active)));
        }
        //******* PNN *******
        // save the site module in database
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'saveSiteModules',
                array('instanceId' => $created));
        //*******
        // success
        LogUtil::registerStatus($this->__('A new instance has been created'));
        //  redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
    }

    /**
     * Delete an instance
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance identity
     * @return:	Returns true if success and false otherwise
     */
    public function deleteInstance($args)
    {
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
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        if ($confirmation == null) {
            // create output object
            $render = Renderer::getInstance('Multisites', false);
            $render->assign('instance', $site);
            return $render->fetch('Multisites_admin_deleteInstance.htm');
        }
        // confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'main'));
        }
        if ($deleteDB == 1) {
            // delete the instance database
            if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteDatabase',
            array('siteDBName' => $site['siteDBName'],
            'siteDBHost' => $site['siteDBHost'],
            'siteDBType' => $site['siteDBType'],
            'siteDBUname' => $site['siteDBUname'],
            'siteDBPass' => $site['siteDBPass']))) {
                LogUtil::registerError($this->__('Error deleting database'));
            }
        }
        if ($deleteFiles == 1) {
            // delete the instance files and directoris
            ModUtil::apiFunc('Multisites', 'admin', 'deleteDir',
                    array('dirName' => $GLOBALS['ZConfig']['Multisites']['filesRealPath'] . '/' . $site['siteDBName']));
        }
        // delete instance information
        if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteInstance',
        array('instanceId' => $site['instanceId']))) {
            LogUtil::registerError($this->__('The instance deletion has failed'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // modify multisites_dbconfig files
        if (!ModUtil::apiFunc('Multisites', 'admin', 'updateDBConfig',
        array('siteDNS' => $siteDNS,
        'siteDBName' => $siteDBName,
        'siteDBPass' => $siteDBPass,
        'siteDBUname' => $siteDBUname,
        'siteDBHost' => $siteDBHost,
        'siteDBType' => $siteDBType))) {
            LogUtil::registerError($this->__('Error updating the file multisites_dbconfig.php.'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // success
        LogUtil::registerStatus($this->__('The instance has been deleted'));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
    }

    /**
     * Load the icons that identify the modules availability for a site
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance identity and the modules state
     * @return:	Returns the needed icons
     */
    public function siteElementsIcons($args)
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
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('name', $name);
        $render->assign('available', $available);
        $render->assign('siteModules', $siteModules);
        $render->assign('instanceId', $instanceId);
        return $render->fetch('Multisites_admin_siteElementsIcons.htm');
    }

    /**
     * Edit an instance
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The instance identity
     * @return:	The form fields prepared to edit
     */
    public function edit($args)
    {
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('site', $site);
        return $render->fetch('Multisites_admin_edit.htm');
    }

    /**
     * Update and instance
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The instance information
     * @return:	Return to admin main page
     */
    public function update($args)
    {
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
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        $edited = ModUtil::apiFunc('Multisites', 'admin', 'updateInstance', array(
                'instanceId' => $instanceId,
                'items' => array('instanceName' => $instanceName,
                        'description' => $description,
                        'siteAdminRealName' => $siteAdminRealName,
                        'siteAdminEmail' => $siteAdminEmail,
                        'siteCompany' => $siteCompany,
                        'active' => $active)));
        if (!$edited) {
            LogUtil::registerError($this->__('Error editing instance'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // success
        LogUtil::registerStatus($this->__('The site information has been edited'));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
    }

    /**
     * Edit a model
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The model identity
     * @return:	The form fields prepared to edit
     */
    public function editModel($args)
    {
        $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                array('modelId' => $modelId));
        if ($model == false) {
            LogUtil::registerError($this->__('Model not found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'manageModels'));
        }
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('model', $model);
        return $render->fetch('Multisites_admin_editModel.htm');
    }

    /**
     * Update and instance
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The instance information
     * @return:	Return to admin main page
     */
    public function updateModel($args)
    {
        $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');
        $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'POST');
        $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
        $folders = FormUtil::getPassedValue('folders', isset($args['folders']) ? $args['folders'] : null, 'POST');
        $modelDBTablesPrefix = FormUtil::getPassedValue('modelDBTablesPrefix', isset($args['modelDBTablesPrefix']) ? $args['modelDBTablesPrefix'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'manageModels'));
        }
        $errorMsg = '';
        if ($modelName == null || $modelName == '') {
            $errorMsg = $this->__('Error! Please provide a model name. It is a mandatory field.<br />');
        }
        if ($modelDBTablesPrefix == null || $modelDBTablesPrefix == '') {
            $errorMsg .= $this->__('Error! Please provide the model database tables prefix. It is a mandatory field.<br />');
        }
        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                array('modelId' => $modelId));
        if ($model == false) {
            $errorMsg = $this->__('Model not found');
        }
        if ($errorMsg == '') {
            $edited = ModUtil::apiFunc('Multisites', 'admin', 'updateModel',
                    array('instanceId' => $instanceId,
                    'items' => array('modelName' => $modelName,
                            'description' => $description,
                            'folders' => $folders,
                            'modelDBTablesPrefix' => $modelDBTablesPrefix)));
            if (!$edited) {
                $errorMsg = $this->__('Error editing model');
            }
        }
        if ($errorMsg != '') {
            LogUtil::registerError($errorMsg);
            return System::redirect(ModUtil::url('Multisites', 'admin', 'editModel',
                    array('modelId' => $modelId)));
        }
        // success
        LogUtil::registerStatus($this->__('Model edited'));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'manageModels'));
    }

    /**
     * Show the form with the configurable parameters for the module
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The form fields
     */
    public function config()
    {
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('modelsFolder', ModUtil::getVar('Multisites', 'modelsFolder'));
        $render->assign('tempAccessFileContent', ModUtil::getVar('Multisites', 'tempAccessFileContent'));
        $render->assign('globalAdminName', ModUtil::getVar('Multisites', 'globalAdminName'));
        $render->assign('globalAdminPassword', ModUtil::getVar('Multisites', 'globalAdminPassword'));
        $render->assign('globalAdminemail', ModUtil::getVar('Multisites', 'globalAdminemail'));
        return $render->fetch('Multisites_admin_config.htm');
    }

    /**
     * Modify module configuration
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The module parameter values
     * @return:	return user to config page
     */
    public function updateConfig($args)
    {
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
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'main'));
        }
        ModUtil::setVar('Multisites', 'modelsFolder', $modelsFolder);
        ModUtil::setVar('Multisites', 'tempAccessFileContent', $tempAccessFileContent);
        ModUtil::setVar('Multisites', 'globalAdminName', $globalAdminName);
        ModUtil::setVar('Multisites', 'globalAdminPassword', $globalAdminPassword);
        ModUtil::setVar('Multisites', 'globalAdminemail', $globalAdminemail);
        // success
        LogUtil::registerStatus($this->__('The module configuration has been modified'));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'config'));
    }

    /**
     * Show the models availables
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	The list of models
     */
    public function manageModels()
    {
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $models = ModUtil::apiFunc('Multisites', 'user', 'getAllModels');
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('modelsArray', $models);
        return $render->fetch('Multisites_admin_manageModels.htm');
    }

    /**
     * Show the form needed to create a new model
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	Form fields
     */
    public function createNewModel($args)
    {
        $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'GET');
        $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'GET');
        $folders = FormUtil::getPassedValue('folders', isset($args['folders']) ? $args['folders'] : null, 'GET');
        $modelDBTablesPrefix = FormUtil::getPassedValue('modelDBTablesPrefix', isset($args['modelDBTablesPrefix']) ? $args['modelDBTablesPrefix'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // check if the models folders exists and it is writeable
        $path = ModUtil::getVar('Multisites', 'modelsFolder');
        // check if models folders is exists
        if (!file_exists($path)) {
            LogUtil::registerError($this->__('The models folder does not exists'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // check if models folders is writeable
        if (!is_writeable($path)) {
            LogUtil::registerError($this->__('The models folder is not writeable'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('modelName', $modelName);
        $render->assign('modelDBTablesPrefix', $modelDBTablesPrefix);
        $render->assign('description', $description);
        $render->assign('folders', $folders);
        return $render->fetch('Multisites_admin_newModel.htm');
    }

    /**
     * Create a new model and upload the model SQL file to the server
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The model properties and the file with the SQL
     * @return:	Returns true if success and false otherwise
     */
    public function createModel($args)
    {
        $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'POST');
        $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
        $folders = FormUtil::getPassedValue('folders', isset($args['folders']) ? $args['folders'] : null, 'POST');
        $modelFile = FormUtil::getPassedValue('modelFile', isset($args['modelFile']) ? $args['modelFile'] : null, 'FILES');
        $modelDBTablesPrefix = FormUtil::getPassedValue('modelDBTablesPrefix', isset($args['modelDBTablesPrefix']) ? $args['modelDBTablesPrefix'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        $errorMsg = '';
        if ($modelName == null || $modelName == '') {
            $errorMsg = $this->__('Error! Please provide a model name. It is a mandatory field.<br />');
        }
        if ($modelDBTablesPrefix == null || $modelDBTablesPrefix == '') {
            $errorMsg .= $this->__('Error! Please provide the model database tables prefix. It is a mandatory field.<br />');
        }
        if ($modelFile == null || $modelFile['name'] == '') {
            $errorMsg .= $this->__('Error! Please provide the model file. It is a mandatory field.<br />');
        }
        // check if the models folders exists and it is writeable
        $path = ModUtil::getVar('Multisites', 'modelsFolder');
        if (!is_writeable($path)) {
            $errorMsg .= $this->__('The models folder does not exists');
        }
        if ($errorMsg == '') {
            // check if the extension of the file is allowed
            $file_extension = strtolower(substr(strrchr($modelFile['name'], "."), 1));
            if ($file_extension != 'txt' && $file_extension != 'sql') {
                $errorMsg = $this->__('The model file extension is not allowed. The only allowed extensions are txt and sql');
            }
        }
        if ($errorMsg == '') {
            // prepare file name
            // replace spaces with _
            // check if file name exists into the folder. In this case change the name
            $fileName = str_replace(' ', '_', $modelFile['name']);
            $fitxer = $fileName;
            $i = 1;
            while (file_exists($path . '/' . $fileName)) {
                $fileName = substr($fitxer, 0, strlen($fitxer) - strlen($file_extension) - (1)) . $i . '.' . $file_extension;
                $i++;
            }
            // update the file
            if (!move_uploaded_file($modelFile['tmp_name'], $path . '/' . $fileName)) {
                $errorMsg = $this->__(' Error updating file');
            }
        }
        if ($errorMsg == '') {
            //Update model information
            $created = ModUtil::apiFunc('Multisites', 'admin', 'createModel',
                    array('modelName' => $modelName,
                    'description' => $description,
                    'fileName' => $fileName,
                    'folders' => $folders,
                    'modelDBTablesPrefix' => $modelDBTablesPrefix));
            if (!$created) {
                // delete the model file
                unlink($path . '/' . $fileName);
                $errorMsg = $this->__('Error creating model');
            }
        }
        if ($errorMsg != '') {
            LogUtil::registerError($errorMsg);
            return System::redirect(ModUtil::url('Multisites', 'admin', 'createNewModel',
                    array('modelName' => $modelName,
                    'modelDBTablesPrefix' => $modelDBTablesPrefix,
                    'description' => $description,
                    'folders' => $folders)));
        }
        // success
        LogUtil::registerStatus($this->__('A new model has been created'));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'manageModels'));
    }

    /**
     * Show the modules available for a site and allow to manage this feature
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance identity
     * @return:	The list of modules and its state in the site
     */
    public function siteElements($args)
    {
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GETPOST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Modules', 'admin', 'getfilemodules');
        sort($modules);
        // get all the modules available in site
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                array('instanceId' => $instanceId));
        foreach ($modules as $mod) {
            if ($mod['type'] != 3) {
                // if module exists in instance database
                $available = (array_key_exists($mod['name'], $siteModules)) ? 1 : 0;
                $icons = pnModFunc('Multisites', 'admin', 'siteElementsIcons',
                        array('instanceId' => $instanceId,
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
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('site', $site);
        $render->assign('modules', $modulesArray);
        return $render->fetch('Multisites_admin_siteElements.htm');
    }

    /**
     * Delete a model
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The model identity
     * @return:	Redirect user to the models page
     */
    public function deleteModel($args)
    {
        $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'GETPOST');
        $confirmation = FormUtil::getPassedValue('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                array('modelId' => $modelId));
        if ($model == false) {
            LogUtil::registerError($this->__('Model not found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'manageModels'));
        }
        if ($confirmation == null) {
            // create output object
            $render = Renderer::getInstance('Multisites', false);
            $render->assign('model', $model);
            return $render->fetch('Multisites_admin_deleteModel.htm');
        }
        // confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // delete file
        $deleted = unlink(ModUtil::getVar('Multisites', 'modelsFolder') . '/' . $model['fileName']);
        // delete model information
        if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteModel', array('modelId' => $model['modelId']))) {
            LogUtil::registerError($this->__('Error deleting the model'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'manageModels'));
        }
        // success
        LogUtil::registerStatus($this->__('Model deleted'));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'manageModels'));
    }

    /**
     * Show the themes available for a site and allow to manage this feature
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance identity
     * @return:	The list of themes and its state in the site
     */
    public function siteThemes($args)
    {
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GETPOST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // get all the themes available in themes directory
        $themes = ModUtil::apiFunc('Multisites', 'admin', 'getAllThemes');
        // get all the themes  inserted in site or instance database
        $siteThemes = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteThemes',
                array('instanceId' => $instanceId));
        $defaultTheme = ModUtil::apiFunc('Multisites', 'admin', 'getSiteDefaultTheme',
                array('instanceId' => $instanceId));
        $pos = strpos($defaultTheme, '"');
        $defaultTheme = substr($defaultTheme, $pos + 1, -2);
        foreach ($themes as $theme) {
            // if module exists in instance database
            $available = (array_key_exists($theme['name'], $siteThemes)) ? 1 : 0;
            $isDefaultTheme = (strtolower($theme['name']) == strtolower($defaultTheme)) ? 1 : 0;
            $icons = pnModFunc('Multisites', 'admin', 'siteThemesIcons',
                    array('instanceId' => $instanceId,
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
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('site', $site);
        $render->assign('themes', $themesArray);
        return $render->fetch('Multisites_admin_siteThemes.htm');
    }

    /**
     * Load the icons that identify the themes availability for a site
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance identity and the modules state
     * @return:	Returns the needed icons
     */
    public function siteThemesIcons($args)
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
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('name', $name);
        $render->assign('available', $available);
        $render->assign('isDefaultTheme', $isDefaultTheme);
        $render->assign('siteThemes', $siteThemes);
        $render->assign('instanceId', $instanceId);
        return $render->fetch('Multisites_admin_siteThemesIcons.htm');
    }

    /**
     * Set a theme as default
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	The instance identity and the theme name
     * @return:	Change the default theme
     */
    public function setThemeAsDefault($args)
    {
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'GET');
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $defaultTheme = ModUtil::apiFunc('Multisites', 'admin', 'setAsDefaultTheme',
                array('instanceId' => $instanceId,
                'name' => $name));
        // redirect to the admin main page
        return System::redirect(ModUtil::url('Multisites', 'admin', 'siteThemes',
                array('instanceId' => $instanceId)));
    }

    /**
     * Give access to some tools
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: Instance identity
     * @return:	The list of available tools
     */
    public function siteTools($args)
    {
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('site', $site);
        return $render->fetch('Multisites_admin_siteTools.htm');
    }

    /**
     * Execute some actions with administration tools
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: Instance identity, tool to use
     * @return:	The list of available tools
     */
    public function executeSiteTool($args)
    {
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'GET');
        $tool = FormUtil::getPassedValue('tool', isset($args['tool']) ? $args['tool'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }
        switch ($tool) {
            case 'createAdministrator':
                $createAdministrator = ModUtil::apiFunc('Multisites', 'admin', 'createAdministrator', array('instanceId' => $instanceId));
                if ($createAdministrator) {
                    LogUtil::registerStatus($this->__('A global administrator has been created'));
                }
                break;
            case 'adminSiteControl':
                $recoverAdminSiteControl = ModUtil::apiFunc('Multisites', 'admin', 'recoverAdminSiteControl', array('instanceId' => $instanceId));
                if ($recoverAdminSiteControl) {
                    LogUtil::registerStatus($this->__('The administration control had been recovered'));
                }
                break;
            default:
                LogUtil::registerError($this->__('Not tool selected'));
        }
        return System::redirect(ModUtil::url('Multisites', 'admin', 'siteTools',
                array('instanceId' => $instanceId)));
    }

    /**
     * Show the list of modules than can be actualized
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: The list of available modules
     */
    public function actualizer(){
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Modules', 'admin', 'getfilemodules');
        sort($modules);
        // checks if any module needs actualization for any site
        $i = 0;
        $upgradeNeeded = false;
        foreach($modules as $module){
            // get the number of sites which have an old version
            $numberOfSites = ModUtil::apiFunc('Multisites', 'admin', 'getNumberOfSites',
                    array('moduleName' => $module['name'],
                    'currentVersion' => $module['version']));
            if($numberOfSites > 0){
                $upgradeNeeded = true;
            }
            $modules[$i]['numberOfSites'] = $numberOfSites;
            $i++;
        }
        // create output object
        $render = Renderer::getInstance('Multisites', false);
        $render->assign('modules', $modules);
        $render->assign('upgradeNeeded', $upgradeNeeded);
        return $render->fetch('Multisites_admin_actualizer.htm');
    }

    /**
     * Actualize the selected module
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: an array with the modules that needs actualization
     * @return: The list of available modules
     */
    public function actualizeModule($args)
    {
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('siteDNS', '', 'GET') != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $GLOBALS['ZConfig']['Multisites']['mainSiteURL'] && $GLOBALS['ZConfig']['Multisites']['basedOnDomains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        if($moduleName == null){
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Modules', 'admin', 'getfilemodules');
        // get the module current version
        foreach($modules as $module){
            if($module['name'] == $moduleName){
                $moduleSelected = $module;
                break;
            }
        }
        $currentVersion = $moduleSelected['version'];
        // get the sites that need upgrade
        $sites = ModUtil::apiFunc('Multisites', 'admin', 'getSitesThatNeedUpgrade',
                array('moduleName' => $moduleName,
                'currentVersion' => $currentVersion));
        if(!$sites){
            LogUtil::registerError($this->__f("Not sites found that needs upgrade in module <strong>%s</strong>", $moduleName));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'actualizer'));
        }

        print_r($sites);die();
    }
}