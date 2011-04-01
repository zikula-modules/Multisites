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
        $instanceid = $this->request->getPost()->get('instanceid', -1);
        if ($instanceid == -1) {
            AjaxUtil::error($this->__('No instanceid value received.'));
        }

        $modulename = $this->request->getPost()->get('modulename', -1);
        if ($modulename == -1) {
            AjaxUtil::error($this->__('No module name received.'));
        }

        $newState = $this->request->getPost()->get('newState', -1);
        if ($newState == -1) {
            AjaxUtil::error($this->__('No new state received.'));
        }

        $site = ModUtil::apiFunc($this->name, 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            AjaxUtil::error($this->__('Not site found.'));
        }
        if (!ModUtil::apiFunc($this->name, 'admin', 'modifyActivation',
                                                            array('instanceid' => $instanceid,
                                                                  'modulename' => $modulename,
                                                                  'newState' => $newState))) {
            AjaxUtil::error($this->__('Error changing module state.'));
        }

        $siteModules = ModUtil::apiFunc($this->name, 'admin', 'getAllSiteModules', array('instanceid' => $instanceid));

        $available = (array_key_exists($modulename, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func($this->name, 'admin', 'siteElementsIcons',
                array('name' => $modulename,
                'available' => $available,
                'siteModules' => $siteModules,
                'instanceid' => $instanceid));

        return new Zikula_Response_Ajax(array('content' => $icons,
                                              'modulename' => $modulename));
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

        $instanceid = $this->request->getPost()->get('instanceid', -1);
        if ($instanceid == -1) {
            AjaxUtil::error($this->__('No instanceid value received.'));
        }
        $modulename = $this->request->getPost()->get('modulename', -1);
        if ($modulename == -1) {
            AjaxUtil::error($this->__('No module name received.'));
        }
        $site = ModUtil::apiFunc('Multisites', 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            AjaxUtil::error($this->__('No site found.'));
        }
        //get site module
        $module = ModUtil::apiFunc($this->name, 'admin', 'getSiteModule',
                                                                array('instanceid' => $instanceid,
                                                                      'modulename' => $modulename));
        if ($module['state'] == 6) {
            //set the module as desactivated
            if (!ModUtil::apiFunc($this->name, 'admin', 'modifyActivation',
                                                                array('instanceid' => $instanceid,
                                                                      'modulename' => $modulename,
                                                                      'newState' => 2))) {
                AjaxUtil::error($this->__('Error changing module state.'));
            }
        } elseif ($module['state'] == 2 || $module['state'] == 3) {
            //set the module as not allowed
            if (!ModUtil::apiFunc($this->name, 'admin', 'modifyActivation',
                                                                array('instanceid' => $instanceid,
                                                                      'modulename' => $modulename,
                                                                      'newState' => 6))) {
                AjaxUtil::error($this->__('Error changing module state.'));
            }
        } elseif ($module['state'] == '') {
            //create module
            if (!ModUtil::apiFunc($this->name, 'admin', 'createSiteModule',
                                                                array('instanceid' => $instanceid,
                                                                      'modulename' => $modulename))) {
                AjaxUtil::error($this->__('Error creating module.'));
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc($this->name, 'admin', 'deleteSiteModule',
                                                                array('instanceid' => $instanceid,
                                                                      'modulename' => $modulename))) {
                AjaxUtil::error($this->__('Error deleting module.'));
            }
        }
        $siteModules = ModUtil::apiFunc($this->name, 'admin', 'getAllSiteModules', array('instanceid' => $instanceid));
        $available = (array_key_exists($modulename, $siteModules)) ? 1 : 0;
        $icons = ModUtil::func($this->name, 'admin', 'siteElementsIcons',
                                                                array('name' => $modulename,
                                                                      'available' => $available,
                                                                      'siteModules' => $siteModules,
                                                                      'instanceid' => $instanceid));

        return new Zikula_Response_Ajax(array('content' => $icons,
                                              'modulename' => $modulename));
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

        $instanceid = $this->request->getPost()->get('instanceid', -1);
        if ($instanceid == -1) {
            AjaxUtil::error($this->__('No instanceid value received.'));
        }
        $themeName = $this->request->getPost()->get('themeName', -1);
        if ($themeName == -1) {
            AjaxUtil::error($this->__('No theme name received.'));
        }
        $site = ModUtil::apiFunc($this->name, 'user', 'getSite', array('instanceid' => $instanceid));
        if ($site == false) {
            AjaxUtil::error($this->__('No site found.'));
        }
        //get site module
        $theme = ModUtil::apiFunc($this->name, 'admin', 'getSiteTheme',
                                                                array('instanceid' => $instanceid,
                                                                      'themeName' => $themeName));
        if ($theme['name'] == '') {
            //create theme
            if (!ModUtil::apiFunc($this->name, 'admin', 'createSiteTheme',
                                                                array('instanceid' => $instanceid,
                                                                      'themeName' => $themeName))) {
                AjaxUtil::error($this->__('Error creating theme.'));
            }
        } else {
            //get site module
            if (!ModUtil::apiFunc($this->name, 'admin', 'deleteSiteTheme',
                                                                array('instanceid' => $instanceid,
                                                                      'themeName' => $themeName))) {
                AjaxUtil::error($this->__('Error deleting theme.'));
            }
        }
        $siteThemes = ModUtil::apiFunc($this->name, 'admin', 'getAllSiteThemes', array('instanceid' => $instanceid));
        $available = (array_key_exists($themeName, $siteThemes)) ? 1 : 0;
        $icons = ModUtil::func($this->name, 'admin', 'siteThemesIcons',
                                                                array('name' => $themeName,
                                                                      'available' => $available,
                                                                      'siteThemes' => $siteThemes,
                                                                      'instanceid' => $instanceid));

        return new Zikula_Response_Ajax(array('content' => $icons,
                                              'themeName' => $themeName));
    }
}