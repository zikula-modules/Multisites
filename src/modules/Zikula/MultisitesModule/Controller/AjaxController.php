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

use Zikula\MultisitesModule\Controller\Base\AjaxController as BaseAjaxController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Response\Ajax\AjaxResponse;
use Zikula\Core\Response\Ajax\BadDataResponse;
use Zikula\Core\Response\Ajax\FatalResponse;
use Zikula\Core\Response\Ajax\NotFoundResponse;

/**
 * Ajax controller class providing navigation and interaction functionality.
 *
 * @Route("/ajax")
 */
class AjaxController extends BaseAjaxController
{
    /**
     * This is the default action handling the mainnull area called without defining arguments.
     *
     * @Route("/ajax",
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance
     * @param string  $ot           Treated object type.
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
     * @param string $ot       Treated object type.
     * @param string $fragment The fragment of the entered item name.
     * @param string $exclude  Comma separated list with ids of other items (to be excluded from search).
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
     * @Route("/toggleFlag", options={"expose"=true})
     * @Method("POST")
     *
     * @param string $ot    Treated object type.
     * @param string $field The field to be toggled.
     * @param int    $id    Identifier of treated entity.
     *
     * @return AjaxResponse
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
     * @Route("/getProjectTemplates", options={"expose"=true})
     * @Method("POST")
     *
     * @param int $id Identifier of treated project entity.
     *
     * @return AjaxResponse
     */
    public function getProjectTemplatesAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $id = (int) $postData->getInt('id', 0);
        if ($id == 0) {
            return new BadDataResponse($this->__('Error: invalid input.'));
        }

        // select project entity
        $project = ModUtil::apiFunc($this->name, 'selection', 'getEntity', ['ot' => 'project', 'id' => $id]);
        if (null == $project) {
            return new NotFoundResponse($this->__('No such item.'));
        }

        $result = [];

        foreach ($project['templates'] as $template) {
            $result[] = [
                'id' => $template->getId(),
                'name' => $template->getTitleFromDisplayPattern(),
                'parameters' => $template->getParameters()
            ];
        }

