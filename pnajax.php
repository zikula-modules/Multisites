<?php
/**
 * Delete a module from a given site
 * @author: Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	args   Array with the module name and the instance identity
 * @return:	Delete a database record
 */
function Multisites_ajax_modifyActivation($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
        AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Sorry! No authorization to access this module.', $dom)));
    }

    $instanceId = FormUtil::getPassedValue('instanceId', -1, 'GET');
    if ($instanceId == -1) {
        LogUtil::registerError('no instanceId value received');
        AjaxUtil::output();
    }

    $moduleName = FormUtil::getPassedValue('moduleName', -1, 'GET');
    if ($moduleName == -1) {
        LogUtil::registerError('no module name received');
        AjaxUtil::output();
    }

    $newState = FormUtil::getPassedValue('newState', -1, 'GET');
    if ($newState == -1) {
        LogUtil::registerError('none new state received');
        AjaxUtil::output();
    }

    $site = pnModAPIFunc('Multisites', 'user', 'getSite',
                          array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        AjaxUtil::output();
    }
    if (!pnModAPIFunc('Multisites', 'admin', 'modifyActivation',
                       array('instanceId' => $instanceId,
                             'moduleName' => $moduleName,
                             'newState' => $newState))) {
        LogUtil::registerError('error changing module state');
        AjaxUtil::output();
    }

    $siteModules = pnModAPIFunc('Multisites', 'admin', 'getAllSiteModules',
                                 array('instanceId' => $instanceId));

    $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
    $icons = pnModFunc('Multisites', 'admin', 'siteElementsIcons',
                        array('name' => $moduleName,
                              'available' => $available,
                              'siteModules' => $siteModules,
                              'instanceId' => $instanceId));

    AjaxUtil::output(array('content' => $icons,
                           'moduleName' => $moduleName));
}

/**
 * Set a module as allowed or not allowed for a site
 * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	args   Array with the module name and instance identity
 * @return:	Create or delete a database record depending on the initial state
 */
function Multisites_ajax_allowModule($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
        AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Sorry! No authorization to access this module.', $dom)));
    }
    $instanceId = FormUtil::getPassedValue('instanceId', -1, 'GET');
    if ($instanceId == -1) {
        LogUtil::registerError('no instanceId value received');
        AjaxUtil::output();
    }
    $moduleName = FormUtil::getPassedValue('moduleName', -1, 'GET');
    if ($moduleName == -1) {
        LogUtil::registerError('no module name received');
        AjaxUtil::output();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite',
                          array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        AjaxUtil::output();
    }
    //get site module
    $module = pnModAPIFunc('Multisites', 'admin', 'getSiteModule',
                            array('instanceId' => $instanceId,
                                  'moduleName' => $moduleName));
    if ($module['state'] == 6) {
        //set the module as desactivated
        if (!pnModAPIFunc('Multisites', 'admin', 'modifyActivation',
                           array('instanceId' => $instanceId,
                                 'moduleName' => $moduleName,
                                 'newState' => 2))) {
            LogUtil::registerError('error changing module state');
            AjaxUtil::output();
        }
    } elseif ($module['state'] == 2 || $module['state'] == 3) {
        //set the module as not allowed
        if (!pnModAPIFunc('Multisites', 'admin', 'modifyActivation',
                           array('instanceId' => $instanceId,
                                 'moduleName' => $moduleName,
                                 'newState' => 6))) {
            LogUtil::registerError('error changing module state');
            AjaxUtil::output();
        }
    } elseif ($module['state'] == '') {
        //create module
        if (!pnModAPIFunc('Multisites', 'admin', 'createSiteModule',
                           array('instanceId' => $instanceId,
                                 'moduleName' => $moduleName))) {
            LogUtil::registerError('error creating module');
            AjaxUtil::output();
        }
    } else {
        //get site module
        if (!pnModAPIFunc('Multisites', 'admin', 'deleteSiteModule',
                           array('instanceId' => $instanceId,
                                 'moduleName' => $moduleName))) {
            LogUtil::registerError('error deleting module');
            AjaxUtil::output();
        }
    }
    $siteModules = pnModAPIFunc('Multisites', 'admin', 'getAllSiteModules',
                                 array('instanceId' => $instanceId));
    $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
    $icons = pnModFunc('Multisites', 'admin', 'siteElementsIcons',
                        array('name' => $moduleName,
                              'available' => $available,
                              'siteModules' => $siteModules,
                              'instanceId' => $instanceId));
    AjaxUtil::output(array('content' => $icons,
                           'moduleName' => $moduleName));
}

/**
 * Set a theme as allowed or not allowed for a site
 * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	args   Array with the theme name and instance identity
 * @return:	Create or delete a database record depending on the initial state
 */
function Multisites_ajax_allowTheme($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
        AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Sorry! No authorization to access this module.', $dom)));
    }
    $instanceId = FormUtil::getPassedValue('instanceId', -1, 'GET');
    if ($instanceId == -1) {
        LogUtil::registerError('no instanceId value received');
        AjaxUtil::output();
    }
    $themeName = FormUtil::getPassedValue('themeName', -1, 'GET');
    if ($themeName == -1) {
        LogUtil::registerError('no theme name received');
        AjaxUtil::output();
    }
    $site = pnModAPIFunc('Multisites', 'user', 'getSite',
                          array('instanceId' => $instanceId));
    if ($site == false) {
        LogUtil::registerError(__('Not site found', $dom));
        AjaxUtil::output();
    }
    //get site module
    $theme = pnModAPIFunc('Multisites', 'admin', 'getSiteTheme',
                           array('instanceId' => $instanceId,
                                 'themeName' => $themeName));
    if ($theme['name'] == '') {
        //create theme
        if (!pnModAPIFunc('Multisites', 'admin', 'createSiteTheme',
                           array('instanceId' => $instanceId,
                                 'themeName' => $themeName))) {
            LogUtil::registerError('error creating theme');
            AjaxUtil::output();
        }
    } else {
        //get site module
        if (!pnModAPIFunc('Multisites', 'admin', 'deleteSiteTheme',
                           array('instanceId' => $instanceId,
                                  'themeName' => $themeName))) {
            LogUtil::registerError('error deleting theme');
            AjaxUtil::output();
        }
    }
    $siteThemes = pnModAPIFunc('Multisites', 'admin', 'getAllSiteThemes',
                                array('instanceId' => $instanceId));
    $available = (array_key_exists($themeName, $siteThemes)) ? 1 : 0;
    $icons = pnModFunc('Multisites', 'admin', 'siteThemesIcons',
                        array('name' => $themeName,
                              'available' => $available,
                              'siteThemes' => $siteThemes,
                              'instanceId' => $instanceId));
    AjaxUtil::output(array('content' => $icons,
                           'themeName' => $themeName));
}