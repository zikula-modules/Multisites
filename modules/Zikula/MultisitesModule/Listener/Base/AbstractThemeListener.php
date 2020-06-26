<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\ThemeModule\Bridge\Event\TwigPostRenderEvent;
use Zikula\ThemeModule\Bridge\Event\TwigPreRenderEvent;
use Zikula\ThemeModule\ThemeEvents;

/**
 * Event handler base class for theme-related events.
 */
abstract class AbstractThemeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ThemeEvents::PRE_RENDER  => ['preRender', 5],
            ThemeEvents::POST_RENDER => ['postRender', 5]
        ];
    }
    
    /**
     * Listener for the `theme.pre_render` event.
     *
     * Occurs immediately before twig theme engine renders a template.
     */
    public function preRender(TwigPreRenderEvent $event)
    {
    }
    
    /**
     * Listener for the `theme.post_render` event.
     *
     * Occurs immediately after twig theme engine renders a template.
     */
    public function postRender(TwigPostRenderEvent $event)
    {
    }
}
