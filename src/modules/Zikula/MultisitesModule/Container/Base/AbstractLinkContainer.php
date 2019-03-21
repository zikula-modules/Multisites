<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Container\Base;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\MultisitesModule\Helper\ControllerHelper;
use Zikula\MultisitesModule\Helper\PermissionHelper;

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
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * LinkContainer constructor.
     *
     * @param TranslatorInterface $translator
     * @param Routerinterface $router
     * @param ControllerHelper $controllerHelper
     * @param PermissionHelper $permissionHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->setTranslator($translator);
        $this->router = $router;
        $this->controllerHelper = $controllerHelper;
        $this->permissionHelper = $permissionHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns available header links.
     *
     * @param string $type The type to collect links for
     *
     * @return array List of header links
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $contextArgs = ['api' => 'linkContainer', 'action' => 'getLinks'];
        $allowedObjectTypes = $this->controllerHelper->getObjectTypes('api', $contextArgs);

        $permLevel = LinkContainerInterface::TYPE_ADMIN == $type ? ACCESS_ADMIN : ACCESS_READ;

        // Create an array of links to return
        $links = [];

        if (LinkContainerInterface::TYPE_ACCOUNT == $type) {

            return $links;
        }

        $routeArea = LinkContainerInterface::TYPE_ADMIN == $type ? 'admin' : '';
        if (LinkContainerInterface::TYPE_ADMIN == $type) {
            if ($this->permissionHelper->hasPermission(ACCESS_READ)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_view'),
                    'text' => $this->__('Frontend', 'zikulamultisitesmodule'),
                    'title' => $this->__('Switch to user area.', 'zikulamultisitesmodule'),
                    'icon' => 'home'
                ];
            }
        } else {
            if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_adminview'),
                    'text' => $this->__('Backend', 'zikulamultisitesmodule'),
                    'title' => $this->__('Switch to administration area.', 'zikulamultisitesmodule'),
                    'icon' => 'wrench'
                ];
            }
        }
        
        if (in_array('site', $allowedObjectTypes)
            && $this->permissionHelper->hasComponentPermission('site', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_site_' . $routeArea . 'view'),
                'text' => $this->__('Sites', 'zikulamultisitesmodule'),
                'title' => $this->__('Sites list', 'zikulamultisitesmodule')
            ];
        }
        if (in_array('template', $allowedObjectTypes)
            && $this->permissionHelper->hasComponentPermission('template', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_template_' . $routeArea . 'view'),
                'text' => $this->__('Templates', 'zikulamultisitesmodule'),
                'title' => $this->__('Templates list', 'zikulamultisitesmodule')
            ];
        }
        if (in_array('project', $allowedObjectTypes)
            && $this->permissionHelper->hasComponentPermission('project', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_project_' . $routeArea . 'view'),
                'text' => $this->__('Projects', 'zikulamultisitesmodule'),
                'title' => $this->__('Projects list', 'zikulamultisitesmodule')
            ];
        }
        if ($routeArea == 'admin' && $this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_config_config'),
                'text' => $this->__('Settings', 'zikulamultisitesmodule'),
                'title' => $this->__('Manage settings for this application', 'zikulamultisitesmodule'),
                'icon' => 'wrench'
            ];
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
