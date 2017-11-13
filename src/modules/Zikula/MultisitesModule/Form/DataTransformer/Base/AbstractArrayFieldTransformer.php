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

namespace Zikula\MultisitesModule\Form\DataTransformer\Base;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Array field transformer base class.
 */
abstract class AbstractArrayFieldTransformer implements DataTransformerInterface
{
    /**
     * Transforms the object array to the normalised value.
     *
     * @param array|null $values The object array
     *
     * @return string Normalised value
     */
    public function transform($values)
    {
        if (null === $values) {
            return '';
        }

        if (!is_array($values)) {
            return $values;
        }

        if (!count($values)) {
            return '';
        }

        $value = $this->removeEmptyEntries($values);

        return implode("\n", $value);
    }

    /**
     * Transforms a textual value back to the array.
     *
     * @param string $value The textual value
     *
     * @return array Resulting array
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return [];
        }

        $items = explode("\n", $value);

        return $this->removeEmptyEntries($items);
    }

    /**
     * Iterates over the given array and removes all empty entries.
     *
     * @param array array The given input array.
     *
     * @return array The cleaned array.
     */
    protected function removeEmptyEntries(array $array = [])
    {
        $items = $array;

        foreach ($items as $k => $v) {
            if (!empty($v)) {
                continue;
            }
            unset($items[$k]);
        }

        return $items;
    }
}
