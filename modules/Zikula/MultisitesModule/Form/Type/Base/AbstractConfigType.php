<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 *
 * @see https://modulestudio.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
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
use Zikula\Bundle\FormExtensionBundle\Form\DataTransformer\NullToEmptyTransformer;
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

    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper
    ) {
        $this->setTranslator($translator);
        $this->listHelper = $listHelper;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addSecuritySettingsFields($builder, $options);
        $this->addListViewsFields($builder, $options);
        $this->addImagesFields($builder, $options);
        $this->addModerationFields($builder, $options);

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds fields for security settings fields.
     */
    public function addSecuritySettingsFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('globalAdminName', TextType::class, [
            'label' => $this->__('Global admin name:'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the global admin name.'),
            ],
            'required' => true,
        ]);
        $builder->add('globalAdminPassword', PasswordType::class, [
            'label' => $this->__('Global admin password:'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the global admin password.'),
            ],
            'required' => true,
        ]);
        $builder->add('globalAdminEmail', EmailType::class, [
            'label' => $this->__('Global admin email:'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the global admin email.'),
            ],
            'required' => true,
        ]);
    }

    /**
     * Adds fields for list views fields.
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('siteEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Site entries per page:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of sites shown per page.'),
            ],
            'help' => $this->__('The amount of sites shown per page.'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the site entries per page. Only digits are allowed.'),
            ],
            'required' => true,
        ]);
        $builder->add('templateEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Template entries per page:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of templates shown per page.'),
            ],
            'help' => $this->__('The amount of templates shown per page.'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the template entries per page. Only digits are allowed.'),
            ],
            'required' => true,
        ]);
        $builder->add('projectEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Project entries per page:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of projects shown per page.'),
            ],
            'help' => $this->__('The amount of projects shown per page.'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the project entries per page. Only digits are allowed.'),
            ],
            'required' => true,
        ]);
        $builder->add($builder->create('showOnlyOwnEntries', CheckboxType::class, [
            'label' => $this->__('Show only own entries:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether only own entries should be shown on view pages by default or not.'),
            ],
            'help' => $this->__('Whether only own entries should be shown on view pages by default or not.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The show only own entries option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
    }

    /**
     * Adds fields for images fields.
     */
    public function addImagesFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add($builder->create('enableShrinkingForSiteLogo', CheckboxType::class, [
            'label' => $this->__('Enable shrinking:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
            ],
            'help' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
            'attr' => [
                'class' => 'shrink-enabler',
                'title' => $this->__('The enable shrinking option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add('shrinkWidthSiteLogo', IntegerType::class, [
            'label' => $this->__('Shrink width:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image width in pixels.'),
            ],
            'help' => $this->__('The maximum image width in pixels.'),
            'empty_data' => 800,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the shrink width'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('shrinkHeightSiteLogo', IntegerType::class, [
            'label' => $this->__('Shrink height:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image height in pixels.'),
            ],
            'help' => $this->__('The maximum image height in pixels.'),
            'empty_data' => 600,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the shrink height'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $listEntries = $this->listHelper->getEntries('appSettings', 'thumbnailModeSiteLogo');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('thumbnailModeSiteLogo', ChoiceType::class, [
            'label' => $this->__('Thumbnail mode:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail mode (inset or outbound).'),
            ],
            'help' => $this->__('Thumbnail mode (inset or outbound).'),
            'empty_data' => 'inset',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the thumbnail mode.'),
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false,
        ]);
        $builder->add('thumbnailWidthSiteLogoView', IntegerType::class, [
            'label' => $this->__('Thumbnail width list:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on view pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail width on view pages in pixels.'),
            'empty_data' => 32,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width view'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('thumbnailHeightSiteLogoView', IntegerType::class, [
            'label' => $this->__('Thumbnail height list:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on view pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail height on view pages in pixels.'),
            'empty_data' => 24,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height view'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('thumbnailWidthSiteLogoEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail width edit:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on edit pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail width on edit pages in pixels.'),
            'empty_data' => 240,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width edit'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('thumbnailHeightSiteLogoEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail height edit:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on edit pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail height on edit pages in pixels.'),
            'empty_data' => 180,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height edit'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add($builder->create('enableShrinkingForSiteFavIcon', CheckboxType::class, [
            'label' => $this->__('Enable shrinking:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
            ],
            'help' => $this->__('Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.'),
            'attr' => [
                'class' => 'shrink-enabler',
                'title' => $this->__('The enable shrinking option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add('shrinkWidthSiteFavIcon', IntegerType::class, [
            'label' => $this->__('Shrink width:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image width in pixels.'),
            ],
            'help' => $this->__('The maximum image width in pixels.'),
            'empty_data' => 800,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the shrink width'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('shrinkHeightSiteFavIcon', IntegerType::class, [
            'label' => $this->__('Shrink height:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The maximum image height in pixels.'),
            ],
            'help' => $this->__('The maximum image height in pixels.'),
            'empty_data' => 600,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the shrink height'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $listEntries = $this->listHelper->getEntries('appSettings', 'thumbnailModeSiteFavIcon');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('thumbnailModeSiteFavIcon', ChoiceType::class, [
            'label' => $this->__('Thumbnail mode:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail mode (inset or outbound).'),
            ],
            'help' => $this->__('Thumbnail mode (inset or outbound).'),
            'empty_data' => 'inset',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the thumbnail mode.'),
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false,
        ]);
        $builder->add('thumbnailWidthSiteFavIconView', IntegerType::class, [
            'label' => $this->__('Thumbnail width list:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on view pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail width on view pages in pixels.'),
            'empty_data' => 32,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width view'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('thumbnailHeightSiteFavIconView', IntegerType::class, [
            'label' => $this->__('Thumbnail height list:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on view pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail height on view pages in pixels.'),
            'empty_data' => 24,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height view'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('thumbnailWidthSiteFavIconEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail width edit:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail width on edit pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail width on edit pages in pixels.'),
            'empty_data' => 240,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail width edit'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
        $builder->add('thumbnailHeightSiteFavIconEdit', IntegerType::class, [
            'label' => $this->__('Thumbnail height edit:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Thumbnail height on edit pages in pixels.'),
            ],
            'help' => $this->__('Thumbnail height on edit pages in pixels.'),
            'empty_data' => 180,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'title' => $this->__('Enter the thumbnail height edit'),
            ],
            'required' => true,
            'input_group' => ['right' => $this->__('pixels')],
        ]);
    }

    /**
     * Adds fields for moderation fields.
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add($builder->create('allowModerationSpecificCreatorForSite', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creator for site:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            ],
            'help' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creator for site option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add($builder->create('allowModerationSpecificCreationDateForSite', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creation date for site:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            ],
            'help' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creation date for site option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add($builder->create('allowModerationSpecificCreatorForTemplate', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creator for template:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            ],
            'help' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creator for template option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add($builder->create('allowModerationSpecificCreationDateForTemplate', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creation date for template:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            ],
            'help' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creation date for template option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add($builder->create('allowModerationSpecificCreatorForProject', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creator for project:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            ],
            'help' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creator for project option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
        $builder->add($builder->create('allowModerationSpecificCreationDateForProject', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creation date for project:'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            ],
            'help' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creation date for project option'),
            ],
            'required' => false,
        ])->addModelTransformer(new NullToEmptyTransformer()));
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('save', SubmitType::class, [
            'label' => $this->__('Update configuration'),
            'icon' => 'fa-check',
            'attr' => [
                'class' => 'btn btn-success',
            ],
        ]);
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate',
            ],
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate',
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulamultisitesmodule_config';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // define class for underlying data
            'data_class' => AppSettings::class,
        ]);
    }
}
