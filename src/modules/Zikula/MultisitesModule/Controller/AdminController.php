<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.1 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\MultisitesModule\Controller\Base\AbstractAdminController;
use Zikula\MultisitesModule\DatabaseInfo;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Admin controller class providing navigation and interaction functionality.
 */
class AdminController extends AbstractController
{
    /**
     * This is the default action handling the main area called without defining arguments.
     *
     * @Route("/admin",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        // return error if we are not on the main site
        if (!$this->isMainSite($request) && $this->isConfigured()) {
            throw new AccessDeniedException();
        }

        // return error if no permissions are granted
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check if configuration is required
        $configRequired = !$this->isConfigured();

        $redirectRoute = null;
        if ($configRequired) {
            $redirectRoute = 'zikulamultisitesmodule_config_config';
        } else {
            $redirectRoute = 'zikulamultisitesmodule_site_adminview';
        }

        return $this->redirectToRoute($redirectRoute);

    }

    /**
     * Check whether Multisites is running or not.
     *
     * @return boolean True if Multisites properties are available
     */
    protected function isConfigured()
    {
        $msConfig = $this->get('service_container')->getParameter('multisites');

        return (isset($msConfig['enabled']) && $msConfig['enabled'] == true
             && isset($msConfig['mainsiteurl'])
             && isset($msConfig['based_on_domains']));
    }

    /**
     * Check if the current request is done on the main site or not.
     *
     * @param Request $request Current request instance
     *
     * @return boolean True if the main site is requested, false otherwise
     */
    protected function isMainSite(Request $request)
    {
        $msConfig = $this->get('service_container')->getParameter('multisites');

        $isBasedOnDomains = isset($msConfig['based_on_domains']) && $msConfig['based_on_domains'] != '~' ? $msConfig['based_on_domains'] : 1;
        $mainUrl = isset($msConfig['mainsiteurl']) && $msConfig['mainsiteurl'] != '~' ? $msConfig['mainsiteurl'] : '';

        return ($isBasedOnDomains == 0 && $mainUrl == $request->query->get('sitedns', '')
            || ($isBasedOnDomains == 1 && $mainUrl == $_SERVER['HTTP_HOST']));
    }

