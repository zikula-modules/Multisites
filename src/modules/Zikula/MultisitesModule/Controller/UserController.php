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

namespace Zikula\MultisitesModule\Controller;

use Zikula\MultisitesModule\Controller\Base\AbstractUserController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * User controller class providing navigation and interaction functionality.
 */
class UserController extends AbstractUserController
{
    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @Route("/user",
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }


    // feel free to add your own controller methods here
}
