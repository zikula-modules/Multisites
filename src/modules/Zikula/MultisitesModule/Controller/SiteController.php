<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Controller;

use Zikula\MultisitesModule\Controller\Base\AbstractSiteController;

use ModUtil;
use RuntimeException;
use ServiceUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserUtil;
use Zikula\MultisitesModule\Entity\SiteEntity;

/**
 * Site controller class providing navigation and interaction functionality.
 */
class SiteController extends AbstractSiteController
{
    /**
     * This action provides an item list overview in the admin area.
     *
     * @Route("/admin/sites/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0, "_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field
     * @param string  $sortdir      Sorting direction
     * @param int     $pos          Current pager position
     * @param int     $num          Amount of entries to display
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::adminViewAction($request, $sort, $sortdir, $pos, $num);
    }
    
    /**
     * This action provides an item list overview.
     *
     * @Route("/sites/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0, "_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field
     * @param string  $sortdir      Sorting direction
     * @param int     $pos          Current pager position
     * @param int     $num          Amount of entries to display
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::viewAction($request, $sort, $sortdir, $pos, $num);
    }

    /**
     * This action provides a handling of edit requests in the admin area.
     *
     * @Route("/admin/site/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminEditAction(Request $request)
    {
        return parent::adminEditAction($request);
    }
    
    /**
     * This action provides a handling of edit requests.
     *
     * @Route("/site/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function editAction(Request $request)
    {
        return parent::editAction($request);
    }

    /**
     * This action provides a handling of simple delete requests in the admin area.
     *
     * @Route("/admin/site/delete/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance
     * @param SiteEntity $site      Treated site instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if item to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminDeleteAction(Request $request, SiteEntity $site)
    {
        return parent::adminDeleteAction($request, $site);
    }
    
    /**
     * This action provides a handling of simple delete requests.
     *
     * @Route("/site/delete/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance
     * @param SiteEntity $site      Treated site instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if item to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function deleteAction(Request $request, SiteEntity $site)
    {
        return parent::deleteAction($request, $site);
    }

    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/sites/manageExtensions",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminManageExtensionsAction(Request $request)
    {
        return parent::adminManageExtensionsAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/sites/manageExtensions",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function manageExtensionsAction(Request $request)
    {
        return parent::manageExtensionsAction($request);
    }
    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/sites/manageThemes",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminManageThemesAction(Request $request)
    {
        return parent::adminManageThemesAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/sites/manageThemes",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function manageThemesAction(Request $request)
    {
        return parent::manageThemesAction($request);
    }
    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/sites/setThemeAsDefault",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminSetThemeAsDefaultAction(Request $request)
    {
        return parent::adminSetThemeAsDefaultAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/sites/setThemeAsDefault",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function setThemeAsDefaultAction(Request $request)
    {
        return parent::setThemeAsDefaultAction($request);
    }
    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/sites/viewTools",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminViewToolsAction(Request $request)
    {
        return parent::adminViewToolsAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/sites/viewTools",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewToolsAction(Request $request)
    {
        return parent::viewToolsAction($request);
    }
    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/sites/executeTool",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminExecuteToolAction(Request $request)
    {
        return parent::adminExecuteToolAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/sites/executeTool",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function executeToolAction(Request $request)
    {
        return parent::executeToolAction($request);
    }
    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/sites/exportDatabaseAsTemplate",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminExportDatabaseAsTemplateAction(Request $request)
    {
        return parent::adminExportDatabaseAsTemplateAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/sites/exportDatabaseAsTemplate",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function exportDatabaseAsTemplateAction(Request $request)
    {
        return parent::exportDatabaseAsTemplateAction($request);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @Route("/admin/sites/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return bool true on sucess, false on failure
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return parent::adminHandleSelectedEntriesAction($request);
    }

    /**
     * This method includes the common implementation code for adminHandleSelectedEntriesAction() and handleSelectedEntriesAction().
     */
    protected function handleSelectedEntriesActionInternal(Request $request, $isAdmin = false)
    {
        $allowedCustomActions = ['cleartemplates'];

        $action = $request->request->get('action', null);
        if (!in_array($action, $allowedCustomActions)) {
            // delegate to parent
            return parent::handleSelectedEntriesActionInternal($request, $isAdmin);
        }

        $items = $request->request->get('items', null);
        $serviceManager = ServiceUtil::getManager();

        // Initialize and prepare the action
        $originalTempFolder = '';
        $needsDatabaseAccess = false;
        switch ($action) {
            case 'cleartemplates':
                // Backup temp folder setting of main site
                $originalTempFolder = $serviceManager->getParameter('temp_dir');
                break;
            case 'anotherOne':
                // This one needs the database
                $needsDatabaseAccess = true;
                break;
        }

        $msConfig = $serviceManager->getParameter('multisites');
        $siteBasePath = $msConfig['files_real_path'];
        $zikula = $this->get('zikula');

        $systemHelper = $needsDatabaseAccess ? $this->get('zikula_multisites_module.system_helper') : null;

        // process each item
        foreach ($items as $itemid) {
            // Reboot the core for this site
            //$zikula->reboot();
            //$zikula->init();

            // Get site information
            $selectionArgs = [
                'ot' => 'site',
                'id' => $itemid,
                'useJoins' => false
            ];
            $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
            if ($entity === false || !is_object($entity)) {
                $this->addFlash('error', $this->__f('Error! No site with id %s could be found.', ['%s' => $itemid]));
                continue;
            }

            $entity->initWorkflow();

            if ($needsDatabaseAccess) {
                $connect = $systemHelper->connectToExternalDatabase($entity->getDatabaseData());
                if (!$connect) {
                    $this->addFlash('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $entity['databaseName']]));
                    continue;
                }
            }

            // Execute action for current site
            switch ($action) {
                case 'cleartemplates':
                    // Set temp folder for this site instance
                    $siteTempDirectory = $siteBasePath . '/' . $entity['sitedns'] . '/' . $msConfig['site_temp_files_folder'];
                    $serviceManager->setParameter('temp_dir', $siteTempDirectory);

                    ModUtil::apiFunc('ZikulaSettingsModule', 'admin', 'clearallcompiledcaches');
                    $this->addFlash('error', $this->__f('Done! Cleared all cache and compile directories for site %s.', ['%s' => $entity->getTitleFromDisplayPattern()]));
                    break;
            }
        }

        // Cleanup
        $needsDatabaseAccess = false;
        switch ($action) {
            case 'cleartemplates':
                // Restore temp folder setting for main site
                $serviceManager->setParameter('temp_dir', $originalTempFolder);
                break;
        }

        return $this->redirectToRoute('zikulamultisitesmodule_site_index');
    }

    /**
     * This method includes the common implementation code for adminManageExtensions() and manageExtensions().
     */
    protected function manageExtensionsInternal(Request $request, $isAdmin = false)
    {
        // load the given entity
        $site = $this->loadCurrentSite($request);
        if (!is_object($site)) {
            return;
        }

        // helper for extensions-related functions
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // get all the modules located in the modules directory
        $allModules = ModUtil::apiFunc('ZikulaExtensionsModule', 'admin', 'getfilemodules');
        sort($allModules);

        // get all the modules available in the site
        $siteModules = $extensionHelper->getAllModulesFromSiteDb($site);

        // create output array
        $modules = [];

        foreach ($allModules as $mod) {
            if (ModUtil::TYPE_SYSTEM == $mod['type']) {
                continue;
            }

            // check if the module exists in the site database
            $available = array_key_exists($mod['name'], $siteModules);

            $icons = $extensionHelper->getActionIconsForSiteModule($site, [
                'name' => $mod['name'],
                'available' => $available,
                'siteModules' => $siteModules
            ]);

            // add to output array
            $module = $mod;
            $module['icons'] = $icons;
            $modules[] = $module;
        }

        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        $templateParameters = [
            'site' => $site,
            'modules' => $modules
        ];

        return $viewHelper->processTemplate($this->get('twig'), 'site', 'manageExtensions', $request, $templateParameters);
    }
    
    /**
     * This method includes the common implementation code for adminManageThemes() and manageThemes().
     */
    protected function manageThemesInternal(Request $request, $isAdmin = false)
    {
        // load the given entity
        $site = $this->loadCurrentSite($request);
        if (!is_object($site)) {
            return;
        }

        // helper for extensions-related functions
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // get all the themes available in the themes directory
        $allThemes = $extensionHelper->getAllThemesInSystem();

        // get all the themes available in the site
        $siteThemes = $extensionHelper->getAllThemesFromSiteDb($site);

        // get information about which theme is the current default one
        $defaultTheme = $extensionHelper->getSiteDefaultTheme($site);
        // legacy... seems to be something like unserialize() :-)
        $pos = strpos($defaultTheme, '"');
        $defaultTheme = substr($defaultTheme, $pos + 1, -2);

        // create output array
        $themes = [];

        foreach ($allThemes as $thm) {
            // check if the theme exists in the site database
            $available = array_key_exists($thm['name'], $siteThemes);
            $isDefaultTheme = strtolower($thm['name']) == strtolower($defaultTheme);

            $icons = $extensionHelper->getActionIconsForSiteTheme($site, [
                'name' => $thm['name'],
                'available' => $available,
                'isDefaultTheme' => $isDefaultTheme,
                'siteThemes' => $siteThemes
            ]);

            // add to output array
            $theme = $thm;
            $theme['icons'] = $icons;
            $themes[] = $theme;
        }

        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        $templateParameters = [
            'site' => $site,
            'themes' => $themes
        ];

        return $viewHelper->processTemplate($this->get('twig'), 'site', 'manageThemes', $request, $templateParameters);
    }

    /**
     * This method includes the common implementation code for adminSetThemeAsDefault() and setThemeAsDefault().
     */
    protected function setThemeAsDefaultInternal(Request $request, $isAdmin = false)
    {
        // load the given entity
        $site = $this->loadCurrentSite($request);
        if (!is_object($site)) {
            return;
        }

        $name = $request->query->get('name', null);
        if (is_null($name) || empty($name)) {
            $this->addFlash('error', $this->__('Error! No valid theme name received.'));
        } else {
            // helper for extensions-related functions
            $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

            $extensionHelper->setAsDefaultTheme($site, $name); // error handling inside
        }

        // redirect to the admin main page
        return $this->redirectToRoute('zikulamultisitesmodule_site_adminmanagethemes', ['id' => $site['id']]);
    }

    /**
     * This method includes the common implementation code for adminViewTools() and viewTools().
     */
    protected function viewToolsInternal(Request $request, $isAdmin = false)
    {
        // load the given entity
        $site = $this->loadCurrentSite($request);
        if (!is_object($site)) {
            return;
        }

        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        $templateParameters = [
            'site' => $site
        ];

        return $viewHelper->processTemplate($this->get('twig'), 'site', 'viewTools', $request, $templateParameters);
    }

    /**
     * This method includes the common implementation code for adminExecuteTool() and executeTool().
     */
    protected function executeToolInternal(Request $request, $isAdmin = false)
    {
        // load the given entity
        $site = $this->loadCurrentSite($request);
        if (!is_object($site)) {
            return;
        }

        $tool = $request->query->get('tool', null);
        $systemHelper = $this->get('zikula_multisites_module.system_helper');

        switch ($tool) {
            case 'createAdministrator':
                $result = $systemHelper->createAdministrator($site);
                if ($result) {
                    $this->addFlash('status', $this->__('A global administrator has been created.'));
                }
                break;
            case 'adminSiteControl':
                $recoverAdminSiteControl = $systemHelper->recoverAdminSiteControl($site);
                if ($recoverAdminSiteControl) {
                    $this->addFlash('status', $this->__('The administration control has been recovered.'));
                }
                break;
            default:
                $this->addFlash('error', $this->__('No tool selected'));
        }

        return $this->redirectToRoute('zikulamultisitesmodule_site_adminviewtools', ['id' => $site['id']]);
    }

    /**
     * This method includes the common implementation code for adminExportDatabaseAsTemplate() and exportDatabaseAsTemplate().
     */
    protected function exportDatabaseAsTemplateInternal(Request $request, $isAdmin = false)
    {
        // load the given entity
        $site = $this->loadCurrentSite($request);
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
        $systemHelper = $this->get('zikula_multisites_module.system_helper');
        if (!$systemHelper->dumpDatabase($site, $tempFilePath)) {
            return $this->redirect('Multisites', 'admin', 'view', ['ot' => 'site']);
        }

        // create name of the final sql output file
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');
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
     * @param Request $request Current request instance.
     *
     * @return SiteEntity|null The entity instance or null if it could not be found.
     */
    protected function loadCurrentSite(Request $request)
    {
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');

        // parameter specifying which type of objects we are treating
        $objectType = 'site';
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        $repository = $this->get('zikula_multisites_module.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);

        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', ['ot' => $objectType]);

        // retrieve identifier of the object we wish to view
        $idValues = $controllerHelper->retrieveIdentifier($request, [], $objectType, $idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($idValues);

        if (!$hasIdentifier) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }

        $selectionArgs = ['ot' => $objectType, 'id' => $idValues];

        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }
        unset($idValues);
        
        $entity->initWorkflow();

        return $entity;
    }
}