        // return the response
        return new AjaxResponse(['templates' => $result]);
    }

    /**
     * Modifies the state of a module in a site database.
     *
     * @Route("/modifyModuleActivation", options={"expose"=true})
     * @Method("POST")
     *
     * @param int    id         Identifier of treated site entity.
     * @param string moduleName Name of module to change.
     * @param int    newState   New module state.
     *
     * @return AjaxResponse
     */
    public function modifyModuleActivationAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $moduleName = $postData->getAlnum('moduleName', '');
        if ($moduleName == '') {
            return new BadDataResponse($this->__('No module name received.'));
        }

        $newState = $postData->getInt('newState', -1);
        if ($newState == -1) {
            return new BadDataResponse($this->__('No new state received.'));
        }

        $id = $postData->getInt('id', 0);
        if ($id == 0) {
            return new BadDataResponse($this->__('Error: invalid input.'));
        }

        // select site entity
        $site = ModUtil::apiFunc($this->name, 'selection', 'getEntity', ['ot' => 'site', 'id' => $id]);
        if (null == $site) {
            return new NotFoundResponse($this->__('No such item.'));
        }

        // helper for extension-related operations
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // apply the state change
        if (!$extensionHelper->modifyModuleActivation($site, ['moduleName' => $moduleName, 'newState' => $newState])) {
            return new FatalResponse($this->__('Error changing module state.'));
        }

        // select site modules
        $siteModules = $extensionHelper->getAllModulesFromSiteDb($site);

        // retrieve updated action icons
        $available = array_key_exists($moduleName, $siteModules);
        $icons = $extensionHelper->getActionIconsForSiteModule($this->view, $site, [
                'name' => $moduleName,
                'available' => $available,
                'siteModules' => $siteModules]);

        // return the response
        return new AjaxResponse(['content' => $icons,
                                 'moduleName' => $moduleName]);
    }

    /**
     * Creates, changes or deletes a module state in a site database depending on the module initial state.
     *
     * @Route("/allowModule", options={"expose"=true})
     * @Method("POST")
     *
     * @param int    id         Identifier of treated site entity.
     * @param string moduleName Name of module to change.
     *
     * @return AjaxResponse
     */
    public function allowModuleAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $moduleName = $postData->getAlnum('moduleName', '');
        if ($moduleName == '') {
            return new BadDataResponse($this->__('No module name received.'));
        }

        $id = $postData->getInt('id', 0);
        if ($id == 0) {
            return new BadDataResponse($this->__('Error: invalid input.'));
        }

        // select site entity
        $site = ModUtil::apiFunc($this->name, 'selection', 'getEntity', ['ot' => 'site', 'id' => $id]);
        if (null == $site) {
            return new NotFoundResponse($this->__('No such item.'));
        }

        // helper for extension-related operations
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // select site module
        $module = $extensionHelper->getModuleFromSiteDb($site, $moduleName);

        // apply the state change
        if ($module['state'] == ModUtil::STATE_NOTALLOWED) {
            // set the module as deactivated
            if (!$extensionHelper->modifyModuleActivation($site, ['moduleName' => $moduleName, 'newState' => ModUtil::STATE_INACTIVE])) {
                return new FatalResponse($this->__('Error changing module state.'));
            }
        } elseif (in_array($module['state'], [ModUtil::STATE_INACTIVE, ModUtil::STATE_ACTIVE])) {
            // set the module as not allowed
            if (!$extensionHelper->modifyModuleActivation($site, ['moduleName' => $moduleName, 'newState' => ModUtil::STATE_NOTALLOWED])) {
                return new FatalResponse($this->__('Error changing module state.'));
            }
        } elseif ($module['state'] == '') {
            // create module
            if (!$extensionHelper->createSiteModule($site, $moduleName)) {
                return new FatalResponse($this->__('Error creating module.'));
            }
        } else {
            // get site module
            if (!$extensionHelper->deleteSiteModule($site, $moduleName)) {
                return new FatalResponse($this->__('Error deleting module.'));
            }
        }

        // select site modules
        $siteModules = $extensionHelper->getAllModulesFromSiteDb($site);

        // retrieve updated action icons
        $available = array_key_exists($moduleName, $siteModules);
        $icons = $extensionHelper->getActionIconsForSiteModule(null, $site, [
                'name' => $moduleName,
                'available' => $available,
                'siteModules' => $siteModules]);

        // return the response
        return new AjaxResponse(['content' => $icons,
                                 'moduleName' => $moduleName]);
    }

    /**
     * Creates or deletes a theme state in a site database depending on the theme initial state.
     *
     * @Route("/allowTheme", options={"expose"=true})
     * @Method("POST")
     *
     * @param int    id        Identifier of treated site entity.
     * @param string themeName Name of theme to change.
     *
     * @return AjaxResponse
     */
    public function allowThemeAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $postData = $request->request;

        $themeName = $postData->getAlnum('themeName', '');
        if ($themeName == '') {
            return new BadDataResponse($this->__('No theme name received.'));
        }

        $id = $postData->getInt('id', 0);
        if ($id == 0) {
            return new BadDataResponse($this->__('Error: invalid input.'));
        }

        // select site entity
        $site = ModUtil::apiFunc($this->name, 'selection', 'getEntity', ['ot' => 'site', 'id' => $id]);
        if (null == $site) {
            return new NotFoundResponse($this->__('No such item.'));
        }

        // helper for extension-related operations
        $extensionHelper = $this->get('zikula_multisites_module.siteextension_helper');

        // select site theme
        $theme = $extensionHelper->getThemeFromSiteDb($site, $themeName);

        // apply the state change
        if ($theme['name'] == '') {
            // create theme
            if (!$extensionHelper->createSiteTheme($site, $themeName)) {
                return new FatalResponse($this->__('Error creating theme.'));
            }
        } else {
            // delete theme
            if (!$extensionHelper->deleteSiteTheme($site, $themeName)) {
                return new FatalResponse($this->__('Error deleting theme.'));
            }
        }

        // select site themes
        $siteThemes = $extensionHelper->getAllThemesFromSiteDb($site);

        // retrieve updated action icons
        $available = array_key_exists($themeName, $siteThemes);
        $icons = $extensionHelper->getActionIconsForSiteTheme(null, $site, [
                'name' => $themeName,
                'available' => $available,
                'siteThemes' => $siteThemes]);

        // return the response
        return new AjaxResponse(['content' => $icons,
                                 'themeName' => $themeName]);
    }
}
