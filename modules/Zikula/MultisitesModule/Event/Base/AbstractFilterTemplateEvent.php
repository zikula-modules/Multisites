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

use Zikula\MultisitesModule\Entity\TemplateEntity;
use Symfony\Component\EventDispatcher\Event;

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

    public function __construct(TemplateEntity $template, array $entityChangeSet = [])
    {
        $this->template = $template;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * @return TemplateEntity
     */
    public function getTemplate()
    {
        return $this->template;
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
