<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Event\Base;

use Symfony\Component\EventDispatcher\Event;
use Zikula\MultisitesModule\Entity\SiteEntity;

/**
 * Event base class for filtering site processing.
 */
class AbstractFilterSiteEvent extends Event
{
    /**
     * @var SiteEntity Reference to treated entity instance.
     */
    protected $site;

    /**
     * @var array Entity change set for preUpdate events.
     */
    protected $entityChangeSet = [];

    /**
     * FilterSiteEvent constructor.
     *
     * @param SiteEntity $site Processed entity
     * @param array $entityChangeSet Change set for preUpdate events
     */
    public function __construct(SiteEntity $site, array $entityChangeSet = [])
    {
        $this->site = $site;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * Returns the entity.
     *
     * @return SiteEntity
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Returns the change set.
     *
     * @return array Entity change set
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }
}
