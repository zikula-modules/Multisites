<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.1.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper\Base;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Helper base class for image methods.
 */
abstract class AbstractImageHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * Name of the application.
     *
     * @var string
     */
    protected $name;

    /**
     * ImageHelper constructor.
     *
     * @param TranslatorInterface  $translator  Translator service instance
     * @param SessionInterface     $session     Session service instance
     * @param VariableApiInterface $variableApi VariableApi service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        SessionInterface $session,
        VariableApiInterface $variableApi
    ) {
        $this->translator = $translator;
        $this->session = $session;
        $this->variableApi = $variableApi;
        $this->name = 'ZikulaMultisitesModule';
    }

    /**
     * This method returns an Imagine runtime options array for the given arguments.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     * @param string $context    Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args       Additional arguments
     *
     * @return array The selected runtime options
     */
    public function getRuntimeOptions($objectType = '', $fieldName = '', $context = '', array $args = [])
    {
        $this->checkIfImagineCacheDirectoryExists();
    
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'])) {
            $context = 'controllerAction';
        }
    
        $contextName = '';
        if ($context == 'controllerAction') {
            if (!isset($args['controller'])) {
                $args['controller'] = 'user';
            }
            if (!isset($args['action'])) {
                $args['action'] = 'index';
            }
    
            $contextName = $this->name . '_' . $args['controller'] . '_' . $args['action'];
        }
        if (empty($contextName)) {
            $contextName = $this->name . '_default';
        }
    
        return $this->getCustomRuntimeOptions($objectType, $fieldName, $contextName, $context, $args);
    }

    /**
     * This method returns an Imagine runtime options array for the given arguments.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     * @param string $contextName Name of desired context
     * @param string $context    Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args       Additional arguments
     *
     * @return array The selected runtime options
     */
    public function getCustomRuntimeOptions($objectType = '', $fieldName = '', $contextName = '', $context = '', array $args = [])
    {
        $options = [
            'thumbnail' => [
                'size'      => [100, 100], // thumbnail width and height in pixels
                'mode'      => $this->variableApi->get('ZikulaMultisitesModule', 'thumbnailMode' . ucfirst($objectType) . ucfirst($fieldName), 'inset'),
                'extension' => null        // file extension for thumbnails (jpg, png, gif; null for original file type)
            ]
        ];
    
        if ($contextName == $this->name . '_relateditem') {
            $options['thumbnail']['size'] = [100, 75];
        } elseif ($context == 'controllerAction') {
            if (in_array($args['action'], ['view', 'display', 'edit'])) {
                $fieldSuffix = ucfirst($objectType) . ucfirst($fieldName) . ucfirst($args['action']);
                $defaultWidth = $args['action'] == 'view' ? 32 : 240;
                $defaultHeight = $args['action'] == 'view' ? 24 : 180;
                $options['thumbnail']['size'] = [
                    $this->variableApi->get('ZikulaMultisitesModule', 'thumbnailWidth' . $fieldSuffix, $defaultWidth),
                    $this->variableApi->get('ZikulaMultisitesModule', 'thumbnailHeight' . $fieldSuffix, $defaultHeight)
                ];
            }
        }
    
        return $options;
    }

    /**
     * Check if cache directory exists and create it if needed.
     */
    protected function checkIfImagineCacheDirectoryExists()
    {
        $cachePath = 'web/imagine/cache';
        if (file_exists($cachePath)) {
            return;
        }
    
        $this->session->getFlashBag()->add('warning', $this->translator->__f('The cache directory "%directory%" does not exist. Please create it and make it writable for the webserver.', ['%directory%' => $cachePath]));
    }
}
