<?php

class Multisites_Controller_Admin extends Zikula_AbstractController
{
    public function postInitialize()
    {
        $this->view->setCaching(false);
    }

    /**
     * Show the list of sites created
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: The list of sites
     */
    public function main($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $letter = $this->request->getGet()->get('letter', isset($args['letter']) ? $args['letter'] : null);
        $startnum = $this->request->getGet()->get('startnum', isset($args['startnum']) ? $args['startnum'] : 1);
        $itemsperpage = 10;
        // get sites
        $sites = ModUtil::apiFunc('Multisites', 'user', 'getAllSites',
                                   array('letter' => $letter,
                                         'itemsperpage' => $itemsperpage,
                                         'startnum' => $startnum));
        // get total sites
        $apiArgs = array();
        if (!is_null($letter)) {
            $apiArgs['letter'] = $letter;
        }
        $numSites = count(ModUtil::apiFunc('Multisites', 'user', 'getAllSites', $apiArgs));

        $pager = array('numitems' => $numSites,
                       'itemsperpage' => $itemsperpage);
        // create output object
        $this->view->assign('sites', $sites)
                   ->assign('pager', $pager)
                   ->assign('wwwroot', $this->serviceManager['multisites.wwwroot'])
                   ->assign('based_on_domains', $this->serviceManager['multisites.based_on_domains']);
        return $this->view->fetch('Multisites_admin_main.tpl');
    }

