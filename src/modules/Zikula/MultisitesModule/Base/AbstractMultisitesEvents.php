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

namespace Zikula\MultisitesModule\Base;

/**
 * Events definition base class.
 */
abstract class AbstractMultisitesEvents
{
    /**
     * The zikulamultisitesmodule.site_post_load event is thrown when sites
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postLoad()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::prePersist()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postPersist()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preRemove()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postRemove()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preUpdate()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postUpdate()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postLoad()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::prePersist()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postPersist()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preRemove()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postRemove()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preUpdate()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const TEMPLATE_POST_UPDATE = 'zikulamultisitesmodule.template_post_update';
    
    /**
     * The zikulamultisitesmodule.siteextension_post_load event is thrown when site extensions
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const SITEEXTENSION_POST_LOAD = 'zikulamultisitesmodule.siteextension_post_load';
    
    /**
     * The zikulamultisitesmodule.siteextension_pre_persist event is thrown before a new site extension
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const SITEEXTENSION_PRE_PERSIST = 'zikulamultisitesmodule.siteextension_pre_persist';
    
    /**
     * The zikulamultisitesmodule.siteextension_post_persist event is thrown after a new site extension
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const SITEEXTENSION_POST_PERSIST = 'zikulamultisitesmodule.siteextension_post_persist';
    
    /**
     * The zikulamultisitesmodule.siteextension_pre_remove event is thrown before an existing site extension
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const SITEEXTENSION_PRE_REMOVE = 'zikulamultisitesmodule.siteextension_pre_remove';
    
    /**
     * The zikulamultisitesmodule.siteextension_post_remove event is thrown after an existing site extension
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const SITEEXTENSION_POST_REMOVE = 'zikulamultisitesmodule.siteextension_post_remove';
    
    /**
     * The zikulamultisitesmodule.siteextension_pre_update event is thrown before an existing site extension
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const SITEEXTENSION_PRE_UPDATE = 'zikulamultisitesmodule.siteextension_pre_update';
    
    /**
     * The zikulamultisitesmodule.siteextension_post_update event is thrown after an existing new site extension
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterSiteExtensionEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const SITEEXTENSION_POST_UPDATE = 'zikulamultisitesmodule.siteextension_post_update';
    
    /**
     * The zikulamultisitesmodule.project_post_load event is thrown when projects
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\MultisitesModule\Event\FilterProjectEvent instance.
     *
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postLoad()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::prePersist()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postPersist()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preRemove()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postRemove()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::preUpdate()
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
     * @see Zikula\MultisitesModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const PROJECT_POST_UPDATE = 'zikulamultisitesmodule.project_post_update';
    
}
