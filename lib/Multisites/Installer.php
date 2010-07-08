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