<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.1 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper;

use Zikula\MultisitesModule\Helper\Base\AbstractModelHelper;

/**
 * Utility implementation class for model helper methods.
 */
class ModelHelper extends AbstractModelHelper
{
    /**
     * @inheritDoc
     */
    public function canBeCreated($objectType)
    {
        $result = false;
    
        switch ($objectType) {
            case 'site':
                $result = $this->hasExistingInstances('project') && $this->hasExistingInstances('template');
                break;
            case 'template':
                $result = $this->hasExistingInstances('project');
                break;
            case 'siteExtension':
                $result = true;
                break;
            case 'project':
                $result = true;
                break;
        }
    
        return $result;
    }
}
