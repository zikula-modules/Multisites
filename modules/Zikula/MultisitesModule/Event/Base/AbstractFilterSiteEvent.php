<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Event\Base;

use Zikula\MultisitesModule\Entity\SiteEntity;
use Symfony\Component\EventDispatcher\Event;

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

    public function __construct(SiteEntity $site, array $entityChangeSet = [])
    {
        $this->site = $site;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * @return SiteEntity
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return array Entity change set
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }

    /**
     * @param array $changeSet Entity change set
     */
    public function setEntityChangeSet(array $changeSet = [])
    {
        $this->entityChangeSet = $changeSet;
    }
}