    /**
     * Core and module update management.
     *
     * @Route("/admin/manageUpdates",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     *
     * @param Request  $request Current request instanc
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function manageUpdatesAction(Request $request)
    {
        // return error if we are not on the main site
        if (!$this->isMainSite() && $this->isConfigured()) {
            throw new AccessDeniedException();
        }

        // return error if no permissions are granted
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        // check if configuration is required
        $configRequired = !$this->isConfigured();
        if ($configRequired) {
            return $this->redirectToRoute('zikulamultisitesmodule_config_config');
        }

        // return template
        return $this->render('@ZikulaMultisitesModule/Admin/manageUpdates.html.twig');
    }

    /**
     * Provides a generic sql shell.
     *
     * @Route("/admin/multiplyQueries",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     *
     * @param Request  $request Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function multiplyQueriesAction(Request $request)
    {
        // return error if we are not on the main site
        if (!$this->isMainSite() && $this->isConfigured()) {
            throw new AccessDeniedException();
        }

        // return error if no permissions are granted
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check if configuration is required
        $configRequired = !$this->isConfigured();
        if ($configRequired) {
            return $this->redirectToRoute('zikulamultisitesmodule_config_config');
        }

        // get all sites
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository('site');
        $sites = $repository->selectWhere();
        if (false === $sites) {
            return false;
        }

        // build lists of databases, database types and hosts
        $databases = [];
        $databaseTypes = [];
        $databaseHosts = [];

        // add main site
        $container = $this->get('service_container');
        $databases[] = [
            'alias' => 'multisitesMainSiteAlias',
            'dbName' => $container->getParameter('database_name'),
            'dbHost' => $container->getParameter('database_host'),
            'dbType' => str_replace('pdo_', '', $container->getParameter('database_driver')),
            'dbUser' => $container->getParameter('database_user'),
            'dbPass' => $container->getParameter('database_password')
        ];

        // add child sites
        foreach ($sites as $site) {
            $dbHost = $site->getDatabaseHost();
            $dbType = $site->getDatabaseType();

            if (!in_array($dbHost, $databaseHosts)) {
                $databaseHosts[] = $dbHost;
            }

            if (!in_array($dbType, $databaseTypes)) {
                $databaseTypes[] = $dbType;
            }

            $dbInfo = new DatabaseInfo($site);
            $databases[] = $dbInfo->getConfigData();
        }

        $sqlInput = '';
        $sqlOutput = '';
        $databaseHostsSelected = [];
        $databaseTypesSelected = [];

        $tokenHandler = $this->get('zikula_core.common.csrf_token_handler');

        // check whether the form has been submitted
        if ($request->isMethod('POST')) {
            $tokenHandler->validate($request->request->get('token', ''));

            $inputValid = true;

            $sqlInput = $request->request->get('inputquery', '');
            if (empty($sqlInput) && isset($_FILES['queryfile']) && is_array($_FILES['queryfile']) && isset($_FILES['queryfile']['tmp_name']) && is_file($_FILES['queryfile']['tmp_name']) && is_readable($_FILES['queryfile']['tmp_name'])) {
                $sqlInput = file_get_contents($_FILES['queryfile']['tmp_name']);
            }
            if (empty($sqlInput)) {
                $this->addFlash('error', $this->__('Error! Please enter some sql commands or provide a sql file.'));
                $inputValid = false;
            }

            $databaseHostsSelected = $request->request->get('dbhosts', []);
            $databaseTypesSelected = $request->request->get('dbtypes', []);
            if (count($databaseHostsSelected) < 1 || count($databaseTypesSelected) < 1) {
                if (count($databaseHostsSelected) < 1) {
                    $this->addFlash('error', $this->__('Error! Please select at least one database host.'));
                }
                if (count($databaseTypesSelected) < 1) {
                    $this->addFlash('error', $this->__('Error! Please select at least one database type.'));
                }
                $inputValid = false;
            }

            if ($inputValid === true) {
                $opMode = $request->request->get('opmode', '');
                if (!in_array($opMode, ['show', 'execute'])) {
                    $opMode = 'show';
                }

                $systemHelper = $opMode == 'execute' ? $this->get('zikula_multisites_module.system_helper') : null;

                foreach ($databases as $database) {
                    // check whether the db host should be processed
                    if (!in_array($database['dbHost'], $databaseHostsSelected)) {
                        continue;
                    }
                    // check whether the db type should be processed
                    if (!in_array($database['dbType'], $databaseTypesSelected)) {
                        continue;
                    }

                    $dbName = $database['dbName'];
                    $sql = str_replace('###DBNAME###', $dbName, $sqlInput);
                    if ($opMode == 'show') {
                        // add sql to output
                        $sqlOutput .= "\n\n";
                        $sqlOutput .= '# ------ ' . $this->__('Site alias') . ': ' . $database['alias'] . ', ' . $this->__('Database name') . ': ' . $dbName . "\n\n";
                        $sqlOutput .= $sql;
                        $sqlOutput .= "\n\n";
                    } elseif ($opMode == 'execute') {
                        // run sql in site database
                        $connect = $systemHelper->connectToExternalDatabase(new DatabaseInfo($database));
                        if (!$connect) {
                            $this->addFlash('error', $this->__f('Error! Connecting to the database %s failed.', ['%s' => $dbName]));
                            continue;
                        }
                        $stmt = $connect->prepare($sql);
                        if (!$stmt->execute()) {
                            $this->addFlash('error', $this->__f('Error during executing query in database %s.', ['%s' => $dbName]));
                        } else {
                            $this->addFlash('status', $this->__f('Query successfully executed in database %s.', ['%s' => $dbName]));
                        }
                    }
                }
            }
        } else {
            // select all hosts per default
            foreach ($databaseHosts as $host) {
                $databaseHostsSelected[] = $host;
            }

            // select all types per default
            foreach ($databaseTypes as $dbType) {
                $databaseTypesSelected[] = $dbType;
            }
        }

        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        $templateParameters = [
            'token' => $tokenHandler->generate(),
            'databases' => $databases,
            'databaseHosts' => $databaseHosts,
            'databaseTypes' => $databaseTypes,
            'databaseHostsSelected' => $databaseHostsSelected,
            'databaseTypesSelected' => $databaseTypesSelected,
            'sqlInput' => $sqlInput,
            'sqlOutput' => $sqlOutput
        ];

        return $viewHelper->processTemplate('admin', 'multiplyQueries', $templateParameters);
    }
}
