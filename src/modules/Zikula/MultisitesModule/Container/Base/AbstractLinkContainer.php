<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.1 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Container\Base;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\PermissionApi;
use Zikula\UsersModule\Api\CurrentUserApi;
use Zikula\MultisitesModule\Helper\ControllerHelper;

/**
 * This is the link container service implementation class.
 */
abstract class AbstractLinkContainer implements LinkContainerInterface
{
    use TranslatorTrait;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var PermissionApi
     */
    protected $permissionApi;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var CurrentUserApi
     */
    private $currentUserApi;

    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param TranslatorInterface $translator       Translator service instance
     * @param Routerinterface     $router           Router service instance
     * @param PermissionApi       $permissionApi    PermissionApi service instance
     * @param ControllerHelper    $controllerHelper ControllerHelper service instance
     * @param CurrentUserApi      $currentUserApi   CurrentUserApi service instance
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, PermissionApi $permissionApi, ControllerHelper $controllerHelper, CurrentUserApi $currentUserApi)
    {
        $this->setTranslator($translator);
        $this->router = $router;
        $this->permissionApi = $permissionApi;
        $this->controllerHelper = $controllerHelper;
        $this->currentUserApi = $currentUserApi;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns available header links.
     *
     * @param string $type The type to collect links for
     *
     * @return array Array of header links
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $utilArgs = ['api' => 'linkContainer', 'action' => 'getLinks'];
        $allowedObjectTypes = $this->controllerHelper->getObjectTypes('api', $utilArgs);

        $permLevel = LinkContainerInterface::TYPE_ADMIN == $type ? ACCESS_ADMIN : ACCESS_READ;

        // Create an array of links to return
        $links = [];

        
        if (LinkContainerInterface::TYPE_ADMIN == $type) {
            if ($this->permissionApi->hasPermission($this->getBundleName() . '::', '::', ACCESS_READ)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_user_index'),
                    'text' => $this->__('Frontend'),
                    'title' => $this->__('Switch to user area.'),
                    'icon' => 'home'
                ];
            }
            
            if (in_array('site', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':Site:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_adminview'),
                    'text' => $this->__('Sites'),
                    'title' => $this->__('Site list')
                ];
            }
            if (in_array('template', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':Template:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_template_adminview'),
                    'text' => $this->__('Templates'),
                    'title' => $this->__('Template list')
                ];
            }
            if (in_array('siteExtension', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':SiteExtension:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_siteextension_adminview'),
                    'text' => $this->__('Site extensions'),
                    'title' => $this->__('Site extension list')
                ];
            }
            if (in_array('project', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':Project:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_project_adminview'),
                    'text' => $this->__('Projects'),
                    'title' => $this->__('Project list')
                ];
            }
            if ($this->permissionApi->hasPermission($this->getBundleName() . '::', '::', ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_config_config'),
                    'text' => $this->__('Configuration'),
                    'title' => $this->__('Manage settings for this application'),
                    'icon' => 'wrench'
                ];
            }
        }
        if (LinkContainerInterface::TYPE_USER == $type) {
            if ($this->permissionApi->hasPermission($this->getBundleName() . '::', '::', ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_admin_index'),
                    'text' => $this->__('Backend'),
                    'title' => $this->__('Switch to administration area.'),
                    'icon' => 'wrench'
                ];
            }
            
            if (in_array('site', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':Site:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_view'),
                    'text' => $this->__('Sites'),
                    'title' => $this->__('Site list')
                ];
            }
            if (in_array('template', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':Template:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_template_view'),
                    'text' => $this->__('Templates'),
                    'title' => $this->__('Template list')
                ];
            }
            if (in_array('siteExtension', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':SiteExtension:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_siteextension_view'),
                    'text' => $this->__('Site extensions'),
                    'title' => $this->__('Site extension list')
                ];
            }
            if (in_array('project', $allowedObjectTypes)
                && $this->permissionApi->hasPermission($this->getBundleName() . ':Project:', '::', $permLevel)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_project_view'),
                    'text' => $this->__('Projects'),
                    'title' => $this->__('Project list')
                ];
            }
            if ($this->permissionApi->hasPermission($this->getBundleName() . '::', '::', ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_config_config'),
                    'text' => $this->__('Configuration'),
                    'title' => $this->__('Manage settings for this application'),
                    'icon' => 'wrench'
                ];
            }
        }

        return $links;
    }

    /**
     * Returns the name of the providing bundle.
     *
     * @return string The bundle name
     */
    public function getBundleName()
    {
        return 'ZikulaMultisitesModule';
    }
}
