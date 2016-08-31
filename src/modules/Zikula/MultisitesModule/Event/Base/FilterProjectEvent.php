<?php
/**
 * Multisites.
 *
 * @copyright Albert P?rez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert P?rez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Event\Base;

use Symfony\Component\EventDispatcher\Event;
use Zikula\MultisitesModule\Entity\ProjectEntity;

/**
 * Event base class for filtering project processing.
 */
class FilterProjectEvent extends Event
{
    /**
     * @var ProjectEntity Reference to treated entity instance.
     */
    protected $project;

    public function __construct(ProjectEntity $project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }
}
