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

namespace Zikula\MultisitesModule\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Response\PlainResponse;
use Zikula\MultisitesModule\Controller\Base\AbstractTemplateController;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Template controller class providing navigation and interaction functionality.
 */
class TemplateController extends AbstractTemplateController
{
    /**
     * @inheritDoc
     *
     * @Route("/admin/templates/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminViewAction(
        Request $request,
        $sort,
        $sortdir,
        $pos,
        $num
    ) {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/templates/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|xml|json"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function viewAction(
        Request $request,
        $sort,
        $sortdir,
        $pos,
        $num
    ) {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/template/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminEditAction(
        Request $request
    ) {
        return $this->editInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/template/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     */
    public function editAction(
        Request $request
    ) {
        return $this->editInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/templates/createParametersCsvTemplate",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminCreateParametersCsvTemplateAction(Request $request)
    {
        return $this->createParametersCsvTemplateInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/templates/createParametersCsvTemplate",
     *        methods = {"GET", "POST"}
     * )
     */
    public function createParametersCsvTemplateAction(Request $request)
    {
        return $this->createParametersCsvTemplateInternal($request, false);
    }

    /**
     * @inheritDoc
     *
     * @Route("/admin/templates/reapply",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminReapplyAction(Request $request)
    {
        return $this->reapplyInternal($request, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/templates/reapply",
     *        methods = {"GET", "POST"}
     * )
     */
    public function reapplyAction(Request $request)
    {
        return $this->reapplyInternal($request, false);
    }

    /**
     * @inheritDoc
     * @Route("/admin/templates/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntriesAction(
        Request $request
    ) {
        return $this->handleSelectedEntriesActionInternal($request, true);
    }

    /**
     * @inheritDoc
     * @Route("/templates/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntriesAction(
        Request $request
    ) {
        return $this->handleSelectedEntriesActionInternal($request, false);
    }

    /**
     * This method includes the common implementation code for adminCreateParametersCsvTemplate() and createParametersCsvTemplate().
     */
    protected function createParametersCsvTemplateInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'template';
        if (!$this->get('zikula_multisites_module.permission_helper')->hasComponentPermission($objectType, ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // retrieve identifier of the object we wish to view
        $id = $request->query->getInt('id', 0);
        if (!$id) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }

        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository($objectType);
        $entity = $repository->selectById($id);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }

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
        $entityDisplayHelper = $this->get('zikula_multisites_module.entity_display_helper');
        $entityTitle = iconv('UTF-8', 'ASCII//TRANSLIT', $entityDisplayHelper->getFormattedTitle($entity));
        $fileTitle = $entityTitle . '-parameters-' . date('Ymd') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $fileTitle . '"');

        // send csv lines to the browser
        return new PlainResponse(stream_get_contents($f));
    }

    /**
     * This method includes the common implementation code for adminReapply() and reapply().
     */
    protected function reapplyInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'template';
        if (!$this->get('zikula_multisites_module.permission_helper')->hasComponentPermission($objectType, ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // retrieve identifier of the object we wish to delete
        $id = $request->query->getInt('id', 0);
        if (!$id) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }
        
        $repository = $this->get('zikula_multisites_module.entity_factory')->getRepository($objectType);
        $entity = $repository->selectById($id);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }
        
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

            $confirmation = $request->request->getBoolean('confirmation', false);
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

        return $viewHelper->processTemplate($objectType, 'reapply', $templateParameters);
    }
}
