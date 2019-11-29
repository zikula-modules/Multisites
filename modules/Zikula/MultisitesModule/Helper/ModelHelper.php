<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper;

use Zikula\MultisitesModule\Entity\TemplateEntity;
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
            case 'project':
                $result = true;
                break;
        }
    
        return $result;
    }
}