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

namespace Zikula\MultisitesModule\Helper\Base;

use Zikula\Common\Translator\TranslatorInterface;

/**
 * Helper base class for list field entries related methods.
 */
abstract class AbstractListEntriesHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the name or names for a given list item.
     *
     * @param string $value      The dropdown value to process
     * @param string $objectType The treated object type
     * @param string $fieldName  The list field's name
     * @param string $delimiter  String used as separator for multiple selections
     *
     * @return string List item name
     */
    public function resolve($value, $objectType = '', $fieldName = '', $delimiter = ', ')
    {
        if ((empty($value) && $value != '0') || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        $isMulti = $this->hasMultipleSelection($objectType, $fieldName);
        if (true === $isMulti) {
            $value = $this->extractMultiList($value);
        }
    
        $options = $this->getEntries($objectType, $fieldName);
        $result = '';
    
        if (true === $isMulti) {
            foreach ($options as $option) {
                if (!in_array($option['value'], $value)) {
                    continue;
                }
                if (!empty($result)) {
                    $result .= $delimiter;
                }
                $result .= $option['text'];
            }
        } else {
            foreach ($options as $option) {
                if ($option['value'] != $value) {
                    continue;
                }
                $result = $option['text'];
                break;
            }
        }
    
        return $result;
    }
    

    /**
     * Extract concatenated multi selection.
     *
     * @param string  $value The dropdown value to process
     *
     * @return array List of single values
     */
    public function extractMultiList($value)
    {
        $listValues = explode('###', $value);
        $amountOfValues = count($listValues);
        if ($amountOfValues > 1 && $listValues[$amountOfValues - 1] == '') {
            unset($listValues[$amountOfValues - 1]);
        }
        if ($listValues[0] == '') {
            // use array_shift instead of unset for proper key reindexing
            // keys must start with 0, otherwise the dropdownlist form plugin gets confused
            array_shift($listValues);
        }
    
        return $listValues;
    }
    

    /**
     * Determine whether a certain dropdown field has a multi selection or not.
     *
     * @param string $objectType The treated object type
     * @param string $fieldName  The list field's name
     *
     * @return boolean True if this is a multi list false otherwise
     */
    public function hasMultipleSelection($objectType, $fieldName)
    {
        if (empty($objectType) || empty($fieldName)) {
            return false;
        }
    
        $result = false;
        switch ($objectType) {
            case 'site':
                switch ($fieldName) {
                    case 'workflowState':
                        $result = false;
                        break;
                }
                break;
            case 'template':
                switch ($fieldName) {
                    case 'workflowState':
                        $result = false;
                        break;
                }
                break;
            case 'siteExtension':
                switch ($fieldName) {
                    case 'workflowState':
                        $result = false;
                        break;
                    case 'extensionType':
                        $result = false;
                        break;
                }
                break;
            case 'project':
                switch ($fieldName) {
                    case 'workflowState':
                        $result = false;
                        break;
                }
                break;
        }
    
        return $result;
    }
    

    /**
     * Get entries for a certain dropdown field.
     *
     * @param string  $objectType The treated object type
     * @param string  $fieldName  The list field's name
     *
     * @return array Array with desired list entries
     */
    public function getEntries($objectType, $fieldName)
    {
        if (empty($objectType) || empty($fieldName)) {
            return [];
        }
    
        $entries = [];
        switch ($objectType) {
            case 'site':
                switch ($fieldName) {
                    case 'workflowState':
                        $entries = $this->getWorkflowStateEntriesForSite();
                        break;
                }
                break;
            case 'template':
                switch ($fieldName) {
                    case 'workflowState':
                        $entries = $this->getWorkflowStateEntriesForTemplate();
                        break;
                }
                break;
            case 'siteExtension':
                switch ($fieldName) {
                    case 'workflowState':
                        $entries = $this->getWorkflowStateEntriesForSiteExtension();
                        break;
                    case 'extensionType':
                        $entries = $this->getExtensionTypeEntriesForSiteExtension();
                        break;
                }
                break;
            case 'project':
                switch ($fieldName) {
                    case 'workflowState':
                        $entries = $this->getWorkflowStateEntriesForProject();
                        break;
                }
                break;
        }
    
        return $entries;
    }

    
    /**
     * Get 'workflow state' list entries.
     *
     * @return array Array with desired list entries
     */
    public function getWorkflowStateEntriesForSite()
    {
        $states = [];
        $states[] = [
            'value'   => 'approved',
            'text'    => $this->translator->__('Approved'),
            'title'   => $this->translator->__('Content has been approved and is available online.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!approved',
            'text'    => $this->translator->__('All except approved'),
            'title'   => $this->translator->__('Shows all items except these which are approved'),
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'workflow state' list entries.
     *
     * @return array Array with desired list entries
     */
    public function getWorkflowStateEntriesForTemplate()
    {
        $states = [];
        $states[] = [
            'value'   => 'approved',
            'text'    => $this->translator->__('Approved'),
            'title'   => $this->translator->__('Content has been approved and is available online.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!approved',
            'text'    => $this->translator->__('All except approved'),
            'title'   => $this->translator->__('Shows all items except these which are approved'),
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'workflow state' list entries.
     *
     * @return array Array with desired list entries
     */
    public function getWorkflowStateEntriesForSiteExtension()
    {
        $states = [];
        $states[] = [
            'value'   => 'approved',
            'text'    => $this->translator->__('Approved'),
            'title'   => $this->translator->__('Content has been approved and is available online.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!approved',
            'text'    => $this->translator->__('All except approved'),
            'title'   => $this->translator->__('Shows all items except these which are approved'),
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'extension type' list entries.
     *
     * @return array Array with desired list entries
     */
    public function getExtensionTypeEntriesForSiteExtension()
    {
        $states = [];
        $states[] = [
            'value'   => 'module',
            'text'    => $this->translator->__('Module'),
            'title'   => '',
            'image'   => '',
            'default' => true
        ];
        $states[] = [
            'value'   => 'theme',
            'text'    => $this->translator->__('Theme'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'systemPlugin',
            'text'    => $this->translator->__('System plugin'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => 'modulePlugin',
            'text'    => $this->translator->__('Module plugin'),
            'title'   => '',
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
    
    /**
     * Get 'workflow state' list entries.
     *
     * @return array Array with desired list entries
     */
    public function getWorkflowStateEntriesForProject()
    {
        $states = [];
        $states[] = [
            'value'   => 'approved',
            'text'    => $this->translator->__('Approved'),
            'title'   => $this->translator->__('Content has been approved and is available online.'),
            'image'   => '',
            'default' => false
        ];
        $states[] = [
            'value'   => '!approved',
            'text'    => $this->translator->__('All except approved'),
            'title'   => $this->translator->__('Shows all items except these which are approved'),
            'image'   => '',
            'default' => false
        ];
    
        return $states;
    }
}
