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

namespace Zikula\MultisitesModule\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JCSSUtil;
use ModUtil;
use System;
use UserUtil;
use ZLanguage;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;

/**
 * Admin controller class.
 */
class AdminController extends AbstractController
{

    /**
     * This is the default action handling the mainnull area called without defining arguments.
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
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'site');
        
        $permLevel = ACCESS_ADMIN;
        if (!$this->hasPermission($this->name . '::', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        
        // redirect to view action
        $routeArea = 'admin';
        
        return $this->redirectToRoute('zikulamultisitesmodule_' . strtolower($objectType) . '_' . $routeArea . 'view');
    }

    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function manageUpdatesAction(Request $request)
    {
        $controllerHelper = $this->get('zikulamultisitesmodule.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'site');
        $utilArgs = ['controller' => 'admin', 'action' => 'manageUpdates'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        $permLevel = ACCESS_ADMIN;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Admin/manageUpdates.html.twig');
    }

    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function multiplyQueriesAction(Request $request)
    {
        $controllerHelper = $this->get('zikulamultisitesmodule.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'site');
        $utilArgs = ['controller' => 'admin', 'action' => 'multiplyQueries'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        $permLevel = ACCESS_ADMIN;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        // return template
        return $this->render('@ZikulaMultisitesModule/Admin/multiplyQueries.html.twig');
    }


    /**
     * This method takes care of the application configuration.
     *
     * @return string Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function configAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        $form = $this->createForm('Zikula\MultisitesModule\Form\AppSettingsType');
        
        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('save')->isClicked()) {
                $this->setVars($form->getData());
        
                $this->addFlash(\Zikula_Session::MESSAGE_STATUS, $this->__('Done! Module configuration updated.'));
                $this->get('logger')->notice('{app}: User {user} updated the configuration.', ['app' => 'ZikulaMultisitesModule', 'user' => \UserUtil::getVar('uname')]);
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash(\Zikula_Session::MESSAGE_STATUS, $this->__('Operation cancelled.'));
            }
        
            // redirect to config page again (to show with GET request)
            return $this->redirectToRoute('zikulamultisitesmodule_admin_config');
        }
        
        $templateParameters = [
            'form' => $form->createView()
        ];
        
        // render the config form
        return $this->render('@ZikulaMultisitesModule/Admin/config.html.twig', $templateParameters);
    }
}
