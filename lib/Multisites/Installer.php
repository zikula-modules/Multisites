<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Multisites
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Multisites_Installer extends Zikula_Installer
{
    /**
     * Installs the Multisites module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function install()
    {
        // Check permissions
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // create the models folder
        // check if the sitesFilesFolder exists
        $path = (isset($this->serviceManager['multisites.filesRealPath']) ? $this->serviceManager['multisites.filesRealPath'] : '');
        if ($path == '') {
            LogUtil::registerError (__('The directory where the sites files have to be created is not defined. Check your configuration values.'));
            return false;
        }
        if (!file_exists($path)) {
            LogUtil::registerError (__('The directory where the sites files have to be created does not exists.'));
            return false;
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($path)) {
            LogUtil::registerError (__('The directory where the sites files have to be created is not writeable.'));
            return false;
        }
        // create the main site folder
        $path .= '/' . FormUtil::getPassedValue(siteDNS, null, 'GET');
        if (!file_exists($path)) {
            if (!mkdir($path, 0777)) {
                LogUtil::registerError (__('Error creating the directory.') . ': ' . $path);
                return false;
            }
        }
        // create the data folder
        $path .= $this->serviceManager['multisites.siteFilesFolder'];
        if (!file_exists($path)) {
            if (!mkdir($path, 0777)) {
                LogUtil::registerError (__('Error creating the directory.') . ': ' . $path);
                return false;
            }
        }
        // create the models folder
        $path .= '/models';
        if (!file_exists($path)) {
            if (!mkdir($path, 0777)) {
                LogUtil::registerError (__('Error creating the directory.') . ': ' . $path);
                return false;
            }
        }
        // Create module table
        if (!DBUtil::createTable('Multisites_sites')) {
            return false;
        }
        if (!DBUtil::createTable('Multisites_access')) {
            return false;
        }
        if (!DBUtil::createTable('Multisites_models')) {
            return false;
        }
        if (!DBUtil::createTable('Multisites_sitesModules')) {
            return false;
        }
        //Create module vars
        $this->setVar('modelsFolder', $path);
        $this->setVar('tempAccessFileContent','SetEnvIf Request_URI "\.css$" object_is_css=css
    SetEnvIf Request_URI "\.js$" object_is_js=js
    Order deny,allow
    Deny from all
    Allow from env=object_is_css
    Allow from env=object_is_js');
        $this->setVar('globalAdminName', '');
        $this->setVar('globalAdminPassword', '');
        $this->setVar('globalAdminemail', '');
        return true;
    }
    /**
     * Delete the Multisites module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function uninstall()
    {
        // Delete module table
        DBUtil::dropTable('Multisites_sites');
        DBUtil::dropTable('Multisites_access');
        DBUtil::dropTable('Multisites_models');
        DBUtil::dropTable('Multisites_sitesModules');

        //Delete module vars
        $this->delVar('modelsFolder');
        $this->delVar('tempAccessFileContent');
        $this->delVar('globalAdminName');
        $this->delVar('globalAdminPassword');
        $this->delVar('globalAdminemail');

        //Deletion successfull
        return true;
    }

    /**
     * Update the Multisites module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function upgrade($oldversion)
    {
        if (!DBUtil::changeTable('Multisites_sites')) return false;
        if (!DBUtil::changeTable('Multisites_access')) return false;
        if (!DBUtil::changeTable('Multisites_models')) return false;
        if (!DBUtil::changeTable('Multisites_sitesModules')) return false;
        return true;
    }
}