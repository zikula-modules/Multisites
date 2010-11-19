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
            LogUtil::registerPermissionError(null,true);
            throw new Zikula_Exception_Forbidden();
        }
/*
        if (!SecurityUtil::confirmAuthKey()) {
            LogUtil::registerAuthidError();
            throw new Zikula_Exception_Fatal();
        }
*/
        $instanceId = FormUtil::getPassedValue('instanceId', -1, 'POST');
        if ($instanceId == -1) {
            AjaxUtil::error($this->__('no instanceId value received.'));
        }

        $moduleName = FormUtil::getPassedValue('moduleName', -1, 'POST');
        if ($moduleName == -1) {
            AjaxUtil::error($this->__('no module name received.'));
        }

        $newState = FormUtil::getPassedValue('newState', -1, 'POST');
        if ($newState == -1) {
            AjaxUtil::error($this->__('none new state received.'));
        }

        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            AjaxUtil::error($this->__('Not site found.'));
        }
        if (!ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
        array('instanceId' => $instanceId,
        'moduleName' => $moduleName,
        'newState' => $newState))) {
            AjaxUtil::error($this->__('error changing module state.'));
        }

        $siteModules = ModUtil::apiFunc('Multisites', 'admin', 'getAllSiteModules',
                array('instanceId' => $instanceId));

        $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func('Multisites', 'admin', 'siteElementsIcons',
                array('name' => $moduleName,
                'available' => $available,
                'siteModules' => $siteModules,
                'instanceId' => $instanceId));

        return new Zikula_Response_Ajax(array('content' => $icons,
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
            LogUtil::registerPermissionError(null,true);
            throw new Zikula_Exception_Forbidden();
        }

        $instanceId = FormUtil::getPassedValue('instanceId', -1, 'POST');
        if ($instanceId == -1) {
            AjaxUtil::error($this->__('no instanceId value received.'));
        }
        $moduleName = FormUtil::getPassedValue('moduleName', -1, 'POST');
        if ($moduleName == -1) {
            AjaxUtil::error($this->__('no module name received.'));
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            AjaxUtil::error($this->__('Not site found.'));
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
                AjaxUtil::error($this->__('error changing module state.'));
            }
        } elseif ($module['state'] == 2 || $module['state'] == 3) {
            //set the module as not allowed
            if (!ModUtil::apiFunc('Multisites', 'admin', 'modifyActivation',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName,
            'newState' => 6))) {
                AjaxUtil::error($this->__('error changing module state.'));
            }
        } elseif ($module['state'] == '') {
            //create module
            if (!ModUtil::apiFunc('Multisites', 'admin', 'createSiteModule',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName))) {
                AjaxUtil::error($this->__('error creating module.'));
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteSiteModule',
            array('instanceId' => $instanceId,
            'moduleName' => $moduleName))) {
                AjaxUtil::error($this->__('error deleting module.'));
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

        return new Zikula_Response_Ajax(array('content' => $icons,
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
            LogUtil::registerPermissionError(null,true);
            throw new Zikula_Exception_Forbidden();
        }

        $instanceId = FormUtil::getPassedValue('instanceId', -1, 'POST');
        if ($instanceId == -1) {
            AjaxUtil::error($this->__('no instanceId value received.'));
        }
        $themeName = FormUtil::getPassedValue('themeName', -1, 'POST');
        if ($themeName == -1) {
            AjaxUtil::error($this->__('no theme name received.'));
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite',
                array('instanceId' => $instanceId));
        if ($site == false) {
            AjaxUtil::error($this->__('Not site found.'));
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
                AjaxUtil::error($this->__('error creating theme.'));
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc('Multisites', 'admin', 'deleteSiteTheme',
            array('instanceId' => $instanceId,
            'themeName' => $themeName))) {
                AjaxUtil::error($this->__('error deleting theme.'));
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

        return new Zikula_Response_Ajax(array('content' => $icons,
                                              'themeName' => $themeName));
    }
}