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

namespace Zikula\MultisitesModule\Entity\Factory\Base;

use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Helper\PermissionHelper;

/**
 * Entity initialiser class used to dynamically apply default values to newly created entities.
 */
abstract class AbstractEntityInitialiser
{
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    public function __construct(
        PermissionHelper $permissionHelper
    ) {
        $this->permissionHelper = $permissionHelper;
    }

    /**
     * Initialises a given site instance.
     *
     * @param SiteEntity $entity The newly created entity instance
     *
     * @return SiteEntity The updated entity instance
     */
    public function initSite(SiteEntity $entity)
    {
        return $entity;
    }

    /**
     * Initialises a given template instance.
     *
     * @param TemplateEntity $entity The newly created entity instance
     *
     * @return TemplateEntity The updated entity instance
     */
    public function initTemplate(TemplateEntity $entity)
    {
        return $entity;
    }

    /**
     * Initialises a given project instance.
     *
     * @param ProjectEntity $entity The newly created entity instance
     *
     * @return ProjectEntity The updated entity instance
     */
    public function initProject(ProjectEntity $entity)
    {
        return $entity;
    }
}
