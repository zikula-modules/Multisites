<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Entity\Factory\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Zikula\MultisitesModule\Entity\Factory\EntityInitialiser;
use Zikula\MultisitesModule\Helper\CollectionFilterHelper;

/**
 * Factory class used to create entities and receive entity repositories.
 */
abstract class AbstractEntityFactory
{
    /**
     * @var ObjectManager The object manager to be used for determining the repository
     */
    protected $objectManager;

    /**
     * @var EntityInitialiser The entity initialiser for dynamical application of default values
     */
    protected $entityInitialiser;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * EntityFactory constructor.
     *
     * @param ObjectManager          $objectManager          The object manager to be used for determining the repositories
     * @param EntityInitialiser      $entityInitialiser      The entity initialiser for dynamical application of default values
     * @param CollectionFilterHelper $collectionFilterHelper CollectionFilterHelper service instance
     */
    public function __construct(
        ObjectManager $objectManager,
        EntityInitialiser $entityInitialiser,
        CollectionFilterHelper $collectionFilterHelper)
    {
        $this->objectManager = $objectManager;
        $this->entityInitialiser = $entityInitialiser;
        $this->collectionFilterHelper = $collectionFilterHelper;
    }

    /**
     * Returns a repository for a given object type.
     *
     * @param string $objectType Name of desired entity type
     *
     * @return EntityRepository The repository responsible for the given object type
     */
    public function getRepository($objectType)
    {
        $entityClass = 'Zikula\\MultisitesModule\\Entity\\' . ucfirst($objectType) . 'Entity';

        $repository = $this->objectManager->getRepository($entityClass);
        $repository->setCollectionFilterHelper($this->collectionFilterHelper);

        return $repository;
    }

    /**
     * Creates a new site instance.
     *
     * @return Zikula\MultisitesModule\Entity\siteEntity The newly created entity instance
     */
    public function createSite()
    {
        $entityClass = 'Zikula\\MultisitesModule\\Entity\\SiteEntity';

        $entity = new $entityClass();

        $this->entityInitialiser->initSite($entity);

        return $entity;
    }

    /**
     * Creates a new template instance.
     *
     * @return Zikula\MultisitesModule\Entity\templateEntity The newly created entity instance
     */
    public function createTemplate()
    {
        $entityClass = 'Zikula\\MultisitesModule\\Entity\\TemplateEntity';

        $entity = new $entityClass();

        $this->entityInitialiser->initTemplate($entity);

        return $entity;
    }

    /**
     * Creates a new project instance.
     *
     * @return Zikula\MultisitesModule\Entity\projectEntity The newly created entity instance
     */
    public function createProject()
    {
        $entityClass = 'Zikula\\MultisitesModule\\Entity\\ProjectEntity';

        $entity = new $entityClass();

        $this->entityInitialiser->initProject($entity);

        return $entity;
    }

    /**
     * Returns the identifier field's name for a given object type.
     *
     * @param string $objectType The object type to be treated
     *
     * @return string Primary identifier field name
     */
    public function getIdField($objectType = '')
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException('Invalid object type received.');
        }
        $entityClass = 'ZikulaMultisitesModule:' . ucfirst($objectType) . 'Entity';
    
        $meta = $this->getObjectManager()->getClassMetadata($entityClass);
    
        return $meta->getSingleIdentifierFieldName();
    }

    /**
     * Returns the object manager.
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
        if ($this->objectManager != $objectManager) {
            $this->objectManager = $objectManager;
        }
    }
    

    /**
     * Returns the entity initialiser.
     *
     * @return EntityInitialiser
     */
    public function getEntityInitialiser()
    {
        return $this->entityInitialiser;
    }
    
    /**
     * Sets the entity initialiser.
     *
     * @param EntityInitialiser $entityInitialiser
     *
     * @return void
     */
    public function setEntityInitialiser($entityInitialiser)
    {
        if ($this->entityInitialiser != $entityInitialiser) {
            $this->entityInitialiser = $entityInitialiser;
        }
    }
    
}
