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

class Multisites_Installer extends Zikula_AbstractInstaller
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
        $path = (isset($this->serviceManager['multisites.files_real_path']) ? $this->serviceManager['multisites.files_real_path'] : '');
        if ($path == '') {
            return LogUtil::registerError(__('The directory where the sites files have to be created is not defined. Check your configuration values.'));
        }
        if (!file_exists($path)) {
            return LogUtil::registerError(__('The directory where the sites files have to be created does not exists.'));
        }
        // check if the sitesFilesFolder is writeable
        if (!is_writeable($path)) {
            return LogUtil::registerError(__('The directory where the sites files have to be created is not writeable.'));
        }
        // create the main site folder
        $path .= '/' . FormUtil::getPassedValue(sitedns, null, 'GET');
        if (!file_exists($path)) {
            if (!mkdir($path, 0777)) {
                return LogUtil::registerError(__('Error creating the directory.') . ': ' . $path);
            }
        }
        // create the data folder
        $path .= $this->serviceManager['multisites.site_files_folder'];
        if (!file_exists($path)) {
            if (!mkdir($path, 0777)) {
                return LogUtil::registerError(__('Error creating the directory.') . ': ' . $path);
            }
        }
        // create the models folder
        $path .= '/models';
        if (!file_exists($path)) {
            if (!mkdir($path, 0777)) {
                return LogUtil::registerError(__('Error creating the directory.') . ': ' . $path);
            }
        }

        // Create module tables
        if (!DBUtil::createTable('multisitessites')) {
            return false;
        }
        if (!DBUtil::createTable('multisitesaccess')) {
            return false;
        }
        if (!DBUtil::createTable('multisitesmodels')) {
            return false;
        }
        if (!DBUtil::createTable('multisitessitemodules')) {
            return false;
        }

        // Create module vars
        $this->setVar('modelsFolder', $path);
        $this->setVar('tempAccessFileContent', 'SetEnvIf Request_URI "\.css$" object_is_css=css
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
        DBUtil::dropTable('multisitessites');
        DBUtil::dropTable('multisitesaccess');
        DBUtil::dropTable('multisitesmodels');
        DBUtil::dropTable('multisitessitemodules');

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
        if (!DBUtil::changeTable('multisitessites')) return false;
        if (!DBUtil::changeTable('multisitesaccess')) return false;
        if (!DBUtil::changeTable('multisitesmodels')) return false;
        if (!DBUtil::changeTable('multisitessitemodules')) return false;
        return true;
    }
}