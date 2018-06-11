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

namespace Zikula\MultisitesModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zikula\MultisitesModule\Entity\Base\AbstractTemplateEntity as BaseEntity;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for template entities.
 * @ORM\Entity(repositoryClass="\Zikula\MultisitesModule\Entity\Repository\TemplateRepository")
 * @ORM\Table(name="zikula_multisites_template",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class TemplateEntity extends BaseEntity
{
    /**
     * @inheritDoc
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }

        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        // reset upload fields
        //$this->setSqlFile(null);
        //$this->setSqlFileMeta([]);
        //$this->setSqlFileUrl('');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    }
}