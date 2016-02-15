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

namespace Zikula\MultisitesModule\Form\Type\QuickNavigation\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\MultisitesModule\Helper\ListEntriesHelper;

/**
 * Template quick navigation form type base class.
 */
class TemplateQuickNavType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * TemplateQuickNavType constructor.
     *
     * @param TranslatorInterface $translator   Translator service instance.
     * @param RequestStack        $requestStack RequestStack service instance.
     * @param ListEntriesHelper   $listHelper   ListEntriesHelper service instance.
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
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
        $objectType = 'template';

        $builder
            ->add('all', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
                'data' => $options['all'],
                'empty_data' => 0
            ])
            ->add('own', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
                'data' => $options['own'],
                'empty_data' => 0
            ])
        ;

        $this->addListFields($builder, $options);
        $this->addSearchField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $builder->add('updateview', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $this->__('OK'),
            'attr' => [
                'id' => 'quicknavSubmit'
            ]
        ]);
    }

    /**
     * Adds list fields.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addListFields(FormBuilderInterface $builder, array $options)
    {
        $listEntries = $this->listHelper->getEntries('template', 'workflowState');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = $entry['title'];
        }
        $builder->add('workflowState', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Workflow state'),
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => $choices,
            'choices_as_values' => true,
            'choice_attr' => $choiceAttributes,
            'multiple' => false
        ]);
    }

    /**
     * Adds a search field.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addSearchField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', 'Symfony\Component\Form\Extension\Core\Type\SearchType', [
            'label' => $this->__('Search'),
            'attr' => [
                'id' => 'searchTerm'
            ],
            'required' => false,
            'max_length' => 255
        ]);
    }


    /**
     * Adds sorting fields.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort by'),
                'attr' => [
                    'id' => 'zikulaMultisitesModuleSort'
                ],
                'choices' => [
                    $this->__('Id') => 'id',
                    $this->__('Name') => 'name',
                    $this->__('Description') => 'description',
                    $this->__('Sql file') => 'sqlFile',
                    $this->__('Parameters') => 'parameters',
                    $this->__('Folders') => 'folders',
                    $this->__('Excluded tables') => 'excludedTables',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdUserId',
                    $this->__('Update date') => 'updatedDate'
                ],
                'choices_as_values' => true
            ])
            ->add('sortdir', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort direction'),
                'empty_data' => 'asc',
                'attr' => [
                    'id' => 'zikulaMultisitesModuleSortDir'
                ],
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'choices_as_values' => true
            ])
        ;
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface The form builder.
     * @param array                The options.
     */
    public function addAmountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Page size'),
            'empty_data' => 20,
            'attr' => [
                'id' => 'zikulaMultisitesModulePageSize',
                'class' => 'text-right'
            ],
            'choices' => [
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20,
                30 => 30,
                50 => 50,
                100 => 100
            ],
            'choices_as_values' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulamultisitesmodule_templatequicknav';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'all' => 0,
                'own' => 0
            ])
            ->setRequired(['all', 'own'])
            ->setAllowedValues([
                'all' => [0, 1],
                'own' => [0, 1]
            ])
        ;
    }
}
