<?php
/**
 * Subsite.
 */

namespace Zikula\SubsiteModule\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zikula\ThemeModule\Bridge\Event\TwigPostRenderEvent;
use Zikula\ThemeModule\ThemeEvents;

/**
 * This event handler cares for replacing parameter placeholders by concrete values.
 */
class ReplaceParametersListener implements EventSubscriberInterface
{
    /**
     * The doctrine annotation reader service.
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var boolean
     */
    private $isAdminArea;

    /**
     * ReplaceParametersListener constructor.
     *
     * @param object $moduleVars Existing module vars
     */
    public function __construct(Reader $annotationReader, $moduleVars) {
        $this->annotationReader = $annotationReader;
        $this->parameters = $moduleVars;
        $this->isAdminArea = false;
    }

    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['determineAdminArea', 5],
            ThemeEvents::POST_RENDER => ['postRender', 5]
        ];
    }

    /**
     * Reads controller annotations to determine whether the current page is rendered
     * inside the admin panel or not.
     *
     * @param FilterControllerEvent $event
     */
    public function determineAdminArea(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // prevents calling this for controller usage within a template or elsewhere
            return;
        }
        $controller = $event->getController();
        list($controller, $method) = $controller;
        // the controller could be a proxy, e.g. when using the JMSSecuriyExtraBundle or JMSDiExtraBundle
        $controllerClassName = ClassUtils::getClass($controller);

        $reflectionClass = new \ReflectionClass($controllerClassName);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $themeAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, 'Zikula\ThemeModule\Engine\Annotation\Theme');

        $this->isAdminArea = isset($themeAnnotation) && $themeAnnotation->value == 'admin';
    }

    /**
     * Listener for the `theme.post_render` event.
     *
     * Occurs immediately after twig theme engine renders a template.
     * The event subject is TwigPostRenderEvent.
     *
     * @param TwigPostRenderEvent $event The event instance
     */
    public function postRender(TwigPostRenderEvent $event)
    {
        // replace parameter placeholders by concrete values
        if (count($this->parameters) < 1) {
            return;
        }

        if (true === $this->isAdminArea) {
            return;
        }

        $delimiter = '###';
        $output = $event->getContent();

        foreach ($this->parameters as $paramName => $paramValue) {
            if (false === strpos($paramName, 'parameterValue')) {
                // normal modvar
                continue;
            }

            $placeholder = $delimiter . strtoupper(str_replace('parameterValue', '', $paramName)) . $delimiter;
            $output = str_replace($placeholder, $paramValue, $output);
        }

        $event->setContent($output);
    }
}
