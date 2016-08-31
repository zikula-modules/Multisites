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

namespace Zikula\MultisitesModule\Controller;

use Zikula\MultisitesModule\Controller\Base\TemplateController as BaseTemplateController;

use ModUtil;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Zikula\MultisitesModule\Entity\TemplateEntity;

/**
 * Template controller class providing navigation and interaction functionality.
 */
class TemplateController extends BaseTemplateController
{
    /**
     * This action provides an item list overview in the admin area.
     *
     * @Route("/admin/templates/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0, "_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance.
     * @param string  $sort         Sorting field.
     * @param string  $sortdir      Sorting direction.
     * @param int     $pos          Current pager position.
     * @param int     $num          Amount of entries to display.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::adminViewAction($request, $sort, $sortdir, $pos, $num);
    }
    
    /**
     * This action provides an item list overviewnull.
     *
     * @Route("/templates/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0, "_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance.
     * @param string  $sort         Sorting field.
     * @param string  $sortdir      Sorting direction.
     * @param int     $pos          Current pager position.
     * @param int     $num          Amount of entries to display.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::viewAction($request, $sort, $sortdir, $pos, $num);
    }
    /**
     * This action provides a handling of edit requests in the admin area.
     *
     * @Route("/admin/template/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found.
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available).
     */
    public function adminEditAction(Request $request)
    {
        return parent::adminEditAction($request);
    }
    
    /**
     * This action provides a handling of edit requestsnull.
     *
     * @Route("/template/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found.
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available).
     */
    public function editAction(Request $request)
    {
        return parent::editAction($request);
    }
    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/templates/createParametersCsvTemplate",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminCreateParametersCsvTemplateAction(Request $request)
    {
        return parent::adminCreateParametersCsvTemplateAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/templates/createParametersCsvTemplate",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function createParametersCsvTemplateAction(Request $request)
    {
        return parent::createParametersCsvTemplateAction($request);
    }

    /**
     * This is a custom action in the admin area.
     *
     * @Route("/admin/templates/reapply",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function adminReapplyAction(Request $request)
    {
        return parent::adminReapplyAction($request);
    }
    
    /**
     * This is a custom action.
     *
     * @Route("/templates/reapply",
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request  $request      Current request instance.
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions.
     */
    public function reapplyAction(Request $request)
    {
        return parent::reapplyAction($request);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @Route("/admin/templates/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     *
     * @param Request $request Current request instance.
     *
     * @return bool true on sucess, false on failure.
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return parent::adminHandleSelectedEntriesAction($request);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @Route("/templates/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     *
     * @param Request $request Current request instance.
     *
     * @return bool true on sucess, false on failure.
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return parent::handleSelectedEntriesAction($request);
    }

    /**
     * This method includes the common implementation code for adminCreateParametersCsvTemplate() and createParametersCsvTemplate().
     */
    protected function createParametersCsvTemplateInternal(Request $request, $isAdmin = false)
    {
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');

        // parameter specifying which type of objects we are treating
        $objectType = 'template';
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        $repository = $this->get('zikula_multisites_module.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);

        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', ['ot' => $objectType]);

        // retrieve identifier of the object we wish to view
        $idValues = $controllerHelper->retrieveIdentifier($request, [], $objectType, $idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($idValues);

        if (!$hasIdentifier) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }

        $selectionArgs = ['ot' => $objectType, 'id' => $idValues];

        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }
        unset($idValues);

        $entity->initWorkflow();

        $delimiter = ';';
        $f = fopen('php://memory', 'w'); 
        fputcsv($f, [$this->__('Name'), $this->__('Value')], $delimiter); 
        foreach ($entity->getParameters() as $paramName) {
            $line = [$paramName, ''];
            fputcsv($f, $line, $delimiter); 
        }

        // rewind file pointer
        fseek($f, 0);

        // create name of the csv output file
        $fileTitle = $controllerHelper->formatPermalink($entity->getTitleFromDisplayPattern())
                   . '-parameters-' . date('Ymd') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $fileTitle . '"');

        // send csv lines to the browser
        fpassthru($f);

        return true;
    }

    /**
     * This method includes the common implementation code for adminReapply() and reapply().
     */
    protected function reapplyInternal(Request $request, $isAdmin = false)
    {
        $controllerHelper = $this->get('zikula_multisites_module.controller_helper');

        // parameter specifying which type of objects we are treating
        $objectType = 'template';
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', ['ot' => $objectType]);
        
        // retrieve identifier of the object we wish to delete
        $idValues = $controllerHelper->retrieveIdentifier($request, [], $objectType, $idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($idValues);
        
        if (!$hasIdentifier) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }
        
        $selectionArgs = ['ot' => $objectType, 'id' => $idValues];
        
        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }
        
        $entity->initWorkflow();

        $redirectRoute = 'zikulamultisitesmodule_template_adminview';

        $sites = $entity['sites'];
        $amountOfSites = count($sites);
        if ($amountOfSites < 1) {
            $this->get('session')->getFlashBag()->add('error', $this->__('Error! This template does not have any sites assigned yet.'));

            return $this->redirectToRoute($redirectRoute);
        }

        $tokenHandler = $this->get('zikula_core.common.csrf_token_handler');

        // check whether the form has been submitted
        if ($request->isMethod('POST')) {
            $tokenHandler->validate($request->request->get('token', ''));

            $confirmation = (bool) $request->request->get('confirmation', false);
            if ($confirmation) {
                $systemHelper = $this->get('zikula_multisites_module.system_helper');

                // perform initialisation process for all sites assigned to this template
                foreach ($sites as $site) {
                    if (!$systemHelper->setupDatabaseContent($site)) {
                        // error has been registered already
                        return $this->redirectToRoute($redirectRoute);
                    }
                }

                $this->get('session')->getFlashBag()->add('status', $this->_fn('The template has been reapplied to %s site.', 'The template has been reapplied to %s sites.', $amountOfSites, ['%s' => $amountOfSites]));

                return $this->redirectToRoute($redirectRoute);
            }
        }

        $viewHelper = $this->get('zikula_multisites_module.view_helper');
        $templateParameters = [
            'token' => $tokenHandler->generate(),
            'template' => $entity
        ];

        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'reapply', $request, $templateParameters);
    }
}
