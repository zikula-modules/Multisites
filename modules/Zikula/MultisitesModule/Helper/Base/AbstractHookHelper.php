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

namespace Zikula\MultisitesModule\Helper\Base;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Zikula\Bundle\HookBundle\Dispatcher\HookDispatcherInterface;
use Zikula\Bundle\HookBundle\FormAwareHook\FormAwareHook;
use Zikula\Bundle\HookBundle\FormAwareHook\FormAwareResponse;
use Zikula\Bundle\HookBundle\Hook\Hook;
use Zikula\Bundle\HookBundle\Hook\ProcessHook;
use Zikula\Bundle\HookBundle\Hook\ValidationHook;
use Zikula\Bundle\HookBundle\Hook\ValidationProviders;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\UrlInterface;

/**
 * Helper base class for hook related methods.
 */
abstract class AbstractHookHelper
{
    /**
     * @var HookDispatcherInterface
     */
    protected $hookDispatcher;
    
    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }
    
    /**
     * Calls validation hooks.
     *
     * @param EntityAccess $entity The currently processed entity
     * @param string $hookType Name of hook type to be called
     *
     * @return string[] List of error messages returned by validators
     */
    public function callValidationHooks(EntityAccess $entity, $hookType)
    {
        $hookAreaPrefix = $entity->getHookAreaPrefix();
    
        $hook = new ValidationHook(new ValidationProviders());
        $validators = $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook)->getValidators();
    
        return $validators->getErrors();
    }
    
    /**
     * Calls process hooks.
     *
     * @param EntityAccess $entity The currently processed entity
     * @param string $hookType Name of hook type to be called
     * @param UrlInterface $routeUrl The route url object
     */
    public function callProcessHooks(EntityAccess $entity, $hookType, UrlInterface $routeUrl = null)
    {
        $hookAreaPrefix = $entity->getHookAreaPrefix();
    
        $hook = new ProcessHook($entity->getKey(), $routeUrl);
        $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook);
    }
    
    /**
     * Calls form aware display hooks.
     *
     * @param FormInterface $form The form instance
     * @param EntityAccess $entity The currently processed entity
     * @param string $hookType Name of hook type to be called
     *
     * @return FormAwareHook The created hook instance
     */
    public function callFormDisplayHooks(FormInterface $form, EntityAccess $entity, $hookType)
    {
        $hookAreaPrefix = $entity->getHookAreaPrefix();
        $hookAreaPrefix = str_replace('.ui_hooks.', '.form_aware_hook.', $hookAreaPrefix);
    
        $hook = new FormAwareHook($form);
        $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook);
    
        return $hook;
    }
    
    /**
     * Calls form aware processing hooks.
     *
     * @param FormInterface $form The form instance
     * @param EntityAccess $entity The currently processed entity
     * @param string $hookType Name of hook type to be called
     * @param UrlInterface $routeUrl The route url object
     */
    public function callFormProcessHooks(
        FormInterface $form,
        EntityAccess $entity,
        $hookType,
        UrlInterface $routeUrl = null
    ) {
        $formResponse = new FormAwareResponse($form, $entity, $routeUrl);
        $hookAreaPrefix = $entity->getHookAreaPrefix();
        $hookAreaPrefix = str_replace('.ui_hooks.', '.form_aware_hook.', $hookAreaPrefix);
    
        $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $formResponse);
    }
    
    /**
     * Dispatch hooks.
     *
     * @param string $eventName Hook event name
     * @param Hook $hook Hook interface
     *
     * @return Event
     */
    public function dispatchHooks($eventName, Hook $hook)
    {
        return $this->hookDispatcher->dispatch($eventName, $hook);
    }
}
