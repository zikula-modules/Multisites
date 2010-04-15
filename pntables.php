<?php
/**
 * PostNuke Application Framework
 *
 * @copyright (c) 2002, PostNuke Development Team
 * @link http://www.postnuke.com
 * @version $Id: pntables.php 22139 2007-06-01 10:57:16Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multi Zikula Installation
 */

/**
 * Define module tables
 * @author Albert Pérez Monfort (aperezm@xtec.cat)
 * @return module tables information
 */
function Multisites_pntables()
{
    // Initialise table array
    $pntable = array();

    // Multisites sites table definition
    $pntable['Multisites_sites'] = DBUtil::getLimitedTablename('Multisites_sites');
    $pntable['Multisites_sites_column'] = array('instanceId' => 'mzk_instanceId',
										        'instanceName' => 'ms_instanceName',
										        'description' => 'ms_description',
										        'siteName' => 'ms_siteName',
										        'siteAdminName' => 'ms_siteAdminName',
										        'siteAdminPwd' => 'ms_siteAdminPwd',
										        'siteAdminRealName' => 'ms_siteAdminRealName',
										        'siteAdminEmail' => 'ms_siteAdminEmail',
										        'siteCompany' => 'ms_siteCompany',
										        'siteDNS' => 'ms_siteDNS',
                                                'siteDB' => 'siteDB',
										        'siteInitModel' => 'ms_siteInitModel',
										        'activationDate' => 'ms_activationDate',
										        'active' => 'ms_active');

    $pntable['Multisites_sites_column_def'] = array('instanceId' => "INT(11) NOTNULL AUTOINCREMENT KEY",
											        'instanceName' => "VARCHAR(150) NOTNULL DEFAULT ''",
											        'description' => "VARCHAR(255) NOTNULL DEFAULT ''",
											        'siteName' => "VARCHAR(150) NOTNULL DEFAULT ''",
											        'siteAdminName' => "VARCHAR(25) NOTNULL DEFAULT ''",
											        'siteAdminPwd' => "VARCHAR(15) NOTNULL DEFAULT ''",
											        'siteAdminRealName' => "VARCHAR(70) NOTNULL DEFAULT ''",
											        'siteAdminEmail' => "VARCHAR(30) NOTNULL DEFAULT ''",
											        'siteCompany' => "VARCHAR(100) NOTNULL DEFAULT ''",
											        'siteDNS' => "VARCHAR(20) NOTNULL DEFAULT ''",
                                                    'siteDB' => "VARCHAR(20) NOTNULL DEFAULT ''",
											        'siteInitModel' => "VARCHAR(30) NOTNULL DEFAULT ''",
											        'activationDate' => "DATETIME NOTNULL DEFAULT '0'",
											        'active' => "TINYINT(1) NOTNULL DEFAULT '0'");

    ObjectUtil::addStandardFieldsToTableDefinition($pntable['Multisites_sites_column']);
    ObjectUtil::addStandardFieldsToTableDataDefinition($pntable['Multisites_sites_column_def']);

    // Multisites access table definition
    $pntable['Multisites_access'] = DBUtil::getLimitedTablename('Multisites_access');
    $pntable['Multisites_access_column'] = array('accessId' => 'ms_accessId',
                                                    'siteDNS' => 'ms_siteDNS',
                                                    'time' => 'ms_time',
                                                    'ip' => 'ms_ip');

    $pntable['Multisites_access_column_def'] = array('accessId' => "INT(11) NOTNULL AUTOINCREMENT KEY",
                                                        'siteDNS' => "VARCHAR(15) NOTNULL DEFAULT ''",
                                                        'time' => "DATETIME NOTNULL DEFAULT '0'",
                                                        'ip' => "VARCHAR(15) NOTNULL DEFAULT ''");

    ObjectUtil::addStandardFieldsToTableDefinition($pntable['Multisites_access_column']);
    ObjectUtil::addStandardFieldsToTableDataDefinition($pntable['Multisites_access_column_def']);

    // Multisites models table definition
    $pntable['Multisites_models'] = DBUtil::getLimitedTablename('Multisites_models');
    $pntable['Multisites_models_column'] = array('modelId' => 'ms_modelId',
                                                    'modelName' => 'ms_modelName',
                                                    'description' => 'ms_description',
                                                    'fileName' => 'ms_fileName',
                                                    'folders' => 'ms_folders');

    $pntable['Multisites_models_column_def'] = array('modelId' => "INT(11) NOTNULL AUTOINCREMENT KEY",
                                                        'modelName' => "VARCHAR(150) NOTNULL DEFAULT ''",
                                                        'description' => "TEXT NOTNULL",
                                                        'fileName' => "VARCHAR(20) NOTNULL DEFAULT ''",
                                                        'folders' => "VARCHAR(150) NOTNULL DEFAULT ''");

    ObjectUtil::addStandardFieldsToTableDefinition($pntable['Multisites_models_column']);
    ObjectUtil::addStandardFieldsToTableDataDefinition($pntable['Multisites_models_column_def']);
    
    // Multisites sites and modules
    $pntable['Multisites_sitesModules'] = DBUtil::getLimitedTablename('Multisites_sitesModules');
    $pntable['Multisites_sitesModules_column'] = array('smId' => 'ms_smId',
                                                        'instanceId' => 'ms_instanceId',
                                                        'moduleName' => 'ms_moduleName',
                                                        'moduleVersion' => 'ms_moduleVersion');

    $pntable['Multisites_sitesModules_column_def'] = array('smId' => "INT(11) NOTNULL AUTOINCREMENT KEY",
                                                            'instanceId' => "INT(11) NOTNULL DEFAULT 0",
                                                            'moduleName' => "VARCHAR(20) NOTNULL DEFAULT ''",
                                                            'moduleVersion' => "VARCHAR(5) NOTNULL DEFAULT ''");

    ObjectUtil::addStandardFieldsToTableDefinition($pntable['Multisites_sitesModules_column']);
    ObjectUtil::addStandardFieldsToTableDataDefinition($pntable['Multisites_sitesModules_column_def']);
    

    // Return the table information
    return $pntable;
}
