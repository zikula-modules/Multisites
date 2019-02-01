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

namespace Zikula\MultisitesModule\Menu\Base;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\MultisitesEvents;
use Zikula\MultisitesModule\Event\ConfigureItemActionsMenuEvent;
use Zikula\MultisitesModule\Helper\PermissionHelper;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;

/**
 * Menu builder base class.
 */
class AbstractMenuBuilder
{
    use TranslatorTrait;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;

    /**
     * MenuBuilder constructor.
     *
     * @param TranslatorInterface      $translator          Translator service instance
     * @param FactoryInterface         $factory             Factory service instance
     * @param EventDispatcherInterface $eventDispatcher     EventDispatcher service instance
     * @param RequestStack             $requestStack        RequestStack service instance
     * @param PermissionHelper         $permissionHelper    PermissionHelper service instance
     * @param CurrentUserApiInterface  $currentUserApi      CurrentUserApi service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi
    ) {
        $this->setTranslator($translator);
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds the item actions menu.
     *
     * @param array $options List of additional options
     *
     * @return ItemInterface The assembled menu
     */
    public function createItemActionsMenu(array $options = [])
    {
        $menu = $this->factory->createItem('itemActions');
        if (!isset($options['entity']) || !isset($options['area']) || !isset($options['context'])) {
            return $menu;
        }

        $entity = $options['entity'];
        $routeArea = $options['area'];
        $context = $options['context'];
        $menu->setChildrenAttribute('class', 'list-inline item-actions');

        $this->eventDispatcher->dispatch(MultisitesEvents::MENU_ITEMACTIONS_PRE_CONFIGURE, new ConfigureItemActionsMenuEvent($this->factory, $menu, $options));

        $currentUserId = $this->currentUserApi->isLoggedIn() ? $this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        if ($entity instanceof SiteEntity) {
            $routePrefix = 'zikulamultisitesmodule_site_';
            $isOwner = $currentUserId > 0 && null !== $entity->getCreatedBy() && $currentUserId == $entity->getCreatedBy()->getUid();
        
            if ($this->permissionHelper->mayEdit($entity)) {
                $title = $this->__('Edit', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Edit this site', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-pencil-square-o');
                $title = $this->__('Reuse', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => ['astemplate' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Reuse for new site', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-files-o');
            }
            if ($this->permissionHelper->mayDelete($entity)) {
                $title = $this->__('Delete', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'delete',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Delete this site', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-trash-o');
            }
        }
        if ($entity instanceof TemplateEntity) {
            $routePrefix = 'zikulamultisitesmodule_template_';
            $isOwner = $currentUserId > 0 && null !== $entity->getCreatedBy() && $currentUserId == $entity->getCreatedBy()->getUid();
        
            if ($this->permissionHelper->mayEdit($entity)) {
                $title = $this->__('Edit', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Edit this template', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-pencil-square-o');
                $title = $this->__('Reuse', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => ['astemplate' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Reuse for new template', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-files-o');
            }
            
            // more actions for adding new related items
            
            if ($isOwner || $this->permissionHelper->hasComponentPermission('site', ACCESS_EDIT)) {
                $title = $this->__('Create sites', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => 'zikulamultisitesmodule_site_' . $routeArea . 'edit',
                    'routeParameters' => ['template' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $title);
                $menu[$title]->setAttribute('icon', 'fa fa-plus');
            }
            
            if ($isOwner || $this->permissionHelper->hasComponentPermission('project', ACCESS_EDIT)) {
                $title = $this->__('Create projects', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => 'zikulamultisitesmodule_project_' . $routeArea . 'edit',
                    'routeParameters' => ['templates' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $title);
                $menu[$title]->setAttribute('icon', 'fa fa-plus');
            }
        }
        if ($entity instanceof ProjectEntity) {
            $routePrefix = 'zikulamultisitesmodule_project_';
            $isOwner = $currentUserId > 0 && null !== $entity->getCreatedBy() && $currentUserId == $entity->getCreatedBy()->getUid();
        
            if ($this->permissionHelper->mayEdit($entity)) {
                $title = $this->__('Edit', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Edit this project', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-pencil-square-o');
                $title = $this->__('Reuse', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => ['astemplate' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Reuse for new project', 'zikulamultisitesmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-files-o');
            }
            
            // more actions for adding new related items
            
            if ($isOwner || $this->permissionHelper->hasComponentPermission('site', ACCESS_EDIT)) {
                $title = $this->__('Create sites', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => 'zikulamultisitesmodule_site_' . $routeArea . 'edit',
                    'routeParameters' => ['project' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $title);
                $menu[$title]->setAttribute('icon', 'fa fa-plus');
            }
            
            if ($isOwner || $this->permissionHelper->hasComponentPermission('template', ACCESS_EDIT)) {
                $title = $this->__('Create templates', 'zikulamultisitesmodule');
                $menu->addChild($title, [
                    'route' => 'zikulamultisitesmodule_template_' . $routeArea . 'edit',
                    'routeParameters' => ['projects' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $title);
                $menu[$title]->setAttribute('icon', 'fa fa-plus');
            }
        }

        $this->eventDispatcher->dispatch(MultisitesEvents::MENU_ITEMACTIONS_POST_CONFIGURE, new ConfigureItemActionsMenuEvent($this->factory, $menu, $options));

        return $menu;
    }
}
