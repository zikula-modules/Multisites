<?php
/**
 * Multisites.
 *
 * @copyright Albert P?rez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert P?rez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Entity\Factory\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

/**
 * Factory class used to retrieve entity and repository instances.
 *
 * This is the base factory class for site extension entities.
 */
class SiteExtensionFactory
{
    /**
     * @var String Full qualified class name to be used for site extensions.
     */
    protected $className;

    /**
     * @var ObjectManager The object manager to be used for determining the repository
     */
    protected $objectManager;

    /**
     * @var EntityRepository The currently used repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectManager $om        The object manager to be used for determining the repository
     * @param String        $className Full qualified class name to be used for site extensions
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->className = $className;
        $this->om = $om;
        $this->repository = $this->om->getRepository($className);
    }

    public function createSiteExtension()
    {
        $entityClass = $this->className;

        return new $entityClass();
    }

    /**
     * Gets the class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Sets the class name.
     *
     * @param string $className
     *
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }
    
    /**
     * Gets the object manager.
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
    
    /**
     * Sets the object manager.
     *
     * @param ObjectManager $objectManager
     *
     * @return void
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    /**
     * Gets the repository.
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * Sets the repository.
     *
     * @param EntityRepository $repository
     *
     * @return void
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
    
}
