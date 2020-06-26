<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper;

use Doctrine\ORM\QueryBuilder;
use Zikula\MultisitesModule\Helper\Base\AbstractCollectionFilterHelper;

/**
 * Entity collection filter helper implementation class.
 */
class CollectionFilterHelper extends AbstractCollectionFilterHelper
{
    public function getViewQuickNavParameters($objectType = '', $context = '', $args = [])
    {
        $parameters = parent::getViewQuickNavParameters($objectType, $context, $args);

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }

        if ($objectType == 'project') {
            $parameters['template'] = $request->query->getInt('template', 0);
        } elseif ($objectType == 'template') {
            $parameters['project'] = $request->query->getInt('project', 0);
        }

        return $parameters;
    }

    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }

        $qb = parent::addCommonViewFilters($objectType, $qb);

        if ($objectType == 'project') {
            $parameters = $this->getViewQuickNavParametersForProject();
            if (
                isset($parameters['template'])
                && 0 < $parameters['template']
                && in_array('tblTemplates', $qb->getAllAliases())
            ) {
                $qb->andWhere(':template MEMBER OF tbl.templates')
                   ->setParameter('template', $parameters['template']);
            }
        } elseif ($objectType == 'template') {
            $parameters = $this->getViewQuickNavParametersForTemplate();
            if (
                isset($parameters['project'])
                && 0 < $parameters['project']
                && in_array('tblProjects', $qb->getAllAliases())
            ) {
                $qb->andWhere(':project MEMBER OF tbl.projects')
                   ->setParameter('project', $parameters['project']);
            }
        }

        return $qb;
    }

    protected function applyDefaultFiltersForSite(QueryBuilder $qb, $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'zikulamultisitesmodule_site_admin');
        if ($isAdminArea) {
            return $qb;
        }

        $qb = parent::applyDefaultFiltersForSite($qb, $parameters);

        $letter = $request->query->getAlnum('letter', '');
        if ($letter != '') {
            $qb->andWhere('tbl.name LIKE :letter')
               ->setParameter('letter', $letter . '%');
        }

        return $qb;
    }
}
