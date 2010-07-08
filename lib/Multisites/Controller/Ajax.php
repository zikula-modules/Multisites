<?php

class Multisites_Controller_Ajax extends Zikula_Controller
{
    public function _postSetup()
    {
        // no need for a Zikula_View so override it.
    }

    /**
     * Delete a module from a given site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the module name and the instance identity
     * @return:	Delete a database record
     */
    public function modifyActivation($args)
    {
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            AjaxUtil::error(DataUtil::formatForDisplayHTML($this->__('Sorry! No authorization to access this module.')));
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

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            AjaxUtil::output();
        }
        if (!ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
        array('instanceId' => $instanceId,
        'moduleName' => $moduleName,
        'newState' => $newState))) {
            LogUtil::registerError('error changing module state');
            AjaxUtil::output();
        }

        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                array('instanceId' => $instanceId));

        $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func('Multisites', 'admin', 'siteElementsIcons',
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
    public function allowModule($args)
    {
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            AjaxUtil::error(DataUtil::formatForDisplayHTML($this->__('Sorry! No authorization to access this module.')));
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
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            AjaxUtil::output();
        }
        //get site module
        $module = ModUtil::apiFunc('Multisites', 'admin', 'getSiteModule',
                array('instanceId' => $instanceId,
                'moduleName' => $moduleName));
        if ($module['state'] == 6) {
            //set the module as desactivated
            if (!ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName,
            'newState' => 2))) {
                LogUtil::registerError('error changing module state');
                AjaxUtil::output();
            }
        } elseif ($module['state'] == 2 || $module['state'] == 3) {
            //set the module as not allowed
            if (!ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName,
            'newState' => 6))) {
                LogUtil::registerError('error changing module state');
                AjaxUtil::output();
            }
        } elseif ($module['state'] == '') {
            //create module
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createSiteModule',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName))) {
                LogUtil::registerError('error creating module');
                AjaxUtil::output();
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteSiteModule',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName))) {
                LogUtil::registerError('error deleting module');
                AjaxUtil::output();
            }
        }
        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                array('instanceId' => $instanceId));
        $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func('Multisites', 'admin', 'siteElementsIcons',
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
    public function allowTheme($args)
    {
        if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
            AjaxUtil::error(DataUtil::formatForDisplayHTML($this->__('Sorry! No authorization to access this module.')));
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
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            LogUtil::registerError($this->__('Not site found'));
            AjaxUtil::output();
        }
        //get site module
        $theme = ModUtil::apiFunc('Multisites', 'admin', 'getSiteTheme',
                array('instanceId' => $instanceId,
                'themeName' => $themeName));
        if ($theme['name'] == '') {
            //create theme
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createSiteTheme',
            array('instanceId' => $instanceId,
            'themeName' => $themeName))) {
                LogUtil::registerError('error creating theme');
                AjaxUtil::output();
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteSiteTheme',
            array('instanceId' => $instanceId,
            'themeName' => $themeName))) {
                LogUtil::registerError('error deleting theme');
                AjaxUtil::output();
            }
        }
        $siteThemes = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteThemes',
                array('instanceId' => $instanceId));
        $available = (array_key_exists($themeName, $siteThemes)) ? 1 : 0;
        $icons = ModUtil::func('Multisites', 'admin', 'siteThemesIcons',
                array('name' => $themeName,
                'available' => $available,
                'siteThemes' => $siteThemes,
                'instanceId' => $instanceId));
        AjaxUtil::output(array('content' => $icons,
                'themeName' => $themeName));
    }
}