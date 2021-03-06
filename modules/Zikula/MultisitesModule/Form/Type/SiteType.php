<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\MultisitesModule\Form\Type\Field\ArrayType;
use Zikula\MultisitesModule\Form\Type\Base\AbstractSiteType;
use Zikula\UsersModule\Validator\Constraints\ValidUname;

/**
 * Site editing form type implementation class.
 */
class SiteType extends AbstractSiteType
{
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        parent::addEntityFields($builder, $options);

        $builder->add('siteAdminName', TextType::class, [
            'label' => $this->__('Site admin name') . ':',
            'empty_data' => 'admin',
            'attr' => [
                'maxlength' => 25,
                'class' => '',
                'title' => $this->__('Enter the site admin name of the site')
            ],
            'required' => true,
            'constraints' => [new ValidUname()]
        ]);

        $builder->add('databaseType', ChoiceType::class, [
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
                $this->__('PostgreSQL') => 'postgres',
                //$this->__('Oracle') => 'oci'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
        $builder->add('allowedLocales', ArrayType::class, [
            'label' => $this->__('Allowed locales') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the allowed locales of the site')
            ],
            'required' => false
        ]);
        $builder->add('parametersArray', ArrayType::class, [
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
