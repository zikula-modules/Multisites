<?php
/**
 * Subsite.
 */

namespace Zikula\SubsiteModule\Listener;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This event subscriber dynamically modifies the system configuration for a certain site.
 */
class DynamicConfigurationListener implements EventSubscriberInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    private $sitesConfigFile = 'var/multisites.json';

    /**
     * @var array
     */
    private $multisitesParameters;

    /**
     * DynamicConfigurationListener constructor.
     *
     * @param ContainerInterface $container
     * @param array              $multisitesParameters
     */
    public function __construct(
        ContainerInterface $container,
        array $multisitesParameters
    ) {
        $this->setContainer($container);
        $this->multisitesParameters = $multisitesParameters;
    }

    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 5]
        ];
    }

    /**
     * Changes several system settings based on the current site.
     *
     * @param GetResponseEvent $event The event instance
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (php_sapi_name() == 'cli') {
            return;
        }

        if (!$this->multisitesParameters['enabled']) {
            return;
        }

        if ($this->isMainSite($event->getRequest())) {
            return;
        }

        $fs = new Filesystem();
        if (!$fs->exists($this->sitesConfigFile)) {
            return;
        }

        $sitesConfig = json_decode(file_get_contents($this->sitesConfigFile));
        if (null === $sitesConfig) {
            return;
        }

        $siteIdentifier = $this->getSiteIdentifier($request);
        if (!array_key_exists($siteIdentifier, $sitesConfig)) {
            $event->setResponse(new RedirectResponse($this->multisitesParameters['mainsiteurl'], Response::HTTP_MOVED_PERMANENTLY));

            return;
        }

        $siteData = $sitesConfig[$siteIdentifier];
        $siteAlias = $siteData['alias'];
        $container = $this->container;

        if ($siteIdentifier == $request->server->get('HTTP_HOST')) {
            // modify only if based_on_domains setting is enabled
            $container->setParameter('router.request_context.host', $siteIdentifier);
        }

        // set site-specific cache directory (includes temp_dir which is defined as %kernel.cache_dir%/ztemp)
        $cacheDirectory = $container->getParameter('kernel.cache_dir') . '/' . $siteAlias;
        $container->setParameter('kernel.cache_dir', $cacheDirectory);

        // set site-specific logs directory
        $logsDirectory = $container->getParameter('kernel.logs_dir') . '/' . $siteAlias;
        $container->setParameter('kernel.logs_dir', $logsDirectory);

        // set site-specific upload directory
        $dataDirectory = $container->getParameter('datadir') . '/' . $siteAlias;
        $container->setParameter('datadir', $dataDirectory);

        // set site-specific database data
        $container->setParameter('database_driver', 'pdo_' . $siteData['dbType']);
        $container->setParameter('database_host', $siteData['dbHost']);
        $container->setParameter('database_port', null);
        $container->setParameter('database_name', $siteData['dbName']);
        $container->setParameter('database_user', $siteData['dbUser']);
        $container->setParameter('database_password', $siteData['dbPass']);

        // $request = $event->getRequest()
        // $kernel = $event->getKernel();
    }

    /**
     * Check if the current request is done on the main site or not.
     *
     * @param Request $request Current request instance
     *
     * @return boolean True if the main site is requested, false otherwise.
     */
    private function isMainSite(Request $request)
    {
        $msConfig = $this->multisitesParameters;

        $mainSiteUrl = isset($msConfig['mainsiteurl']) && $msConfig['mainsiteurl'] != '~' ? $msConfig['mainsiteurl'] : '';

        return $mainSiteUrl == $this->getSiteIdentifier($request);
    }

    /**
     * Returns the site identifier for the given request.
     *
     * @param Request $request Current request instance
     *
     * @return string
     */
    private function getSiteIdentifier(Request $request)
    {
        $msConfig = $this->multisitesParameters;

        $isBasedOnDomains = isset($msConfig['based_on_domains']) && $msConfig['based_on_domains'] != '~' ? $msConfig['based_on_domains'] : 1;

        return $isBasedOnDomains == 1 ? $request->server->get('HTTP_HOST') : $request->query->get('sitedns', '');
    }
}
