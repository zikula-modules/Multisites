<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
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

    public function __construct(ProjectEntity $project, array $entityChangeSet = [])
    {
        $this->project = $project;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * @return ProjectEntity
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return array Entity change set
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }
}