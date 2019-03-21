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

namespace Zikula\MultisitesModule\Form\Type\Base;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\MultisitesModule\Entity\Factory\EntityFactory;
use Zikula\MultisitesModule\Form\Type\Field\ArrayType;
use Zikula\MultisitesModule\Form\Type\Field\UploadType;
use Zikula\MultisitesModule\Helper\CollectionFilterHelper;
use Zikula\MultisitesModule\Helper\EntityDisplayHelper;
use Zikula\MultisitesModule\Helper\ListEntriesHelper;
use Zikula\MultisitesModule\Helper\UploadHelper;
use Zikula\MultisitesModule\Traits\ModerationFormFieldsTrait;

/**
 * Template editing form type base class.
 */
abstract class AbstractTemplateType extends AbstractType
{
    use TranslatorTrait;
    use ModerationFormFieldsTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * @var UploadHelper
     */
    protected $uploadHelper;

    /**
     * TemplateType constructor.
     *
     * @param TranslatorInterface $translator
     * @param EntityFactory $entityFactory
     * @param CollectionFilterHelper $collectionFilterHelper
     * @param EntityDisplayHelper $entityDisplayHelper
     * @param ListEntriesHelper $listHelper
     * @param UploadHelper $uploadHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        EntityDisplayHelper $entityDisplayHelper,
        ListEntriesHelper $listHelper,
        UploadHelper $uploadHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->listHelper = $listHelper;
        $this->uploadHelper = $uploadHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addEntityFields($builder, $options);
        $this->addIncomingRelationshipFields($builder, $options);
        $this->addModerationFields($builder, $options);
        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('name', TextType::class, [
            'label' => $this->__('Name') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 150,
                'class' => '',
                'title' => $this->__('Enter the name of the template.')
            ],
            'required' => true,
        ]);
        
        $builder->add('description', TextType::class, [
            'label' => $this->__('Description') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 250,
                'class' => '',
                'title' => $this->__('Enter the description of the template.')
            ],
            'required' => false,
        ]);
        
        $builder->add('sqlFile', UploadType::class, [
            'label' => $this->__('Sql file') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the sql file of the template.')
            ],
            'required' => true && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => implode(', ', $this->uploadHelper->getAllowedFileExtensions('template', 'sqlFile')),
            'allowed_size' => ''
        ]);
        
        $builder->add('parameters', ArrayType::class, [
            'label' => $this->__('Parameters') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the parameters of the template.')
            ],
            'required' => false,
        ]);
        
        $builder->add('folders', ArrayType::class, [
            'label' => $this->__('Folders') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the folders of the template.')
            ],
            'required' => false,
        ]);
        
        $builder->add('excludedTables', ArrayType::class, [
            'label' => $this->__('Excluded tables') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the excluded tables of the template.')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for incoming relationships.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIncomingRelationshipFields(FormBuilderInterface $builder, array $options = [])
    {
        $queryBuilder = function(EntityRepository $er) {
            // select without joins
            return $er->getListQueryBuilder('', '', false);
        };
        $entityDisplayHelper = $this->entityDisplayHelper;
        $choiceLabelClosure = function ($entity) use ($entityDisplayHelper) {
            return $entityDisplayHelper->getFormattedTitle($entity);
        };
        $builder->add('projects', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:ProjectEntity',
            'choice_label' => $choiceLabelClosure,
            'by_reference' => false,
            'multiple' => true,
            'expanded' => false,
            'query_builder' => $queryBuilder,
            'required' => false,
            'label' => $this->__('Projects'),
            'attr' => [
                'title' => $this->__('Choose the projects.')
            ]
        ]);
    }

    /**
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = [])
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], SubmitType::class, [
                'label' => $action['title'],
                'icon' => ($action['id'] == 'delete' ? 'fa-trash-o' : ''),
                'attr' => [
                    'class' => $action['buttonClass']
                ]
            ]);
            if ($options['mode'] == 'create' && $action['id'] == 'submit' && !$options['inline_usage']) {
                // add additional button to submit item and return to create form
                $builder->add('submitrepeat', SubmitType::class, [
                    'label' => $this->__('Submit and repeat'),
                    'icon' => 'fa-repeat',
                    'attr' => [
                        'class' => $action['buttonClass']
                    ]
                ]);
            }
        }
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulamultisitesmodule_template';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'Zikula\MultisitesModule\Entity\TemplateEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createTemplate();
                },
                'error_mapping' => [
                    'sqlFile' => 'sqlFile.sqlFile',
                ],
                'mode' => 'create',
                'actions' => [],
                'has_moderate_permission' => false,
                'allow_moderation_specific_creator' => false,
                'allow_moderation_specific_creation_date' => false,
                'filter_by_ownership' => true,
                'inline_usage' => false
            ])
            ->setRequired(['entity', 'mode', 'actions'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedTypes('has_moderate_permission', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creator', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creation_date', 'bool')
            ->setAllowedTypes('filter_by_ownership', 'bool')
            ->setAllowedTypes('inline_usage', 'bool')
            ->setAllowedValues('mode', ['create', 'edit'])
        ;
    }
}
