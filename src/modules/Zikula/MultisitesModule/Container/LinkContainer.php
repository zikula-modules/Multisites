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

namespace Zikula\MultisitesModule\Container;

use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\MultisitesModule\Container\Base\AbstractLinkContainer;

/**
 * This is the link container service implementation class.
 */
class LinkContainer extends AbstractLinkContainer
{
    /**
     * @inheritDoc
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        if (LinkContainerInterface::TYPE_ADMIN != $type) {
            return parent::getLinks($type);
        }

        $links = parent::getLinks();

        if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_admin_manageUpdates'),
                'text'  => $this->__('Updates', 'zikulamultisitesmodule'),
                'title' => $this->__('Manage module updates', 'zikulamultisitesmodule'),
                'icon' => 'refresh'
            ];
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_admin_multiplyQueries'),
                'text'  => $this->__('Queries', 'zikulamultisitesmodule'),
                'title' => $this->__('Execute mass queries in site databases', 'zikulamultisitesmodule'),
                'icon' => 'database'
            ];
            $links[] = [
                'url' => $this->router->generate('zikulamultisitesmodule_config_config'),
                'text' => $this->__('Configuration', 'zikulamultisitesmodule'),
                'title' => $this->__('Manage settings for this application', 'zikulamultisitesmodule'),
                'icon' => 'wrench'
            ];
        }

        return $links;
    }
}
