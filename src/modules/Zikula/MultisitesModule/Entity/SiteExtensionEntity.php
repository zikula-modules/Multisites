<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Entity;

use Zikula\MultisitesModule\Entity\Base\AbstractSiteExtensionEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for site extension entities.
 * @ORM\Entity(repositoryClass="Zikula\MultisitesModule\Entity\Repository\SiteExtensionRepository")
 * @ORM\Table(name="zikula_multisites_siteextension",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class SiteExtensionEntity extends BaseEntity
{
    // feel free to add your own methods here
}
