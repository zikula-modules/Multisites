<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper;

use Doctrine\ORM\QueryBuilder;
use Zikula\MultisitesModule\Helper\Base\AbstractCollectionFilterHelper;

/**
 * Entity collection filter helper implementation class.
 */
class CollectionFilterHelper extends AbstractCollectionFilterHelper
{
    /**
     * @inheritDoc
     */
    public function getViewQuickNavParameters($objectType = '', $context = '', $args = [])
    {
        $parameters = parent::getViewQuickNavParameters($objectType, $context, $args);

        if ($objectType == 'project') {
            $parameters['template'] = $this->request->query->getInt('template', 0);
        } elseif ($objectType == 'template') {
            $parameters['project'] = $this->request->query->getInt('project', 0);
        }

        return $parameters;
    }

    /**
     * @inheritDoc
     */
    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        if (null === $this->request) {
            return $qb;
        }
        $routeName = $this->request->get('_route');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }

        $qb = parent::addCommonViewFilters($objectType, $qb);

        if ($objectType == 'project') {
            $parameters = $this->getViewQuickNavParametersForProject();
            if (isset($parameters['template']) && $parameters['template'] > 0 && false !== strpos($qb->getDql(), 'tblTemplates')) {
                $qb->andWhere(':template MEMBER OF tbl.templates')
                   ->setParameter('template', $parameters['template']);
            }
        } elseif ($objectType == 'template') {
            $parameters = $this->getViewQuickNavParametersForTemplate();
            if (isset($parameters['project']) && $parameters['project'] > 0 && false !== strpos($qb->getDql(), 'tblProjects')) {
                $qb->andWhere(':project MEMBER OF tbl.projects')
                   ->setParameter('project', $parameters['project']);
            }
        }

        return $qb;
    }

    /**
     * @inheritDoc
     */
    protected function applyDefaultFiltersForSite(QueryBuilder $qb, $parameters = [])
    {
        if (null === $this->request) {
            return $qb;
        }
        $routeName = $this->request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'zikulamultisitesmodule_site_admin');
        if ($isAdminArea) {
            return $qb;
        }

        $qb = parent::applyDefaultFiltersForSite($qb, $parameters);

        $letter = $this->request->query->getAlnum('letter', '');
        if ($letter != '') {
            $qb->andWhere('tbl.name LIKE :letter')
               ->setParameter('letter', $letter . '%');
        }

        return $qb;
    }
}
