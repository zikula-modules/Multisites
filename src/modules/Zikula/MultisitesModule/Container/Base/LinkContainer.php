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

namespace Zikula\MultisitesModule\Container\Base;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\PermissionApi;
use Zikula\UsersModule\Api\CurrentUserApi;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\MultisitesModule\Entity\SiteExtensionEntity;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Helper\ControllerHelper;

/**
 * This is the link container service implementation class.
 */
class LinkContainer implements LinkContainerInterface
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

        return $links;
    }

    /**
     * Returns action links for a given entity.
     *
     * @param EntityAccess $entity  The entity
     * @param string       $area    The context area name (e.g. admin or nothing for user)
     * @param string       $context The context page name (e.g. view, display, edit, delete)
     *
     * @return array Array of action links
     */
    public function getActionLinks(EntityAccess $entity, $area = '', $context = 'view')
    {
        // Create an array of links to return
        $links = [];

        
        $currentLegacyControllerType = $area != '' ? $area : 'user';
        $currentFunc = $context;
        
        if ($entity instanceof SiteEntity) {
            $component = 'ZikulaMultisitesModule:Site:';
            $instance = $this->id . '::';
        
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, ['index', 'view'])) {
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_site_adminedit', ['id' => $this['id']]),
                        'icon' => 'pencil-square-o',
                        'linkTitle' => $this->__('Edit'),
                        'linkText' => $this->__('Edit')
                    ];
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_site_adminedit', ['astemplate' => $this['id']]),
                        'icon' => 'files-o',
                        'linkTitle' => $this->__('Reuse for new item'),
                        'linkText' => $this->__('Reuse')
                    ];
                }
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_DELETE)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_site_admindelete', ['id' => $this['id']]),
                        'icon' => 'trash-o',
                        'linkTitle' => $this->__('Delete'),
                        'linkText' => $this->__('Delete')
                    ];
                }
            }
            
            // more actions for adding new related items
            $authAdmin = $this->permissionApi->hasPermission($component, $instance, ACCESS_ADMIN);
            
            $uid = $this->currentUserApi->get('uid');
            if ($authAdmin || (isset($uid) && isset($entity->createdUserId) && $entity->createdUserId == $uid)) {
            
                $urlArgs = ['site' => $this->id];
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewSite';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplaySite';
                }
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_siteextension_adminedit', $urlArgs),
                    'icon' => 'plus',
                    'linkTitle' => $this->__('Create site extension'),
                    'linkText' => $this->__('Create site extension')
                ];
            }
        }
        }
        if ($entity instanceof TemplateEntity) {
            $component = 'ZikulaMultisitesModule:Template:';
            $instance = $this->id . '::';
        
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, ['index', 'view'])) {
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_template_adminedit', ['id' => $this['id']]),
                        'icon' => 'pencil-square-o',
                        'linkTitle' => $this->__('Edit'),
                        'linkText' => $this->__('Edit')
                    ];
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_template_adminedit', ['astemplate' => $this['id']]),
                        'icon' => 'files-o',
                        'linkTitle' => $this->__('Reuse for new item'),
                        'linkText' => $this->__('Reuse')
                    ];
                }
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_DELETE)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_template_admindelete', ['id' => $this['id']]),
                        'icon' => 'trash-o',
                        'linkTitle' => $this->__('Delete'),
                        'linkText' => $this->__('Delete')
                    ];
                }
            }
            
            // more actions for adding new related items
            $authAdmin = $this->permissionApi->hasPermission($component, $instance, ACCESS_ADMIN);
            
            $uid = $this->currentUserApi->get('uid');
            if ($authAdmin || (isset($uid) && isset($entity->createdUserId) && $entity->createdUserId == $uid)) {
            
                $urlArgs = ['template' => $this->id];
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewTemplate';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplayTemplate';
                }
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_adminedit', $urlArgs),
                    'icon' => 'plus',
                    'linkTitle' => $this->__('Create site'),
                    'linkText' => $this->__('Create site')
                ];
            
                $urlArgs = ['templates' => $this->id];
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewTemplate';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplayTemplate';
                }
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_project_adminedit', $urlArgs),
                    'icon' => 'plus',
                    'linkTitle' => $this->__('Create project'),
                    'linkText' => $this->__('Create project')
                ];
            }
        }
        }
        if ($entity instanceof SiteExtensionEntity) {
            $component = 'ZikulaMultisitesModule:SiteExtension:';
            $instance = $this->id . '::';
        
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, ['index', 'view'])) {
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_siteextension_adminedit', ['id' => $this['id']]),
                        'icon' => 'pencil-square-o',
                        'linkTitle' => $this->__('Edit'),
                        'linkText' => $this->__('Edit')
                    ];
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_siteextension_adminedit', ['astemplate' => $this['id']]),
                        'icon' => 'files-o',
                        'linkTitle' => $this->__('Reuse for new item'),
                        'linkText' => $this->__('Reuse')
                    ];
                }
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_DELETE)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_siteextension_admindelete', ['id' => $this['id']]),
                        'icon' => 'trash-o',
                        'linkTitle' => $this->__('Delete'),
                        'linkText' => $this->__('Delete')
                    ];
                }
            }
        }
        }
        if ($entity instanceof ProjectEntity) {
            $component = 'ZikulaMultisitesModule:Project:';
            $instance = $this->id . '::';
        
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, ['index', 'view'])) {
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_project_adminedit', ['id' => $this['id']]),
                        'icon' => 'pencil-square-o',
                        'linkTitle' => $this->__('Edit'),
                        'linkText' => $this->__('Edit')
                    ];
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_project_adminedit', ['astemplate' => $this['id']]),
                        'icon' => 'files-o',
                        'linkTitle' => $this->__('Reuse for new item'),
                        'linkText' => $this->__('Reuse')
                    ];
                }
                if ($this->permissionApi->hasPermission($component, $instance, ACCESS_DELETE)) {
                    $links[] = [
                        'url' => $this->router->generate('zikulamultisitesmodule_project_admindelete', ['id' => $this['id']]),
                        'icon' => 'trash-o',
                        'linkTitle' => $this->__('Delete'),
                        'linkText' => $this->__('Delete')
                    ];
                }
            }
            
            // more actions for adding new related items
            $authAdmin = $this->permissionApi->hasPermission($component, $instance, ACCESS_ADMIN);
            
            $uid = $this->currentUserApi->get('uid');
            if ($authAdmin || (isset($uid) && isset($entity->createdUserId) && $entity->createdUserId == $uid)) {
            
                $urlArgs = ['project' => $this->id];
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewProject';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplayProject';
                }
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_adminedit', $urlArgs),
                    'icon' => 'plus',
                    'linkTitle' => $this->__('Create site'),
                    'linkText' => $this->__('Create site')
                ];
            
                $urlArgs = ['projects' => $this->id];
                if ($currentFunc == 'view') {
                    $urlArgs['returnTo'] = 'adminViewProject';
                } elseif ($currentFunc == 'display') {
                    $urlArgs['returnTo'] = 'adminDisplayProject';
                }
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_template_adminedit', $urlArgs),
                    'icon' => 'plus',
                    'linkTitle' => $this->__('Create template'),
                    'linkText' => $this->__('Create template')
                ];
            }
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
