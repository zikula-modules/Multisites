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

namespace Zikula\MultisitesModule\HookSubscriber\Base;

use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Base class for form aware hook subscriber.
 */
abstract class AbstractTemplateFormAwareHookSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getOwner()
    {
        return 'ZikulaMultisitesModule';
    }
    
    public function getCategory()
    {
        return FormAwareCategory::NAME;
    }
    
    public function getTitle()
    {
        return $this->translator->__('Template form aware subscriber');
    }

    public function getEvents()
    {
        return [
            // Display hook for create/edit forms.
            FormAwareCategory::TYPE_EDIT => 'zikulamultisitesmodule.form_aware_hook.templates.edit',
            // Process the results of the edit form after the main form is processed.
            FormAwareCategory::TYPE_PROCESS_EDIT => 'zikulamultisitesmodule.form_aware_hook.templates.process_edit'
        ];
    }
}
