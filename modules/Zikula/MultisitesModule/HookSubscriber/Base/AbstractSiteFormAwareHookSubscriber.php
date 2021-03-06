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

namespace Zikula\MultisitesModule\HookSubscriber\Base;

use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Base class for form aware hook subscriber.
 */
abstract class AbstractSiteFormAwareHookSubscriber implements HookSubscriberInterface
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
        return $this->translator->__('Site form aware subscriber');
    }
    
    public function getAreaName()
    {
        return 'subscriber.zikulamultisitesmodule.form_aware_hook.sites';
    }

    public function getEvents()
    {
        return [
            // Display hook for create/edit forms.
            FormAwareCategory::TYPE_EDIT => 'zikulamultisitesmodule.form_aware_hook.sites.edit',
            // Process the results of the edit form after the main form is processed.
            FormAwareCategory::TYPE_PROCESS_EDIT => 'zikulamultisitesmodule.form_aware_hook.sites.process_edit',
            // Display hook for delete forms.
            FormAwareCategory::TYPE_DELETE => 'zikulamultisitesmodule.form_aware_hook.sites.delete',
            // Process the results of the delete form after the main form is processed.
            FormAwareCategory::TYPE_PROCESS_DELETE => 'zikulamultisitesmodule.form_aware_hook.sites.process_delete',
        ];
    }
}
