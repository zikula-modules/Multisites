<?php

class Multisites_Api_Admin extends Zikula_AbstractApi
{

    /**
     * Connect with an external database
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  Database name
     * @return: Connection object
     */
    public function connectExtDB($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        include_once('config/multisites_dbconfig.php');
        // if it is received the parameter "site" it is assumed that the database connection values are in the $databaseArray array
        $sitedbname = (isset($args['sitedbname'])) ? $args['sitedbname'] : null;
        $sitedbuname = (isset($args['sitedbuname'])) ? $args['sitedbuname'] : null;
        $sitedbpass = (isset($args['sitedbpass'])) ? $args['sitedbpass'] : null;
        $sitedbhost = (isset($args['sitedbhost'])) ? $args['sitedbhost'] : null;
        $sitedbtype = (isset($args['sitedbtype'])) ? $args['sitedbtype'] : null;
        try {
            $connect = new PDO("$sitedbtype:host=$sitedbhost;dbname=$sitedbname", $sitedbuname, $sitedbpass);
        } catch (PDOException $e) {
            return false;
        }
        return $connect;
    }

    /**
     * Create a new database for the new site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The database name
     * @return: true if success or false otherwise
     */
    public function createDB($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB',
                                     array('sitedbuname' => $args['sitedbuname'],
                                           'sitedbpass' => $args['sitedbpass'],
                                           'sitedbhost' => $args['sitedbhost'],
                                           'sitedbtype' => $args['sitedbtype']));
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }
        try {
            switch ($args['sitedbtype']) {
                case 'mysql':
                case 'mysqli':
                    $query = "CREATE DATABASE $args[sitedbname] DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
                    break;
                case 'pgsql':
                    $query = "CREATE DATABASE $args[sitedbname] ENCODING='utf8'";
                    break;
                case 'oci':
                    $query = "CREATE DATABASE $args[sitedbname] national character set utf8";
                    break;
            }
            try {
                $connect->query($query);
            } catch (PDOException $e) {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Create database tables based on the model file
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model database that is the same as sitDNS and the model file name
     * @return: true if success or false otherwise
     */
    public function createTables($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        //check if the models folders exists and it is writeable
        $path = ModUtil::getVar('Multisites', 'modelsFolder');
        if (!is_dir($path)) {
            return LogUtil::registerError($this->__('Error! The model does not exist.'));
        }
        //check if the file model exists and it is readable
        $file = $path . '/' . $args['filename'];
        if (!file_exists($file) || !is_readable($file)) {
            return LogUtil::registerError($this->__('Error! The model file has not been found.'));
        }
        //read the file
        $fh = fopen($file, 'r+');
        if ($fh == false) {
            return LogUtil::registerError($this->__('Error! Opening the model file failed.'));
        }
        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB',
                                     array('sitedbname' => $args['sitedbname'],
                                           'sitedbuname' => $args['sitedbuname'],
                                           'sitedbpass' => $args['sitedbpass'],
                                           'sitedbhost' => $args['sitedbhost'],
                                           'sitedbtype' => $args['sitedbtype']));
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }
        $lines = file($file);
        $exec = '';
        $done = false;
        foreach ($lines as $line_num => $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '--') === 0)
                continue;
            $exec .= $line;
            if (strrpos($line, ';') === strlen($line) - 1) {
                if (!$connect->query(str_replace($args['modeldbtablesprefix'] . '_', $args['sitedbprefix'] . '_', $exec))) {
                    return LogUtil::registerError($this->__('Error importing the database in line') . " " . $line_num . ":<br>" . $exec . "<br>" . mysql_error() . "\n");
                } else {
                    $done = true;
                }
                $exec = '';
            }
        }
        fclose($fh);
        if (!$done) {
            return LogUtil::registerError($this->__('Error! Importing the database failed. Perhaps there is a problem with the model file.'));
        }
        return true;
    }

    /**
     * Update the module vars values for an instance that is being created
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The site main information
     * @return: true if success or false otherwise
     */
    public function updateConfigValues($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB',
                                     array('sitedbname' => $args['sitedbname'],
                                           'sitedbuname' => $args['sitedbuname'],
                                           'sitedbpass' => $args['sitedbpass'],
                                           'sitedbhost' => $args['sitedbhost'],
                                           'sitedbtype' => $args['sitedbtype']));
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }
        $prefix = (($args['sitedbprefix'] != '') ? $args['sitedbprefix'] . '_' : '');
        // modify the site name
        $value = serialize($args['sitename']);
        $sql = "UPDATE " . $prefix . "module_vars set value='$value' WHERE modname='ZConfig' AND name IN ('sitename', 'defaultpagetitle')";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error! Setting configurating value failed.') . ":<br />" . $sql  . "\n");
        }
        // modify the site description
        $value = serialize($args['siteDescription']);
        $sql = "UPDATE " . $prefix . "module_vars set value='$value' WHERE modname='ZConfig' AND name IN ('slogan', 'defaultmetadescription')";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error! Setting configurating value failed.') . ":<br />" . $sql  . "\n");
        }
        // modify the adminmail
        $value = serialize($args['siteadminemail']);
        $sql = "UPDATE " . $prefix . "module_vars set value='$value' WHERE modname='ZConfig' AND name='adminmail'";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error! Setting configurating value failed.') . ":<br />" . $sql . "\n");
        }
        // modify the sessionCookieName
        $value = serialize('ZKSID_' . $args['sitedbname']);
        $sql = "UPDATE " . $prefix . "module_vars set value='$value' WHERE modname='ZConfig' AND name='sessionname'";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error! Setting configurating value failed.') . ":<br />" . $sql . "\n");
        }
        // checks if the user that has been give as administrator exists
        $sql = "SELECT uname, uid FROM " . $prefix . "users WHERE uname='" . $args['siteadminname'] . "'";
        $rs = $connect->query($sql)->fetch();
        $password = UserUtil::getHashedPassword($args['siteadminpwd']);
        if ($rs['uname'] == '') {
            $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
            $nowUTCStr = $nowUTC->format('Y-m-d H:i:s');
            // create administrator
            $sql = "INSERT INTO " . $prefix . "users (uname,email,pass,approved_date,user_regdate,activated) VALUES ('$args[siteadminname]','$args[siteadminemail]','$password','$nowUTCStr','$nowUTCStr',1)";
            if (!$connect->query($sql)) {
                return LogUtil::registerError($this->__('Error! Creating the site administrator failed.') . ":<br />" . $sql . "\n");
            }
            $sql = "SELECT uid FROM " . $prefix . "users WHERE uname='" . $args['siteadminname'] . "'";
            $rs = $connect->query($sql)->fetch();
            $uid = $rs['uid'];
        } else {
            // modify administrator password and email
            $sql = "UPDATE " . $prefix . "users SET pass='$password', email='$args[siteadminemail]' WHERE uname='$rs[uname]'";
            if (!$connect->query($sql)) {
                return LogUtil::registerError($this->__('Error! Creating the site administrator failed.') . ":<br />" . $sql . "\n");
            }
            $uid = $rs['uid'];
        }
        // insert administrator in administrators group if it is not in it
        $sql = "SELECT uid FROM " . $prefix . "group_membership WHERE uid=$uid AND gid=2";
        $rs = $connect->query($sql)->fetch();
        if ($rs['uid'] == '') {
            // user is not administrator and add it to the administrators group
            $sql = "INSERT INTO " . $prefix . "group_membership (uid,gid) VALUES ($uid,2)";
            if (!$connect->query($sql)) {
                return LogUtil::registerError($this->__('Error! Creating the site administrator failed.') . ":<br />" . $sql . "\n");
            }
        }
        return true;
    }

    /**
     * Modify the file multisites_dbconfig.php file and add there the new instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance DNS and database connexion parameters
     * @return: true if success and false otherwise
     */
    public function updateDBConfig($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $apiArgs = array('itemsperpage' => -1, 'startnum' => -1);
        $letter = (isset($args['letter']) && !empty($args['letter'])) ? $args['letter'] : '';
        if (!empty($letter)) {
            $apiArgs['letter'] = $letter;
        }

        // get sites
        $sites = ModUtil::apiFunc('Multisites', 'user', 'getAllSites', $apiArgs);
        if ($sites === false) {
            return false;
        }
        foreach ($sites as $site) {
            $databaseArray[$site['sitedns']] = array('alias' => $site['alias'],
                                                     'sitedbname' => $site['sitedbname'],
                                                     'sitedbhost' => $site['sitedbhost'],
                                                     'sitedbtype' => $site['sitedbtype'],
                                                     'sitedbuname' => $site['sitedbuname'],
                                                     'sitedbpass' => $site['sitedbpass'],
                                                     'sitedbprefix' => $site['sitedbprefix']);
        }
        // add the site that is being created in this moment
        $databaseArray[$args['sitedns']] = array('alias' => $args['alias'],
                                                 'sitedbname' => $args['sitedbname'],
                                                 'sitedbhost' => $args['sitedbhost'],
                                                 'sitedbtype' => $args['sitedbtype'],
                                                 'sitedbuname' => $args['sitedbuname'],
                                                 'sitedbpass' => $args['sitedbpass'],
                                                 'sitedbprefix' => $args['sitedbprefix']);
        // write file
        $dbconfig = var_export($databaseArray, true);
        $phpCode = "<?php\n\$databaseArray = $dbconfig;";
        if (!file_put_contents('config/multisites_dbconfig.php', $phpCode)) return false;
        return true;
    }

    /**
     * Create a new instance in database
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance properties received from the creation form
     * @return: instanceid if success and false otherwise
     */
    public function createInstance($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // needed arguments
        if ($args['sitedns'] == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
        $args['activationdate'] = $nowUTC->format('Y-m-d H:i:s');
        $item = DataUtil::formatForStore($args);
        if (!DBUtil::insertObject($item, 'multisitessites', 'instanceid')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'create', $item['instanceid'],
                          array('module' => 'Multisites'));
        return $item['instanceid'];
    }

    /**
     * Create a new model in database
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model properties received from the creation form
     * @return: modelid if success and false otherwise
     */
    public function createModel($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $item = array('modelname' => $args['modelname'],
                      'description' => $args['description'],
                      'filename' => $args['filename'],
                      'folders' => $args['folders'],
                      'modeldbtablesprefix' => $args['modeldbtablesprefix']);
        if (!DBUtil::insertObject($item, 'multisitesmodels', 'modelid')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'create', $item['modelid'],
                          array('module' => 'Multisites'));
        return $item['modelid'];
    }

    /**
     * Delete a database
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The database name
     * @return: true if success or false otherwise
     */
    public function deleteDatabase($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $args);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }
        try {
            $sql = "DROP DATABASE $args[sitedbname];";
            try {
                $connect->query($sql);
            } catch (PDOException $e) {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete an instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identify
     * @return: true if success or false otherwise
     */
    public function deleteInstance($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if ($instanceid == null || !is_numeric($instanceid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //Get instance information
        $instance = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($instance == false) {
            LogUtil::registerError($this->__('Error! No site could be found.'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }

        //delete instance information
        if (!DBUtil::deleteObjectByID('multisitessites', $instanceid, 'instanceid')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'delete', $item['instanceid'],
                          array('module' => 'Multisites'));
        return true;
    }

    /**
     * Get all the modules available
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  instance identity
     * @return: An array with all the modules
     */
    public function getAllSiteModules($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        //$type = ($args['system'] == null) ? 3 : 1000;
        // Needed argument
        if ($args['instanceid'] == null || !is_numeric($args['instanceid'])) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $args['instanceid']));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');

        //$sql = "SELECT name, state FROM " . $prefix . "modules WHERE type<>$type";
        $sql = "SELECT name, state, version FROM " . $prefix . "modules";
        foreach ($connect->query($sql) as $row) {
            $items[$row['name']] = array('name' => $row['name'],
                                           'state' => $row['state'],
                                           'version' => $row['version']);
        }
        return $items;
    }

    /**
     * Get a site modules information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  instance identity and module name
     * @return: An array with the module needed information
     */
    public function getSiteModule($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $modulename = FormUtil::getPassedValue('modulename', isset($args['modulename']) ? $args['modulename'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if ($instanceid == null || !is_numeric($instanceid) || $modulename == null || $modulename == '') {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');

        $sql = "SELECT name, state FROM " . $prefix . "modules WHERE name='$modulename'";
        $rs = $connect->query($sql)->fetch();
        if (!$rs) {
            //return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $item = array('name' => $rs['name'],
                      'state' => $rs['state']);
        return $item;
    }

    /**
     * Modify the state of a module for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity, the module name and the new state
     * @return: true if success or false otherwise
     */
    public function modifyActivation($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $modulename = FormUtil::getPassedValue('modulename', isset($args['modulename']) ? $args['modulename'] : null, 'POST');
        $newState = FormUtil::getPassedValue('newState', isset($args['newState']) ? $args['newState'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! connecting to the database failed.'));
        }

        //update the module state in the site
        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "UPDATE " . $prefix . "modules set state = " . $newState . " WHERE name = '" . $modulename . "'";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }
        return true;
    }

    /**
     * Get a site theme information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  instance identity and module name
     * @return: An array with the theme needed information
     */
    public function getSiteTheme($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if ($instanceid == null || !is_numeric($instanceid) || $themeName == null || $themeName == '') {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "SELECT name, state FROM " . $prefix . "themes WHERE name='$themeName'";
        $rs = $connect->query($sql)->fetch();
        if (!$rs) {
            //return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $item = array('name' => $rs['name'],
                      'state' => $rs['state']);
        return $item;
    }

    /**
     * Delete a model
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model identify
     * @return: true if success or false otherwise
     */
    public function deleteModel($args)
    {
        $modelid = FormUtil::getPassedValue('modelid', isset($args['modelid']) ? $args['modelid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if ($modelid == null || !is_numeric($modelid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //Get instance information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelid' => $modelid));
        if ($model == false) {
            return LogUtil::registerError($this->__('Model not found'));
        }
        //delete instance information
        if (!DBUtil::deleteObjectByID('multisitesmodels', $modelid, 'modelid')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'delete', $item['modelid'],
                          array('module' => 'Multisites'));
        return true;
    }

    /**
     * Delete a module form a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the module name
     * @return: true if success or false otherwise
     */
    public function deleteSiteModule($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $modulename = FormUtil::getPassedValue('modulename', isset($args['modulename']) ? $args['modulename'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        //get module information
        $siteModule = ModUtil::apiFunc('Multisites', 'admin', 'getSiteModule',
                                        array('modulename' => $modulename,
                                              'instanceid' => $instanceid));
        if ($siteModule['state'] == 3) {
            ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
                              array('modulename' => $modulename,
                                    'instanceid' => $instanceid,
                                    'newState' => 2));
            return true;
        }
        if ($siteModule['state'] == 2) {
            return true;
        }
        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "DELETE FROM " . $prefix . "modules WHERE name='$modulename'";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        return true;
    }

    /**
     * Create a module for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the module name
     * @return: true if success or false otherwise
     */
    public function createSiteModule($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $modulename = FormUtil::getPassedValue('modulename', isset($args['modulename']) ? $args['modulename'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }
        $filemodules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
        $fields = '';
        $values = '';
        $module = $filemodules[$modulename];
        $textual = array('name',
                         'displayname',
                         'url',
                         'description',
                         'directory',
                         'version',
                         'capabilities',
                         'securityschema',
                         'core_min',
                         'core_max',
                         );
        $exclude = array('oldnames',
                         'dependencies');
        foreach ($module as $key => $value) {
            if (!in_array($key, $exclude)) {
                $fields .= /*'z_' . */$key . ',';
                $apos = (in_array($key, $textual)) ? "'" : '';
                $valueString = ($value == '') ? "''" : $apos . DataUtil::formatForStore($value) . $apos;
                $values .= $valueString . ',';
            }
        }
        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);
        // set module state to 1
        $fields .= ',state';
        $values .= ',1';

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        //create the module in the site
        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "INSERT INTO " . $prefix . "modules
            ($fields)
            VALUES
            ($values)";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.' . $sql));
        }
        return true;
    }

    /**
     * delete a directory recursivily
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The directory name
     * @return: true if success or false otherwise
     */
    public function deleteDir($args)
    {
        $dirName = FormUtil::getPassedValue('dirName', isset($args['dirName']) ? $args['dirName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        if (file_exists($dirName)) {
            $dir = dir($dirName);
            while ($file = $dir->read()) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dirName . '/' . $file)) {
                        ModUtil::apiFunc('Multisites', 'admin', 'deleteDir',
                                          array('dirName' => $dirName . '/' . $file));
                    } else {
                        if (!@unlink($dirName . '/' . $file)) {
                            return LogUtil::registerError($this->__('Error deleting file') . ': ' . $dirName . '/' . $file);
                        }
                    }
                }
            }
            $dir->close();
            if (!@rmdir($dirName)) {
                return LogUtil::registerError($this->__('Error deleting file') . ': ' . $dirName);
            }
        }
    }

    /**
     * get all themes available in themes directori
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: An array with all the themes in themes folder
     */
    public function getAllThemes()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // Get all themes on filesystem
        $filethemes = array();
        if (is_dir('themes')) {
            $dh = opendir('themes');
            $dirArray = array();
            while ($dir = readdir($dh)) {
                if (in_array($dir, array('.', '..', '.svn', 'CVS', 'index.html', 'index.htm', '.htaccess'))) {
                    continue;
                }
                $dirArray[] = $dir;
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
                    $themeversion['state'] = ThemeUtil::STATE_INACTIVE;
                    $themetype = 2;
                } elseif (file_exists("themes/$dir/theme.php")) {
                    $themetype = 1;
                } else {
                    // anything else isn't a theme
                    continue;
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
                        LogUtil::registerError($this->__f('Error! Could not include %s', $file));
                    }
                } else if ($themetype == 4 && file_exists($file = "themes/$dir/theme.cfg")) {
                    if (!include ($file)) {
                        LogUtil::registerError($this->__f('Error! Could not include %s', $file));
                    }
                    if (!isset($themeversion['name'])) {
                        $themeversion['name'] = $dir;
                    }
                    $themeversion['displayname'] = $themeversion['name'];
                } else if ($themetype == 2 && file_exists($file = "themes/$dir/xaninfo.php")) {
                    if (!include ($file)) {
                        LogUtil::registerError($this->__f('Error! Could not include %s', $file));
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
                        'state' => (isset($themeversion['state']) ? $themeversion['state'] : ThemeUtil::STATE_ACTIVE),
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
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity
     * @return: An array with all the themes for a site
     */
    public function getAllSiteThemes($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if ($instanceid == null || !is_numeric($instanceid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "SELECT name, state FROM " . $prefix . "themes";

        foreach ($connect->query($sql) as $row) {
            $items[$row['name']] = array('name' => $row['name'],
                    'state' => $row['state']);
        }

        return $items;
    }

    /**
     * Delete a theme form a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the theme name
     * @return: true if success or false otherwise
     */
    public function deleteSiteTheme($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "DELETE FROM " . $prefix . "themes WHERE name='$themeName'";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        return true;
    }

    /**
     * Create a theme for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the theme name
     * @return: true if success or false otherwise
     */
    public function createSiteTheme($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }
        $themes = ModUtil::apiFunc('Multisites', 'admin', 'getAllThemes');
        $theme = $themes[$themeName];
        $textual = array('name',
                         'displayname',
                         'description',
                         'directory',
                         'version',
                         'contact',
                         );
        $fields = '';
        $values = '';
        $exclude = array('official',
                         'author',
                         'credits',
                         'help',
                         'changelog',
                         'license');
        foreach ($theme as $key => $value) {
            if (!in_array($key, $exclude)) {
                $fields .= /*'z_' . */$key . ',';
                $apos = (in_array($key, $textual)) ? "'" : '';
                $valueString = ($value == '') ? "''" : $apos . DataUtil::formatForStore($value) . $apos;
                $values .= $valueString . ',';
            }
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        //create the module in the site
        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "INSERT INTO " . $prefix . "themes ($fields) VALUES ($values)";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.' . $sql));
        }
        return true;
    }

    /**
     * Get the default theme for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity
     * @return: The site default theme name
     */
    public function getSiteDefaultTheme($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "SELECT value FROM " . $prefix . "module_vars WHERE modname='ZConfig' AND name='Default_Theme'";
        $rs = $connect->query($sql)->fetch();
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $defaultTheme = $rs['value'];
        return $defaultTheme;
    }

    /**
     * Update the site default theme
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the theme name
     * @return: true if success or false otherwise
     */
    public function setAsDefaultTheme($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $value = serialize($name);
        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "UPDATE " . $prefix . "module_vars SET value = '$value' WHERE modname='ZConfig' AND name='Default_Theme'";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }
        return true;
    }

    /**
     * Update the site main information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance values
     * @return: true if success or false otherwise
     */
    public function updateInstance($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $table = DBUtil::getTables();
        $c = $table['multisitessites_column'];
        $where = "$c[instanceid] = $instanceid";
        if (!DBUTil::updateObject($items, 'multisitessites', $where)) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }
        return true;
    }

    /**
     * Update the model main information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model values
     * @return: true if success or false otherwise
     */
    public function updateModel($args)
    {
        $modelid = FormUtil::getPassedValue('modelid', isset($args['modelid']) ? $args['modelid'] : null, 'POST');
        $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelid' => $modelid));
        if ($model == false) {
            return LogUtil::registerError($this->__('Error! Model could not be found.'));
        }
        $table = DBUtil::getTables();
        $c = $table['multisitesmodels_column'];
        $where = "$c[modelid] = $modelid";
        if (!DBUTil::updateObject($items, 'multisitesmodels', $where)) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }
        return true;
    }

    /**
     * Create a global administrator for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance values
     * @return: true if success or false otherwise
     */
    public function createAdministrator($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get global administrator parameters
        $globalAdminName = ModUtil::getVar('Multisites', 'globalAdminName');
        $globalAdminPassword = ModUtil::getVar('Multisites', 'globalAdminPassword');
        $globalAdminemail = ModUtil::getVar('Multisites', 'globalAdminemail');
        // check if the global administrator name, password and email had been defined
        if ($globalAdminName == '' || $globalAdminPassword == '' || $globalAdminemail == '') {
            return LogUtil::registerError($this->__('You have not defined the global administrator name or password. Check the module configuration'));
        }

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        // check if the super administrator exists
        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        $sql = "SELECT uid FROM " . $prefix . "users WHERE `uname`='" . $globalAdminName  . "'";
        $rs = $connect->query($sql)->fetch();
        $uid = $rs['uid'];
        // encript the password with the hash method
        $password = UserUtil::getHashedPassword($globalAdminPassword);
        if ($uid == '') {
            // the user doesn't exists and create it
            $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
            $nowUTCStr = $nowUTC->format('Y-m-d H:i:s');
            $sql = "INSERT INTO " . $prefix . "users (uname, pass, email, approved_date, user_regdate, activated)
                    VALUES ('$globalAdminName','$password','$globalAdminemail','$nowUTCStr','$nowUTCStr',1)";
            $rs = $connect->query($sql);
            if (!$rs) {
                return LogUtil::registerError($this->__('Error! Creating global administrator failed.'));
            }
            $sql = "SELECT uid FROM " . $prefix . "users WHERE `uname`='" . $globalAdminName  . "'";
            $rs = $connect->query($sql)->fetch();
            if (!$rs) {
                return LogUtil::registerError($this->__('Error! Getting global administrator values failed.'));
            }
            $uid = $rs['uid'];
            if ($uid != '') {
                // insert the user into administrators group
                $sql = "INSERT INTO " . $prefix . "group_membership (uid, gid) VALUES ($uid,2)";
                $rs = $connect->query($sql);
                if (!$rs) {
                    return LogUtil::registerError($this->__('Error! Adding global administrator to admin group failed.'));
                }
            }
        } else {
            // check if the user is administrator
            $sql = "SELECT gid FROM " . $prefix . "group_membership WHERE `uid`=$uid AND gid=2";
            $rs = $connect->query($sql)->fetch();
            if (!$rs) {
                return LogUtil::registerError($this->__('Error! Getting global administrator group failed.'));
            }
            $gid = $rs['gid'];
            if ($gid == '') {
                // the user is not administrator and insert the user into administrators group
                $sql = "INSERT INTO " . $prefix . "group_membership (uid, gid) VALUES ($uid,2)";
                $rs = $connect->query($sql);
                if (!$rs) {
                    return LogUtil::registerError($this->__('Error! Adding global administrator to admin group failed.'));
                }
            }
            // update global administrator password
            $sql = "UPDATE " . $prefix . "users SET pass='$password' WHERE uid=$uid";
            $rs = $connect->query($sql);
            if (!$rs) {
               return LogUtil::registerError($this->__('Error! Updating global administrator password failed.'));
            }
        }
        return true;
    }

    /**
     * Recover the first row in the permissions table for administrators
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance values
     * @return: true if success or false otherwise
     */
    public function recoverAdminSiteControl($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceid' => $instanceid));
        if ($site == false) {
            return LogUtil::registerError($this->__('Error! No site could be found.'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error! Connecting to the database failed.'));
        }

        $prefix = (($GLOBALS['ZConfig']['System']['prefix'] != '') ? $GLOBALS['ZConfig']['System']['prefix'] . '_' : '');
        //delete the sequence in the first position
        $sql = "DELETE FROM " . $prefix . "group_perms WHERE `sequence` < 1 OR `pid` = 1";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Deleting the sequences with value under 0 failed.'));
        }
        //insert a new sequence
        $sql = "INSERT INTO " . $prefix . "group_perms (gid, sequence, component, instance, level, pid) VALUES (2, 0, '.*', '.*', 800, 1)";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Creating the sequence failed.'));
        }
        return true;
    }

//******* PNN *******
    /**
     * Save the site modules and versions
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance values
     * @return: true if success or false otherwise
     */
    public function saveSiteModules($args)
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get all the modules available in site
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                                         array('instanceid' => $args['instanceid']));
        // save all modules in database
        foreach($siteModules as $module) {
            $item = array('instanceid' => $args['instanceid'],
                          'modulename' => $module['name'],
                          'moduleversion' => $module['version']);
            if (!DBUtil::insertObject($item, 'multisitessitemodules', 'smid')) {
                return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
            }
        }
        return true;
    }
//*******

    /**
     * Delete the site modules information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance values
     * @return: true if success or false otherwise
     */
    public function deleteSiteModules($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        //delete instance information
        if (!DBUtil::deleteObjectByID('multisitessitemodules', $instanceid, 'instanceid')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        return true;
    }

    /**
     * Update the site modules information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance values
     * @return: true if success or false otherwise
     */
    public function updateSiteModules($args)
    {
        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get site available modules
        // get all modules available

        /*
    //delete instance information
    if (!DBUtil::deleteObjectByID('multisitessitemodules', $instanceid, 'instanceid')) {
        return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
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
    public function getNumberOfSites($args)
    {
        $modulename = FormUtil::getPassedValue('modulename', isset($args['modulename']) ? $args['modulename'] : null, 'POST');
        $currentVersion = FormUtil::getPassedValue('currentVersion', isset($args['currentVersion']) ? $args['currentVersion'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission($this->name, '::', ACCESS_ADMIN) ||
                ($this->request->getGet()->get('sitedns', '') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $table = DBUtil::getTables();
        $c = $table['multisitessitemodules_column'];
        $where = "$c[modulename] = '$modulename' AND $c[moduleversion] < '$currentVersion'";
        $numberOfItems = DBUtil::selectObjectCount('multisitessitemodules', $where);
        if ($numberOfItems === false) {
            return LogUtil::registerError($this->__f('Error! Getting number of sites where the module <strong>%s</strong> need to be upgraded.', $modulename ,$dom));
        }
        return $numberOfItems;
    }

    /**
     * get the sites ids where upgrading is needed
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  Module name
     * @return: The number of sites
     */
    public function getSitesThatNeedUpgrade($args)
    {
        $modulename = FormUtil::getPassedValue('modulename', isset($args['modulename']) ? $args['modulename'] : null, 'POST');
        $currentVersion = FormUtil::getPassedValue('currentVersion', isset($args['currentVersion']) ? $args['currentVersion'] : null, 'POST');
        $table = DBUtil::getTables();
        $c = $table['multisitessitemodules_column'];
        $where = "$c[modulename] = '$modulename' AND $c[moduleversion] < $currentVersion";
        $sites = DBUtil::selectObjectArray('multisitessitemodules', $where);
        if ($sites === false) {
            return LogUtil::registerError($this->__f('Error! Getting sites where the module <strong>%s</strong> need to be upgraded.', $modulename ,$dom));
        }
        return $sites;
    }

    /**
     * get available Admin panel links
     *
     * @return array Array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return $links;
        }

        $links[] = array('url'   => ModUtil::url('Multisites', 'admin', 'main'),
            'text'  => $this->__('View instances'),
            'title' => $this->__('View instances'));

        $links[] = array('url'   => ModUtil::url('Multisites', 'admin', 'newIns'),
            'text'  => $this->__('New Instance'),
            'title' => $this->__('New Instance'));

        $links[] = array('url'   => ModUtil::url('Multisites', 'admin', 'manageModels'),
            'text'  => $this->__('View Models'),
            'title' => $this->__('View Models'));

        $links[] = array('url'   => ModUtil::url('Multisites', 'admin', 'createNewModel'),
            'text'  => $this->__('Create New Model'),
            'title' => $this->__('Create New Model'));

        $links[] = array('url'   => ModUtil::url('Multisites', 'admin', 'actualizer'),
            'text'  => $this->__('Actualise Modules'),
            'title' => $this->__('Actualise Modules'));

        $links[] = array('url'   => ModUtil::url('Multisites', 'admin', 'config'),
            'text'  => $this->__('Configuration'),
            'title' => $this->__('Configuration'));

        return $links;
    }
}