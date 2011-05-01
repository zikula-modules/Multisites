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

        $instancename = $this->request->getGet()->get('instancename', isset($args['instancename']) ? $args['instancename'] : null);
        $description = $this->request->getGet()->get('description', isset($args['description']) ? $args['description'] : null);
        $sitename = $this->request->getGet()->get('sitename', isset($args['sitename']) ? $args['sitename'] : null);
        $siteDescription = $this->request->getGet()->get('siteDescription', isset($args['siteDescription']) ? $args['siteDescription'] : null);
        $siteadminname = $this->request->getGet()->get('siteadminname', isset($args['siteadminname']) ? $args['siteadminname'] : null);
        $siteadminrealname = $this->request->getGet()->get('siteadminrealname', isset($args['siteadminrealname']) ? $args['siteadminrealname'] : null);
        $siteadminemail = $this->request->getGet()->get('siteadminemail', isset($args['siteadminemail']) ? $args['siteadminemail'] : null);
        $sitecompany = $this->request->getGet()->get('sitecompany', isset($args['sitecompany']) ? $args['sitecompany'] : null);
        $sitedns = $this->request->getGet()->get('sitedns', isset($args['sitedns']) ? $args['sitedns'] : null);
        $sitedbname = $this->request->getGet()->get('sitedbname', isset($args['sitedbname']) ? $args['sitedbname'] : null);
        $sitedbuname = $this->request->getGet()->get('sitedbuname', isset($args['sitedbuname']) ? $args['sitedbuname'] : null);
        $sitedbhost = $this->request->getGet()->get('sitedbhost', isset($args['sitedbhost']) ? $args['sitedbhost'] : null);
        $sitedbtype = $this->request->getGet()->get('sitedbtype', isset($args['sitedbtype']) ? $args['sitedbtype'] : null);
        $sitedbprefix = $this->request->getGet()->get('sitedbprefix', isset($args['sitedbprefix']) ? $args['sitedbprefix'] : null);
        $createDB = $this->request->getGet()->get('createDB', isset($args['createDB']) ? $args['createDB'] : 0);
        $siteinitmodel = $this->request->getGet()->get('siteinitmodel', isset($args['siteinitmodel']) ? $args['siteinitmodel'] : null);
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
                   ->assign('instancename', $instancename)
                   ->assign('description', $description)
                   ->assign('sitename', $sitename)
                   ->assign('siteDescription', $siteDescription)
                   ->assign('siteadminname', $siteadminname)
                   ->assign('siteadminrealname', $siteadminrealname)
                   ->assign('siteadminemail', $siteadminemail)
                   ->assign('sitecompany', $sitecompany)
                   ->assign('sitedns', $sitedns)
                   ->assign('sitedbname', $sitedbname)
                   ->assign('sitedbuname', $sitedbuname)
                   ->assign('sitedbhost', $sitedbhost)
                   ->assign('sitedbtype', $sitedbtype)
                   ->assign('sitedbprefix', $sitedbprefix)
                   ->assign('createDB', $createDB)
                   ->assign('siteinitmodel', $siteinitmodel)
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

        $instancename = $this->request->getPost()->get('instancename', isset($args['instancename']) ? $args['instancename'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $sitename = $this->request->getPost()->get('sitename', isset($args['sitename']) ? $args['sitename'] : null);
        $siteDescription = $this->request->getPost()->get('siteDescription', isset($args['siteDescription']) ? $args['siteDescription'] : null);
        $siteadminname = $this->request->getPost()->get('siteadminname', isset($args['siteadminname']) ? $args['siteadminname'] : null);
        $siteadminpwd = $this->request->getPost()->get('siteadminpwd', isset($args['siteadminpwd']) ? $args['siteadminpwd'] : null);
        $siteadminrealname = $this->request->getPost()->get('siteadminrealname', isset($args['siteadminrealname']) ? $args['siteadminrealname'] : null);
        $siteadminemail = $this->request->getPost()->get('siteadminemail', isset($args['siteadminemail']) ? $args['siteadminemail'] : null);
        $sitecompany = $this->request->getPost()->get('sitecompany', isset($args['sitecompany']) ? $args['sitecompany'] : null);
        $sitedns = $this->request->getPost()->get('sitedns', isset($args['sitedns']) ? $args['sitedns'] : null);
        $sitedbname = $this->request->getPost()->get('sitedbname', isset($args['sitedbname']) ? $args['sitedbname'] : null);
        $sitedbuname = $this->request->getPost()->get('sitedbuname', isset($args['sitedbuname']) ? $args['sitedbuname'] : null);
        $sitedbpass = $this->request->getPost()->get('sitedbpass', isset($args['sitedbpass']) ? $args['sitedbpass'] : null);
        $sitedbhost = $this->request->getPost()->get('sitedbhost', isset($args['sitedbhost']) ? $args['sitedbhost'] : null);
        $sitedbtype = $this->request->getPost()->get('sitedbtype', isset($args['sitedbtype']) ? $args['sitedbtype'] : null);
        $sitedbprefix = $this->request->getPost()->get('sitedbprefix', isset($args['sitedbprefix']) ? $args['sitedbprefix'] : null);
        $createDB = $this->request->getPost()->get('createDB', isset($args['createDB']) ? $args['createDB'] : 0);
        $siteinitmodel = $this->request->getPost()->get('siteinitmodel', isset($args['siteinitmodel']) ? $args['siteinitmodel'] : null);
        $active = $this->request->getPost()->get('active', isset($args['active']) ? $args['active'] : 0);

        $errorMsg = '';
        if ($instancename == null || $instancename == '') {
            $errorMsg = $this->__('Error! Please provide an instance name. It is a mandatory field.') . '<br />';
        }
        if ($siteadminname == null || $siteadminname == '') {
            $errorMsg .= $this->__('Error! Please provide an admin\'s site name. It is a mandatory field.') . '<br />';
        }
        if ($siteadminpwd == null || $siteadminpwd == '') {
            $errorMsg .= $this->__('Error! Please provide an admin\'s site password. It is a mandatory field.') . '<br />';
        }
        if ($siteadminemail == null || $siteadminemail == '') {
            $errorMsg .= $this->__('Error! Please provide an admin\'s site email. It is a mandatory field.') . '<br />';
        }
        if ($sitedns == null || $sitedns == '') {
            $errorMsg .= $this->__('Error! Please provide the site domain. It is a mandatory field.') . '<br />';
        }
        if ($sitedbhost == null || $sitedbhost == '') {
            $errorMsg .= $this->__('Error! Please provide the site database host. It is a mandatory field.') . '<br />';
        }
        if ($sitedbhost == null || $sitedbhost == '') {
            $errorMsg .= $this->__('Error! Please provide the site database host. It is a mandatory field.') . '<br />';
        }
        if ($sitedbname == null || $sitedbname == '') {
            $errorMsg .= $this->__('Error! Please provide the site database name. It is a mandatory field.') . '<br />';
        }
        if ($sitedbuname == null || $sitedbuname == '') {
            $errorMsg .= $this->__('Error! Please provide the site database user name. It is a mandatory field.') . '<br />';
        }
        if ($sitedbpass == null || $sitedbpass == '') {
            $errorMsg .= $this->__('Error! Please provide the site database user password. It is a mandatory field.') . '<br />';
        }
        if ($sitedbprefix == null || $sitedbprefix == '') {
            $errorMsg .= $this->__('Error! Please provide the site database prefix. It is a mandatory field.') . '<br />';
        }
        if ($siteinitmodel == null || $siteinitmodel == '') {
            $errorMsg .= $this->__('Error! Please provide the model on the site will be based. It is a mandatory field.') . '<br />';
        }
        if ($sitedns != null) {
            // check that the sitedns exists and if it exists return error
            if (ModUtil::apiFunc('Multisites', 'user', 'getSiteInfo',
                                  array('site' => $sitedns))) {
                $errorMsg .= $this->__('This site just exists. The site DNS must be unique.');
            }
        }
        if ($siteinitmodel != null) {
            // get model information
            $model = ModUtil::apiFunc('Multisites', 'user', 'getModel',
                                       array('modelname' => $siteinitmodel));
            if ($model == false) {
                $errorMsg .= $this->__('Model not found');
            }
        }
        if ($errorMsg == '') {
            // create the instance directories
            $initDir = $this->serviceManager['multisites.files_real_path'] . '/' . /*$sitedbname*/$sitedns;
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
                        $errorMsg = $this->__('Error! Creating site directories failed') . ': ' . $dir;
                    }
                } else if (!is_writeable($dir)) $errorMsg = $this->__f('Error with the folder <strong>%s</strong> because it is not writeable.', array($dir));
            }
        }
        if ($createDB == 1 && $errorMsg == '') {
            // create a new database if it doesn't exist
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createDB',
                                   array('sitedbname' => $sitedbname,
                                         'sitedbuname' => $sitedbuname,
                                         'sitedbpass' => $sitedbpass,
                                         'sitedbtype' => $sitedbtype,
                                         'sitedbhost' => $sitedbhost))) {
                $errorMsg = $this->__('Error! Creation of database failed.');
            }
        }
        if ($errorMsg == '') {
            // created the database tables based on the model file
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createTables',
                                   array('filename' => $model['filename'],
                                         'modeldbtablesprefix' => $model['modeldbtablesprefix'],
                                         'sitedbname' => $sitedbname,
                                         'sitedbpass' => $sitedbpass,
                                         'sitedbuname' => $sitedbuname,
                                         'sitedbhost' => $sitedbhost,
                                         'sitedbtype' => $sitedbtype,
                                         'sitedbprefix' => $sitedbprefix))) {
                $errorMsg = $this->__('Error! Creation of database tables failed.');
            }
        }
        if ($errorMsg == '') {
            // update instance values like admin name, admin password, cookie name, site name...
            if (!ModUtil::apiFunc('Multisites', 'admin', 'updateConfigValues',
                                   array('siteadminname' => $siteadminname,
                                         'siteadminpwd' => $siteadminpwd,
                                         'siteadminemail' => $siteadminemail,
                                         'sitename' => $sitename,
                                         'siteDescription' => $siteDescription,
                                         'sitedbname' => $sitedbname,
                                         'sitedbpass' => $sitedbpass,
                                         'sitedbuname' => $sitedbuname,
                                         'sitedbhost' => $sitedbhost,
                                         'sitedbtype' => $sitedbtype,
                                         'sitedbprefix' => $sitedbprefix))) {
                $errorMsg = $this->__('Error! Updating the site configuration failed.');
            }
        }
        if ($errorMsg == '') {
            // modify multisites_dbconfig file
            if (!ModUtil::apiFunc('Multisites', 'admin', 'updateDBConfig',
                                   array('sitedns' => $sitedns,
                                         'sitedbname' => $sitedbname,
                                         'sitedbpass' => $sitedbpass,
                                         'sitedbuname' => $sitedbuname,
                                         'sitedbhost' => $sitedbhost,
                                         'sitedbtype' => $sitedbtype,
                                         'sitedbprefix' => $sitedbprefix))) {
                $errorMsg = $this->__('Error! Updating the file multisites_dbconfig.php failed.');
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
                                         array('instancename' => $instancename,
                                               'description' => $description,
                                               'sitename' => $sitename,
                                               'siteDescription' => $siteDescription,
                                               'siteadminname' => $siteadminname,
                                               'siteadminpwd' => $siteadminpwd,
                                               'siteadminrealname' => $siteadminrealname,
                                               'siteadminemail' => $siteadminemail,
                                               'sitecompany' => $sitecompany,
                                               'sitedns' => $sitedns,
                                               'sitedbname' => $sitedbname,
                                               'sitedbuname' => $sitedbuname,
                                               'sitedbpass' => $sitedbpass,
                                               'sitedbhost' => $sitedbhost,
                                               'sitedbtype' => $sitedbtype,
                                               'sitedbprefix' => $sitedbprefix,
                                               'siteinitmodel' => $siteinitmodel,
                                               'active' => $active));
            if ($created == false) {
                $errorMsg = $this->__('Error! Creation of the instance failed.');
            }
        }
        if ($errorMsg != '') {
            LogUtil::registerError($errorMsg);
            return $this->redirect(ModUtil::url($this->name, 'admin', 'newIns',
                                                  array('instancename' => $instancename,
                                                        'description' => $description,
                                                        'sitename' => $sitename,
                                                        'siteDescription' => $siteDescription,
                                                        'siteadminname' => $siteadminname,
                                                        'siteadminrealname' => $siteadminrealname,
                                                        'siteadminemail' => $siteadminemail,
                                                        'sitecompany' => $sitecompany,
                                                        'sitedns' => $sitedns,
                                                        'sitedbtype' => $sitedbtype,
                                                        'sitedbhost' => $sitedbhost,
                                                        'sitedbname' => $sitedbname,
                                                        'sitedbuname' => $sitedbuname,
                                                        'sitedbprefix' => $sitedbprefix,
                                                        'createDB' => $createDB,
                                                        'siteinitmodel' => $siteinitmodel,
                                                        'active' => $active)));
        }
        //******* PNN *******
        // save the site module in database
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'saveSiteModules',
                                         array('instanceid' => $created));
        //*******
        // success
        LogUtil::registerStatus($this->__('Done! A new instance has been created.'));
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

        $instanceid = null;
        if ($this->request->getPost()->has('instanceid')) {
            $instanceid = $this->request->getPost()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        }
        elseif ($this->request->getGet()->has('instanceid')) {
            $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        }
        $confirmation = $this->request->getPost()->get('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null);
        $deleteDB = $this->request->getPost()->get('deleteDB', isset($args['deleteDB']) ? $args['deleteDB'] : 0);
        $deleteFiles = $this->request->getPost()->get('deleteFiles', isset($args['deleteFiles']) ? $args['deleteFiles'] : 0);

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            LogUtil::registerError($this->__('Error! Site could not be found.'));
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
                                   array('sitedbname' => $site['sitedbname'],
                                         'sitedbhost' => $site['sitedbhost'],
                                         'sitedbtype' => $site['sitedbtype'],
                                         'sitedbuname' => $site['sitedbuname'],
                                         'sitedbpass' => $site['sitedbpass']))) {
                LogUtil::registerError($this->__('Error deleting database'));
            }
        }
        if ($deleteFiles == 1) {
            // delete the instance files and directoris
            ModUtil::apiFunc('Multisites', 'admin', 'deleteDir',
                              array('dirName' => $this->serviceManager['multisites.files_real_path'] . '/' . $site['sitedbname']));
        }
        // delete instance information
        if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteInstance',
                               array('instanceid' => $site['instanceid']))) {
            LogUtil::registerError($this->__('The instance deletion has failed'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // modify multisites_dbconfig files
        if (!ModUtil::apiFunc('Multisites', 'admin', 'updateDBConfig',
                               array('sitedns' => $sitedns,
                                     'sitedbname' => $sitedbname,
                                     'sitedbpass' => $sitedbpass,
                                     'sitedbuname' => $sitedbuname,
                                     'sitedbhost' => $sitedbhost,
                                     'sitedbtype' => $sitedbtype))) {
            LogUtil::registerError($this->__('Error! Updating the file multisites_dbconfig.php failed.'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // success
        LogUtil::registerStatus($this->__('Done! The instance has been deleted.'));
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
        $instanceid = $this->request->getPost()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);

        $this->view->assign('name', $name)
                   ->assign('available', $available)
                   ->assign('siteModules', $siteModules)
                   ->assign('instanceid', $instanceid);
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

        $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
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

        $instanceid = $this->request->getPost()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        $instancename = $this->request->getPost()->get('instancename', isset($args['instancename']) ? $args['instancename'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $siteadminrealname = $this->request->getPost()->get('siteadminrealname', isset($args['siteadminrealname']) ? $args['siteadminrealname'] : null);
        $siteadminemail = $this->request->getPost()->get('siteadminemail', isset($args['siteadminemail']) ? $args['siteadminemail'] : null);
        $sitecompany = $this->request->getPost()->get('sitecompany', isset($args['sitecompany']) ? $args['sitecompany'] : null);
        $active = $this->request->getPost()->get('active', isset($args['active']) ? $args['active'] : 0);

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            LogUtil::registerError($this->__('Error! Site could not be found.'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        $edited = ModUtil::apiFunc('Multisites', 'admin', 'updateInstance',
                                    array('instanceid' => $instanceid,
                                          'items' => array('instancename' => $instancename,
                                          'description' => $description,
                                          'siteadminrealname' => $siteadminrealname,
                                          'siteadminemail' => $siteadminemail,
                                          'sitecompany' => $sitecompany,
                                          'active' => $active)));
        if (!$edited) {
            LogUtil::registerError($this->__('Error! Updating the instance failed.'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // success
        LogUtil::registerStatus($this->__('Done! The site information has been updated.'));
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

        $modelid = $this->request->getGet()->get('modelid', isset($args['modelid']) ? $args['modelid'] : null);

        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelid' => $modelid));
        if ($model == false) {
            LogUtil::registerError($this->__('Error! Model could not be found.'));
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

        $modelid = $this->request->getPost()->get('modelid', isset($args['modelid']) ? $args['modelid'] : null);
        $modelname = $this->request->getPost()->get('modelname', isset($args['modelname']) ? $args['modelname'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $folders = $this->request->getPost()->get('folders', isset($args['folders']) ? $args['folders'] : null);
        $modeldbtablesprefix = $this->request->getPost()->get('modeldbtablesprefix', isset($args['modeldbtablesprefix']) ? $args['modeldbtablesprefix'] : null);

        $errorMsg = '';
        if ($modelname == null || $modelname == '') {
            $errorMsg = $this->__('Error! Please provide a model name. It is a mandatory field.') . '<br />';
        }
        if ($modeldbtablesprefix == null || $modeldbtablesprefix == '') {
            $errorMsg .= $this->__('Error! Please provide the model database tables prefix. It is a mandatory field.') . '<br />';
        }
        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelid' => $modelid));
        if ($model == false) {
            $errorMsg = $this->__('Error! Model could not be found.');
        }
        if ($errorMsg == '') {
            $edited = ModUtil::apiFunc('Multisites', 'admin', 'updateModel',
                                        array('instanceid' => $instanceid,
                                              'items' => array('modelname' => $modelname,
                                              'description' => $description,
                                              'folders' => $folders,
                                              'modeldbtablesprefix' => $modeldbtablesprefix)));
            if (!$edited) {
                $errorMsg = $this->__('Error! Updating the model failed.');
            }
        }
        if ($errorMsg != '') {
            LogUtil::registerError($errorMsg);
            return $this->redirect(ModUtil::url($this->name, 'admin', 'editModel',
                                                  array('modelid' => $modelid)));
        }
        // success
        LogUtil::registerStatus($this->__('Done! Model updated.'));
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

        $modelname = $this->request->getGet()->get('modelname', isset($args['modelname']) ? $args['modelname'] : null);
        $description = $this->request->getGet()->get('description', isset($args['description']) ? $args['description'] : null);
        $folders = $this->request->getGet()->get('folders', isset($args['folders']) ? $args['folders'] : null);
        $modeldbtablesprefix = $this->request->getGet()->get('modeldbtablesprefix', isset($args['modeldbtablesprefix']) ? $args['modeldbtablesprefix'] : null);

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
            if (!in_array($model['filename'], $modelsFiles)) {
                $modelsFiles[$model['modelid']] = $model['filename'];
            }
        }
        $this->view->assign('modelname', $modelname)
                   ->assign('modeldbtablesprefix', $modeldbtablesprefix)
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

        $modelname = $this->request->getPost()->get('modelname', isset($args['modelname']) ? $args['modelname'] : null);
        $description = $this->request->getPost()->get('description', isset($args['description']) ? $args['description'] : null);
        $folders = $this->request->getPost()->get('folders', isset($args['folders']) ? $args['folders'] : null);
        $modelFile = $this->request->getFiles()->get('modelFile', isset($args['modelFile']) ? $args['modelFile'] : null);
        $modeldbtablesprefix = $this->request->getPost()->get('modeldbtablesprefix', isset($args['modeldbtablesprefix']) ? $args['modeldbtablesprefix'] : null);
        $modelFileSelected = $this->request->getPost()->get('modelFileSelected', isset($args['modelFileSelected']) ? $args['modelFileSelected'] : 0);

        $errorMsg = '';
        if ($modelname == null || $modelname == '') {
            $errorMsg = $this->__('Error! Please provide a model name. It is a mandatory field.<br />');
        }
        if ($modeldbtablesprefix == null || $modeldbtablesprefix == '') {
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
                $filename = str_replace(' ', '_', $modelFile['name']);
                $fitxer = $filename;
                $i = 1;
                while (file_exists($path . '/' . $filename)) {
                    $filename = substr($fitxer, 0, strlen($fitxer) - strlen($file_extension) - (1)) . $i . '.' . $file_extension;
                    $i++;
                }
                // update the file
                if (!move_uploaded_file($modelFile['tmp_name'], $path . '/' . $filename)) {
                    $errorMsg = $this->__(' Error updating file');
                }
            }
        } else {
            $filename = $modelFileSelected;
        }
        if ($errorMsg == '') {
            //Update model information
            $created = ModUtil::apiFunc('Multisites', 'admin', 'createModel',
                                         array('modelname' => $modelname,
                                               'description' => $description,
                                               'filename' => $filename,
                                               'folders' => $folders,
                                               'modeldbtablesprefix' => $modeldbtablesprefix));
            if (!$created) {
                // delete the model file
                unlink($path . '/' . $filename);
                $errorMsg = $this->__('Error creating model');
            }
        }
        if ($errorMsg != '') {
            LogUtil::registerError($errorMsg);
            return $this->redirect(ModUtil::url($this->name, 'admin', 'createNewModel',
                                                  array('modelname' => $modelname,
                                                        'modeldbtablesprefix' => $modeldbtablesprefix,
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

        $instanceid = null;
        if ($this->request->getPost()->has('instanceid')) {
            $instanceid = $this->request->getPost()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        }
        elseif ($this->request->getGet()->has('instanceid')) {
            $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
        sort($modules);
        // get all the modules available in site
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                                         array('instanceid' => $instanceid));
        foreach ($modules as $mod) {
            if ($mod['type'] != 3) {
                // if module exists in instance database
                $available = (array_key_exists($mod['name'], $siteModules)) ? 1 : 0;
                $icons = ModUtil::func('Multisites', 'admin', 'siteElementsIcons',
                                        array('instanceid' => $instanceid,
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

        $modelid = null;
        if ($this->request->getPost()->has('modelid')) {
            $modelid = $this->request->getPost()->get('modelid', isset($args['modelid']) ? $args['modelid'] : null);
        }
        elseif ($this->request->getGet()->has('modelid')) {
            $modelid = $this->request->getGet()->get('modelid', isset($args['modelid']) ? $args['modelid'] : null);
        }

        $confirmation = $this->request->getPost()->get('confirmation', isset($args['confirmation']) ? $args['confirmation'] : null);

        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelid' => $modelid));
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
            if ($m['filename'] == $model['filename'] && $m['modelid'] != $model['modelid']) {
                $fileNeeded = true;
            }
        }
        if (!$fileNeeded) {
            $deleted = unlink($this->getVar('modelsFolder') . '/' . $model['filename']);
        }
        // delete model information
        if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteModel',
                               array('modelid' => $model['modelid']))) {
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

        $instanceid = null;
        if ($this->request->getPost()->has('instanceid')) {
            $instanceid = $this->request->getPost()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        }
        elseif ($this->request->getGet()->has('instanceid')) {
            $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        // get all the themes available in themes directory
        $themes = ModUtil::apiFunc('Multisites', 'admin', 'getAllThemes');
        // get all the themes  inserted in site or instance database
        $siteThemes = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteThemes',
                                        array('instanceid' => $instanceid));
        $defaultTheme = ModUtil::apiFunc('Multisites', 'admin', 'getSiteDefaultTheme',
                                          array('instanceid' => $instanceid));
        $pos = strpos($defaultTheme, '"');
        $defaultTheme = substr($defaultTheme, $pos + 1, -2);
        foreach ($themes as $theme) {
            // if module exists in instance database
            $available = (array_key_exists($theme['name'], $siteThemes)) ? 1 : 0;
            $isDefaultTheme = (strtolower($theme['name']) == strtolower($defaultTheme)) ? 1 : 0;
            $icons = ModUtil::func('Multisites', 'admin', 'siteThemesIcons',
                                    array('instanceid' => $instanceid,
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
        $instanceid = $this->request->getPost()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        $isDefaultTheme = $this->request->getPost()->get('isDefaultTheme', isset($args['isDefaultTheme']) ? $args['isDefaultTheme'] : null);

        $this->view->assign('name', $name)
                   ->assign('available', $available)
                   ->assign('isDefaultTheme', $isDefaultTheme)
                   ->assign('siteThemes', $siteThemes)
                   ->assign('instanceid', $instanceid);
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
        $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);

        $defaultTheme = ModUtil::apiFunc('Multisites', 'admin', 'setAsDefaultTheme',
                                          array('instanceid' => $instanceid,
                'name' => $name));
        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'siteThemes',
                                              array('instanceid' => $instanceid)));
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

        $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
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

        $instanceid = $this->request->getGet()->get('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null);
        $tool = $this->request->getGet()->get('tool', isset($args['tool']) ? $args['tool'] : null);

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }
        switch ($tool) {
            case 'createAdministrator':
                $createAdministrator = ModUtil::apiFunc('Multisites', 'admin', 'createAdministrator',
                                                         array('instanceid' => $instanceid));
                if ($createAdministrator) {
                    LogUtil::registerStatus($this->__('A global administrator has been created'));
                }
                break;
            case 'adminSiteControl':
                $recoverAdminSiteControl = ModUtil::apiFunc('Multisites', 'admin', 'recoverAdminSiteControl',
                                                             array('instanceid' => $instanceid));
                if ($recoverAdminSiteControl) {
                    LogUtil::registerStatus($this->__('The administration control has been recovered'));
                }
                break;
            default:
                LogUtil::registerError($this->__('Not tool selected'));
        }
        return $this->redirect(ModUtil::url($this->name, 'admin', 'siteTools',
                array('instanceid' => $instanceid)));
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
                                               array('modulename' => $module['name'],
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

        $modulename = $this->request->getGet()->get('modulename', isset($args['modulename']) ? $args['modulename'] : null);

        if ($modulename == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        // get all the modules located in modules folder
        $modules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
        // get the module current version
        foreach($modules as $module){
            if($module['name'] == $modulename){
                $moduleSelected = $module;
                break;
            }
        }
        $currentVersion = $moduleSelected['version'];
        // get the sites that need upgrade
        $sites = ModUtil::apiFunc('Multisites', 'admin', 'getSitesThatNeedUpgrade',
                                   array('modulename' => $modulename,
                                         'currentVersion' => $currentVersion));
        if (!$sites) {
            LogUtil::registerError($this->__f('Error! No sites could be found that needs an upgrade of module <strong>%s</strong>.', $modulename));
            return $this->redirect(ModUtil::url($this->name, 'admin', 'actualizer'));
        }

        print_r($sites);die();
    }
}
