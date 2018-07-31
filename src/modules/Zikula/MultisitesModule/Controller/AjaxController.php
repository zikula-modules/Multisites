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

use Zikula\MultisitesModule\Controller\Base\AbstractAjaxController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\ExtensionsModule\Constant as ExtensionsConstant;

/**
 * Ajax controller class providing navigation and interaction functionality.
 *
 * @Route("/ajax")
 */
class AjaxController extends AbstractAjaxController
{
    /**
     * This is the default action handling the main area called without defining arguments.
     *
     * @Route("/ajax",
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    
    /**
     * Searches for entities for auto completion usage.
     *
     * @Route("/getItemListAutoCompletion", options={"expose"=true})
    
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function getItemListAutoCompletionAction(Request $request)
    {
        return parent::getItemListAutoCompletionAction($request);
    }
    
    /**
     * Changes a given flag (boolean field) by switching between true and false.
     *
     * @Route("/toggleFlag", methods = {"POST"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function toggleFlagAction(Request $request)
    {
        return parent::toggleFlagAction($request);
    }

    /**
     * Returns information about templates for a given project.
     *
     * @Route("/getProjectTemplates", methods = {"GET"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function getProjectTemplatesAction(Request $request)
    {
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $id = $request->query->getInt('id', 0);
        if ($id == 0) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        // select project entity
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('project');
        $project = $repository->selectById($id);
        if (null === $project) {
            return $this->json($this->__('No such item.'), JsonResponse::HTTP_NOT_FOUND);
        }

        $result = [];
        $entityDisplayHelper = $this->get('zikula_multisites_module.entity_display_helper');

        foreach ($project['templates'] as $template) {
            $result[] = [
                'id' => $template->getId(),
                'name' => $entityDisplayHelper->getFormattedTitle($template),
                'parameters' => $template->getParameters()
            ];
        }

        // return the response
        return $this->json([
            'templates' => $result
        ]);
    }

    /**
     * Modifies the state of a module in a site database.
     *
     * @Route("/modifyModuleActivation", methods = {"POST"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function modifyModuleActivationAction(Request $request)
    {
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $moduleName = $postData->getAlnum('moduleName', '');
        if ($moduleName == '') {
            return $this->json($this->__('No module name received.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $newState = $postData->getInt('newState', -1);
        if ($newState == -1) {
            return $this->json($this->__('No new state received.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $id = $postData->getInt('id', 0);
        if ($id == 0) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        // select site entity
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('site');
        $site = $repository->selectById($id);
        if (null === $site) {
            return $this->json($this->__('No such item.'), JsonResponse::HTTP_NOT_FOUND);
        }

        // helper for extension-related operations
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // apply the state change
        if (!$extensionHelper->modifyModuleActivation($site, $moduleName, $newState)) {
            return $this->json($this->__('Error changing module state.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        // select site modules
        $siteModules = $extensionHelper->getAllModulesFromSiteDb($site);

        // retrieve updated action icons
        $available = array_key_exists($moduleName, $siteModules);
        $icons = $extensionHelper->getActionIconsForSiteModule($site, [
            'name' => $moduleName,
            'available' => $available,
            'siteModules' => $siteModules
        ]);

        // return the response
        return $this->json([
            'content' => $icons,
            'moduleName' => $moduleName
        ]);
    }

    /**
     * Creates, changes or deletes a module state in a site database depending on the module initial state.
     *
     * @Route("/allowModule", methods = {"POST"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function allowModuleAction(Request $request)
    {
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $moduleName = $postData->getAlnum('moduleName', '');
        if ($moduleName == '') {
            return $this->json($this->__('No module name received.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $id = $postData->getInt('id', 0);
        if ($id == 0) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        // select site entity
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('site');
        $site = $repository->selectById($id);
        if (null === $site) {
            return $this->json($this->__('No such item.'), JsonResponse::HTTP_NOT_FOUND);
        }

        // helper for extension-related operations
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // select site module
        $module = $extensionHelper->getModuleFromSiteDb($site, $moduleName);

        // apply the state change
        if ($module['state'] == ExtensionsConstant::STATE_NOTALLOWED) {
            // set the module as deactivated
            if (!$extensionHelper->modifyModuleActivation($site, $moduleName, ExtensionsConstant::STATE_INACTIVE)) {
                return $this->json($this->__('Error changing module state.'), JsonResponse::HTTP_BAD_REQUEST);
            }
        } elseif (in_array($module['state'], [ExtensionsConstant::STATE_INACTIVE, ExtensionsConstant::STATE_ACTIVE])) {
            // set the module as not allowed
            if (!$extensionHelper->modifyModuleActivation($site, $moduleName, ExtensionsConstant::STATE_NOTALLOWED)) {
                return $this->json($this->__('Error changing module state.'), JsonResponse::HTTP_BAD_REQUEST);
            }
        } elseif ($module['state'] == '') {
            // create module
            if (!$extensionHelper->createSiteModule($site, $moduleName)) {
                return $this->json($this->__('Error creating module.'), JsonResponse::HTTP_BAD_REQUEST);
            }
        } else {
            // get site module
            if (!$extensionHelper->deleteSiteModule($site, $moduleName)) {
                return $this->json($this->__('Error deleting module.'), JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // select site modules
        $siteModules = $extensionHelper->getAllModulesFromSiteDb($site);

        // retrieve updated action icons
        $available = array_key_exists($moduleName, $siteModules);
        $icons = $extensionHelper->getActionIconsForSiteModule($site, [
            'name' => $moduleName,
            'available' => $available,
            'siteModules' => $siteModules
        ]);

        // return the response
        return $this->json([
            'content' => $icons,
            'moduleName' => $moduleName
        ]);
    }

    /**
     * Creates or deletes a theme state in a site database depending on the theme initial state.
     *
     * @Route("/allowTheme", methods = {"POST"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function allowThemeAction(Request $request)
    {
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $themeName = $postData->getAlnum('themeName', '');
        if ($themeName == '') {
            return $this->json($this->__('No theme name received.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $id = $postData->getInt('id', 0);
        if ($id == 0) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }

        // select site entity
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('site');
        $site = $repository->selectById($id);
        if (null === $site) {
            return $this->json($this->__('No such item.'), JsonResponse::HTTP_NOT_FOUND);
        }

        // helper for extension-related operations
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // select site theme
        $theme = $extensionHelper->getThemeFromSiteDb($site, $themeName);

        // apply the state change
        if ($theme['name'] == '') {
            // create theme
            if (!$extensionHelper->createSiteTheme($site, $themeName)) {
                return $this->json($this->__('Error creating theme.'), JsonResponse::HTTP_BAD_REQUEST);
            }
        } else {
            // delete theme
            if (!$extensionHelper->deleteSiteTheme($site, $themeName)) {
                return $this->json($this->__('Error deleting theme.'), JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // select site themes
        $siteThemes = $extensionHelper->getAllThemesFromSiteDb($site);

        // retrieve updated action icons
        $available = array_key_exists($themeName, $siteThemes);
        $icons = $extensionHelper->getActionIconsForSiteTheme($site, [
            'name' => $themeName,
            'available' => $available,
            'siteThemes' => $siteThemes
        ]);

        // return the response
        return $this->json([
            'content' => $icons,
            'themeName' => $themeName
        ]);
    }
}
