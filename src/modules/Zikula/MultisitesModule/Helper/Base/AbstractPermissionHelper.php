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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\GroupsModule\Entity\GroupEntity;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * Permission helper base class.
 */
abstract class AbstractPermissionHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionApiInterface $permissionApi,
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository
    ) {
        $this->requestStack = $requestStack;
        $this->permissionApi = $permissionApi;
        $this->currentUserApi = $currentUserApi;
        $this->userRepository = $userRepository;
    }
    
    /**
     * Checks if the given entity instance may be read.
     *
     * @param EntityAccess $entity
     * @param int $userId
     *
     * @return bool
     */
    public function mayRead(EntityAccess $entity, $userId = null)
    {
        return $this->hasEntityPermission($entity, ACCESS_READ, $userId);
    }
    
    /**
     * Checks if the given entity instance may be edited.
     *
     * @param EntityAccess $entity
     * @param int $userId
     *
     * @return bool
     */
    public function mayEdit(EntityAccess $entity, $userId = null)
    {
        return $this->hasEntityPermission($entity, ACCESS_EDIT, $userId);
    }
    
    /**
     * Checks if the given entity instance may be deleted.
     *
     * @param EntityAccess $entity
     * @param int $userId
     *
     * @return bool
     */
    public function mayDelete(EntityAccess $entity, $userId = null)
    {
        return $this->hasEntityPermission($entity, ACCESS_DELETE, $userId);
    }
    
    /**
     * Checks if a certain permission level is granted for the given entity instance.
     *
     * @param EntityAccess $entity
     * @param int $permissionLevel
     * @param int $userId
     *
     * @return bool
     */
    public function hasEntityPermission(EntityAccess $entity, $permissionLevel, $userId = null)
    {
        $objectType = $entity->get_objectType();
        $instance = $entity->getKey() . '::';
    
        return $this->permissionApi->hasPermission('ZikulaMultisitesModule:' . ucfirst($objectType) . ':', $instance, $permissionLevel, $userId);
    }
    
    /**
     * Filters a given collection of entities based on different permission checks.
     *
     * @param array|ArrayCollection $entities The given list of entities
     *
     * @return array The filtered list of entities
     */
    public function filterCollection($objectType, $entities, $permissionLevel, $userId = null)
    {
        $filteredEntities = [];
        foreach ($entities as $multisites) {
            if (!$this->hasEntityPermission($multisites, $permissionLevel, $userId)) {
                continue;
            }
            $filteredEntities[] = $multisites;
        }
    
        return $filteredEntities;
    }
    
    /**
     * Checks if a certain permission level is granted for the given object type.
     *
     * @param string $objectType
     * @param int $permissionLevel
     * @param int $userId
     *
     * @return bool
     */
    public function hasComponentPermission($objectType, $permissionLevel, $userId = null)
    {
        return $this->permissionApi->hasPermission('ZikulaMultisitesModule:' . ucfirst($objectType) . ':', '::', $permissionLevel, $userId);
    }
    
    /**
     * Checks if the quick navigation form for the given object type may be used or not.
     *
     * @param string $objectType
     * @param int $userId
     *
     * @return bool
     */
    public function mayUseQuickNav($objectType, $userId = null)
    {
        return $this->hasComponentPermission($objectType, ACCESS_READ, $userId);
    }
    
    /**
     * Checks if a certain permission level is granted for the application in general.
     *
     * @param int $permissionLevel
     * @param int $userId
     *
     * @return bool
     */
    public function hasPermission($permissionLevel, $userId = null)
    {
        return $this->permissionApi->hasPermission('ZikulaMultisitesModule::', '::', $permissionLevel, $userId);
    }
    
    /**
     * Returns the list of user group ids of the current user.
     *
     * @return int[] List of group ids
     */
    public function getUserGroupIds()
    {
        $isLoggedIn = $this->currentUserApi->isLoggedIn();
        if (!$isLoggedIn) {
            return [];
        }
    
        $groupIds = [];
        $groups = $this->currentUserApi->get('groups');
        /** @var GroupEntity $group */
        foreach ($groups as $group) {
            $groupIds[] = $group->getGid();
        }
    
        return $groupIds;
    }
    
    /**
     * Returns the the current user's id.
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->currentUserApi->get('uid');
    }
    
    /**
     * Returns the the current user's entity.
     *
     * @return UserEntity
     */
    public function getUser()
    {
        return $this->userRepository->find($this->getUserId());
    }
}
