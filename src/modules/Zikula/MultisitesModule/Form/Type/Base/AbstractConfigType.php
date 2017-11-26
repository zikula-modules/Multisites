<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.2.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Type\Base;

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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\MultisitesModule\AppSettings;
use Zikula\MultisitesModule\Helper\ListEntriesHelper;

/**
 * Configuration form type base class.
 */
abstract class AbstractConfigType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * ConfigType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     * @param ListEntriesHelper $listHelper ListEntriesHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper
    ) {
        $this->setTranslator($translator);
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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addSecuritySettingsFields($builder, $options);
        $this->addListViewsFields($builder, $options);
        $this->addImagesFields($builder, $options);

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds fields for security settings fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSecuritySettingsFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('globalAdminName', TextType::class, [
            'label' => $this->__('Global admin name') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the global admin name')
            ],
            'required' => true,
        ]);
        
        $builder->add('globalAdminPassword', PasswordType::class, [
            'label' => $this->__('Global admin password') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the global admin password')
            ],
            'required' => true,
        ]);
        
        $builder->add('globalAdminEmail', EmailType::class, [
            'label' => $this->__('Global admin email') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the global admin email')
            ],
            'required' => true,
        ]);
    }

    /**
     * Adds fields for list views fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('siteEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Site entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of sites shown per page')
            ],
            'help' => $this->__('The amount of sites shown per page'),
            'empty_data' => '10',
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the site entries per page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
        
        $builder->add('templateEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Template entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of templates shown per page')
            ],
            'help' => $this->__('The amount of templates shown per page'),
            'empty_data' => '10',
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the template entries per page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
        
        $builder->add('projectEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Project entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of projects shown per page')
            ],
            'help' => $this->__('The amount of projects shown per page'),
            'empty_data' => '10',
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the project entries per page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
    }

    /**
     * Adds fields for images fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addImagesFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('enableShrinkingForSiteLogo', CheckboxType::class, [
            'label' => $this->__('Enable shrinking for site logo') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.')
            ],
            'help' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
            'attr' => [
        ,
                    'class' => 'shrink-enabler',
                'title' => $this->__('The enable shrinking option')
            ],
            'required' => false,
        ]);
        
        $builder->add('shrinkWidthSiteLogo', IntegerType::class, [
            'label' => $this->__('Shrink width site logo') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image width in pixels.')
            ],
            'help' => $this->__('The maximum image width in pixels.'),
            'empty_data' => '800',
            'attr' => [
                'maxlength' => 4,
                'class' => 'shrinkdimension-shrinkwidthsitelogo',
                                    'title' => $this->__('Enter the shrink width')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('shrinkHeightSiteLogo', IntegerType::class, [
            'label' => $this->__('Shrink height site logo') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image height in pixels.')
            ],
            'help' => $this->__('The maximum image height in pixels.'),
            'empty_data' => '600',
            'attr' => [
                'maxlength' => 4,
                'class' => 'shrinkdimension-shrinkheightsitelogo',
                                    'title' => $this->__('Enter the shrink height')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'thumbnailModeSiteLogo');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('thumbnailModeSiteLogo', ChoiceType::class, [
            'label' => $this->__('Thumbnail mode site logo') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail mode (inset or outbound).')
            ],
            'help' => $this->__('Thumbnail mode (inset or outbound).'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the thumbnail mode.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $builder->add('thumbnailWidthSiteLogoView', IntegerType::class, [
            'label' => $this->__('Thumbnail width site logo view') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on view pages in pixels.')
            ],
            'help' => $this->__('Thumbnail width on view pages in pixels.'),
            'empty_data' => '32',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width view')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('thumbnailHeightSiteLogoView', IntegerType::class, [
            'label' => $this->__('Thumbnail height site logo view') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on view pages in pixels.')
            ],
            'help' => $this->__('Thumbnail height on view pages in pixels.'),
            'empty_data' => '24',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height view')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('thumbnailWidthSiteLogoEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail width site logo edit') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on edit pages in pixels.')
            ],
            'help' => $this->__('Thumbnail width on edit pages in pixels.'),
            'empty_data' => '240',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width edit')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('thumbnailHeightSiteLogoEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail height site logo edit') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on edit pages in pixels.')
            ],
            'help' => $this->__('Thumbnail height on edit pages in pixels.'),
            'empty_data' => '180',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height edit')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('enableShrinkingForSiteFavIcon', CheckboxType::class, [
            'label' => $this->__('Enable shrinking for site fav icon') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.')
            ],
            'help' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
            'attr' => [
        ,
                    'class' => 'shrink-enabler',
                'title' => $this->__('The enable shrinking option')
            ],
            'required' => false,
        ]);
        
        $builder->add('shrinkWidthSiteFavIcon', IntegerType::class, [
            'label' => $this->__('Shrink width site fav icon') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image width in pixels.')
            ],
            'help' => $this->__('The maximum image width in pixels.'),
            'empty_data' => '800',
            'attr' => [
                'maxlength' => 4,
                'class' => 'shrinkdimension-shrinkwidthsitefavicon',
                                    'title' => $this->__('Enter the shrink width')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('shrinkHeightSiteFavIcon', IntegerType::class, [
            'label' => $this->__('Shrink height site fav icon') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image height in pixels.')
            ],
            'help' => $this->__('The maximum image height in pixels.'),
            'empty_data' => '600',
            'attr' => [
                'maxlength' => 4,
                'class' => 'shrinkdimension-shrinkheightsitefavicon',
                                    'title' => $this->__('Enter the shrink height')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'thumbnailModeSiteFavIcon');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('thumbnailModeSiteFavIcon', ChoiceType::class, [
            'label' => $this->__('Thumbnail mode site fav icon') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail mode (inset or outbound).')
            ],
            'help' => $this->__('Thumbnail mode (inset or outbound).'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the thumbnail mode.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $builder->add('thumbnailWidthSiteFavIconView', IntegerType::class, [
            'label' => $this->__('Thumbnail width site fav icon view') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on view pages in pixels.')
            ],
            'help' => $this->__('Thumbnail width on view pages in pixels.'),
            'empty_data' => '32',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width view')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('thumbnailHeightSiteFavIconView', IntegerType::class, [
            'label' => $this->__('Thumbnail height site fav icon view') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on view pages in pixels.')
            ],
            'help' => $this->__('Thumbnail height on view pages in pixels.'),
            'empty_data' => '24',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height view')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('thumbnailWidthSiteFavIconEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail width site fav icon edit') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on edit pages in pixels.')
            ],
            'help' => $this->__('Thumbnail width on edit pages in pixels.'),
            'empty_data' => '240',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width edit')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
        ]);
        
        $builder->add('thumbnailHeightSiteFavIconEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail height site fav icon edit') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on edit pages in pixels.')
            ],
            'help' => $this->__('Thumbnail height on edit pages in pixels.'),
            'empty_data' => '180',
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height edit')
            ],
            'required' => true,
            'scale' => 0,
            'input_group' => ['right' => $this->__('pixels')]
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
        $builder->add('save', SubmitType::class, [
            'label' => $this->__('Update configuration'),
            'icon' => 'fa-check',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
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
        return 'zikulamultisitesmodule_config';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data
                'data_class' => AppSettings::class,
            ]);
    }
}
