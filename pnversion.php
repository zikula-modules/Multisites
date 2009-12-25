<?php
/**
 * Load the module version information
 *
 * @author		Albert Pérez Monfort (aperezm@xtec.cat)
 * @return		The version information
 */

$dom = ZLanguage::getModuleDomain('Multisites');
$modversion['name'] = 'Multisites';
$modversion['version'] = '0.8';
$modversion['description'] = __('Zikula Multisites module', $dom);
$modversion['displayname'] = __('Multisites manager', $dom);
$modversion['credits'] = 'pndocs/credits.txt';
$modversion['help'] = 'pndocs/help.txt';
$modversion['changelog'] = 'pndocs/changelog.txt';
$modversion['license'] = 'pndocs/license.txt';
$modversion['official'] = 0;
$modversion['author'] = 'Albert Pérez Monfort';
$modversion['contact'] = 'aperezm@xtec.cat';
$modversion['admin'] = 1;
$modversion['securityschema'] = array('Multisites::' => '::');
