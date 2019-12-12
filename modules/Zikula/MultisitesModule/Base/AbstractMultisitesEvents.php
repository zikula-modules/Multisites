<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Base;

use Zikula\MultisitesModule\Listener\EntityLifecycleListener;
use Zikula\MultisitesModule\Menu\MenuBuilder;

/**
 * Events definition base class.
 */
abstract class AbstractMultisitesEvents
{
    /**
     * The zikulamultisitesmodule.itemactionsmenu_pre_configure event is thrown before the item actions
     * menu is built in the menu builder.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\ConfigureItemActionsMenuEvent instance.
     *
     * @see MenuBuilder::createItemActionsMenu()
     * @var string
     */
    const MENU_ITEMACTIONS_PRE_CONFIGURE = 'zikulamultisitesmodule.itemactionsmenu_pre_configure';
    
    /**
     * The zikulamultisitesmodule.itemactionsmenu_post_configure event is thrown after the item actions
     * menu has been built in the menu builder.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\ConfigureItemActionsMenuEvent instance.
     *
     * @see MenuBuilder::createItemActionsMenu()
     * @var string
     */
    const MENU_ITEMACTIONS_POST_CONFIGURE = 'zikulamultisitesmodule.itemactionsmenu_post_configure';
    
    /**
     * The zikulamultisitesmodule.site_post_load event is thrown when sites
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::postLoad()
     * @var string
     */
    const SITE_POST_LOAD = 'zikulamultisitesmodule.site_post_load';
    
    /**
     * The zikulamultisitesmodule.site_pre_persist event is thrown before a new site
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::prePersist()
     * @var string
     */
    const SITE_PRE_PERSIST = 'zikulamultisitesmodule.site_pre_persist';
    
    /**
     * The zikulamultisitesmodule.site_post_persist event is thrown after a new site
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::postPersist()
     * @var string
     */
    const SITE_POST_PERSIST = 'zikulamultisitesmodule.site_post_persist';
    
    /**
     * The zikulamultisitesmodule.site_pre_remove event is thrown before an existing site
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::preRemove()
     * @var string
     */
    const SITE_PRE_REMOVE = 'zikulamultisitesmodule.site_pre_remove';
    
    /**
     * The zikulamultisitesmodule.site_post_remove event is thrown after an existing site
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::postRemove()
     * @var string
     */
    const SITE_POST_REMOVE = 'zikulamultisitesmodule.site_post_remove';
    
    /**
     * The zikulamultisitesmodule.site_pre_update event is thrown before an existing site
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::preUpdate()
     * @var string
     */
    const SITE_PRE_UPDATE = 'zikulamultisitesmodule.site_pre_update';
    
    /**
     * The zikulamultisitesmodule.site_post_update event is thrown after an existing new site
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see EntityLifecycleListener::postUpdate()
     * @var string
     */
    const SITE_POST_UPDATE = 'zikulamultisitesmodule.site_post_update';
    /**
     * The zikulamultisitesmodule.template_post_load event is thrown when templates
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::postLoad()
     * @var string
     */
    const TEMPLATE_POST_LOAD = 'zikulamultisitesmodule.template_post_load';
    
    /**
     * The zikulamultisitesmodule.template_pre_persist event is thrown before a new template
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::prePersist()
     * @var string
     */
    const TEMPLATE_PRE_PERSIST = 'zikulamultisitesmodule.template_pre_persist';
    
    /**
     * The zikulamultisitesmodule.template_post_persist event is thrown after a new template
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::postPersist()
     * @var string
     */
    const TEMPLATE_POST_PERSIST = 'zikulamultisitesmodule.template_post_persist';
    
    /**
     * The zikulamultisitesmodule.template_pre_remove event is thrown before an existing template
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::preRemove()
     * @var string
     */
    const TEMPLATE_PRE_REMOVE = 'zikulamultisitesmodule.template_pre_remove';
    
    /**
     * The zikulamultisitesmodule.template_post_remove event is thrown after an existing template
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::postRemove()
     * @var string
     */
    const TEMPLATE_POST_REMOVE = 'zikulamultisitesmodule.template_post_remove';
    
    /**
     * The zikulamultisitesmodule.template_pre_update event is thrown before an existing template
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::preUpdate()
     * @var string
     */
    const TEMPLATE_PRE_UPDATE = 'zikulamultisitesmodule.template_pre_update';
    
    /**
     * The zikulamultisitesmodule.template_post_update event is thrown after an existing new template
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterTemplateEvent instance.
     *
     * @see EntityLifecycleListener::postUpdate()
     * @var string
     */
    const TEMPLATE_POST_UPDATE = 'zikulamultisitesmodule.template_post_update';
    /**
     * The zikulamultisitesmodule.project_post_load event is thrown when projects
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::postLoad()
     * @var string
     */
    const PROJECT_POST_LOAD = 'zikulamultisitesmodule.project_post_load';
    
    /**
     * The zikulamultisitesmodule.project_pre_persist event is thrown before a new project
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::prePersist()
     * @var string
     */
    const PROJECT_PRE_PERSIST = 'zikulamultisitesmodule.project_pre_persist';
    
    /**
     * The zikulamultisitesmodule.project_post_persist event is thrown after a new project
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::postPersist()
     * @var string
     */
    const PROJECT_POST_PERSIST = 'zikulamultisitesmodule.project_post_persist';
    
    /**
     * The zikulamultisitesmodule.project_pre_remove event is thrown before an existing project
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::preRemove()
     * @var string
     */
    const PROJECT_PRE_REMOVE = 'zikulamultisitesmodule.project_pre_remove';
    
    /**
     * The zikulamultisitesmodule.project_post_remove event is thrown after an existing project
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::postRemove()
     * @var string
     */
    const PROJECT_POST_REMOVE = 'zikulamultisitesmodule.project_post_remove';
    
    /**
     * The zikulamultisitesmodule.project_pre_update event is thrown before an existing project
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::preUpdate()
     * @var string
     */
    const PROJECT_PRE_UPDATE = 'zikulamultisitesmodule.project_pre_update';
    
    /**
     * The zikulamultisitesmodule.project_post_update event is thrown after an existing new project
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see EntityLifecycleListener::postUpdate()
     * @var string
     */
    const PROJECT_POST_UPDATE = 'zikulamultisitesmodule.project_post_update';
}
