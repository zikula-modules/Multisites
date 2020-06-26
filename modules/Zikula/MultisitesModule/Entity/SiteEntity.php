<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Entity;

use Zikula\MultisitesModule\Entity\Base\AbstractSiteEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for site entities.
 * @ORM\Entity(repositoryClass="Zikula\MultisitesModule\Entity\Repository\SiteRepository")
 * @ORM\Table(name="zikula_multisites_site",
 *     indexes={
 *         @ORM\Index(name="sitednsindex", columns={"siteDns"}),
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class SiteEntity extends BaseEntity
{
    // feel free to add your own methods here
}
