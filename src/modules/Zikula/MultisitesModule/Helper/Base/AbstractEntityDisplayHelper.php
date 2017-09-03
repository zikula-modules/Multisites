<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.2 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Helper\Base;

use Zikula\Common\Translator\TranslatorInterface;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Helper\ListEntriesHelper;

/**
 * Entity display helper base class.
 */
abstract class AbstractEntityDisplayHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ListEntriesHelper Helper service for managing list entries
     */
    protected $listEntriesHelper;

    /**
     * EntityDisplayHelper constructor.
     *
     * @param TranslatorInterface $translator        Translator service instance
     * @param ListEntriesHelper   $listEntriesHelper Helper service for managing list entries
     */
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->translator = $translator;
        $this->listEntriesHelper = $listEntriesHelper;
    }

    /**
     * Returns the formatted title for a given entity.
     *
     * @param object $entity The given entity instance
     *
     * @return string The formatted title
     */
    public function getFormattedTitle($entity)
    {
        if ($entity instanceof SiteEntity) {
            return $this->formatSite($entity);
        }
        if ($entity instanceof TemplateEntity) {
            return $this->formatTemplate($entity);
        }
        if ($entity instanceof ProjectEntity) {
            return $this->formatProject($entity);
        }
    
        return '';
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param SiteEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatSite(SiteEntity $entity)
    {
        return $this->translator->__f('%name%', [
            '%name%' => $entity->getName()
        ]);
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param TemplateEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatTemplate(TemplateEntity $entity)
    {
        return $this->translator->__f('%name%', [
            '%name%' => $entity->getName()
        ]);
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param ProjectEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatProject(ProjectEntity $entity)
    {
        return $this->translator->__f('%name%', [
            '%name%' => $entity->getName()
        ]);
    }
    
    /**
     * Returns name of the field used as title / name for entities of this repository.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as title
     */
    public function getTitleFieldName($objectType)
    {
        if ($objectType == 'site') {
            return 'name';
        }
        if ($objectType == 'template') {
            return 'name';
        }
        if ($objectType == 'project') {
            return 'name';
        }
    
        return '';
    }
    
    /**
     * Returns name of the field used for describing entities of this repository.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as description
     */
    public function getDescriptionFieldName($objectType)
    {
        if ($objectType == 'site') {
            return 'description';
        }
        if ($objectType == 'template') {
            return 'description';
        }
        if ($objectType == 'project') {
            return 'name';
        }
    
        return '';
    }
    
    /**
     * Returns name of first upload field which is capable for handling images.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used for preview images
     */
    public function getPreviewFieldName($objectType)
    {
        if ($objectType == 'site') {
            return 'logo';
        }
    
        return '';
    }
    
    /**
     * Returns name of the date(time) field to be used for representing the start
     * of this object. Used for providing meta data to the tag module.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as date
     */
    public function getStartDateFieldName($objectType)
    {
        if ($objectType == 'site') {
            return 'createdDate';
        }
        if ($objectType == 'template') {
            return 'createdDate';
        }
        if ($objectType == 'project') {
            return 'createdDate';
        }
    
        return '';
    }
}
