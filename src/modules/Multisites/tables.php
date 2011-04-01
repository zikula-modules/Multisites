<?php
/**
 * PostNuke Application Framework
 *
 * @copyright (c) 2002, PostNuke Development Team
 * @link http://www.postnuke.com
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multi Zikula Installation
 */

/**
 * Define module tables
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return module tables information
 */
function Multisites_tables()
{
    // Initialise table array
    $table = array();

    // Multisites sites table definition
    $table['Multisites_sites'] = DBUtil::getLimitedTablename('Multisites_sites');

    $table['Multisites_sites_column_def'] = array('instanceid' => "I PRIMARY AUTO",
                                                  'instancename' => "C(150) NOTNULL DEFAULT ''",
                                                  'description' => "C(255) NOTNULL DEFAULT ''",
                                                  'sitename' => "C(150) NOTNULL DEFAULT ''",
                                                  'sitedescription' => "C(255) NOTNULL DEFAULT ''",
                                                  'siteadminname' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteadminpwd' => "C(15) NOTNULL DEFAULT ''",
                                                  'siteadminrealname' => "C(70) NOTNULL DEFAULT ''",
                                                  'siteadminemail' => "C(40) NOTNULL DEFAULT ''",
                                                  'sitecompany' => "C(100) NOTNULL DEFAULT ''",
                                                  'sitedns' => "C(255) NOTNULL DEFAULT ''",
                                                  'sitedbname' => "C(25) NOTNULL DEFAULT ''",
                                                  'sitedbuname' => "C(25) NOTNULL DEFAULT ''",
                                                  'sitedbpass' => "C(25) NOTNULL DEFAULT ''",
                                                  'sitedbhost' => "C(25) NOTNULL DEFAULT ''",
                                                  'sitedbtype' => "C(25) NOTNULL DEFAULT ''",
                                                  'sitedbprefix' => "C(5) NOTNULL DEFAULT ''",
                                                  'siteinitmodel' => "C(30) NOTNULL DEFAULT ''",
                                                  'activationdate' => "T DEFAULT '1970-01-01 00:00:00'",
                                                  'active' => "I1 NOTNULL DEFAULT '0'");

    // Multisites access table definition
    $table['multisitesaccess'] = DBUtil::getLimitedTablename('multisitesaccess');

    $table['multisitesaccess_column_def'] = array('accessid' => "I PRIMARY AUTO",
                                                   'sitedns' => "C(15) NOTNULL DEFAULT ''",
                                                   'time' => "T DEFAULT '1970-01-01 00:00:00'",
                                                   'ip' => "C(15) NOTNULL DEFAULT ''");

    // Multisites models table definition
    $table['multisitesmodels'] = DBUtil::getLimitedTablename('multisitesmodels');
    
    $table['multisitesmodels_column_def'] = array('modelid' => "I PRIMARY AUTO",
                                                   'modelname' => "C(150) NOTNULL DEFAULT ''",
                                                   'description' => "C(250) NOTNULL DEFAULT ''",
                                                   'filename' => "C(100) NOTNULL DEFAULT ''",
                                                   'folders' => "C(150) NOTNULL DEFAULT ''",
                                                   'modeldbtablesprefix' => "C(5) NOTNULL DEFAULT ''");

    // Multisites sites and modules
    $table['multisitessitemodules'] = DBUtil::getLimitedTablename('multisitessitemodules');

    $table['multisitessitemodules_column_def'] = array('smid' => "I PRIMARY AUTO",
                                                         'instanceid' => "I NOTNULL DEFAULT 0",
                                                         'modulename' => "C(20) NOTNULL DEFAULT ''",
                                                         'moduleversion' => "C(5) NOTNULL DEFAULT ''");

    // Return the table information
    return $table;
}
