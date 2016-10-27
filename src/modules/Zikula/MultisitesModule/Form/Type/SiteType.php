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

namespace Zikula\MultisitesModule\Form\Type;

use Zikula\MultisitesModule\Form\Type\Base\AbstractSiteType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Site editing form type implementation class.
 */
class SiteType extends AbstractSiteType
{
    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The options.
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        parent::addEntityFields($builder, $options);

        $builder->add('databaseType', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Database type') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the database type of the site')
            ],
            'required' => true,
            'placeholder' => '',
            'choices' => [
                $this->__('MySQL') => 'mysql',
                $this->__('MySQL Improved') => 'mysqli',
                $this->__('PostgreSQL') => 'postgres',
                //$this->__('Oracle') => 'oci'
            ],
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false
        ]);
        $builder->add('allowedLocales', 'Zikula\MultisitesModule\Form\Type\Field\ArrayType', [
            'label' => $this->__('Allowed locales') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the allowed locales of the site')
            ],
            'required' => false
        ]);
        $builder->add('parametersArray', 'Zikula\MultisitesModule\Form\Type\Field\ArrayType', [
            'label' => $this->__('or enter them manually'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter values for the template parameters')
            ],
            'required' => false
        ]);
    }
}
