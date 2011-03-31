<?php

class Multisites_Controller_Ajax extends Zikula_AbstractController
{
    public function _postSetup()
    {
        // no need for a Zikula_View so override it.
    }

    /**
     * Delete a module from a given site
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  args   Array with the module name and the instance identity
     * @return: Delete a database record
     */
    public function modifyActivation($args)
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));
/*
        if (!SecurityUtil::confirmAuthKey()) {
            LogUtil::registerAuthidError();
            throw new Zikula_Exception_Fatal();
        }
*/
        $instanceId = $this->request->getPost()->get('instanceId', -1);
        if ($instanceId == -1) {
            AjaxUtil::error($this->__('No instanceId value received.'));
        }

        $moduleName = $this->request->getPost()->get('moduleName', -1);
        if ($moduleName == -1) {
            AjaxUtil::error($this->__('No module name received.'));
        }

        $newState = $this->request->getPost()->get('newState', -1);
        if ($newState == -1) {
            AjaxUtil::error($this->__('No new state received.'));
        }

        $site = ModUtil::apiFunc($this->name, 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            AjaxUtil::error($this->__('Not site found.'));
        }
        if (!ModUtil::apiFunc($this->name, 'admin', 'modifyActivation',
                                                            array('instanceId' => $instanceId,
                                                                  'moduleName' => $moduleName,
                                                                  'newState' => $newState))) {
            AjaxUtil::error($this->__('Error changing module state.'));
        }

        $siteModules = ModUtil::apiFunc($this->name, 'admin', 'getAllSiteModules', array('instanceId' => $instanceId));

        $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func($this->name, 'admin', 'siteElementsIcons',
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
     * @param:  args   Array with the module name and instance identity
     * @return: Create or delete a database record depending on the initial state
     */
    public function allowModule($args)
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        $instanceId = $this->request->getPost()->get('instanceId', -1);
        if ($instanceId == -1) {
            AjaxUtil::error($this->__('No instanceId value received.'));
        }
        $moduleName = $this->request->getPost()->get('moduleName', -1);
        if ($moduleName == -1) {
            AjaxUtil::error($this->__('No module name received.'));
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            AjaxUtil::error($this->__('No site found.'));
        }
        //get site module
        $module = ModUtil::apiFunc($this->name, 'admin', 'getSiteModule',
                                                                array('instanceId' => $instanceId,
                                                                      'moduleName' => $moduleName));
        if ($module['state'] == 6) {
            //set the module as desactivated
            if (!ModUtil::apiFunc($this->name, 'admin', 'modifyActivation',
                                                                array('instanceId' => $instanceId,
                                                                      'moduleName' => $moduleName,
                                                                      'newState' => 2))) {
                AjaxUtil::error($this->__('Error changing module state.'));
            }
        } elseif ($module['state'] == 2 || $module['state'] == 3) {
            //set the module as not allowed
            if (!ModUtil::apiFunc($this->name, 'admin', 'modifyActivation',
                                                                array('instanceId' => $instanceId,
                                                                      'moduleName' => $moduleName,
                                                                      'newState' => 6))) {
                AjaxUtil::error($this->__('Error changing module state.'));
            }
        } elseif ($module['state'] == '') {
            //create module
            if (!ModUtil::apiFunc($this->name, 'admin', 'createSiteModule',
                                                                array('instanceId' => $instanceId,
                                                                      'moduleName' => $moduleName))) {
                AjaxUtil::error($this->__('Error creating module.'));
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc($this->name, 'admin', 'deleteSiteModule',
                                                                array('instanceId' => $instanceId,
                                                                      'moduleName' => $moduleName))) {
                AjaxUtil::error($this->__('Error deleting module.'));
            }
        }
        $siteModules = ModUtil::apiFunc($this->name, 'admin', 'getAllSiteModules', array('instanceId' => $instanceId));
        $available = (array_key_exists($moduleName, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func($this->name, 'admin', 'siteElementsIcons',
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
     * @param:  args   Array with the theme name and instance identity
     * @return: Create or delete a database record depending on the initial state
     */
    public function allowTheme($args)
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        $instanceId = $this->request->getPost()->get('instanceId', -1);
        if ($instanceId == -1) {
            AjaxUtil::error($this->__('No instanceId value received.'));
        }
        $themeName = $this->request->getPost()->get('themeName', -1);
        if ($themeName == -1) {
            AjaxUtil::error($this->__('No theme name received.'));
        }
        $site = ModUtil::apiFunc($this->name, 'user', 'getSite', array('instanceId' => $instanceId));
        if ($site == false) {
            AjaxUtil::error($this->__('No site found.'));
        }
        //get site module
        $theme = ModUtil::apiFunc($this->name, 'admin', 'getSiteTheme',
                                                                array('instanceId' => $instanceId,
                                                                      'themeName' => $themeName));
        if ($theme['name'] == '') {
            //create theme
            if (!ModUtil::apiFunc($this->name, 'admin', 'createSiteTheme',
                                                                array('instanceId' => $instanceId,
                                                                      'themeName' => $themeName))) {
                AjaxUtil::error($this->__('Error creating theme.'));
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc($this->name, 'admin', 'deleteSiteTheme',
                                                                array('instanceId' => $instanceId,
                                                                      'themeName' => $themeName))) {
                AjaxUtil::error($this->__('Error deleting theme.'));
            }
        }
        $siteThemes = ModUtil::apiFunc($this->name, 'admin', 'getAllSiteThemes', array('instanceId' => $instanceId));
        $available = (array_key_exists($themeName, $siteThemes)) ? 1 : 0;
        $icons = ModUtil::func($this->name, 'admin', 'siteThemesIcons',
                                                                array('name' => $themeName,
                                                                      'available' => $available,
                                                                      'siteThemes' => $siteThemes,
                                                                      'instanceId' => $instanceId));

        return new Zikula_Response_Ajax(array('content' => $icons,
                                              'themeName' => $themeName));
    }
}