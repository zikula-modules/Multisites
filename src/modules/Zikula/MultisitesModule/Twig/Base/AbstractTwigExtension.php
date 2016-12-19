<?php
/**
 * Multisites.
 *
 * @copyright Albert P?rez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert P?rez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Twig\Base;

use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\MultisitesModule\Helper\ListEntriesHelper;
use Zikula\MultisitesModule\Helper\ViewHelper;
use Zikula\MultisitesModule\Helper\WorkflowHelper;
use Zikula\MultisitesModule\Container\LinkContainer;

/**
 * Twig extension base class.
 */
abstract class AbstractTwigExtension extends \Twig_Extension
{
    use TranslatorTrait;
    
    /**
     * @var VariableApi
     */
    protected $variableApi;
    
    /**
     * @var LinkContainer
     */
    protected $linkContainer;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * @var ViewHelper
     */
    protected $viewHelper;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;
    
    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param TranslatorInterface $translator     Translator service instance
     * @param VariableApi         $variableApi    VariableApi service instance
     * @param LinkContainer       $linkContainer  LinkContainer service instance
     * @param WorkflowHelper      $workflowHelper WorkflowHelper service instance
     * @param ViewHelper          $viewHelper     ViewHelper service instance
     * @param ListEntriesHelper   $listHelper     ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, VariableApi $variableApi, LinkContainer $linkContainer, WorkflowHelper $workflowHelper, ViewHelper $viewHelper, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->variableApi = $variableApi;
        $this->linkContainer = $linkContainer;
        $this->workflowHelper = $workflowHelper;
        $this->viewHelper = $viewHelper;
        $this->listHelper = $listHelper;
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
     * Returns a list of custom Twig functions.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('zikulamultisitesmodule_actions', [$this, 'getActionLinks']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_templateSelector', [$this, 'getTemplateSelector']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_userVar', [$this, 'getUserVar']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_userAvatar', [$this, 'getUserAvatar'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * Returns a list of custom Twig filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('zikulamultisitesmodule_fileSize', [$this, 'getFileSize']),
            new \Twig_SimpleFilter('zikulamultisitesmodule_listEntry', [$this, 'getListEntry']),
            new \Twig_SimpleFilter('zikulamultisitesmodule_objectState', [$this, 'getObjectState'], ['is_safe' => ['html']])
        ];
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
    public function getActionLinks(/*EntityAccess */$entity, $area = '', $context = 'view')
    {
        return $this->linkContainer->getActionLinks($entity, $area, $context);
    }
    
    /**
     * The zikulamultisitesmodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|zikulamultisitesmodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|zikulamultisitesmodule_objectState(false) }} {# no ui feedback #}
     *
     * @param string  $state      Name of given workflow state
     * @param boolean $uiFeedback Whether the output should include some visual feedback about the state
     *
     * @return string Enriched and translated workflow state ready for display
     */
    public function getObjectState($state = 'initial', $uiFeedback = true)
    {
        $stateInfo = $this->workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if (true === $uiFeedback) {
            $result = '<span class="label label-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    
    /**
     * The zikulamultisitesmodule_fileSize filter displays the size of a given file in a readable way.
     * Example:
     *     {{ 12345|zikulamultisitesmodule_fileSize }}
     *
     * @param integer $size     File size in bytes
     * @param string  $filepath The input file path including file name (if file size is not known)
     * @param boolean $nodesc   If set to true the description will not be appended
     * @param boolean $onlydesc If set to true only the description will be returned
     *
     * @return string File size in a readable form
     */
    public function getFileSize($size = 0, $filepath = '', $nodesc = false, $onlydesc = false)
    {
        if (!is_numeric($size)) {
            $size = (int) $size;
        }
        if (!$size) {
            if (empty($filepath) || !file_exists($filepath)) {
                return '';
            }
            $size = filesize($filepath);
        }
        if (!$size) {
            return '';
        }
    
        return $this->viewHelper->getReadableFileSize($size, $nodesc, $onlydesc);
    }
    
    
    /**
     * The zikulamultisitesmodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|zikulamultisitesmodule_listEntry('entityName', 'fieldName') }}
     *
     * @param string $value      The dropdown value to process
     * @param string $objectType The treated object type
     * @param string $fieldName  The list field's name
     * @param string $delimiter  String used as separator for multiple selections
     *
     * @return string List item name
     */
    public function getListEntry($value, $objectType = '', $fieldName = '', $delimiter = ', ')
    {
        if ((empty($value) && $value != '0') || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        return $this->listHelper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    
    /**
     * The zikulamultisitesmodule_objectTypeSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getObjectTypeSelector()
    {
        $result = [];
    
        $result[] = ['text' => $this->__('Sites'), 'value' => 'site'];
        $result[] = ['text' => $this->__('Templates'), 'value' => 'template'];
        $result[] = ['text' => $this->__('Site extensions'), 'value' => 'siteExtension'];
        $result[] = ['text' => $this->__('Projects'), 'value' => 'project'];
    
        return $result;
    }
    
    
    /**
     * The zikulamultisitesmodule_templateSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getTemplateSelector()
    {
        $result = [];
    
        $result[] = ['text' => $this->__('Only item titles'), 'value' => 'itemlist_display.html.twig'];
        $result[] = ['text' => $this->__('With description'), 'value' => 'itemlist_display_description.html.twig'];
        $result[] = ['text' => $this->__('Custom template'), 'value' => 'custom'];
    
        return $result;
    }
    
    /**
     * Returns the value of a user variable.
     *
     * @param string     $name    Name of desired property
     * @param int        $uid     The user's id
     * @param string|int $default The default value
     *
     * @return string
     */
    public function getUserVar($name, $uid = -1, $default = '')
    {
        if (!$uid) {
            $uid = -1;
        }
    
        $result = \UserUtil::getVar($name, $uid, $default);
    
        return $result;
    }
    
    /**
     * Display the avatar of a user.
     *
     * @param int    $uid    The user's id
     * @param int    $width  Image width (optional)
     * @param int    $height Image height (optional)
     * @param int    $size   Gravatar size (optional)
     * @param string $rating Gravatar self-rating [g|pg|r|x] see: http://en.gravatar.com/site/implement/images/ (optional)
     *
     * @return string
     */
    public function getUserAvatar($uid, $width = 0, $height = 0, $size = 0, $rating = '')
    {
        $params = ['uid' => $uid];
        if ($width > 0) {
            $params['width'] = $width;
        }
        if ($height > 0) {
            $params['height'] = $height;
        }
        if ($size > 0) {
            $params['size'] = $size;
        }
        if ($rating != '') {
            $params['rating'] = $rating;
        }
    
        include_once 'lib/legacy/viewplugins/function.useravatar.php';
    
        $view = \Zikula_View::getInstance('ZikulaMultisitesModule');
        $result = smarty_function_useravatar($params, $view);
    
        return $result;
    }
}
