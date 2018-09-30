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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
use Zikula\MultisitesModule\Traits\ModerationFormFieldsTrait;

/**
 * Site editing form type base class.
 */
abstract class AbstractSiteType extends AbstractType
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
     * SiteType constructor.
     *
     * @param TranslatorInterface $translator    Translator service instance
     * @param EntityFactory $entityFactory EntityFactory service instance
     * @param CollectionFilterHelper $collectionFilterHelper CollectionFilterHelper service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param ListEntriesHelper $listHelper ListEntriesHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        EntityDisplayHelper $entityDisplayHelper,
        ListEntriesHelper $listHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->listHelper = $listHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
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
                'title' => $this->__('Enter the name of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('description', TextType::class, [
            'label' => $this->__('Description') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the description of the site.')
            ],
            'required' => false,
        ]);
        
        $builder->add('siteAlias', TextType::class, [
            'label' => $this->__('Site alias') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 80,
                'class' => '',
                'title' => $this->__('Enter the site alias of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('siteName', TextType::class, [
            'label' => $this->__('Site name') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 150,
                'class' => '',
                'title' => $this->__('Enter the site name of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('siteDescription', TextType::class, [
            'label' => $this->__('Site description') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the site description of the site.')
            ],
            'required' => false,
        ]);
        
        $builder->add('siteAdminName', TextType::class, [
            'label' => $this->__('Site admin name') . ':',
            'empty_data' => 'admin',
            'attr' => [
                'maxlength' => 25,
                'class' => '',
                'title' => $this->__('Enter the site admin name of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('siteAdminPassword', PasswordType::class, [
            'label' => $this->__('Site admin password') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 15,
                'class' => '',
                'title' => $this->__('Enter the site admin password of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('siteAdminRealName', TextType::class, [
            'label' => $this->__('Site admin real name') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 70,
                'class' => '',
                'title' => $this->__('Enter the site admin real name of the site.')
            ],
            'required' => false,
        ]);
        
        $builder->add('siteAdminEmail', EmailType::class, [
            'label' => $this->__('Site admin email') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 40,
                'class' => '',
                'title' => $this->__('Enter the site admin email of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('siteCompany', TextType::class, [
            'label' => $this->__('Site company') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 100,
                'class' => '',
                'title' => $this->__('Enter the site company of the site.')
            ],
            'required' => false,
        ]);
        
        $builder->add('siteDns', TextType::class, [
            'label' => $this->__('Site dns') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the site dns of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('databaseName', TextType::class, [
            'label' => $this->__('Database name') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 50,
                'class' => '',
                'title' => $this->__('Enter the database name of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('databaseUserName', TextType::class, [
            'label' => $this->__('Database user name') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 50,
                'class' => '',
                'title' => $this->__('Enter the database user name of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('databasePassword', PasswordType::class, [
            'label' => $this->__('Database password') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 50,
                'class' => '',
                'title' => $this->__('Enter the database password of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('databaseHost', TextType::class, [
            'label' => $this->__('Database host') . ':',
            'empty_data' => 'localhost',
            'attr' => [
                'maxlength' => 50,
                'class' => '',
                'title' => $this->__('Enter the database host of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('databaseType', TextType::class, [
            'label' => $this->__('Database type') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 25,
                'class' => '',
                'title' => $this->__('Enter the database type of the site.')
            ],
            'required' => true,
        ]);
        
        $builder->add('logo', UploadType::class, [
            'label' => $this->__('Logo') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the logo of the site.')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => ''
        ]);
        
        $builder->add('favIcon', UploadType::class, [
            'label' => $this->__('Fav icon') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the fav icon of the site.')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'png, ico',
            'allowed_size' => ''
        ]);
        
        $builder->add('allowedLocales', ArrayType::class, [
            'label' => $this->__('Allowed locales') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the allowed locales of the site.')
            ],
            'required' => false,
        ]);
        
        $builder->add('parametersCsvFile', UploadType::class, [
            'label' => $this->__('Parameters csv file') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the parameters csv file of the site.')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'csv',
            'allowed_size' => ''
        ]);
        
        $builder->add('parametersArray', ArrayType::class, [
            'label' => $this->__('Parameters array') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the parameters array of the site.')
            ],
            'required' => false,
        ]);
        
        $builder->add('active', CheckboxType::class, [
            'label' => $this->__('Active') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('active ?')
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
        $builder->add('template', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:TemplateEntity',
            'choice_label' => $choiceLabelClosure,
            'multiple' => false,
            'expanded' => false,
            'query_builder' => $queryBuilder,
            'placeholder' => $this->__('Please choose an option.'),
            'required' => false,
            'label' => $this->__('Template'),
            'attr' => [
                'title' => $this->__('Choose the template.')
            ]
        ]);
        $queryBuilder = function(EntityRepository $er) {
            // select without joins
            return $er->getListQueryBuilder('', '', false);
        };
        $entityDisplayHelper = $this->entityDisplayHelper;
        $choiceLabelClosure = function ($entity) use ($entityDisplayHelper) {
            return $entityDisplayHelper->getFormattedTitle($entity);
        };
        $builder->add('project', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:ProjectEntity',
            'choice_label' => $choiceLabelClosure,
            'multiple' => false,
            'expanded' => false,
            'query_builder' => $queryBuilder,
            'placeholder' => $this->__('Please choose an option.'),
            'required' => false,
            'label' => $this->__('Project'),
            'attr' => [
                'title' => $this->__('Choose the project.')
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
        return 'zikulamultisitesmodule_site';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'Zikula\MultisitesModule\Entity\SiteEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createSite();
                },
                'error_mapping' => [
                    'logo' => 'logo.logo',
                    'favIcon' => 'favIcon.favIcon',
                    'parametersCsvFile' => 'parametersCsvFile.parametersCsvFile',
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
