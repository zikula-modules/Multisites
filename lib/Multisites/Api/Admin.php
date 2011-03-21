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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        include_once('config/multisites_dbconfig.php');
        // if it is received the parameter "site" it is assumed that the database connection values are in the $databaseArray array
        $siteDBName = (isset($args['siteDBName'])) ? $args['siteDBName'] : null;
        $siteDBUname = (isset($args['siteDBUname'])) ? $args['siteDBUname'] : null;
        $siteDBPass = (isset($args['siteDBPass'])) ? $args['siteDBPass'] : null;
        $siteDBHost = (isset($args['siteDBHost'])) ? $args['siteDBHost'] : null;
        $siteDBType = (isset($args['siteDBType'])) ? $args['siteDBType'] : null;
        try {
            $connect = new PDO("$siteDBType:host=$siteDBHost;dbname=$siteDBName", $siteDBUname, $siteDBPass);
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB',
                                     array('siteDBUname' => $args['siteDBUname'],
                                           'siteDBPass' => $args['siteDBPass'],
                                           'siteDBHost' => $args['siteDBHost'],
                                           'siteDBType' => $args['siteDBType']));
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }
        try {
            switch ($args['siteDBType']) {
                case 'mysql':
                case 'mysqli':
                    $query = "CREATE DATABASE $args[siteDBName] DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
                    break;
                case 'pgsql':
                    $query = "CREATE DATABASE $args[siteDBName] ENCODING='utf8'";
                    break;
                case 'oci':
                    $query = "CREATE DATABASE $args[siteDBName] national character set utf8";
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        //check if the models folders exists and it is writeable
        $path = ModUtil::getVar('Multisites', 'modelsFolder');
        if (!is_dir($path)) {
            return LogUtil::registerError($this->__('The model do not exists'));
        }
        //check if the file model exists and it is readable
        $file = $path . '/' . $args['fileName'];
        if (!file_exists($file) || !is_readable($file)) {
            return LogUtil::registerError($this->__('The model file has not been found'));
        }
        //read the file
        $fh = fopen($file, 'r+');
        if ($fh == false) {
            return LogUtil::registerError($this->__('Error opening the model file'));
        }
        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB',
                                     array('siteDBName' => $args['siteDBName'],
                                           'siteDBUname' => $args['siteDBUname'],
                                           'siteDBPass' => $args['siteDBPass'],
                                           'siteDBHost' => $args['siteDBHost'],
                                           'siteDBType' => $args['siteDBType']));
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
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
                if (!$connect->query(str_replace($args['modelDBTablesPrefix'] . '_', $args['siteDBPrefix'] . '_', $exec))) {
                    return LogUtil::registerError($this->__('Error importing the database in line') . " " . $line_num . ":<br>" . $exec . "<br>" . mysql_error() . "\n");
                } else {
                    $done = true;
                }
                $exec = '';
            }
        }
        fclose($fh);
        if (!$done) {
            return LogUtil::registerError($this->__('Error importing the database. Perhaps there is a problem with the model file'));
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB',
                                     array('siteDBName' => $args['siteDBName'],
                                           'siteDBUname' => $args['siteDBUname'],
                                           'siteDBPass' => $args['siteDBPass'],
                                           'siteDBHost' => $args['siteDBHost'],
                                           'siteDBType' => $args['siteDBType']));
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }
        $prefix = $args['siteDBPrefix'];
        // modify the site name
        $value = serialize($args['siteName']);
        $sql = "UPDATE " . $prefix . "_module_vars set z_value='$value' WHERE z_modname='ZConfig' AND z_name='sitename'";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error configurating value') . ":<br />" . $sql  . "\n");
        }
        // modify the site description
        $value = serialize($args['siteDescription']);
        $sql = "UPDATE " . $prefix . "_module_vars set z_value='$value' WHERE z_modname='ZConfig' AND z_name='slogan'";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error configurating value') . ":<br />" . $sql  . "\n");
        }
        // modify the adminmail
        $value = serialize($args['siteAdminEmail']);
        $sql = "UPDATE " . $prefix . "_module_vars set z_value='$value' WHERE z_modname='ZConfig' AND z_name='adminmail'";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error configurating value') . ":<br />" . $sql . "\n");
        }
        // modify the sessionCookieName
        $value = serialize('ZKSID_' . $args['siteDBName']);
        $sql = "UPDATE " . $prefix . "_module_vars set z_value='$value' WHERE z_modname='ZConfig' AND z_name='sessionname'";
        if (!$connect->query($sql)) {
            return LogUtil::registerError($this->__('Error configurating value') . ":<br />" . $sql . "\n");
        }
        // checks if the user that has been give as administrator exists
        $sql = "SELECT z_uname,z_uid FROM " . $prefix . "_users WHERE z_uname='" . $args['siteAdminName'] . "'";
        $rs = $connect->query($sql)->fetch();
        $password = UserUtil::getHashedPassword($args['siteAdminPwd']);
        if ($rs['z_uname'] == '') {
            $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
            $nowUTCStr = $nowUTC->format(UserUtil::DATETIME_FORMAT);
            // create administrator
            $sql = "INSERT INTO " . $prefix . "_users (z_uname,z_email,z_pass,z_approved_date,z_user_regdate,z_activated) VALUES ('$args[siteAdminName]','$args[siteAdminEmail]','$password','$nowUTCStr','$nowUTCStr',1)";
            if (!$connect->query($sql)) {
                return LogUtil::registerError($this->__('Error creating the site administrator') . ":<br />" . $sql . "\n");
            }
            $sql = "SELECT z_uid FROM " . $prefix . "_users WHERE z_uname='" . $args['siteAdminName'] . "'";
            $rs = $connect->query($sql)->fetch();
            $uid = $rs['z_uid'];
        } else {
            // modify administrator password and email
            $sql = "UPDATE " . $prefix . "_users SET z_pass='$password', z_email='$args[siteAdminEmail]' WHERE z_uname='$rs[z_uname]'";
            if (!$connect->query($sql)) {
                return LogUtil::registerError($this->__('Error creating the site administrator') . ":<br />" . $sql . "\n");
            }
            $uid = $rs['z_uid'];
        }
        // insert administrator in administrators group if it is not in it
        $sql = "SELECT z_uid FROM " . $prefix . "_group_membership WHERE z_uid=$uid AND z_gid=2";
        $rs = $connect->query($sql)->fetch();
        if ($rs['z_uid'] == '') {
            // user is not administrator and add it to the administrators group
            $sql = "INSERT INTO " . $prefix . "_group_membership (z_uid,z_gid) VALUES ($uid,2)";
            if (!$connect->query($sql)) {
                return LogUtil::registerError($this->__('Error creating the site administrator') . ":<br />" . $sql . "\n");
            }
        }
        return true;
    }

    /**
     * Modify the file multisites_dbconig.php file and add there the new instance
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance DNS and database connexion parameters
     * @return: true if success and false otherwise
     */
    public function updateDBConfig($args)
    {
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get sites
        $sites = ModUtil::apiFunc('Multisites', 'user', 'getAllSites',
                                   array('letter' => $letter,
                                         'itemsperpage' => -1,
                                         'startnum' => -1));
        if ($sites === false) {
            return false;
        }
        foreach ($sites as $site) {
            $databaseArray[$site['sitedns']] = array('siteDBName' => $site['siteDBName'],
                                                     'siteDBHost' => $site['siteDBHost'],
                                                     'siteDBType' => $site['siteDBType'],
                                                     'siteDBUname' => $site['siteDBUname'],
                                                     'siteDBPass' => $site['siteDBPass'],
                                                     'siteDBPrefix' => $site['siteDBPrefix']);
        }
        // add the site that is being created in this moment
        $databaseArray[$args['sitedns']] = array('siteDBName' => $args['siteDBName'],
                                                 'siteDBHost' => $args['siteDBHost'],
                                                 'siteDBType' => $args['siteDBType'],
                                                 'siteDBUname' => $args['siteDBUname'],
                                                 'siteDBPass' => $args['siteDBPass'],
                                                 'siteDBPrefix' => $args['siteDBPrefix']);
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
     * @return: instanceId if success and false otherwise
     */
    public function createInstance($args)
    {
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // needed arguments
        if ($args['sitedns'] == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
        $args['activationDate'] = $nowUTC->format(UserUtil::DATETIME_FORMAT);
        $item = DataUtil::formatForStore($args);
        if (!DBUtil::insertObject($item, 'Multisites_sites', 'instanceId')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'create', $item['instanceId'],
                          array('module' => 'Multisites'));
        return $item['instanceId'];
    }

    /**
     * Create a new model in database
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model properties received from the creation form
     * @return: modelId if success and false otherwise
     */
    public function createModel($args)
    {
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $item = array('modelName' => $args['modelName'],
                      'description' => $args['description'],
                      'fileName' => $args['fileName'],
                      'folders' => $args['folders'],
                      'modelDBTablesPrefix' => $args['modelDBTablesPrefix']);
        if (!DBUtil::insertObject($item, 'Multisites_models', 'modelId')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'create', $item['modelId'],
                          array('module' => 'Multisites'));
        return $item['modelId'];
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $args);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }
        try {
            $sql = "DROP DATABASE $args[siteDBName];";
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if ($instanceId == null || !is_numeric($instanceId)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //Get instance information
        $instance = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($instance == false) {
            LogUtil::registerError($this->__('Not site found'));
            return System::redirect(ModUtil::url('Multisites', 'admin', 'main'));
        }

        //delete instance information
        if (!DBUtil::deleteObjectByID('Multisites_sites', $instanceId, 'instanceId')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'delete', $item['instanceId'],
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        //$type = ($args['system'] == null) ? 3 : 1000;
        // Needed argument
        if ($args['instanceId'] == null || !is_numeric($args['instanceId'])) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $args['instanceId']));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        //$sql = "SELECT z_name, z_state FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_modules WHERE z_type<>$type";
        $sql = "SELECT z_name, z_state, z_version FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_modules";
        foreach ($connect->query($sql) as $row) {
            $items[$row['z_name']] = array('name' => $row['z_name'],
                                           'state' => $row['z_state'],
                                           'version' => $row['z_version']);
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if ($instanceId == null || !is_numeric($instanceId) || $moduleName == null || $moduleName == '') {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        $sql = "SELECT z_name, z_state FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_modules WHERE z_name='$moduleName'";
        $rs = $connect->query($sql)->fetch();
        if (!$rs) {
            //return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $item = array('name' => $rs['z_name'],
                      'state' => $rs['z_state']);
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
        $newState = FormUtil::getPassedValue('newState', isset($args['newState']) ? $args['newState'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        //update the module state in the site
        $sql = "UPDATE " . $GLOBALS['ZConfig']['System']['prefix'] . "_modules set z_state = " . $newState . " where z_name = '" . $moduleName . "'";
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if ($instanceId == null || !is_numeric($instanceId) || $themeName == null || $themeName == '') {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        $sql = "SELECT z_name, z_state FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_themes WHERE z_name='$themeName'";
        $rs = $connect->query($sql)->fetch();
        if (!$rs) {
            //return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $item = array('name' => $rs['z_name'],
                      'state' => $rs['z_state']);
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
        $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if ($modelId == null || !is_numeric($modelId)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //Get instance information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelId' => $modelId));
        if ($model == false) {
            return LogUtil::registerError($this->__('Model not found'));
        }
        //delete instance information
        if (!DBUtil::deleteObjectByID('Multisites_models', $modelId, 'modelId')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }
        // Let any hooks know that we have created a new item
        $this->callHooks('item', 'delete', $item['modelId'],
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        //get module information
        $siteModule = ModUtil::apiFunc('Multisites', 'admin', 'getSiteModule',
                                        array('moduleName' => $moduleName,
                                              'instanceId' => $instanceId));
        if ($siteModule['state'] == 3) {
            ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
                              array('moduleName' => $moduleName,
                                    'instanceId' => $instanceId,
                                    'newState' => 2));
            return true;
        }
        if ($siteModule['state'] == 2) {
            return true;
        }
        $sql = "DELETE FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_modules WHERE z_name='$moduleName'";
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }
        $filemodules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
        $fields = '';
        $values = '';
        $module = $filemodules[$moduleName];
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
                $fields .= 'z_' . $key . ',';
                $apos = (in_array($key, $textual)) ? "'" : '';
                $valueString = ($value == '') ? "''" : $apos . DataUtil::formatForStore($value) . $apos;
                $values .= $valueString . ',';
            }
        }
        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);
        // set module state to 1
        $fields .= ',z_state';
        $values .= ',1';

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        //create the module in the site
        $sql = "INSERT INTO " . $GLOBALS['ZConfig']['System']['prefix'] . "_modules
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if ($instanceId == null || !is_numeric($instanceId)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        $sql = "SELECT z_name, z_state FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_themes";

        foreach ($connect->query($sql) as $row) {
            $items[$row['z_name']] = array('name' => $row['z_name'],
                    'state' => $row['z_state']);
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        $sql = "DELETE FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_themes WHERE z_name='$themeName'";
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $themeName = FormUtil::getPassedValue('themeName', isset($args['themeName']) ? $args['themeName'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
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
                $fields .= 'z_' . $key . ',';
                $apos = (in_array($key, $textual)) ? "'" : '';
                $valueString = ($value == '') ? "''" : $apos . DataUtil::formatForStore($value) . $apos;
                $values .= $valueString . ',';
            }
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        //create the module in the site
        $sql = "INSERT INTO " . $GLOBALS['ZConfig']['System']['prefix'] . "_themes
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
     * Get the default theme for a site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity
     * @return: The site default theme name
     */
    public function getSiteDefaultTheme($args)
    {
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        $sql = "SELECT z_value FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_module_vars WHERE z_modname='ZConfig' AND z_name='Default_Theme'";
        $rs = $connect->query($sql)->fetch();
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $defaultTheme = $rs['z_value'];
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $name = FormUtil::getPassedValue('name', isset($args['name']) ? $args['name'] : null, 'GET');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        $value = serialize($name);
        $sql = "UPDATE " . $GLOBALS['ZConfig']['System']['prefix'] . "_module_vars SET z_value = '$value' WHERE z_modname='ZConfig' AND z_name='Default_Theme'";
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $table = DBUtil::getTables();
        $c = $table['Multisites_sites_column'];
        $where = "$c[instanceId] = $instanceId";
        if (!DBUTil::updateObject($items, 'Multisites_sites', $where)) {
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
        $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');
        $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get model information
        $model = ModUtil::apiFunc('Multisites', 'user', 'getModelById',
                                   array('modelId' => $modelId));
        if ($model == false) {
            return LogUtil::registerError($this->__('Model not found'));
        }
        $table = DBUtil::getTables();
        $c = $table['Multisites_models_column'];
        $where = "$c[modelId] = $modelId";
        if (!DBUTil::updateObject($items, 'Multisites_models', $where)) {
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
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
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        // check if the super administrator exists
        $sql = "SELECT z_uid FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_users WHERE `z_uname`='" . $globalAdminName  . "'";
        $rs = $connect->query($sql)->fetch();
        $uid = $rs['z_uid'];
        // encript the password with the hash method
        $password = UserUtil::getHashedPassword($globalAdminPassword);
        if ($uid == '') {
            // the user doesn't exists and create it
            $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
            $nowUTCStr = $nowUTC->format(UserUtil::DATETIME_FORMAT);
            $sql = "INSERT INTO " . $GLOBALS['ZConfig']['System']['prefix'] . "_users (z_uname, z_pass, z_email, z_approved_date, z_user_regdate, z_activated)
                    VALUES ('$globalAdminName','$password','$globalAdminemail','$nowUTCStr','$nowUTCStr',1)";
            $rs = $connect->query($sql);
            if (!$rs) {
                return LogUtil::registerError($this->__('Error! Creating global administrator.'));
            }
            $sql = "SELECT z_uid FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_users WHERE `z_uname`='" . $globalAdminName  . "'";
            $rs = $connect->query($sql)->fetch();
            if (!$rs) {
                return LogUtil::registerError($this->__('Error! Getting global administrator values.'));
            }
            $uid = $rs['z_uid'];
            if ($uid != '') {
                // insert the user into administrators group
                $sql = "INSERT INTO " . $GLOBALS['ZConfig']['System']['prefix'] . "_group_membership (z_uid, z_gid) VALUES ($uid,2)";
                $rs = $connect->query($sql);
                if (!$rs) {
                    return LogUtil::registerError($this->__('Error! Adding global administrator as administrators group membership.'));
                }
            }
        } else {
            // check if the user is administrator
            $sql = "SELECT z_gid FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_group_membership
                WHERE `z_uid`=$uid AND z_gid=2";
            $rs = $connect->query($sql)->fetch();
            if (!$rs) {
                return LogUtil::registerError($this->__('Error! Getting global administrator group.'));
            }
            $gid = $rs['z_gid'];
            if ($gid == '') {
                // the user is not administrator and insert the user into administrators group
                $sql = "INSERT INTO " . $GLOBALS['ZConfig']['System']['prefix'] . "_group_membership (z_uid, z_gid) VALUES ($uid,2)";
                $rs = $connect->query($sql);
                if (!$rs) {
                    return LogUtil::registerError($this->__('Error! Adding global administrator as administrators group membership.'));
                }
            }
            // update global administrator password
            $sql = "UPDATE " . $GLOBALS['ZConfig']['System']['prefix'] . "_users SET z_pass='$password' WHERE z_uid=$uid";
            $rs = $connect->query($sql);
            if (!$rs) {
               return LogUtil::registerError($this->__('Error! Updating global administrator password.'));
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }

        // get site information
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                                  array('instanceId' => $instanceId));
        if ($site == false) {
            return LogUtil::registerError($this->__('Not site found'));
        }

        $connect = ModUtil::apiFunc('Multisites', 'admin', 'connectExtDB', $site);
        if (!$connect) {
            return LogUtil::registerError($this->__('Error connecting to database'));
        }

        //delete the sequence in the first position
        $sql = "DELETE FROM " . $GLOBALS['ZConfig']['System']['prefix'] . "_group_perms WHERE `z_sequence` < 1 OR `z_pid` = 1";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Deleting the sequences with value under 0.'));
        }
        //insert a new sequence
        $sql = "INSERT INTO " . $GLOBALS['ZConfig']['System']['prefix'] . "_group_perms (z_gid, z_sequence, z_component, z_instance, z_level, z_pid)
            VALUES (2,0,'.*','.*',800,1)";
        $rs = $connect->query($sql);
        if (!$rs) {
            return LogUtil::registerError($this->__('Error! Creating the sequence.'));
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
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get all the modules available in site
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                                         array('instanceId' => $args['instanceId']));
        // save all modules in database
        foreach($siteModules as $module) {
            $item = array('instanceId' => $args['instanceId'],
                          'moduleName' => $module['name'],
                          'moduleVersion' => $module['version']);
            if (!DBUtil::insertObject($item, 'Multisites_sitesModules', 'smId')) {
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        //delete instance information
        if (!DBUtil::deleteObjectByID('Multisites_sitesModules', $instanceId, 'instanceId')) {
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
        $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        // get site available modules
        // get all modules available

        /*
    //delete instance information
    if (!DBUtil::deleteObjectByID('Multisites_sitesModules', $instanceId, 'instanceId')) {
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
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
        $currentVersion = FormUtil::getPassedValue('currentVersion', isset($args['currentVersion']) ? $args['currentVersion'] : null, 'POST');
        // security check
        if (!SecurityUtil::checkPermission('Multisites', '::', ACCESS_ADMIN) ||
                (FormUtil::getPassedValue('sitedns', '', 'GET') != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 0) ||
                ($_SERVER['HTTP_HOST'] != $this->serviceManager['multisites.mainsiteurl'] && $this->serviceManager['multisites.based_on_domains'] == 1)) {
            return LogUtil::registerPermissionError();
        }
        $table = DBUtil::getTables();
        $c = $table['Multisites_sitesModules_column'];
        $where = "$c[moduleName] = '$moduleName' AND $c[moduleVersion] < '$currentVersion'";
        $numberOfItems = DBUtil::selectObjectCount('Multisites_sitesModules', $where);
        if ($numberOfItems === false) {
            return LogUtil::registerError($this->__f('Error! Getting number of sites where the module <strong>%s</strong> need to be upgraded.', $moduleName ,$dom));
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
        $moduleName = FormUtil::getPassedValue('moduleName', isset($args['moduleName']) ? $args['moduleName'] : null, 'POST');
        $currentVersion = FormUtil::getPassedValue('currentVersion', isset($args['currentVersion']) ? $args['currentVersion'] : null, 'POST');
        $table = DBUtil::getTables();
        $c = $table['Multisites_sitesModules_column'];
        $where = "$c[moduleName] = '$moduleName' AND $c[moduleVersion] < $currentVersion";
        $sites = DBUtil::selectObjectArray('Multisites_sitesModules', $where);
        if ($sites === false) {
            return LogUtil::registerError($this->__f('Error! Getting sites where the module <strong>%s</strong> need to be upgraded.', $moduleName ,$dom));
        }
        return $sites;
    }
}