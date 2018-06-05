<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package Multisites
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

/**
 * Site controller class providing navigation and interaction functionality.
 */
class Multisites_Controller_Site extends Multisites_Controller_Base_Site
{
    /**
     * Renders the module management overview page.
     *
     * @return mixed Output.
     */
    public function manageExtensions()
    {
        $legacyControllerType = $this->request->query->filter('lct', 'user', FILTER_SANITIZE_STRING);
        System::queryStringSetVar('type', $legacyControllerType);
        $this->request->query->set('type', $legacyControllerType);

        // load the given entity
        $site = $this->loadCurrentSite();
        if (!is_object($site)) {
            return;
        }

        // helper for extensions-related functions
        $extensionHandler = new Multisites_Util_SiteExtensionHandler($this->serviceManager);

        // get all the modules located in the modules directory
        $allModules = ModUtil::apiFunc('Extensions', 'admin', 'getfilemodules');
        sort($allModules);

        // get all the modules available in the site
        $siteModules = $extensionHandler->getAllModulesFromSiteDb($site);

        // create output array
        $modules = array();

        foreach ($allModules as $mod) {
            if ($mod['type'] == ModUtil::TYPE_SYSTEM) {
                continue;
            }

            // check if the module exists in the site database
            $available = array_key_exists($mod['name'], $siteModules);

            $icons = $extensionHandler->getActionIconsForSiteModule($this->view, $site, array(
                                        'name' => $mod['name'],
                                        'available' => $available,
                                        'siteModules' => $siteModules));

            // add to output array
            $module = $mod;
            $module['icons'] = $icons;
            $modules[] = $module;
        }

        $this->view->assign('site', $site)
                   ->assign('modules', $modules);

        $this->view->addPluginDir('system/Admin/templates/plugins');

        // return template
        return $this->view->fetch('site/manageExtensions.tpl');
    }
    
    /**
     * Renders the theme management overview page.
     *
     * @return mixed Output.
     */
    public function manageThemes()
    {
        $legacyControllerType = $this->request->query->filter('lct', 'user', FILTER_SANITIZE_STRING);
        System::queryStringSetVar('type', $legacyControllerType);
        $this->request->query->set('type', $legacyControllerType);

        // load the given entity
        $site = $this->loadCurrentSite();
        if (!is_object($site)) {
            return;
        }

        // helper for extensions-related functions
        $extensionHandler = new Multisites_Util_SiteExtensionHandler($this->serviceManager);

        // get all the themes available in the themes directory
        $allThemes = $extensionHandler->getAllThemesInSystem();

        // get all the themes available in the site
        $siteThemes = $extensionHandler->getAllThemesFromSiteDb($site);

        // get information about which theme is the current default one
        $defaultTheme = $extensionHandler->getSiteDefaultTheme($site);
        // legacy... seems to be something like unserialize() :-)
        $pos = strpos($defaultTheme, '"');
        $defaultTheme = substr($defaultTheme, $pos + 1, -2);

        // create output array
        $themes = array();

        foreach ($allThemes as $thm) {
            // check if the theme exists in the site database
            $available = array_key_exists($thm['name'], $siteThemes);
            $isDefaultTheme = strtolower($thm['name']) == strtolower($defaultTheme);

            $icons = $extensionHandler->getActionIconsForSiteTheme($this->view, $site, array(
                                        'name' => $thm['name'],
                                        'available' => $available,
                                        'isDefaultTheme' => $isDefaultTheme,
                                        'siteThemes' => $siteThemes));

            // add to output array
            $theme = $thm;
            $theme['icons'] = $icons;
            $themes[] = $theme;
        }

        $this->view->assign('site', $site)
                   ->assign('themes', $themes);

        $this->view->addPluginDir('system/Admin/templates/plugins');

        // return template
        return $this->view->fetch('site/manageThemes.tpl');
    }

