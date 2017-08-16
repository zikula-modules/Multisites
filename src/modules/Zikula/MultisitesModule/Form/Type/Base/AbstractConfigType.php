<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * Configuration form type base class.
 */
abstract class AbstractConfigType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var array
     */
    protected $moduleVars;

    /**
     * ConfigType constructor.
     *
     * @param TranslatorInterface $translator  Translator service instance
     * @param object              $moduleVars  Existing module vars
     */
    public function __construct(
        TranslatorInterface $translator,
        $moduleVars
    ) {
        $this->setTranslator($translator);
        $this->moduleVars = $moduleVars;
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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addGeneralFields($builder, $options);
        $this->addSecuritySettingsFields($builder, $options);
        $this->addListViewsFields($builder, $options);
        $this->addImagesFields($builder, $options);

        $builder
            ->add('save', SubmitType::class, [
                'label' => $this->__('Update configuration'),
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', SubmitType::class, [
                'label' => $this->__('Cancel'),
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default',
                    'formnovalidate' => 'formnovalidate'
                ]
            ])
        ;
    }

    /**
     * Adds fields for general fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addGeneralFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tempAccessFileContent', TextType::class, [
                'label' => $this->__('Temp access file content') . ':',
                'required' => false,
                'data' => isset($this->moduleVars['tempAccessFileContent']) ? $this->moduleVars['tempAccessFileContent'] : '',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the temp access file content.')
                ],
            ])
        ;
    }

    /**
     * Adds fields for security settings fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSecuritySettingsFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('globalAdminName', TextType::class, [
                'label' => $this->__('Global admin name') . ':',
                'required' => false,
                'data' => isset($this->moduleVars['globalAdminName']) ? $this->moduleVars['globalAdminName'] : '',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the global admin name.')
                ],
            ])
            ->add('globalAdminPassword', TextType::class, [
                'label' => $this->__('Global admin password') . ':',
                'required' => false,
                'data' => isset($this->moduleVars['globalAdminPassword']) ? $this->moduleVars['globalAdminPassword'] : '',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the global admin password.')
                ],
            ])
            ->add('globalAdminEmail', TextType::class, [
                'label' => $this->__('Global admin email') . ':',
                'required' => false,
                'data' => isset($this->moduleVars['globalAdminEmail']) ? $this->moduleVars['globalAdminEmail'] : '',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the global admin email.')
                ],
            ])
        ;
    }

    /**
     * Adds fields for list views fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteEntriesPerPage', IntegerType::class, [
                'label' => $this->__('Site entries per page') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The amount of sites shown per page')
                ],
                'help' => $this->__('The amount of sites shown per page'),
                'required' => false,
                'data' => isset($this->moduleVars['siteEntriesPerPage']) ? intval($this->moduleVars['siteEntriesPerPage']) : intval(10),
                'empty_data' => intval('10'),
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the site entries per page.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0
            ])
            ->add('templateEntriesPerPage', IntegerType::class, [
                'label' => $this->__('Template entries per page') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The amount of templates shown per page')
                ],
                'help' => $this->__('The amount of templates shown per page'),
                'required' => false,
                'data' => isset($this->moduleVars['templateEntriesPerPage']) ? intval($this->moduleVars['templateEntriesPerPage']) : intval(10),
                'empty_data' => intval('10'),
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the template entries per page.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0
            ])
            ->add('projectEntriesPerPage', IntegerType::class, [
                'label' => $this->__('Project entries per page') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The amount of projects shown per page')
                ],
                'help' => $this->__('The amount of projects shown per page'),
                'required' => false,
                'data' => isset($this->moduleVars['projectEntriesPerPage']) ? intval($this->moduleVars['projectEntriesPerPage']) : intval(10),
                'empty_data' => intval('10'),
                'attr' => [
                    'maxlength' => 255,
                    'title' => $this->__('Enter the project entries per page.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0
            ])
        ;
    }

    /**
     * Adds fields for images fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addImagesFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enableShrinkingForSiteLogo', CheckboxType::class, [
                'label' => $this->__('Enable shrinking') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.')
                ],
                'help' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
                'required' => false,
                'data' => (bool)(isset($this->moduleVars['enableShrinkingForSiteLogo']) ? $this->moduleVars['enableShrinkingForSiteLogo'] : false),
                'attr' => [
                    'title' => $this->__('The enable shrinking option.'),
                    'class' => 'shrink-enabler'
                ],
            ])
            ->add('shrinkWidthSiteLogo', IntegerType::class, [
                'label' => $this->__('Shrink width') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The maximum image width in pixels.')
                ],
                'help' => $this->__('The maximum image width in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['shrinkWidthSiteLogo']) ? intval($this->moduleVars['shrinkWidthSiteLogo']) : intval(800),
                'empty_data' => intval('800'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the shrink width.') . ' ' . $this->__('Only digits are allowed.'),
                    'class' => 'shrinkdimension-shrinkwidthsitelogo'
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('shrinkHeightSiteLogo', IntegerType::class, [
                'label' => $this->__('Shrink height') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The maximum image height in pixels.')
                ],
                'help' => $this->__('The maximum image height in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['shrinkHeightSiteLogo']) ? intval($this->moduleVars['shrinkHeightSiteLogo']) : intval(600),
                'empty_data' => intval('600'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the shrink height.') . ' ' . $this->__('Only digits are allowed.'),
                    'class' => 'shrinkdimension-shrinkheightsitelogo'
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailModeSiteLogo', ChoiceType::class, [
                'label' => $this->__('Thumbnail mode') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail mode (inset or outbound).')
                ],
                'help' => $this->__('Thumbnail mode (inset or outbound).'),
                'data' => isset($this->moduleVars['thumbnailModeSiteLogo']) ? $this->moduleVars['thumbnailModeSiteLogo'] : '',
                'empty_data' => 'inset',
                'attr' => [
                    'title' => $this->__('Choose the thumbnail mode.')
                ],'choices' => [
                    $this->__('Inset') => 'inset',
                    $this->__('Outbound') => 'outbound'
                ],
                'multiple' => false
            ])
            ->add('thumbnailWidthSiteLogoView', IntegerType::class, [
                'label' => $this->__('Thumbnail width view') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail width on view pages in pixels.')
                ],
                'help' => $this->__('Thumbnail width on view pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailWidthSiteLogoView']) ? intval($this->moduleVars['thumbnailWidthSiteLogoView']) : intval(32),
                'empty_data' => intval('32'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail width view.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailHeightSiteLogoView', IntegerType::class, [
                'label' => $this->__('Thumbnail height view') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail height on view pages in pixels.')
                ],
                'help' => $this->__('Thumbnail height on view pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailHeightSiteLogoView']) ? intval($this->moduleVars['thumbnailHeightSiteLogoView']) : intval(24),
                'empty_data' => intval('24'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail height view.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailWidthSiteLogoEdit', IntegerType::class, [
                'label' => $this->__('Thumbnail width edit') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail width on edit pages in pixels.')
                ],
                'help' => $this->__('Thumbnail width on edit pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailWidthSiteLogoEdit']) ? intval($this->moduleVars['thumbnailWidthSiteLogoEdit']) : intval(240),
                'empty_data' => intval('240'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail width edit.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailHeightSiteLogoEdit', IntegerType::class, [
                'label' => $this->__('Thumbnail height edit') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail height on edit pages in pixels.')
                ],
                'help' => $this->__('Thumbnail height on edit pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailHeightSiteLogoEdit']) ? intval($this->moduleVars['thumbnailHeightSiteLogoEdit']) : intval(180),
                'empty_data' => intval('180'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail height edit.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('enableShrinkingForSiteFavIcon', CheckboxType::class, [
                'label' => $this->__('Enable shrinking') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.')
                ],
                'help' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
                'required' => false,
                'data' => (bool)(isset($this->moduleVars['enableShrinkingForSiteFavIcon']) ? $this->moduleVars['enableShrinkingForSiteFavIcon'] : false),
                'attr' => [
                    'title' => $this->__('The enable shrinking option.'),
                    'class' => 'shrink-enabler'
                ],
            ])
            ->add('shrinkWidthSiteFavIcon', IntegerType::class, [
                'label' => $this->__('Shrink width') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The maximum image width in pixels.')
                ],
                'help' => $this->__('The maximum image width in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['shrinkWidthSiteFavIcon']) ? intval($this->moduleVars['shrinkWidthSiteFavIcon']) : intval(800),
                'empty_data' => intval('800'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the shrink width.') . ' ' . $this->__('Only digits are allowed.'),
                    'class' => 'shrinkdimension-shrinkwidthsitefavicon'
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('shrinkHeightSiteFavIcon', IntegerType::class, [
                'label' => $this->__('Shrink height') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('The maximum image height in pixels.')
                ],
                'help' => $this->__('The maximum image height in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['shrinkHeightSiteFavIcon']) ? intval($this->moduleVars['shrinkHeightSiteFavIcon']) : intval(600),
                'empty_data' => intval('600'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the shrink height.') . ' ' . $this->__('Only digits are allowed.'),
                    'class' => 'shrinkdimension-shrinkheightsitefavicon'
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailModeSiteFavIcon', ChoiceType::class, [
                'label' => $this->__('Thumbnail mode') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail mode (inset or outbound).')
                ],
                'help' => $this->__('Thumbnail mode (inset or outbound).'),
                'data' => isset($this->moduleVars['thumbnailModeSiteFavIcon']) ? $this->moduleVars['thumbnailModeSiteFavIcon'] : '',
                'empty_data' => 'inset',
                'attr' => [
                    'title' => $this->__('Choose the thumbnail mode.')
                ],'choices' => [
                    $this->__('Inset') => 'inset',
                    $this->__('Outbound') => 'outbound'
                ],
                'multiple' => false
            ])
            ->add('thumbnailWidthSiteFavIconView', IntegerType::class, [
                'label' => $this->__('Thumbnail width view') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail width on view pages in pixels.')
                ],
                'help' => $this->__('Thumbnail width on view pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailWidthSiteFavIconView']) ? intval($this->moduleVars['thumbnailWidthSiteFavIconView']) : intval(32),
                'empty_data' => intval('32'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail width view.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailHeightSiteFavIconView', IntegerType::class, [
                'label' => $this->__('Thumbnail height view') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail height on view pages in pixels.')
                ],
                'help' => $this->__('Thumbnail height on view pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailHeightSiteFavIconView']) ? intval($this->moduleVars['thumbnailHeightSiteFavIconView']) : intval(24),
                'empty_data' => intval('24'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail height view.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailWidthSiteFavIconEdit', IntegerType::class, [
                'label' => $this->__('Thumbnail width edit') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail width on edit pages in pixels.')
                ],
                'help' => $this->__('Thumbnail width on edit pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailWidthSiteFavIconEdit']) ? intval($this->moduleVars['thumbnailWidthSiteFavIconEdit']) : intval(240),
                'empty_data' => intval('240'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail width edit.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
            ->add('thumbnailHeightSiteFavIconEdit', IntegerType::class, [
                'label' => $this->__('Thumbnail height edit') . ':',
                'label_attr' => [
                    'class' => 'tooltips',
                    'title' => $this->__('Thumbnail height on edit pages in pixels.')
                ],
                'help' => $this->__('Thumbnail height on edit pages in pixels.'),
                'required' => false,
                'data' => isset($this->moduleVars['thumbnailHeightSiteFavIconEdit']) ? intval($this->moduleVars['thumbnailHeightSiteFavIconEdit']) : intval(180),
                'empty_data' => intval('180'),
                'attr' => [
                    'maxlength' => 4,
                    'title' => $this->__('Enter the thumbnail height edit.') . ' ' . $this->__('Only digits are allowed.')
                ],'scale' => 0,
                'input_group' => ['right' => $this->__('pixels')]
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulamultisitesmodule_config';
    }
}