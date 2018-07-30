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
use Zikula\MultisitesModule\Entity\TemplateEntity;

/**
 * Event base class for filtering template processing.
 */
class AbstractFilterTemplateEvent extends Event
{
    /**
     * @var TemplateEntity Reference to treated entity instance.
     */
    protected $template;

    /**
     * @var array Entity change set for preUpdate events.
     */
    protected $entityChangeSet = [];

    /**
     * FilterTemplateEvent constructor.
     *
     * @param TemplateEntity $template Processed entity
     * @param array $entityChangeSet Change set for preUpdate events
     */
    public function __construct(TemplateEntity $template, array $entityChangeSet = [])
    {
        $this->template = $template;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * Returns the entity.
     *
     * @return TemplateEntity
     */
    public function getTemplate()
    {
        return $this->template;
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
