<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Twig\Base;

/**
 * Twig extension base class.
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * Returns a list of custom Twig functions.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('zikulamultisitesmodule_templateHeaders', [$this, 'templateHeaders']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_templateSelector', [$this, 'getTemplateSelector']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_userVar', [$this, 'getUserVar']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_userAvatar', [$this, 'getUserAvatar']),
            new \Twig_SimpleFunction('zikulamultisitesmodule_thumb', [$this, 'getImageThumb'])
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
            new \Twig_SimpleFilter('zikulamultisitesmodule_actionUrl', [$this, 'buildActionUrl']),
            new \Twig_SimpleFilter('zikulamultisitesmodule_objectState', [$this, 'getObjectState']),
            new \Twig_SimpleFilter('zikulamultisitesmodule_fileSize', [$this, 'getFileSize']),
            new \Twig_SimpleFilter('zikulamultisitesmodule_listEntry', [$this, 'getListEntry']),
            new \Twig_SimpleFilter('zikulamultisitesmodule_profileLink', [$this, 'profileLink'])
        ];
    }
    
    /**
     * The zikulamultisitesmodule_actionUrl filter creates the URL for a given action.
     *
     * @param string $urlType      The url type (admin, user, etc.)
     * @param string $urlFunc      The url func (view, display, edit, etc.)
     * @param array  $urlArguments The argument array containing ids and other additional parameters
     *
     * @return string Desired url in encoded form.
     */
    public function buildActionUrl($urlType, $urlFunc, $urlArguments)
    {
        return \DataUtil::formatForDisplay(\ModUtil::url('ZikulaMultisitesModule', $urlType, $urlFunc, $urlArguments));
    }
    
    
    /**
     * The zikulamultisitesmodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|zikulamultisitesmodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|zikulamultisitesmodule_objectState(false) }} {# no ui feedback #}
     *
     * @param string  $state      Name of given workflow state.
     * @param boolean $uiFeedback Whether the output should include some visual feedback about the state.
     *
     * @return string Enriched and translated workflow state ready for display.
     */
    public function getObjectState($state = 'initial', $uiFeedback = true)
    {
        $serviceManager = \ServiceUtil::getManager();
        $workflowHelper = $serviceManager->get('zikula_multisites_module.workflow_helper');
    
        $stateInfo = $workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if ($uiFeedback === true) {
            $result = '<span class="label label-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    
    /**
     * The zikulamultisitesmodule_templateHeaders function performs header() operations
     * to change the content type provided to the user agent.
     *
     * Available parameters:
     *   - contentType:  Content type for corresponding http header.
     *   - asAttachment: If set to true the file will be offered for downloading.
     *   - fileName:     Name of download file.
     *
     * @return boolean false.
     */
    public function templateHeaders($contentType, $asAttachment = false, $fileName = '')
    {
        // apply header
        header('Content-Type: ' . $contentType);
    
        // if desired let the browser offer the given file as a download
        if ($asAttachment && !empty($fileName)) {
            header('Content-Disposition: attachment; filename=' . $fileName);
        }
    
        return;
    }
    
    
    /**
     * The zikulamultisitesmodule_fileSize filter displays the size of a given file in a readable way.
     * Example:
     *     {{ 12345|zikulamultisitesmodule_fileSize }}
     *
     * @param integer $size     File size in bytes.
     * @param string  $filepath The input file path including file name (if file size is not known).
     * @param boolean $nodesc   If set to true the description will not be appended.
     * @param boolean $onlydesc If set to true only the description will be returned.
     *
     * @return string File size in a readable form.
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
    
        $serviceManager = \ServiceUtil::getManager();
        $viewHelper = $serviceManager->get('zikula_multisites_module.view_helper');
    
        $result = $viewHelper->getReadableFileSize($size, $nodesc, $onlydesc);
    
        return $result;
    }
    
    
    /**
     * The zikulamultisitesmodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|zikulamultisitesmodule_listEntry('entityName', 'fieldName') }}
     *
     * @param string $value      The dropdown value to process.
     * @param string $objectType The treated object type.
     * @param string $fieldName  The list field's name.
     * @param string $delimiter  String used as separator for multiple selections.
     *
     * @return string List item name.
     */
    public function getListEntry($value, $objectType = '', $fieldName = '', $delimiter = ', ')
    {
        if ((empty($value) && $value != '0') || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        $serviceManager = \ServiceUtil::getManager();
        $helper = $serviceManager->get('zikula_multisites_module.listentries_helper');
    
        return $helper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    
    /**
     * The zikulamultisitesmodule_objectTypeSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin.
     */
    public function getObjectTypeSelector()
    {
        $serviceManager = \ServiceUtil::getManager();
        $translator = $serviceManager->get('translator.default');
        $result = [];
    
        $result[] = ['text' => $translator->__('Sites'), 'value' => 'site'];
        $result[] = ['text' => $translator->__('Templates'), 'value' => 'template'];
        $result[] = ['text' => $translator->__('Site extensions'), 'value' => 'siteExtension'];
        $result[] = ['text' => $translator->__('Projects'), 'value' => 'project'];
    
        return $result;
    }
    
    
    /**
     * The zikulamultisitesmodule_templateSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin.
     */
    public function getTemplateSelector()
    {
        $serviceManager = \ServiceUtil::getManager();
        $translator = $serviceManager->get('translator.default');
        $result = [];
    
        $result[] = ['text' => $translator->__('Only item titles'), 'value' => 'itemlist_display.html.twig'];
        $result[] = ['text' => $translator->__('With description'), 'value' => 'itemlist_display_description.html.twig'];
        $result[] = ['text' => $translator->__('Custom template'), 'value' => 'custom'];
    
        return $result;
    }
    
    /**
     * Returns the value of a user variable.
     *
     * @param string     $name    Name of desired property.
     * @param int        $uid     The user's id.
     * @param string|int $default The default value.
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
     * @param int    $uid    The user's id.
     * @param int    $width  Image width (optional).
     * @param int    $height Image height (optional).
     * @param int    $size   Gravatar size (optional).
     * @param string $rating Gravatar self-rating [g|pg|r|x] see: http://en.gravatar.com/site/implement/images/ (optional).
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
    
    /**
     * Display an image thumbnail using Imagine system plugin.
     *
     * @param array $params Parameters assigned to bridged Smarty plugin.
     *
     * @return string Thumb path.
     */
    public function getImageThumb($params)
    {
        include_once 'plugins/Imagine/templates/plugins/function.thumb.php';
    
        $view = \Zikula_View::getInstance('ZikulaMultisitesModule');
        $result = smarty_function_thumb($params, $view);
    
        return $result;
    }
    
    /**
     * Returns a link to the user's profile.
     *
     * @param int     $uid       The user's id (optional).
     * @param string  $class     The class name for the link (optional).
     * @param integer $maxLength If set then user names are truncated to x chars.
     *
     * @return string
     */
    public function profileLink($uid, $class = '', $maxLength = 0)
    {
        $result = '';
        $image = '';
    
        if ($uid == '') {
            return $result;
        }
    
        if (\ModUtil::getVar('ZConfig', 'profilemodule') != '') {
            include_once 'lib/legacy/viewplugins/modifier.profilelinkbyuid.php';
            $result = smarty_modifier_profilelinkbyuid($uid, $class, $image, $maxLength);
        } else {
            $result = \UserUtil::getVar('uname', $uid);
        }
    
        return $result;
    }
    
    /**
     * Returns internal name of this extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'zikulamultisitesmodule_twigextension';
    }
}
