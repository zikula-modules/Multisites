{* purpose of this template: build the Form to edit an instance of template *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
{pageaddvar name='javascript' value='modules/Multisites/javascript/Multisites_editFunctions.js'}
{pageaddvar name='javascript' value='modules/Multisites/javascript/Multisites_validation.js'}

{if $mode eq 'edit'}
    {gt text='Edit template' assign='templateTitle'}
    {if $lct eq 'admin'}
        {assign var='adminPageIcon' value='edit'}
    {/if}
{elseif $mode eq 'create'}
    {gt text='Create template' assign='templateTitle'}
    {if $lct eq 'admin'}
        {assign var='adminPageIcon' value='new'}
    {/if}
{else}
    {gt text='Edit template' assign='templateTitle'}
    {if $lct eq 'admin'}
        {assign var='adminPageIcon' value='edit'}
    {/if}
{/if}
<div class="multisites-template multisites-edit">
    {pagesetvar name='title' value=$templateTitle}
    {if $lct eq 'admin'}
        <div class="z-admin-content-pagetitle">
            {icon type=$adminPageIcon size='small' alt=$templateTitle}
            <h3>{$templateTitle}</h3>
        </div>
    {else}
        <h2>{$templateTitle}</h2>
    {/if}
{form enctype='multipart/form-data' cssClass='z-form'}
    {* add validation summary and a <div> element for styling the form *}
    {multisitesFormFrame}
    {formsetinitialfocus inputId='name'}

    <fieldset>
        <legend>{gt text='Content'}</legend>
        
        <div class="z-formrow">
            {formlabel for='name' __text='Name' mandatorysym='1' cssClass=''}
            {formtextinput group='template' id='name' mandatory=true readOnly=false __title='Enter the name of the template' textMode='singleline' maxLength=150 cssClass='required' }
            {multisitesValidationError id='name' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='description' __text='Description' cssClass=''}
            {formtextinput group='template' id='description' mandatory=false readOnly=false __title='Enter the description of the template' textMode='singleline' maxLength=250 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {assign var='mandatorySym' value='1'}
            {if $mode ne 'create'}
                {assign var='mandatorySym' value='0'}
            {/if}
            {formlabel for='sqlFile' __text='Sql file' mandatorysym=$mandatorySym cssClass=''}<br />{* break required for Google Chrome *}
            {if $mode eq 'create'}
                {formuploadinput group='template' id='sqlFile' mandatory=true readOnly=false cssClass='required validate-upload' }
            {else}
                {formuploadinput group='template' id='sqlFile' mandatory=false readOnly=false cssClass=' validate-upload' }
                <span class="z-formnote"><a id="resetSqlFileVal" href="javascript:void(0);" class="z-hide">{gt text='Reset to empty value'}</a></span>
            {/if}
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="sqlFileFileExtensions">sql, txt</span></span>
            {if $mode ne 'create'}
                {if $template.sqlFile ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$template.sqlFileFullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $template.sqlFileMeta.isImage} rel="imageviewer[template]"{/if}>
                        {if $template.sqlFileMeta.isImage}
                            {thumb image=$template.sqlFileFullPath objectid="template-`$template.id`" preset=$templateThumbPresetSqlFile tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$template.sqlFileMeta.size|multisitesGetFileSize:$template.sqlFileFullPath:false:false})
                        {/if}
                        </a>
                    </span>
                {/if}
            {/if}
            {multisitesValidationError id='sqlFile' class='required'}
            {multisitesValidationError id='sqlFile' class='validate-upload'}
        </div>
    </fieldset>
    
    {include file='project/include_selectMany.tpl' group='template' alias='projects' aliasReverse='templates' mandatory=false idPrefix='multisitesTemplate_Projects' linkingItem=$template displayMode='dropdown' allowEditing=false}
    {if $mode ne 'create'}
        {include file='helper/include_standardfields_edit.tpl' obj=$template}
    {/if}
    
    {* include display hooks *}
    {if $mode ne 'create'}
        {assign var='hookId' value=$template.id}
        {notifydisplayhooks eventname='multisites.ui_hooks.templates.form_edit' id=$hookId assign='hooks'}
    {else}
        {notifydisplayhooks eventname='multisites.ui_hooks.templates.form_edit' id=null assign='hooks'}
    {/if}
    {if is_array($hooks) && count($hooks)}
        {foreach name='hookLoop' key='providerArea' item='hook' from=$hooks}
            <fieldset>
                {$hook}
            </fieldset>
        {/foreach}
    {/if}
    
    
    {* include return control *}
    {if $mode eq 'create'}
        <fieldset>
            <legend>{gt text='Return control'}</legend>
            <div class="z-formrow">
                {formlabel for='repeatCreation' __text='Create another item after save'}
                    {formcheckbox group='template' id='repeatCreation' readOnly=false}
            </div>
        </fieldset>
    {/if}
    
    {* include possible submit actions *}
    <div class="z-buttons z-formbuttons">
    {foreach item='action' from=$actions}
        {assign var='actionIdCapital' value=$action.id|@ucfirst}
        {gt text=$action.title assign='actionTitle'}
        {*gt text=$action.description assign='actionDescription'*}{* TODO: formbutton could support title attributes *}
        {if $action.id eq 'delete'}
            {gt text='Really delete this template?' assign='deleteConfirmMsg'}
            {formbutton id="btn`$actionIdCapital`" commandName=$action.id text=$actionTitle class=$action.buttonClass confirmMessage=$deleteConfirmMsg}
        {else}
            {formbutton id="btn`$actionIdCapital`" commandName=$action.id text=$actionTitle class=$action.buttonClass}
        {/if}
    {/foreach}
    {formbutton id='btnCancel' commandName='cancel' __text='Cancel' class='z-bt-cancel'}
    </div>
    {/multisitesFormFrame}
{/form}
</div>
{include file="`$lct`/footer.tpl"}

{icon type='edit' size='extrasmall' assign='editImageArray'}
{icon type='delete' size='extrasmall' assign='removeImageArray'}


<script type="text/javascript">
/* <![CDATA[ */
    
    var formButtons, formValidator;
    
    function handleFormButton (event) {
        var result = formValidator.validate();
        if (!result) {
            // validation error, abort form submit
            Event.stop(event);
        } else {
            // hide form buttons to prevent double submits by accident
            formButtons.each(function (btn) {
                btn.addClassName('z-hide');
            });
        }
    
        return result;
    }
    
    document.observe('dom:loaded', function() {
    
        multisitesAddCommonValidationRules('template', '{{if $mode ne 'create'}}{{$template.id}}{{/if}}');
        {{* observe validation on button events instead of form submit to exclude the cancel command *}}
        formValidator = new Validation('{{$__formid}}', {onSubmit: false, immediate: true, focusOnError: false});
        {{if $mode ne 'create'}}
            var result = formValidator.validate();
        {{/if}}
    
        formButtons = $('{{$__formid}}').select('div.z-formbuttons input');
    
        formButtons.each(function (elem) {
            if (elem.id != 'btnCancel') {
                elem.observe('click', handleFormButton);
            }
        });
    
        Zikula.UI.Tooltips($$('.multisites-form-tooltips'));
        multisitesInitUploadField('sqlFile');
    });
/* ]]> */
</script>
