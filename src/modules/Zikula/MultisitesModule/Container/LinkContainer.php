<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Container;

use Zikula\MultisitesModule\Container\Base\AbstractLinkContainer;

use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;

/**
 * This is the link container service implementation class.
 */
class LinkContainer extends AbstractLinkContainer
{
    /**
     * Returns available header links.
     *
     * @param string $type The type to collect links for
     *
     * @return array Array of header links
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        if (LinkContainerInterface::TYPE_ADMIN != $type) {
            return parent::getLinks($type);
        }

        $utilArgs = ['api' => 'linkContainer', 'action' => 'getLinks'];
        $allowedObjectTypes = $this->controllerHelper->getObjectTypes('api', $utilArgs);

        $permLevel = ACCESS_ADMIN;

        $links = [];

        if (in_array('project', $allowedObjectTypes)
            && $this->permissionApi->hasPermission($this->getBundleName() . ':Project:', '::', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_project_adminview'),
                'text' => $this->__('Projects'),
                'title' => $this->__('Project list'),
                'icon' => 'group'
            ];
        }
        if (in_array('template', $allowedObjectTypes)
            && $this->permissionApi->hasPermission($this->getBundleName() . ':Template:', '::', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_template_adminview'),
                'text' => $this->__('Templates'),
                'title' => $this->__('Template list'),
                'icon' => 'cubes'
            ];
        }
        if (in_array('site', $allowedObjectTypes)
            && $this->permissionApi->hasPermission($this->getBundleName() . ':Site:', '::', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_site_adminview'),
                'text' => $this->__('Sites'),
                'title' => $this->__('Site list'),
                'icon' => 'list-alt'
            ];
        }
        if ($this->permissionApi->hasPermission($this->getBundleName() . '::', '::', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_admin_manageUpdates'),
                'text'  => $this->__('Updates'),
                'title' => $this->__('Manage module updates'),
                'icon' => 'refresh'
            ];
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_admin_multiplyQueries'),
                'text'  => $this->__('Queries'),
                'title' => $this->__('Execute mass queries in site databases'),
                'icon' => 'database'
            ];
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_config_config'),
                'text' => $this->__('Configuration'),
                'title' => $this->__('Manage settings for this application'),
                'icon' => 'wrench'
            ];
        }

        return $links;
    }

    /**
     * Returns action links for a given entity.
     *
     * @param EntityAccess $entity  The entity
     * @param string       $area    The context area name (e.g. admin or nothing for user)
     * @param string       $context The context page name (e.g. view, display, edit, delete)
     *
     * @return array Array of action links
     */
    public function getActionLinks(EntityAccess $entity, $area = '', $context = 'view')
    {
        $links = parent::getActionLinks($entity, $area, $context);

        if ($entity instanceof TemplateEntity) {
            foreach ($links as $k => $v) {
                if ($v['linkText'] != $this->__('Create project')) {
                    continue;
                }
                unset($links[$k]);
            }

            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_template_createparameterscsvtemplate', ['id' => $this['id']]),
                'icon' => 'file-o',
                'linkTitle' => $this->__('Create a CSV file for the defined parameters'),
                'linkText' => $this->__('Parameters CSV')
            ];

            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_template_reapply', ['id' => $this['id']]),
                'icon' => 'refresh',
                'linkTitle' => $this->__('Reapply template to all assigned sites'),
                'linkText' => $this->__('Reapply template')
            ];
        } elseif ($entity instanceof SiteEntity) {
            $deleteAction = null;

            foreach ($links as $k => $v) {
                if ($v['linkText'] == $this->__('Create site extension')) {
                    unset($links[$k]);
                } elseif ($v['linkText'] == $this->__('Delete')) {
                    $deleteAction = $links[$k];
                    unset($links[$k]);
                }
            }

            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_site_manageextensions', ['id' => $this['id']]),
                'icon' => 'cubes',
                'linkTitle' => $this->__('Manage the modules for this site'),
                'linkText' => $this->__('Allowed extensions')
            ];

            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_site_managethemes', ['id' => $this['id']]),
                'icon' => 'paint-brush',
                'linkTitle' => $this->__('Manage the themes for this site'),
                'linkText' => $this->__('Allowed layouts')
            ];

            // check if system() is allowed
            if (in_array($this['databaseType'], ['mysql', 'mysqli']) && $this->isFunctionAllowed('system')) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultisitesmodule_site_exportdatabaseastemplate', ['id' => $this['id']]),
                    'icon' => 'file-o',
                    'linkTitle' => $this->__('Export the database as SQL file'),
                    'linkText' => $this->__('Database SQL Export')
                ];
            }

            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_site_viewtools', ['id' => $this['id']]),
                'icon' => 'briefcase',
                'linkTitle' => $this->__('Site tools'),
                'linkText' => $this->__('Site tools')
            ];

            // readd delete action
            if (null !== $deleteAction) {
                $links[] = $deleteAction;
            }
        }

        return $links;
    }

    /**
     * Checks whether a certain PHP function is allowed or not.
     *
     * @param string $func Name of function
     *
     * @return boolean true if function is allowed, false otherwise
     */
    protected function isFunctionAllowed($func)
    {
        if (ini_get('safe_mode')) {
            return false;
        }
        $disabled = ini_get('disable_functions');
        if ($disabled) {
            $disabled = explode(',', $disabled);
            $disabled = array_map('trim', $disabled);

            return !in_array($func, $disabled);
        }

        return true;
    }
}