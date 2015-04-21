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
            {formtextinput group='template' id='description' mandatory=false readOnly=false __title='Enter the description of the template' textMode='multiline' rows=7 maxLength=250 cssClass='' }
        </div>
    </fieldset>

    <fieldset>
        <legend>{gt text='Template data'}</legend>
        
        <div class="z-formrow">
            {formlabel for='sqlFile' __text='Sql file' mandatorysym='0' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='template' id='sqlFile' mandatory=false readOnly=false cssClass='validate-upload'}
            {if $mode ne 'create'}
                <span class="z-formnote z-hide"><a id="resetSqlFileVal" href="javascript:void(0);" class="z-hide">{gt text='Reset to empty value'}</a></span>
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
        <div class="z-formrow">
            {formlabel for='sqlFileSelected' __text='or select an existing file' mandatorySym='0' cssClass=''}
            {formdropdownlist group='additions' id='sqlFileSelected' mandatory=false readOnly=false}
        </div>
    </fieldset>

    {include file='project/include_selectMany.tpl' group='template' alias='projects' aliasReverse='templates' mandatory=false idPrefix='multisitesTemplate_Projects' linkingItem=$template displayMode='dropdown' allowEditing=false}

    <fieldset>
        <legend>{gt text='Advanced options'}</legend>
        <div class="z-formrow">
            {formlabel for='folders' __text='Folders' cssClass=''}
            {multisitesArrayInput group='template' id='folders' mandatory=false readOnly=false __title='Enter additional folders of the template' rows=7 cssClass=''}
            <span class="z-informationmsg z-formnote">{gt text='Enter the folders to be created for new sites separated by line breaks. If you need a folder within another one you can write expressions like "folder/folder".'}</span>
        </div>
        <div class="z-formrow">
            {formlabel for='excludedTables' __text='Excluded tables' cssClass=''}
            {multisitesArrayInput group='template' id='excludedTables' mandatory=false readOnly=false __title='Enter table names to be excluded from reapplications' rows=7 cssClass=''}
            <span class="z-informationmsg z-formnote">{gt text='Enter the names of database tables which should be skipped during template reapplications separated by line breaks. With this you can for example avoid overriding your local user table. Note you can use * as a placeholder, like content_* for all Content tables for only * for all tables; ensure to use this if you want to use a template for different sites without any parameters, otherwise you will end up with overriding your data later on when the template is reapplied.'}</span>
        </div>
        <div class="z-formrow">
            {formlabel for='parameters' __text='Parameters' mandatorySym='0' cssClass=''}
            {multisitesArrayInput group='template' id='parameters' mandatory=false readOnly=false __title='Enter required parameter names for this template' rows=7 cssClass=''}
            <span class="z-informationmsg z-formnote">{gt text='Enter parameter names separated by line breaks. Each parameter represents a variable information which is being replaced by concrete values when creating a new site or reapplying the template on existing sites. The parameter names can be used as placeholders anywhere in the template data accordingly.'}</span>
            <span class="z-informationmsg z-formnote">{gt text='Placeholder syntax: ###PARAMETERNAME###'}</span>
        </div>
    </fieldset>

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
            {gt text='Really delete this template? This includes deletion of assigned parameters and sites, too!' assign='deleteConfirmMsg'}
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

        $('parameters').observe('keyup', function() {
            $('parameters').value = $('parameters').value.toUpperCase();
        });
    });
/* ]]> */
</script>
