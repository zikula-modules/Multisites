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
    $table['Multisites_sites_column'] = array('instanceId' => 'ms_instanceId',
                                              'instanceName' => 'ms_instanceName',
                                              'description' => 'ms_description',
                                              'siteName' => 'ms_siteName',
                                              'siteDescription' => 'ms_siteDescription',
                                              'siteAdminName' => 'ms_siteAdminName',
                                              'siteAdminPwd' => 'ms_siteAdminPwd',
                                              'siteAdminRealName' => 'ms_siteAdminRealName',
                                              'siteAdminEmail' => 'ms_siteAdminEmail',
                                              'siteCompany' => 'ms_siteCompany',
                                              'sitedns' => 'ms_sitedns',
                                              'siteDBName' => 'ms_siteDBName',
                                              'siteDBUname' => 'ms_siteDBUname',
                                              'siteDBPass' => 'ms_siteDBPass',
                                              'siteDBHost' => 'ms_siteDBHost',
                                              'siteDBType' => 'ms_siteDBType',
                                              'siteDBPrefix' => 'ms_siteDBPrefix',
                                              'siteInitModel' => 'ms_siteInitModel',
                                              'activationDate' => 'ms_activationDate',
                                              'active' => 'ms_active');

    $table['Multisites_sites_column_def'] = array('instanceId' => "I PRIMARY AUTO",
                                                  'instanceName' => "C(150) NOTNULL DEFAULT ''",
                                                  'description' => "C(255) NOTNULL DEFAULT ''",
                                                  'siteName' => "C(150) NOTNULL DEFAULT ''",
                                                  'siteDescription' => "C(255) NOTNULL DEFAULT ''",
                                                  'siteAdminName' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteAdminPwd' => "C(15) NOTNULL DEFAULT ''",
                                                  'siteAdminRealName' => "C(70) NOTNULL DEFAULT ''",
                                                  'siteAdminEmail' => "C(30) NOTNULL DEFAULT ''",
                                                  'siteCompany' => "C(100) NOTNULL DEFAULT ''",
                                                  'sitedns' => "C(70) NOTNULL DEFAULT ''",
                                                  'siteDBName' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteDBUname' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteDBPass' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteDBHost' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteDBType' => "C(25) NOTNULL DEFAULT ''",
                                                  'siteDBPrefix' => "C(5) NOTNULL DEFAULT ''",
                                                  'siteInitModel' => "C(30) NOTNULL DEFAULT ''",
                                                  'activationDate' => "T DEFAULT '1970-01-01 00:00:00'",
                                                  'active' => "I1 NOTNULL DEFAULT '0'");

    // Multisites access table definition
    $table['Multisites_access'] = DBUtil::getLimitedTablename('Multisites_access');
    $table['Multisites_access_column'] = array('accessId' => 'ms_accessId',
                                               'sitedns' => 'ms_sitedns',
                                               'time' => 'ms_time',
                                               'ip' => 'ms_ip');

    $table['Multisites_access_column_def'] = array('accessId' => "I PRIMARY AUTO",
                                                   'sitedns' => "C(15) NOTNULL DEFAULT ''",
                                                   'time' => "T DEFAULT '1970-01-01 00:00:00'",
                                                   'ip' => "C(15) NOTNULL DEFAULT ''");

    // Multisites models table definition
    $table['Multisites_models'] = DBUtil::getLimitedTablename('Multisites_models');
    $table['Multisites_models_column'] = array('modelId' => 'ms_modelId',
                                               'modelName' => 'ms_modelName',
                                               'description' => 'ms_description',
                                               'fileName' => 'ms_fileName',
                                               'folders' => 'ms_folders',
                                               'modelDBTablesPrefix' => 'ms_modelDBTablesPrefix');

    $table['Multisites_models_column_def'] = array('modelId' => "I PRIMARY AUTO",
                                                   'modelName' => "C(150) NOTNULL DEFAULT ''",
                                                   'description' => "C(250) NOTNULL DEFAULT ''",
                                                   'fileName' => "C(100) NOTNULL DEFAULT ''",
                                                   'folders' => "C(150) NOTNULL DEFAULT ''",
                                                   'modelDBTablesPrefix' => "C(5) NOTNULL DEFAULT ''");

    // Multisites sites and modules
    $table['Multisites_sitesModules'] = DBUtil::getLimitedTablename('Multisites_sitesModules');
    $table['Multisites_sitesModules_column'] = array('smId' => 'ms_smId',
                                                     'instanceId' => 'ms_instanceId',
                                                     'moduleName' => 'ms_moduleName',
                                                     'moduleVersion' => 'ms_moduleVersion');

    $table['Multisites_sitesModules_column_def'] = array('smId' => "I PRIMARY AUTO",
                                                         'instanceId' => "I NOTNULL DEFAULT 0",
                                                         'moduleName' => "C(20) NOTNULL DEFAULT ''",
                                                         'moduleVersion' => "C(5) NOTNULL DEFAULT ''");

    // Return the table information
    return $table;
}
