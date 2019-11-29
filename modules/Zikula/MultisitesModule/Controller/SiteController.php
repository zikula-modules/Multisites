<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\ExtensionsModule\Helper\ExtensionHelper;
use Zikula\MultisitesModule\Controller\Base\AbstractSiteController;
use Zikula\MultisitesModule\DatabaseInfo;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Site controller class providing navigation and interaction functionality.
 */
class SiteController extends AbstractSiteController
{
    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminViewAction(
        Request $request,
        $sort,
        $sortdir,
        $pos,
        $num
    ) {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function viewAction(
        Request $request,
        $sort,
        $sortdir,
        $pos,
        $num
    ) {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/site/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminEditAction(
        Request $request
    ) {
        return $this->editInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/site/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     */
    public function editAction(
        Request $request
    ) {
        return $this->editInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/site/delete/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminDeleteAction(Request $request, $id)
    {
        return $this->deleteInternal($request, $id, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/site/delete/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->deleteInternal($request, $id, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/manageExtensions",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminManageExtensionsAction(Request $request)
    {
        return $this->manageExtensionsInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/manageExtensions",
     *        methods = {"GET", "POST"}
     * )
     */
    public function manageExtensionsAction(Request $request)
    {
        return $this->manageExtensionsInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/manageThemes",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminManageThemesAction(Request $request)
    {
        return $this->manageThemesInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/manageThemes",
     *        methods = {"GET", "POST"}
     * )
     */
    public function manageThemesAction(Request $request)
    {
        return $this->manageThemesInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/setThemeAsDefault",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminSetThemeAsDefaultAction(Request $request)
    {
        return $this->setThemeAsDefaultInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/setThemeAsDefault",
     *        methods = {"GET", "POST"}
     * )
     */
    public function setThemeAsDefaultAction(Request $request)
    {
        return $this->setThemeAsDefaultInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/viewTools",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminViewToolsAction(Request $request)
    {
        return $this->viewToolsInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/viewTools",
     *        methods = {"GET", "POST"}
     * )
     */
    public function viewToolsAction(Request $request)
    {
        return $this->viewToolsInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/executeTool",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminExecuteToolAction(Request $request)
    {
        return $this->executeToolInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/executeTool",
     *        methods = {"GET", "POST"}
     * )
     */
    public function executeToolAction(Request $request)
    {
        return $this->executeToolInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/sites/exportDatabaseAsTemplate",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminExportDatabaseAsTemplateAction(Request $request)
    {
        return $this->exportDatabaseAsTemplateInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/sites/exportDatabaseAsTemplate",
     *        methods = {"GET", "POST"}
     * )
     */
    public function exportDatabaseAsTemplateAction(Request $request)
    {
        return $this->exportDatabaseAsTemplateInternal($request, false);
    }

    /**
     * @inheritDoc
     * @Route("/admin/sites/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntriesAction(
        Request $request
    ) {
        return $this->handleSelectedEntriesActionInternal($request, true);
    }

    /**
     * @inheritDoc
     * @Route("/sites/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntriesAction(
        Request $request
    ) {
        return $this->handleSelectedEntriesActionInternal($request, false);
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
        $container = $this->get('service_container');

        // Initialize and prepare the action
        $originalCacheDirectory = '';
        $needsDatabaseAccess = false;
        switch ($action) {
            case 'cleartemplates':
                // Backup cache folder setting of main site
                $originalCacheDirectory = $container->getParameter('kernel.cache_dir');
                break;
            case 'anotherOne':
                // This one needs the database
                $needsDatabaseAccess = true;
                break;
        }

        $zikula = $this->get('zikula');
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('site');
        $systemHelper = $needsDatabaseAccess ? $this->get('zikula_multisites_module.system_helper') : null;
        $entityDisplayHelper = $this->get('zikula_multisites_module.entity_display_helper');
        $cacheClearer = $this->get('zikula.cache_clearer');

        // process each item
        foreach ($items as $itemid) {
            // Reboot the core for this site
            //$zikula->reboot();
            //$zikula->init();

            // Get site information
            $entity = $repository->selectById($itemid, false);
            if (null === $entity || !is_object($entity)) {
                $this->addFlash('error', $this->__f('Error! No site with id %s could be found.', ['%s' => $itemid]));
                continue;
            }

            if ($needsDatabaseAccess) {
                $connect = $systemHelper->connectToExternalDatabase(new DatabaseInfo($entity));
                if (!$connect) {
                    $this->addFlash('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $entity['databaseName']]));
                    continue;
                }
            }

            // Execute action for current site
            switch ($action) {
                case 'cleartemplates':
                    // Set cache folder for this site instance
                    $container->setParameter('kernel.cache_dir', $originalCacheDirectory . '/' . $entity->getSiteAlias());

                    $cacheClearer->clear('twig');
                    $cacheClearer->clear('assets');

                    $this->addFlash('error', $this->__f('Done! Cleared all cache and compile directories for site %s.', ['%s' => $entityDisplayHelper->getFormattedTitle($entity)]));
                    break;
            }
        }

        // Cleanup
        $needsDatabaseAccess = false;
        switch ($action) {
            case 'cleartemplates':
                // Restore cache folder setting for main site
                $container->setParameter('kernel.cache_dir', $originalCacheDirectory);
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
        $allModules = $this->get('kernel')->getModules();
        ksort($allModules);

        // get all the modules available in the site
        $siteModules = $extensionHelper->getAllModulesFromSiteDb($site);

        // create output array
        $modules = [];

        foreach ($allModules as $mod) {
            if (ExtensionHelper::TYPE_SYSTEM == $mod['type']) {
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

        return $viewHelper->processTemplate('site', 'manageExtensions', $templateParameters);
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
        $allThemes = $this->get('kernel')->getThemes();
        ksort($allThemes);

        // get all the themes available in the site
        $siteThemes = $extensionHelper->getAllThemesFromSiteDb($site);

        // get information about which theme is the current default one
        $defaultTheme = $extensionHelper->getSiteDefaultTheme($site);

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

        return $viewHelper->processTemplate('site', 'manageThemes', $templateParameters);
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

        return $viewHelper->processTemplate('site', 'viewTools', $templateParameters);
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
        $tempPrefix = $this->get('zikula_users_module.current_user')->get('uid'); // to avoid a race condition
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
        $entityDisplayHelper = $this->get('zikula_multisites_module.entity_display_helper');
        $sqlFileName = $controllerHelper->formatPermalink($entityDisplayHelper->getFormattedTitle($site))
                   . '-dump-' . date('Ymd_His', time()) . '.sql';

        // send output file to the browser
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $sqlFileName . '"');
        header('Content-Length: ' . filesize($tempFilePath));

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
        if (!$this->get('zikula_multisites_module.permission_helper')->hasComponentPermission('site', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // retrieve identifier of the object we wish to view
        $id = $request->query->getInt('id', 0);
        if (!$id) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }

        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('site');
        $entity = $repository->selectById($id);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }

        return $entity;
    }
}