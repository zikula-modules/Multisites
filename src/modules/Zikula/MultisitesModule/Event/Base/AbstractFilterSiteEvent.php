<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.1 (http://modulestudio.de).
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

    public function __construct(SiteEntity $site)
    {
        $this->site = $site;
    }

    public function getSite()
    {
        return $this->site;
    }
}
