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

use Zikula\MultisitesModule\Controller\Base\AbstractConfigController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * config controller class providing navigation and interaction functionality.
 */
class ConfigController extends AbstractConfigController
{
    /**
     * This method takes care of the application configuration.
     *
     * @Route("/config",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function configAction(Request $request)
    {
        // return error if we are not on the main site
        if (!$this->isMainSite($request) && $this->isConfigured()) {
            throw new AccessDeniedException();
        }

        // return error if no permissions are granted
        if (!$this->get('zikula_multisites_module.permission_helper')->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // create new instance of configurator
        $configurator = $this->get('zikula_multisites_module.configurator_helper');
        $configValid = $configurator->verify();

        // if configuration is not completed we show special content
        if (!$configValid) {
            $templateParameters = $configurator->getTemplateParameters();

            return $this->render('@ZikulaMultisitesModule/Config/wizard.html.twig', $templateParameters);
        }

        // check whether the global administrator has already been configured
        if ($this->getVar('globalAdminName', '') == '' || $this->getVar('globalAdminPassword', '') == '' || $this->getVar('globalAdminEmail', '') == '') {
            $this->addFlash('warning', $this->__('Please configure the global administrator settings.'));
        }

        // else we call the parent method to render the default configuration form
        return parent::configAction($request);
    }

    /**
     * Check whether Multisites is running or not.
     *
     * @return boolean True if Multisites properties are available.
     */
    protected function isConfigured()
    {
        $msConfig = $this->get('service_container')->getParameter('multisites');

        return (isset($msConfig['enabled']) && true == $msConfig['enabled']
             && isset($msConfig['mainsiteurl'])
             && isset($msConfig['based_on_domains']));
    }

    /**
     * Check if the current request is done on the main site or not.
     *
     * @param Request $request Current request instance
     *
     * @return boolean True if the main site is requested, false otherwise.
     */
    protected function isMainSite(Request $request)
    {
        $msConfig = $this->get('service_container')->getParameter('multisites');

        $isBasedOnDomains = isset($msConfig['based_on_domains']) && $msConfig['based_on_domains'] != '~' ? $msConfig['based_on_domains'] : 1;
        $mainUrl = isset($msConfig['mainsiteurl']) && $msConfig['mainsiteurl'] != '~' ? $msConfig['mainsiteurl'] : '';

        return ($isBasedOnDomains == 0 && $mainUrl == $request->query->get('sitedns', '')
            || ($isBasedOnDomains == 1 && $mainUrl == $request->server->get('HTTP_HOST')));
    }
}