    /**
     * Show the form needed to create a new instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: The form needed to create a new instance
     */
    public function newIns($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceName = $this->request->getGet()->get('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null);
        $description = $this->request->getGet()->get('description', isset($args['description']) ? $args['description'] : null);
        $siteName = $this->request->getGet()->get('siteName', isset($args['siteName']) ? $args['siteName'] : null);
        $siteDescription = $this->request->getGet()->get('siteDescription', isset($args['siteDescription']) ? $args['siteDescription'] : null);
        $siteAdminName = $this->request->getGet()->get('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null);
        $siteAdminRealName = $this->request->getGet()->get('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null);
        $siteAdminEmail = $this->request->getGet()->get('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null);
        $siteCompany = $this->request->getGet()->get('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null);
        $sitedns = $this->request->getGet()->get('sitedns', isset($args['sitedns']) ? $args['sitedns'] : null);
        $siteDBName = $this->request->getGet()->get('siteDBName', isset($args['siteDBName']) ? $args['siteDBName'] : null);
        $siteDBUname = $this->request->getGet()->get('siteDBUname', isset($args['siteDBUname']) ? $args['siteDBUname'] : null);
        $siteDBHost = $this->request->getGet()->get('siteDBHost', isset($args['siteDBHost']) ? $args['siteDBHost'] : null);
        $siteDBType = $this->request->getGet()->get('siteDBType', isset($args['siteDBType']) ? $args['siteDBType'] : null);
        $siteDBPrefix = $this->request->getGet()->get('siteDBPrefix', isset($args['siteDBPrefix']) ? $args['siteDBPrefix'] : null);
        $createDB = $this->request->getGet()->get('createDB', isset($args['createDB']) ? $args['createDB'] : 0);
        $siteInitModel = $this->request->getGet()->get('siteInitModel', isset($args['siteInitModel']) ? $args['siteInitModel'] : null);
        $active = $this->request->getGet()->get('active', isset($args['active']) ? $args['active'] : 0);

        // get all the models for new instances
        $models = ModUtil::apiFunc('Multisites', 'user', 'getAllModels');
        if (!$models) {
            LogUtil::registerError($this->__('There is not any model defined'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // checks that multisites_dbconfig.php exists and it is writeable
        $path = 'config/multisites_dbconfig.php';
        $configFileWriteable = (is_writeable($path)) ? true : false;
        if (!$configFileWriteable) {
            $this->view->assign('configFileWriteable', $configFileWriteable);
            return $this->view->fetch('Multisites_admin_newNotPossible.tpl');
        }
        $this->view->assign('models', $models)
                   ->assign('instanceName', $instanceName)
                   ->assign('description', $description)
                   ->assign('siteName', $siteName)
                   ->assign('siteDescription', $siteDescription)
                   ->assign('siteAdminName', $siteAdminName)
                   ->assign('siteAdminRealName', $siteAdminRealName)
                   ->assign('siteAdminEmail', $siteAdminEmail)
                   ->assign('siteCompany', $siteCompany)
                   ->assign('sitedns', $sitedns)
                   ->assign('siteDBName', $siteDBName)
                   ->assign('siteDBUname', $siteDBUname)
                   ->assign('siteDBHost', $siteDBHost)
                   ->assign('siteDBType', $siteDBType)
                   ->assign('siteDBPrefix', $siteDBPrefix)
                   ->assign('createDB', $createDB)
                   ->assign('siteInitModel', $siteInitModel)
                   ->assign('active', $active);
        return $this->view->fetch('Multisites_admin_new.tpl');
    }

    /**
     * Create a new instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance properties received from the creation form
     * @return: Returns user to administrator main page
     */
    public function createInstance($args)
    {
        $this->checkCsrfToken();

        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceName = $this->request->getPost()->get('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $siteName = $this->request->getPost()->get('siteName', isset($args['siteName']) ? $args['siteName'] : null);
        $siteDescription = $this->request->getPost()->get('siteDescription', isset($args['siteDescription']) ? $args['siteDescription'] : null);
        $siteAdminName = $this->request->getPost()->get('siteAdminName', isset($args['siteAdminName']) ? $args['siteAdminName'] : null);
        $siteAdminPwd = $this->request->getPost()->get('siteAdminPwd', isset($args['siteAdminPwd']) ? $args['siteAdminPwd'] : null);
        $siteAdminRealName = $this->request->getPost()->get('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null);
        $siteAdminEmail = $this->request->getPost()->get('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null);
        $siteCompany = $this->request->getPost()->get('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null);
        $sitedns = $this->request->getPost()->get('sitedns', isset($args['sitedns']) ? $args['sitedns'] : null);
        $siteDBName = $this->request->getPost()->get('siteDBName', isset($args['siteDBName']) ? $args['siteDBName'] : null);
        $siteDBUname = $this->request->getPost()->get('siteDBUname', isset($args['siteDBUname']) ? $args['siteDBUname'] : null);
        $siteDBPass = $this->request->getPost()->get('siteDBPass', isset($args['siteDBPass']) ? $args['siteDBPass'] : null);
        $siteDBHost = $this->request->getPost()->get('siteDBHost', isset($args['siteDBHost']) ? $args['siteDBHost'] : null);
        $siteDBType = $this->request->getPost()->get('siteDBType', isset($args['siteDBType']) ? $args['siteDBType'] : null);
        $siteDBPrefix = $this->request->getPost()->get('siteDBPrefix', isset($args['siteDBPrefix']) ? $args['siteDBPrefix'] : null);
        $createDB = $this->request->getPost()->get('createDB', isset($args['createDB']) ? $args['createDB'] : 0);
        $siteInitModel = $this->request->getPost()->get('siteInitModel', isset($args['siteInitModel']) ? $args['siteInitModel'] : null);
        $active = $this->request->getPost()->get('active', isset($args['active']) ? $args['active'] : 0);

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
        if ($sitedns == null || $sitedns == '') {
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
        if ($sitedns != null) {
            // check that the sitedns exists and if it exists return error
            if (ModUtil::apiFunc('Multisites', 'user', 'getSiteInfo',
                                  array('site' => $sitedns))) {
                $errorMsg .= $this->__('This site just exists. The site DNS must be unique.');
            }
        }
        if ($siteInitModel != null) {
            // get model information
            $model = ModUtil::apiFunc('Multisites', 'user', 'getModel',
                                       array('modelName' => $siteInitModel));
            if ($model == false) {
                $errorMsg .= $this->__('Model not found');
            }
        }
        if ($errorMsg == '') {
            // create the instance directories
            $initDir = $this->serviceManager['multisites.files_real_path'] . '/' . $siteDBName;
            $initTemp = $initDir . $this->serviceManager['multisites.site_temp_files_folder'];
            $dirArray = array($initDir,
                              $initDir . $this->serviceManager['multisites.site_files_folder'],
                              $initTemp,
                              $initTemp . '/error_logs',
                              $initTemp . '/idsTmp',
                              $initTemp . '/purifierCache',
                              $initTemp . '/view_cache',
                              $initTemp . '/view_compiled',
                              $initTemp . '/Theme_cache',
                              $initTemp . '/Theme_compiled',
                              $initTemp . '/Theme_Config');
            $modelFoldersArray = explode(',', $model['folders']);
            foreach ($modelFoldersArray as $folder) {
                if ($folder != '') {
                    $dirArray[] = $initDir . $this->serviceManager['multisites.site_files_folder'] . '/' . trim($folder);
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
                                         'siteDescription' => $siteDescription,
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
                                   array('sitedns' => $sitedns,
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
            $tempAccessFileContent = $this->getVar('tempAccessFileContent');
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
                                               'siteDescription' => $siteDescription,
                                               'siteAdminName' => $siteAdminName,
                                               'siteAdminPwd' => $siteAdminPwd,
                                               'siteAdminRealName' => $siteAdminRealName,
                                               'siteAdminEmail' => $siteAdminEmail,
                                               'siteCompany' => $siteCompany,
                                               'sitedns' => $sitedns,
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
            return $this->redirect(ModUtil::url($this->name, 'admin', 'newIns',
                                                  array('instanceName' => $instanceName,
                                                        'description' => $description,
                                                        'siteName' => $siteName,
                                                        'siteDescription' => $siteDescription,
                                                        'siteAdminName' => $siteAdminName,
                                                        'siteAdminRealName' => $siteAdminRealName,
                                                        'siteAdminEmail' => $siteAdminEmail,
                                                        'siteCompany' => $siteCompany,
                                                        'sitedns' => $sitedns,
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
        return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
    }

    /**
     * Delete an instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity
     * @return: Returns true if success and false otherwise
     */
    public function deleteInstance($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = null;
        if ($this->request->getPost()->has('instanceId')) {
            $instanceId = $this->request->getPost()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        }
        elseif ($this->request->getGet()->has('instanceId')) {
            $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        }
        $confirmation = $this->request->getPost()->get('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null);
        $deleteDB = $this->request->getPost()->get('deleteDB', isset($args['deleteDB']) ? $args['deleteDB'] : 0);
        $deleteFiles = $this->request->getPost()->get('deleteFiles', isset($args['deleteFiles']) ? $args['deleteFiles'] : 0);

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        if ($confirmation == null) {
            // create output object
            $this->view->assign('instance', $site);
            return $this->view->fetch('Multisites_admin_deleteInstance.tpl');
        }

        $this->checkCsrfToken();

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
                              array('dirName' => $this->serviceManager['multisites.files_real_path'] . '/' . $site['siteDBName']));
        }
        // delete instance information
        if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteInstance',
                               array('instanceId' => $site['instanceId']))) {
            LogUtil::registerError($this->__('The instance deletion has failed'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // modify multisites_dbconfig files
        if (!ModUtil::apiFunc('Multisites', 'admin', 'updateDBConfig',
                               array('sitedns' => $sitedns,
                                     'siteDBName' => $siteDBName,
                                     'siteDBPass' => $siteDBPass,
                                     'siteDBUname' => $siteDBUname,
                                     'siteDBHost' => $siteDBHost,
                                     'siteDBType' => $siteDBType))) {
            LogUtil::registerError($this->__('Error updating the file multisites_dbconfig.php.'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // success
        LogUtil::registerStatus($this->__('The instance has been deleted'));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
    }

    /**
     * Load the icons that identify the modules availability for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the modules state
     * @return: Returns the needed icons
     */
    public function siteElementsIcons($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $name = $this->request->getPost()->get('name', isset($args['name']) ? $args['name'] : null);
        $available = $this->request->getPost()->get('available', isset($args['available']) ? $args['available'] : null);
        $siteModules = $this->request->getPost()->get('siteModules', isset($args['siteModules']) ? $args['siteModules'] : null);
        $instanceId = $this->request->getPost()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);

        $this->view->assign('name', $name)
                   ->assign('available', $available)
                   ->assign('siteModules', $siteModules)
                   ->assign('instanceId', $instanceId);
        return $this->view->fetch('Multisites_admin_siteElementsIcons.tpl');
    }

    /**
     * Edit an instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The instance identity
     * @return: The form fields prepared to edit
     */
    public function edit($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        // create output object
        $this->view->assign('site', $site);
        return $this->view->fetch('Multisites_admin_edit.tpl');
    }

    /**
     * Update an instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The instance information
     * @return: Return to admin main page
     */
    public function update($args)
    {
        $this->checkCsrfToken();

        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = $this->request->getPost()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        $instanceName = $this->request->getPost()->get('instanceName', isset($args['instanceName']) ? $args['instanceName'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $siteAdminRealName = $this->request->getPost()->get('siteAdminRealName', isset($args['siteAdminRealName']) ? $args['siteAdminRealName'] : null);
        $siteAdminEmail = $this->request->getPost()->get('siteAdminEmail', isset($args['siteAdminEmail']) ? $args['siteAdminEmail'] : null);
        $siteCompany = $this->request->getPost()->get('siteCompany', isset($args['siteCompany']) ? $args['siteCompany'] : null);
        $active = $this->request->getPost()->get('active', isset($args['active']) ? $args['active'] : 0);

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        $edited = ModUtil::apiFunc('Multisites', 'admin', 'updateInstance',
                                    array('instanceId' => $instanceId,
                                          'items' => array('instanceName' => $instanceName,
                                          'description' => $description,
                                          'siteAdminRealName' => $siteAdminRealName,
                                          'siteAdminEmail' => $siteAdminEmail,
                                          'siteCompany' => $siteCompany,
                                          'active' => $active)));
        if (!$edited) {
            LogUtil::registerError($this->__('Error editing instance'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // success
        LogUtil::registerStatus($this->__('The site information has been edited'));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
    }

    /**
     * Edit a model
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The model identity
     * @return: The form fields prepared to edit
     */
    public function editModel($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $modelId = $this->request->getGet()->get('modelId', isset($args['modelId']) ? $args['modelId'] : null);

        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelId' => $modelId));
        if ($model == false) {
            LogUtil::registerError($this->__('Model not found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'manageModels'));
        }
        // create output object
        $render = Zikula_View::getInstance('Multisites', false);
        $this->view->assign('model', $model);
        return $this->view->fetch('Multisites_admin_editModel.tpl');
    }

    /**
     * Update and instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: The instance information
     * @return: Return to admin main page
     */
    public function updateModel($args)
    {
        $this->checkCsrfToken();

        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $modelId = $this->request->getPost()->get('modelId', isset($args['modelId']) ? $args['modelId'] : null);
        $modelName = $this->request->getPost()->get('modelName', isset($args['modelName']) ? $args['modelName'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $folders = $this->request->getPost()->get('folders', isset($args['folders']) ? $args['folders'] : null);
        $modelDBTablesPrefix = $this->request->getPost()->get('modelDBTablesPrefix', isset($args['modelDBTablesPrefix']) ? $args['modelDBTablesPrefix'] : null);

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
            return $this->redirect(ModUtil::url($this->name, 'admin', 'editModel',
                                                  array('modelId' => $modelId)));
        }
        // success
        LogUtil::registerStatus($this->__('Model edited'));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'manageModels'));
    }

    /**
     * Show the form with the configurable parameters for the module
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: The form fields
     */
    public function config()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // create output object
        $this->view->assign('modelsFolder', $this->getVar('modelsFolder'))
                   ->assign('tempAccessFileContent', $this->getVar('tempAccessFileContent'))
                   ->assign('globalAdminName', $this->getVar('globalAdminName'))
                   ->assign('globalAdminPassword', $this->getVar('globalAdminPassword'))
                   ->assign('globalAdminemail', $this->getVar('globalAdminemail'));
        return $this->view->fetch('Multisites_admin_config.tpl');
    }

    /**
     * Modify module configuration
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The module parameter values
     * @return: return user to config page
     */
    public function updateConfig($args)
    {
        $this->checkCsrfToken();

        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $modelsFolder = $this->request->getPost()->get('modelsFolder', isset($args['modelsFolder']) ? $args['modelsFolder'] : null);
        $tempAccessFileContent = $this->request->getPost()->get('tempAccessFileContent', isset($args['tempAccessFileContent']) ? $args['tempAccessFileContent'] : null);
        $globalAdminName = $this->request->getPost()->get('globalAdminName', isset($args['globalAdminName']) ? $args['globalAdminName'] : null);
        $globalAdminPassword = $this->request->getPost()->get('globalAdminPassword', isset($args['globalAdminPassword']) ? $args['globalAdminPassword'] : null);
        $globalAdminemail = $this->request->getPost()->get('globalAdminemail', isset($args['globalAdminemail']) ? $args['globalAdminemail'] : null);

        $this->setVar('modelsFolder', $modelsFolder);
        $this->setVar('tempAccessFileContent', $tempAccessFileContent);
        $this->setVar('globalAdminName', $globalAdminName);
        $this->setVar('globalAdminPassword', $globalAdminPassword);
        $this->setVar('globalAdminemail', $globalAdminemail);
        // success
        LogUtil::registerStatus($this->__('The module configuration has been modified'));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'config'));
    }

    /**
     * Show the models availables
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: The list of models
     */
    public function manageModels()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $models = ModUtil::apiFunc('Multisites', 'user', 'getAllModels');
        // create output object
        $this->view->assign('modelsArray', $models);
        return $this->view->fetch('Multisites_admin_manageModels.tpl');
    }

    /**
     * Show the form needed to create a new model
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: Form fields
     */
    public function createNewModel($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $modelName = $this->request->getGet()->get('modelName', isset($args['modelName']) ? $args['modelName'] : null);
        $description = $this->request->getGet()->get('description', isset($args['description']) ? $args['description'] : null);
        $folders = $this->request->getGet()->get('folders', isset($args['folders']) ? $args['folders'] : null);
        $modelDBTablesPrefix = $this->request->getGet()->get('modelDBTablesPrefix', isset($args['modelDBTablesPrefix']) ? $args['modelDBTablesPrefix'] : null);

        // check if the models folders exists and it is writeable
        $path = $this->getVar('modelsFolder');
        // check if models folders is exists
        if (!file_exists($path)) {
            LogUtil::registerError($this->__('The models folder does not exists'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // check if models folders is writeable
        if (!is_writeable($path)) {
            LogUtil::registerError($this->__('The models folder is not writeable'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // get all the models for new instances
        $models = ModUtil::apiFunc('Multisites', 'user', 'getAllModels');
        $modelsFiles = array();
        foreach ($models as $model) {
            if (!in_array($model['fileName'], $modelsFiles)) {
                $modelsFiles[$model['modelId']] = $model['fileName'];
            }
        }
        $this->view->assign('modelName', $modelName)
                   ->assign('modelDBTablesPrefix', $modelDBTablesPrefix)
                   ->assign('description', $description)
                   ->assign('folders', $folders)
                   ->assign('modelsFiles', $modelsFiles);
        return $this->view->fetch('Multisites_admin_newModel.tpl');
    }

    /**
     * Create a new model and upload the model SQL file to the server
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model properties and the file with the SQL
     * @return: Returns true if success and false otherwise
     */
    public function createModel($args)
    {
        $this->checkCsrfToken();

        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $modelName = $this->request->getPost()->get('modelName', isset($args['modelName']) ? $args['modelName'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $folders = $this->request->getPost()->get('folders', isset($args['folders']) ? $args['folders'] : null);
        $modelFile = $this->request->getFiles()->get('modelFile', isset($args['modelFile']) ? $args['modelFile'] : null);
        $modelDBTablesPrefix = $this->request->getPost()->get('modelDBTablesPrefix', isset($args['modelDBTablesPrefix']) ? $args['modelDBTablesPrefix'] : null);
        $modelFileSelected = $this->request->getPost()->get('modelFileSelected', isset($args['modelFileSelected']) ? $args['modelFileSelected'] : 0);

        $errorMsg = '';
        if ($modelName == null || $modelName == '') {
            $errorMsg = $this->__('Error! Please provide a model name. It is a mandatory field.<br />');
        }
        if ($modelDBTablesPrefix == null || $modelDBTablesPrefix == '') {
            $errorMsg .= $this->__('Error! Please provide the model database tables prefix. It is a mandatory field.<br />');
        }
        if (($modelFile == null || $modelFile['name'] == '') && $modelFileSelected == '0') {
            $errorMsg .= $this->__('Error! Please provide the model file. It is a mandatory field.<br />');
        }
        // check if the models folders exists and it is writeable
        $path = $this->getVar('modelsFolder');
        if (!is_writeable($path)) {
            $errorMsg .= $this->__('The models folder does not exists');
        }
        if ($modelFileSelected == '0') {
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
        } else {
            $fileName = $modelFileSelected;
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
            return $this->redirect(ModUtil::url($this->name, 'admin', 'createNewModel',
                                                  array('modelName' => $modelName,
                                                        'modelDBTablesPrefix' => $modelDBTablesPrefix,
                                                        'description' => $description,
                                                        'folders' => $folders)));
        }
        // success
        LogUtil::registerStatus($this->__('A new model has been created'));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'manageModels'));
    }

    /**
     * Show the modules available for a site and allow to manage this feature
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity
     * @return: The list of modules and its state in the site
     */
    public function siteElements($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = null;
        if ($this->request->getPost()->has('instanceId')) {
            $instanceId = $this->request->getPost()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        }
        elseif ($this->request->getGet()->has('instanceId')) {
            $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
        sort($modules);
        // get all the modules available in site
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                                         array('instanceId' => $instanceId));
        foreach ($modules as $mod) {
            if ($mod['type'] != 3) {
                // if module exists in instance database
                $available = (array_key_exists($mod['name'], $siteModules)) ? 1 : 0;
                $icons = ModUtil::func('Multisites', 'admin', 'siteElementsIcons',
                                        array('instanceId' => $instanceId,
                                              'name' => $mod['name'],
                                              'available' => $available,
                                              'siteModules' => $siteModules));
                $modulesArray[] = array('name' => $mod['name'],
                                        'version' => $mod['version'],
                                        'description' => $mod['description'],
                                        'icons' => $icons);
            }
        }
        $this->view->assign('site', $site);
        $this->view->assign('modules', $modulesArray);
        return $this->view->fetch('Multisites_admin_siteElements.tpl');
    }

    /**
     * Delete a model
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model identity
     * @return: Redirect user to the models page
     */
    public function deleteModel($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $modelId = null;
        if ($this->request->getPost()->has('modelId')) {
            $modelId = $this->request->getPost()->get('modelId', isset($args['modelId']) ? $args['modelId'] : null);
        }
        elseif ($this->request->getGet()->has('modelId')) {
            $modelId = $this->request->getGet()->get('modelId', isset($args['modelId']) ? $args['modelId'] : null);
        }

        $confirmation = $this->request->getPost()->get('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null);

        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelId' => $modelId));
        if ($model == false) {
            LogUtil::registerError($this->__('Model not found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'manageModels'));
        }
        if ($confirmation == null) {
            // create output object
            $render = Zikula_View::getInstance('Multisites', false);
            $this->view->assign('model', $model);
            return $this->view->fetch('Multisites_admin_deleteModel.tpl');
        }

        $this->checkCsrfToken();

        // delete file if it is not needed for any model
        // get all the models for new instances
        $fileNeeded = false;
        $models = ModUtil::apiFunc('Multisites', 'user', 'getAllModels');
        foreach ($models as $m) {
            if ($m['fileName'] == $model['fileName'] && $m['modelId'] != $model['modelId']) {
                $fileNeeded = true;
            }
        }
        if (!$fileNeeded) {
            $deleted = unlink($this->getVar('modelsFolder') . '/' . $model['fileName']);
        }
        // delete model information
        if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteModel',
                               array('modelId' => $model['modelId']))) {
            LogUtil::registerError($this->__('Error deleting the model'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'manageModels'));
        }
        // success
        LogUtil::registerStatus($this->__('Model deleted'));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'manageModels'));
    }

    /**
     * Show the themes available for a site and allow to manage this feature
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity
     * @return: The list of themes and its state in the site
     */
    public function siteThemes($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = null;
        if ($this->request->getPost()->has('instanceId')) {
            $instanceId = $this->request->getPost()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        }
        elseif ($this->request->getGet()->has('instanceId')) {
            $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
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
            $icons = ModUtil::func('Multisites', 'admin', 'siteThemesIcons',
                                    array('instanceId' => $instanceId,
                                          'name' => $theme['name'],
                                          'available' => $available,
                                          'isDefaultTheme' => $isDefaultTheme,
                                          'siteThemes' => $siteThemes));
            $themesArray[] = array('name' => $theme['name'],
                                   'version' => $theme['version'],
                                   'description' => $theme['description'],
                                   'icons' => $icons);
        }
        // create output object
        $render = Zikula_View::getInstance('Multisites', false);
        $this->view->assign('site', $site)
                   ->assign('themes', $themesArray);
        return $this->view->fetch('Multisites_admin_siteThemes.tpl');
    }

    /**
     * Load the icons that identify the themes availability for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the modules state
     * @return: Returns the needed icons
     */
    public function siteThemesIcons($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $name = $this->request->getPost()->get('name', isset($args['name']) ? $args['name'] : null);
        $available = $this->request->getPost()->get('available', isset($args['available']) ? $args['available'] : null);
        $siteThemes = $this->request->getPost()->get('siteThemes', isset($args['siteThemes']) ? $args['siteThemes'] : null);
        $instanceId = $this->request->getPost()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        $isDefaultTheme = $this->request->getPost()->get('isDefaultTheme', isset($args['isDefaultTheme']) ? $args['isDefaultTheme'] : null);

        $this->view->assign('name', $name)
                   ->assign('available', $available)
                   ->assign('isDefaultTheme', $isDefaultTheme)
                   ->assign('siteThemes', $siteThemes)
                   ->assign('instanceId', $instanceId);
        return $this->view->fetch('Multisites_admin_siteThemesIcons.tpl');
    }

    /**
     * Set a theme as default
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the theme name
     * @return: Change the default theme
     */
    public function setThemeAsDefault($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $name = $this->request->getGet()->get('name', isset($args['name']) ? $args['name'] : null);
        $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);

        $defaultTheme = ModUtil::apiFunc('Multisites', 'admin', 'setAsDefaultTheme',
                                          array('instanceId' => $instanceId,
                'name' => $name));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'siteThemes',
                                              array('instanceId' => $instanceId)));
    }

    /**
     * Give access to some tools
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: Instance identity
     * @return: The list of available tools
     */
    public function siteTools($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        $this->view->assign('site', $site);
        return $this->view->fetch('Multisites_admin_siteTools.tpl');
    }

    /**
     * Execute some actions with administration tools
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: Instance identity, tool to use
     * @return: The list of available tools
     */
    public function executeSiteTool($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $instanceId = $this->request->getGet()->get('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null);
        $tool = $this->request->getGet()->get('tool', isset($args['tool']) ? $args['tool'] : null);

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        switch ($tool) {
            case 'createAdministrator':
                $createAdministrator = ModUtil::apiFunc('Multisites', 'admin', 'createAdministrator',
                                                         array('instanceId' => $instanceId));
                if ($createAdministrator) {
                    LogUtil::registerStatus($this->__('A global administrator has been created'));
                }
                break;
            case 'adminSiteControl':
                $recoverAdminSiteControl = ModUtil::apiFunc('Multisites', 'admin', 'recoverAdminSiteControl',
                                                             array('instanceId' => $instanceId));
                if ($recoverAdminSiteControl) {
                    LogUtil::registerStatus($this->__('The administration control has been recovered'));
                }
                break;
            default:
                LogUtil::registerError($this->__('Not tool selected'));
        }
        return $this->redirect(ModUtil::url($this->name, 'admin', 'siteTools',
                array('instanceId' => $instanceId)));
    }

    /**
     * Show the list of modules than can be actualized
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: The list of available modules
     */
    public function actualizer()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
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
        $this->view->assign('modules', $modules)
                   ->assign('upgradeNeeded', $upgradeNeeded);
        return $this->view->fetch('Multisites_admin_actualizer.tpl');
    }

    /**
     * Actualize the selected module
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: an array with the modules that needs actualization
     * @return: The list of available modules
     */
    public function actualizeModule($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $moduleName = $this->request->getGet()->get('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null);

        if ($moduleName == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
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
        if (!$sites) {
            LogUtil::registerError($this->__f("Not sites found that needs upgrade in module <strong>%s</strong>", $moduleName));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'actualizer'));
        }

        print_r($sites);die();
    }
}
