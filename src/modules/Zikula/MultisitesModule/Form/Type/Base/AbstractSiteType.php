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

namespace Zikula\MultisitesModule\Form\Type\Base;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\MultisitesModule\Entity\Factory\SiteFactory;
use Zikula\MultisitesModule\Helper\ListEntriesHelper;

/**
 * Site editing form type base class.
 */
abstract class AbstractSiteType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var SiteFactory
     */
    protected $entityFactory;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * SiteType constructor.
     *
     * @param TranslatorInterface $translator    Translator service instance
     * @param SiteFactory        $entityFactory Entity factory service instance
     * @param ListEntriesHelper   $listHelper    ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, SiteFactory $entityFactory, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->listHelper = $listHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addEntityFields($builder, $options);
        $this->addIncomingRelationshipFields($builder, $options);
        $this->addOutgoingRelationshipFields($builder, $options);
        $this->addReturnControlField($builder, $options);
        $this->addSubmitButtons($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['logo', 'favIcon', 'parametersCsvFile'] as $uploadFieldName) {
                if ($entity[$uploadFieldName] instanceof File) {
                    $entity[$uploadFieldName] = [$uploadFieldName => $entity[$uploadFieldName]->getPathname()];
                }
            }
        });
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['logo', 'favIcon', 'parametersCsvFile'] as $uploadFieldName) {
                if (is_array($entity[$uploadFieldName])) {
                    $entity[$uploadFieldName] = $entity[$uploadFieldName][$uploadFieldName];
                }
            }
        });
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the name of the site')
            ],'required' => true,
            'max_length' => 150,
        ]);
        $builder->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Description') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the description of the site')
            ],'required' => false,
            'max_length' => 255,
        ]);
        $builder->add('siteAlias', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site alias') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site alias of the site')
            ],'required' => true,
            'max_length' => 80,
        ]);
        $builder->add('siteName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site name of the site')
            ],'required' => true,
            'max_length' => 150,
        ]);
        $builder->add('siteDescription', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site description') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site description of the site')
            ],'required' => false,
            'max_length' => 255,
        ]);
        $builder->add('siteAdminName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site admin name') . ':',
            'empty_data' => 'admin',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site admin name of the site')
            ],'required' => true,
            'max_length' => 25,
        ]);
        $builder->add('siteAdminPassword', 'Symfony\Component\Form\Extension\Core\Type\PasswordType', [
            'label' => $this->__('Site admin password') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site admin password of the site')
            ],'required' => true,
            'max_length' => 15,
        ]);
        $builder->add('siteAdminRealName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site admin real name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site admin real name of the site')
            ],'required' => false,
            'max_length' => 70,
        ]);
        $builder->add('siteAdminEmail', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
            'label' => $this->__('Site admin email') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => ' validate-email',
                'title' => $this->__('Enter the site admin email of the site')
            ],'required' => true,
            'max_length' => 40
        ]);
        $builder->add('siteCompany', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site company') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site company of the site')
            ],'required' => false,
            'max_length' => 100,
        ]);
        $builder->add('siteDns', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Site dns') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the site dns of the site')
            ],'required' => true,
            'max_length' => 255,
        ]);
        $builder->add('databaseName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Database name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the database name of the site')
            ],'required' => true,
            'max_length' => 50,
        ]);
        $builder->add('databaseUserName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Database user name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the database user name of the site')
            ],'required' => true,
            'max_length' => 50,
        ]);
        $builder->add('databasePassword', 'Symfony\Component\Form\Extension\Core\Type\PasswordType', [
            'label' => $this->__('Database password') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the database password of the site')
            ],'required' => true,
            'max_length' => 50,
        ]);
        $builder->add('databaseHost', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Database host') . ':',
            'empty_data' => 'localhost',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the database host of the site')
            ],'required' => true,
            'max_length' => 50,
        ]);
        $builder->add('databaseType', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Database type') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the database type of the site')
            ],'required' => true,
            'max_length' => 25,
        ]);
        $builder->add('logo', 'Zikula\MultisitesModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Logo') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the logo of the site')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => 0
        ]);
        $builder->add('favIcon', 'Zikula\MultisitesModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Fav icon') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the fav icon of the site')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'png, ico',
            'allowed_size' => 0
        ]);
        $builder->add('parametersCsvFile', 'Zikula\MultisitesModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Parameters csv file') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the parameters csv file of the site')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'csv',
            'allowed_size' => 0
        ]);
        $builder->add('active', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label' => $this->__('Active') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('active ?')
            ],'required' => false,
        ]);
    }

    /**
     * Adds fields for incoming relationships.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIncomingRelationshipFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('template', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:TemplateEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->getListQueryBuilder();
            },
            'label' => $this->__('Template'),
            'attr' => [
                'title' => $this->__('Choose the template')
            ]
        ]);
        $builder->add('project', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:ProjectEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->getListQueryBuilder();
            },
            'label' => $this->__('Project'),
            'attr' => [
                'title' => $this->__('Choose the project')
            ]
        ]);
    }

    /**
     * Adds fields for outgoing relationships.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addOutgoingRelationshipFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('extensions', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:SiteExtensionEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'multiple' => true,
            'expanded' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->getListQueryBuilder();
            },
            'label' => $this->__('Extensions'),
            'attr' => [
                'title' => $this->__('Choose the extensions')
            ]
        ]);
    }

    /**
     * Adds the return control field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addReturnControlField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('repeatCreation', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'mapped' => false,
            'label' => $this->__('Create another item after save'),
            'required' => false
        ]);
    }

    /**
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $this->__(/** @Ignore */$action['title']),
                'icon' => ($action['id'] == 'delete' ? 'fa-trash-o' : ''),
                'attr' => [
                    'class' => $action['buttonClass'],
                    'title' => $this->__(/** @Ignore */$action['description'])
                ]
            ]);
        }
        $builder->add('reset', 'Symfony\Component\Form\Extension\Core\Type\ResetType', [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulamultisitesmodule_site';
    }

    /**
     * {@inheritdoc}
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
                'inlineUsage' => false
            ])
            ->setRequired(['entity', 'mode', 'actions'])
            ->setAllowedTypes([
                'mode' => 'string',
                'actions' => 'array',
                'inlineUsage' => 'bool'
            ])
            ->setAllowedValues([
                'mode' => ['create', 'edit']
            ])
        ;
    }
}