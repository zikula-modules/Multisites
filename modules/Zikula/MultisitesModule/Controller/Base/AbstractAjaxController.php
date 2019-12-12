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

namespace Zikula\MultisitesModule\Controller\Base;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;

/**
 * Ajax controller base class.
 */
abstract class AbstractAjaxController extends AbstractController
{
    
    /**
     * Changes a given flag (boolean field) by switching between true and false.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function toggleFlagAction(
        Request $request
    ) {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }
        
        if (!$this->hasPermission('ZikulaMultisitesModule::Ajax', '::', ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        
        $objectType = $request->request->getAlnum('ot', 'site');
        $field = $request->request->getAlnum('field');
        $id = $request->request->getInt('id');
        
        if (
            0 === $id
            || ('site' !== $objectType)
            || ('site' === $objectType && !in_array($field, ['active'], true))
        ) {
            return $this->json($this->__('Error: invalid input.'), JsonResponse::HTTP_BAD_REQUEST);
        }
        
        // select data from data source
        $entityFactory = $this->get('zikula_multisites_module.entity_factory');
        $repository = $entityFactory->getRepository($objectType);
        $entity = $repository->selectById($id, false);
        if (null === $entity) {
            return $this->json($this->__('No such item.'), JsonResponse::HTTP_NOT_FOUND);
        }
        
        // toggle the flag
        $entity[$field] = !$entity[$field];
        
        // save entity back to database
        $entityFactory->getEntityManager()->flush();
        
        $logger = $this->get('logger');
        $logArgs = [
            'app' => 'ZikulaMultisitesModule',
            'user' => $this->get('zikula_users_module.current_user')->get('uname'),
            'field' => $field,
            'entity' => $objectType,
            'id' => $id
        ];
        $logger->notice('{app}: User {user} toggled the {field} flag the {entity} with id {id}.', $logArgs);
        
        // return response
        return $this->json([
            'id' => $id,
            'state' => $entity[$field],
            'message' => $this->__('The setting has been successfully changed.')
        ]);
    }
}
