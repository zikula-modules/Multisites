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

namespace Zikula\MultisitesModule\Helper\Base;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\MultisitesModule\Helper\PermissionHelper;

/**
 * Entity collection filter helper base class.
 */
abstract class AbstractCollectionFilterHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var bool Fallback value to determine whether only own entries should be selected or not
     */
    protected $showOnlyOwnEntries = false;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi,
        VariableApiInterface $variableApi
    ) {
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
        $this->showOnlyOwnEntries = (bool)$variableApi->get('ZikulaMultisitesModule', 'showOnlyOwnEntries');
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $objectType Name of treated entity type
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    public function getViewQuickNavParameters($objectType = '', $context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'], true)) {
            $context = 'controllerAction';
        }
    
        if ('site' === $objectType) {
            return $this->getViewQuickNavParametersForSite($context, $args);
        }
        if ('template' === $objectType) {
            return $this->getViewQuickNavParametersForTemplate($context, $args);
        }
        if ('project' === $objectType) {
            return $this->getViewQuickNavParametersForProject($context, $args);
        }
    
        return [];
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        if ('site' === $objectType) {
            return $this->addCommonViewFiltersForSite($qb);
        }
        if ('template' === $objectType) {
            return $this->addCommonViewFiltersForTemplate($qb);
        }
        if ('project' === $objectType) {
            return $this->addCommonViewFiltersForProject($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function applyDefaultFilters($objectType, QueryBuilder $qb, array $parameters = [])
    {
        if ('site' === $objectType) {
            return $this->applyDefaultFiltersForSite($qb, $parameters);
        }
        if ('template' === $objectType) {
            return $this->applyDefaultFiltersForTemplate($qb, $parameters);
        }
        if ('project' === $objectType) {
            return $this->applyDefaultFiltersForProject($qb, $parameters);
        }
    
        return $qb;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForSite($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['template'] = $request->query->get('template', 0);
        $parameters['project'] = $request->query->get('project', 0);
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['active'] = $request->query->get('active', '');
    
        return $parameters;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForTemplate($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['q'] = $request->query->get('q', '');
    
        return $parameters;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForProject($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['q'] = $request->query->get('q', '');
    
        return $parameters;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForSite(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForSite();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('site', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['active'], true)) {
                // boolean filter
                if ('no' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ('yes' === $v || '1' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForSite($qb, $parameters);
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForTemplate(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForTemplate();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('template', $qb, $v);
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForTemplate($qb, $parameters);
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForProject(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForProject();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('project', $qb, $v);
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForProject($qb, $parameters);
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForSite(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulamultisitesmodule_site_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved sites only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForTemplate(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulamultisitesmodule_template_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved templates only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForProject(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulamultisitesmodule_project_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved projects only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        return $qb;
    }
    
    /**
     * Adds a where clause for search query.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param string $fragment The fragment to search for
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addSearchFilter($objectType, QueryBuilder $qb, $fragment = '')
    {
        if ('' === $fragment) {
            return $qb;
        }
    
        $filters = [];
        $parameters = [];
    
        if ('site' === $objectType) {
            $filters[] = 'tbl.name LIKE :searchName';
            $parameters['searchName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.description LIKE :searchDescription';
            $parameters['searchDescription'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteAlias LIKE :searchSiteAlias';
            $parameters['searchSiteAlias'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteName LIKE :searchSiteName';
            $parameters['searchSiteName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteDescription LIKE :searchSiteDescription';
            $parameters['searchSiteDescription'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteAdminName LIKE :searchSiteAdminName';
            $parameters['searchSiteAdminName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteAdminPassword LIKE :searchSiteAdminPassword';
            $parameters['searchSiteAdminPassword'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteAdminRealName LIKE :searchSiteAdminRealName';
            $parameters['searchSiteAdminRealName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteAdminEmail = :searchSiteAdminEmail';
            $parameters['searchSiteAdminEmail'] = $fragment;
            $filters[] = 'tbl.siteCompany LIKE :searchSiteCompany';
            $parameters['searchSiteCompany'] = '%' . $fragment . '%';
            $filters[] = 'tbl.siteDns LIKE :searchSiteDns';
            $parameters['searchSiteDns'] = '%' . $fragment . '%';
            $filters[] = 'tbl.databaseName LIKE :searchDatabaseName';
            $parameters['searchDatabaseName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.databaseUserName LIKE :searchDatabaseUserName';
            $parameters['searchDatabaseUserName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.databasePassword LIKE :searchDatabasePassword';
            $parameters['searchDatabasePassword'] = '%' . $fragment . '%';
            $filters[] = 'tbl.databaseHost LIKE :searchDatabaseHost';
            $parameters['searchDatabaseHost'] = '%' . $fragment . '%';
            $filters[] = 'tbl.databaseType LIKE :searchDatabaseType';
            $parameters['searchDatabaseType'] = '%' . $fragment . '%';
            $filters[] = 'tbl.logoFileName = :searchLogo';
            $parameters['searchLogo'] = $fragment;
            $filters[] = 'tbl.favIconFileName = :searchFavIcon';
            $parameters['searchFavIcon'] = $fragment;
            $filters[] = 'tbl.parametersCsvFileFileName = :searchParametersCsvFile';
            $parameters['searchParametersCsvFile'] = $fragment;
        }
        if ('template' === $objectType) {
            $filters[] = 'tbl.name LIKE :searchName';
            $parameters['searchName'] = '%' . $fragment . '%';
            $filters[] = 'tbl.description LIKE :searchDescription';
            $parameters['searchDescription'] = '%' . $fragment . '%';
            $filters[] = 'tbl.sqlFileFileName = :searchSqlFile';
            $parameters['searchSqlFile'] = $fragment;
        }
        if ('project' === $objectType) {
            $filters[] = 'tbl.name LIKE :searchName';
            $parameters['searchName'] = '%' . $fragment . '%';
        }
    
        $qb->andWhere('(' . implode(' OR ', $filters) . ')');
    
        foreach ($parameters as $parameterName => $parameterValue) {
            $qb->setParameter($parameterName, $parameterValue);
        }
    
        return $qb;
    }
    
    /**
     * Adds a filter for the createdBy field.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param int $userId The user identifier used for filtering
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCreatorFilter(QueryBuilder $qb, $userId = null)
    {
        if (null === $userId) {
            $userId = $this->currentUserApi->isLoggedIn() ? (int)$this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        }
    
        $qb->andWhere('tbl.createdBy = :userId')
           ->setParameter('userId', $userId);
    
        return $qb;
    }
}
