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

namespace Zikula\MultisitesModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\MultisitesModule\Entity\Factory\TemplateFactory
use Zikula\MultisitesModule\Helper\ListEntriesHelper;

/**
 * Template editing form type base class.
 */
class TemplateType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var TemplateFactory
     */
    protected $entityFactory;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * TemplateType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance.
     * @param TemplateFactory $entityFactory Entity factory service instance.
     * @param ListEntriesHelper   $listHelper   ListEntriesHelper service instance.
     */
    public function __construct(TranslatorInterface $translator, TemplateFactory $entityFactory, , ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->listHelper = $listHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance.
     */
    public function setTranslator(TranslatorInterface $translator)
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
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the name of the template')
            ],
        'required' => true,
            'max_length' => 150,
        ]);
        $builder->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Description') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the description of the template')
            ],
        'required' => false,
            'max_length' => 250,
        ]);
        $builder->add('sqlFile', 'Symfony\Component\Form\Extension\Core\Type\FileType', [
            'label' => $this->__('Sql file') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the sql file of the template')
            ],
        'required' => true && $options['mode'] == 'create',
            'file_meta' => 'getSqlFileMeta',
            'file_path' => 'getSqlFileFullPath',
            'file_url' => 'getSqlFileFullPathUrl',
            'allowed_extensions' => 'sql, txt',
            'allowed_size' => 0
        ]);
    }

    /**
     * Adds fields for incoming relationships.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addIncomingRelationshipFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('projects', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:ProjectEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'multiple' => true,
            'expanded' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->selectWhere('', '', false, true);
            },
            'label' => $this->__('Projects'),
            'attr' => [
                'id' => 'projects',
                'title' => $this->__('Choose the projects')
            ]
        ]);
    }

    /**
     * Adds fields for outgoing relationships.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addOutgoingRelationshipFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sites', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'ZikulaMultisitesModule:SiteEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'multiple' => true,
            'expanded' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->selectWhere('', '', false, true);
            },
            'label' => $this->__('Sites'),
            'attr' => [
                'id' => 'sites',
                'title' => $this->__('Choose the sites')
            ]
        ]);
    }

    /**
     * Adds the return control field.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
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
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $this->__($action['title']),
                'attr' => [
                    'id' => 'btn' . ucfirst($action['id']),
                    'class' => $action['buttonClass'],
                    'title' => $this->__($action['description'])
                ]
            ]);
        }
        $builder->add('reset', 'Symfony\Component\Form\Extension\Core\Type\ResetType', [
            'label' => $this->__('Reset'),
            'attr' => [
                'id' => 'btnReset'
            ]
        ]);
        $builder->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $this->__('Cancel'),
            'attr' => [
                'id' => 'btnCancel'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulamultisitesmodule_template';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'Zikula\MultisitesModule\Entity\TemplateEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createTemplate():
                },
                'error_mapping' => [
                ],
                'mode' => 'create',
                'actions' => [],
                'inlineUsage' => false
            ])
            ->setRequired(['mode', 'actions'])
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