    /**
     * Sets a given theme as default theme.
     *
     * @return mixed Output.
     */
    public function setThemeAsDefault()
    {
        $legacyControllerType = $this->request->query->filter('lct', 'user', FILTER_SANITIZE_STRING);
        System::queryStringSetVar('type', $legacyControllerType);
        $this->request->query->set('type', $legacyControllerType);

        // load the given entity
        $site = $this->loadCurrentSite();
        if (!is_object($site)) {
            return;
        }

        $name = $this->request->query->get('name', null);
        if (is_null($name) || empty($name)) {
            LogUtil::registerError($this->__('Error! No valid theme name received.'));
        } else {
            // helper for extensions-related functions
            $extensionHandler = new Multisites_Util_SiteExtensionHandler($this->serviceManager);

            $extensionHandler->setAsDefaultTheme($site, $name); // error handling inside
        }

        // redirect to the admin main page
        return $this->redirect(ModUtil::url($this->name, 'admin', 'manageThemes',
                                              array('ot' => 'site', 'id' => $site['id'])));
    }
    
    /**
     * Renders an overview page showing available site tools.
     *
     * @return mixed Output.
     */
    public function viewTools()
    {
        $legacyControllerType = $this->request->query->filter('lct', 'user', FILTER_SANITIZE_STRING);
        System::queryStringSetVar('type', $legacyControllerType);
        $this->request->query->set('type', $legacyControllerType);

        // load the given entity
        $site = $this->loadCurrentSite();
        if (!is_object($site)) {
            return;
        }

        $this->view->assign('site', $site);

        $this->view->addPluginDir('system/Admin/templates/plugins');

        // return template
        return $this->view->fetch('site/viewTools.tpl');
    }
    
    /**
     * Executes an administrative action.
     *
     * @return mixed Output.
     */
    public function executeTool()
    {
        $legacyControllerType = $this->request->query->filter('lct', 'user', FILTER_SANITIZE_STRING);
        System::queryStringSetVar('type', $legacyControllerType);
        $this->request->query->set('type', $legacyControllerType);
    
        // load the given entity
        $site = $this->loadCurrentSite();
        if (!is_object($site)) {
            return;
        }

        $tool = $this->request->query->get('tool', null);
        $systemHelper = new Multisites_Util_System($this->serviceManager);

        switch ($tool) {
            case 'createAdministrator':
                $result = $systemHelper->createAdministrator($site);
                if ($result) {
                    LogUtil::registerStatus($this->__('A global administrator has been created.'));
                }
                break;
            case 'adminSiteControl':
                $recoverAdminSiteControl = $systemHelper->recoverAdminSiteControl($site);
                if ($recoverAdminSiteControl) {
                    LogUtil::registerStatus($this->__('The administration control has been recovered.'));
                }
                break;
            default:
                LogUtil::registerError($this->__('Not tool selected'));
        }

        return $this->redirect(ModUtil::url($this->name, 'site', 'viewTools', array('id' => $site['id'])));
    }

    /**
     * This is a custom method.
     *
     *
     * @return mixed Output.
     */
    public function exportDatabaseAsTemplate()
    {
        $legacyControllerType = $this->request->query->filter('lct', 'user', FILTER_SANITIZE_STRING);
        System::queryStringSetVar('type', $legacyControllerType);
        $this->request->query->set('type', $legacyControllerType);

        // load the given entity
        $site = $this->loadCurrentSite();
        if (!is_object($site)) {
            return;
        }

        // create a temporary output file in the temp folder (where the database server has probably write access)
        $tempPrefix = UserUtil::getVar('uid'); // to avoid a race condition
        $tempFilePath = tempnam(sys_get_temp_dir(), $tempPrefix);
        if (file_exists($tempFilePath)) {
            @unlink($tempFilePath);
        }
        $tempFilePath = str_replace('\\', '/', $tempFilePath);

        // start the database dump
        $systemHelper = new Multisites_Util_System($this->serviceManager);
        if (!$systemHelper->dumpDatabase($site, $tempFilePath)) {
            return $this->redirect('Multisites', 'admin', 'view', array('ot' => 'site'));
        }

        // create name of the final sql output file
        $controllerHelper = new Multisites_Util_Controller($this->serviceManager);
        $sqlFileName = $controllerHelper->formatPermalink($site->getTitleFromDisplayPattern())
                   . '-dump-' . date('Ymd_His', time()) . '.sql';

        // send output file to the browser
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $sqlFileName . '"');

        header("Content-Length: " . filesize($tempFilePath));

        $fp = fopen($tempFilePath, 'rb');

