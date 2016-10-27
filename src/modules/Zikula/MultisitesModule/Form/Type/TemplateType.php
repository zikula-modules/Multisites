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

use Zikula\MultisitesModule\Form\Type\Base\AbstractTemplateType;

use ModUtil;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Template editing form type implementation class.
 */
class TemplateType extends AbstractTemplateType
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

        list($templateChoices, $templateId) = $this->getExistingTemplateDetails($options);
        $builder->add('sqlFileSelected', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('or select an existing file') . ':',
            'mapped' => false,
            'empty_data' => $templateId,
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose an existing template file')
            ],
            'required' => false,
            'placeholder' => '',
            'choices' => $templateChoices,
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false
        ]);

        $builder->add('folders', 'Zikula\MultisitesModule\Form\Type\Field\ArrayType', [
            'label' => $this->__('Folders') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter additional folders of the template')
            ],
            'required' => false
        ]);
        $builder->add('excludedTables', 'Zikula\MultisitesModule\Form\Type\Field\ArrayType', [
            'label' => $this->__('Excluded tables') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter table names to be excluded from reapplications')
            ],
            'required' => false
        ]);
        $builder->add('parameters', 'Zikula\MultisitesModule\Form\Type\Field\ArrayType', [
            'label' => $this->__('Parameters') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter required parameter names for this template')
            ],
            'required' => false
        ]);
    }

    /**
     * Returns a list of existing template files.
     *
     * @param array $options The options.
     *
     * @return array List of choices and preselected entry id.
     */
    private function getExistingTemplateDetails(array $options)
    {
        // build distinct list of all existing sql files
        $templates = ModUtil::apiFunc('ZikulaMultisitesModule', 'selection', 'getEntities', ['ot' => 'template', 'useJoins' => false]);

        $sqlFiles = [];
        $sqlFileSelected = 0;
        foreach ($templates as $template) {
            if (in_array($template['sqlFile'], $sqlFiles)) {
                continue;
            }
            $sqlFiles[$template['id']] = $template['sqlFile'];
        }

        // TODO review
        /*$entity = $this->entityRef;
        if ($options['mode'] != 'create') {
            $sqlFileSelected = $entity['id'];
            // ensure own id is used in sql file list
            foreach ($sqlFiles as $id => $sqlFile) {
                if ($sqlFile != $entity['sqlFile']) {
                    continue;
                }
                if ($id != $sqlFileSelected) {
                    unset($sqlFiles[$id]);
                    $sqlFiles[$sqlFileSelected] = $sqlFile;
                }
                break;
            }
        } elseif ($this->hasTemplateId === true) {
            // creation based on reuse
            foreach ($sqlFiles as $id => $sqlFile) {
                if ($sqlFile != $entity['sqlFile']) {
                    continue;
                }
                $sqlFileSelected = $id;
                break;
            }
        }*/

        $choices = [];
        $choices[$this->__('Select an existing file...')] = 0;
        foreach ($sqlFiles as $id => $sqlFile) {
            $choices[$sqlFile] = $id;
        }

        return [$choices, $sqlFileSelected];
    }
}
