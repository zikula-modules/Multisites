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

namespace Zikula\MultisitesModule\Event\Base;

use Symfony\Component\EventDispatcher\Event;
use Zikula\MultisitesModule\Entity\ProjectEntity;

/**
 * Event base class for filtering project processing.
 */
class AbstractFilterProjectEvent extends Event
{
    /**
     * @var ProjectEntity Reference to treated entity instance.
     */
    protected $project;

    /**
     * @var array Entity change set for preUpdate events.
     */
    protected $entityChangeSet = [];

    /**
     * FilterProjectEvent constructor.
     *
     * @param ProjectEntity $project Processed entity
     * @param array $entityChangeSet Change set for preUpdate events
     */
    public function __construct(ProjectEntity $project, $entityChangeSet = [])
    {
        $this->project = $project;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * Returns the entity.
     *
     * @return ProjectEntity
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Returns the change set.
     *
     * @return array
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }
}
