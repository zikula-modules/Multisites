<?php
/**
 * Copyright 2016 Zikula Foundation
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula_View
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

namespace Zikula\MultisitesModule\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Event\GenericEvent;
use Zikula\ExtensionsModule\Api\ExtensionApi;
use Zikula\ExtensionsModule\ExtensionEvents;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

/**
 * Event handler implementation class for extensions events.
 */
class MultisitesExtensionsListener implements EventSubscriberInterface
{
    use TranslatorTrait;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;

    /**
     * @var array
     */
    private $multisites;

    /**
     * MultisitesExtensionsListener constructor.
     *
     * @param TranslatorInterface    $translator           Translator service instance.
     * @param RequestStack           $requestStack         RequestStack service instance.
     * @param PermissionApiInterface $permissionApi        PermissionApi service instance.
     * @param array                  $multisitesParameters Multisites parameters array.
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        PermissionApiInterface $permissionApi,
        array $multisitesParameters
    ) {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
        $this->permissionApi = $permissionApi;
        $this->multisites = $multisitesParameters;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance.
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            ExtensionEvents::REGENERATE_VETO => 'checkAllowed',
            ExtensionEvents::UPDATE_STATE => 'updateState',
            ExtensionEvents::REMOVE_VETO => 'remove',
            ExtensionEvents::INSERT_VETO => 'checkAllowed'
        ];
    }

    public function checkAllowed(GenericEvent $event)
    {
        if (!$this->multisites['enabled'] || $this->isAllowed()) {
            return;
        }

        $event->stopPropagation();
    }

    public function updateState(GenericEvent $event)
    {
        if ($this->multisites['enabled'] && $event->getArgument('state') == ExtensionApi::STATE_UNINITIALISED) {
            if (!$this->permissionApi->hasPermission('ZikulaExtensionsModule::', '::', ACCESS_ADMIN)) {
                throw new \RuntimeException($this->translator->__('Error! Invalid module state transition.'));
            }
        }
    }

    public function remove(GenericEvent $event)
    {
        if (!$this->multisites['enabled'] || $this->isAllowed()) {
            return;
        }

        $currentState = $event->getSubject()->getState();
        if (in_array($currentState, [ExtensionApi::STATE_NOTALLOWED, ExtensionApi::STATE_MISSING, ExtensionApi::STATE_INVALID])) {
            return;
        }

        $event->stopPropagation();
    }

    private function isAllowed()
    {
        $request = $this->requestStack->getMasterRequest();
        // only the main site can regenerate modules/themes lists and remove components
        if (($this->multisites['mainsiteurl'] == $request->query->get('sitedns', null)
                && $this->multisites['based_on_domains'] == false)
            || ($this->multisites['mainsiteurl'] == $request->server->get('HTTP_HOST')
                && $this->multisites['based_on_domains'] == true)
        ) {
            return true;
        }

        return false;
    }
}