        fpassthru($fp);

        fclose($fp);

        return true;
    }

    /**
     * Helper method for easier entity retrieval.
     *
     * @return Multisites_Entity_Site|null The entity instance or null if it could not be found.
     */
    protected function loadCurrentSite()
    {
        $controllerHelper = new Multisites_Util_Controller($this->serviceManager);

        // parameter specifying which type of objects we are treating
        $objectType = 'site';
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . ':' . ucfirst($objectType) . ':', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());
        $entityClass = $this->name . '_Entity_' . ucfirst($objectType);
        $repository = $this->entityManager->getRepository($entityClass);
        $repository->setControllerArguments(array());

        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $objectType));

        // retrieve identifier of the object we wish to view
        $idValues = $controllerHelper->retrieveIdentifier($this->request, array(), $objectType, $idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($idValues);

        $this->throwNotFoundUnless($hasIdentifier, $this->__('Error! Invalid identifier received.'));

        $selectionArgs = array('ot' => $objectType, 'id' => $idValues);

        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
        $this->throwNotFoundUnless($entity != null, $this->__('No such item.'));
        unset($idValues);
        
        $entity->initWorkflow();

        return $entity;
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param string $action The action to be executed.
     * @param array  $items  Identifier list of the items to be processed.
     *
     * @return bool true on sucess, false on failure.
     */
    public function handleSelectedEntries()
    {
        $allowedCustomActions = array('cleartemplates');

        $action = $this->request->request->get('action', null);
        if (!in_array($action, $allowedCustomActions)) {
            // delegate to parent
            return $parent::handleSelectedEntries();
        }

        $this->checkCsrfToken();

        $redirectUrl = ModUtil::url($this->name, 'admin', 'main', array('ot' => 'site'));

        $items = $this->request->request->get('items', null);

        // Initialize and prepare the action
        $needsDatabaseAccess = false;
        switch ($action) {
            case 'cleartemplates':
                // Backup temp folder setting of main site
                $originalTempFolder = $GLOBALS['ZConfig']['System']['temp'];
                break;
            case 'anotherOne':
                // This one needs the database
                $needsDatabaseAccess = true;
                break;
        }

        $siteBasePath = $this->serviceManager['multisites.files_real_path'];
        $zikula = $this->getService('zikula');

        $systemHelper = null;
        if ($needsDatabaseAccess) {
            $systemHelper = new Multisites_Util_System($this->serviceManager);
        }

        // process each item
        foreach ($items as $itemid) {
            // Reboot the core for this site
            //$zikula->reboot();
            //$zikula->init();

            // Get site information
            $selectionArgs = array('ot' => 'site',
                                   'id' => $itemid,
                                   'useJoins' => false);
            $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
            if ($entity === false || !is_object($entity)) {
                LogUtil::registerError($this->__f('Error! No site with id %s could be found.', array(DataUtil::formatForDisplay($itemid))));
                continue;
            }

            $entity->initWorkflow();

            if ($needsDatabaseAccess) {
                $connect = $systemHelper->connectToExternalDatabase($entity->getDatabaseData());
                if (!$connect) {
                    LogUtil::registerError($this->__f('Error! Connecting to the database %s failed.', array($entity['databaseName'])));
                    continue;
                }
            }

            // Execute action for current site
            switch ($action) {
                case 'cleartemplates':
                    // Set temp folder for this site instance
                    $siteTempDirectory = $siteBasePath . '/' . $instance['sitedns'] . '/' . $this->serviceManager['multisites.site_temp_files_folder'];
                    $GLOBALS['ZConfig']['System']['temp'] = $siteTempDirectory;

                    ModUtil::apiFunc('Settings', 'admin', 'clearallcompiledcaches');
                    LogUtil::registerStatus($this->__f('Done! Cleared all cache and compile directories for site %s.', array($entity->getTitleFromDisplayPattern())));
                    break;
            }
        }

        // Cleanup
        $needsDatabaseAccess = false;
        switch ($action) {
            case 'cleartemplates':
                // Restore temp folder setting for main site
                $GLOBALS['ZConfig']['System']['temp'] = $originalTempFolder;
                break;
        }

        return $this->redirect($redirectUrl);
    }
}