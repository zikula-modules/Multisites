{* purpose of this template: build the Form to edit an instance of site *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
{pageaddvar name='javascript' value='modules/Multisites/javascript/Multisites_editFunctions.js'}
{pageaddvar name='javascript' value='modules/Multisites/javascript/Multisites_validation.js'}

{if $mode eq 'edit'}
    {gt text='Edit site' assign='templateTitle'}
    {if $lct eq 'admin'}
        {assign var='adminPageIcon' value='edit'}
    {/if}
{elseif $mode eq 'create'}
    {gt text='Create site' assign='templateTitle'}
    {if $lct eq 'admin'}
        {assign var='adminPageIcon' value='new'}
    {/if}
{else}
    {gt text='Edit site' assign='templateTitle'}
    {if $lct eq 'admin'}
        {assign var='adminPageIcon' value='edit'}
    {/if}
{/if}
<div class="multisites-site multisites-edit">
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
            {formtextinput group='site' id='name' mandatory=true readOnly=false __title='Enter the name of the site' textMode='singleline' maxLength=150 cssClass='required' }
            {multisitesValidationError id='name' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='description' __text='Description' cssClass=''}
            {formtextinput group='site' id='description' mandatory=false readOnly=false __title='Enter the description of the site' textMode='singleline' maxLength=255 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAlias' __text='Site alias' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='siteAlias' mandatory=true readOnly=false __title='Enter the site alias of the site' textMode='singleline' maxLength=80 cssClass='required' }
            {multisitesValidationError id='siteAlias' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteName' __text='Site name' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='siteName' mandatory=true readOnly=false __title='Enter the site name of the site' textMode='singleline' maxLength=150 cssClass='required' }
            {multisitesValidationError id='siteName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteDescription' __text='Site description' cssClass=''}
            {formtextinput group='site' id='siteDescription' mandatory=false readOnly=false __title='Enter the site description of the site' textMode='singleline' maxLength=255 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAdminName' __text='Site admin name' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='siteAdminName' mandatory=true readOnly=false __title='Enter the site admin name of the site' textMode='singleline' maxLength=25 cssClass='required' }
            {multisitesValidationError id='siteAdminName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAdminPassword' __text='Site admin password' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='siteAdminPassword' mandatory=true readOnly=false __title='Enter the site admin password of the site' textMode='password' maxLength=15 cssClass='required' }
            {multisitesValidationError id='siteAdminPassword' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAdminRealName' __text='Site admin real name' cssClass=''}
            {formtextinput group='site' id='siteAdminRealName' mandatory=false readOnly=false __title='Enter the site admin real name of the site' textMode='singleline' maxLength=70 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAdminEmail' __text='Site admin email' mandatorysym='1' cssClass=''}
                {formemailinput group='site' id='siteAdminEmail' mandatory=true readOnly=false __title='Enter the site admin email of the site' textMode='singleline' maxLength=40 cssClass='required validate-email' }
            {multisitesValidationError id='siteAdminEmail' class='required'}
            {multisitesValidationError id='siteAdminEmail' class='validate-email'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteCompany' __text='Site company' cssClass=''}
            {formtextinput group='site' id='siteCompany' mandatory=false readOnly=false __title='Enter the site company of the site' textMode='singleline' maxLength=100 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteDns' __text='Site dns' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='siteDns' mandatory=true readOnly=false __title='Enter the site dns of the site' textMode='singleline' maxLength=255 cssClass='required' }
            {multisitesValidationError id='siteDns' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databaseName' __text='Database name' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseName' mandatory=true readOnly=false __title='Enter the database name of the site' textMode='singleline' maxLength=50 cssClass='required' }
            {multisitesValidationError id='databaseName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databaseUserName' __text='Database user name' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseUserName' mandatory=true readOnly=false __title='Enter the database user name of the site' textMode='singleline' maxLength=50 cssClass='required' }
            {multisitesValidationError id='databaseUserName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databasePassword' __text='Database password' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databasePassword' mandatory=true readOnly=false __title='Enter the database password of the site' textMode='password' maxLength=50 cssClass='required' }
            {multisitesValidationError id='databasePassword' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databaseHost' __text='Database host' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseHost' mandatory=true readOnly=false __title='Enter the database host of the site' textMode='singleline' maxLength=50 cssClass='required' }
            {multisitesValidationError id='databaseHost' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databaseType' __text='Database type' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseType' mandatory=true readOnly=false __title='Enter the database type of the site' textMode='singleline' maxLength=25 cssClass='required' }
            {multisitesValidationError id='databaseType' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='logo' __text='Logo' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='site' id='logo' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetLogoVal" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="logoFileExtensions">gif, jpeg, jpg, png</span></span>
            {if $mode ne 'create'}
                {if $site.logo ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$site.logoFullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $site.logoMeta.isImage} rel="imageviewer[site]"{/if}>
                        {if $site.logoMeta.isImage}
                            {thumb image=$site.logoFullPath objectid="site-`$site.id`" preset=$siteThumbPresetLogo tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$site.logoMeta.size|multisitesGetFileSize:$site.logoFullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='site' id='logoDeleteFile' readOnly=false __title='Delete logo ?'}
                        {formlabel for='logoDeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {multisitesValidationError id='logo' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='favIcon' __text='Fav icon' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='site' id='favIcon' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetFavIconVal" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="favIconFileExtensions">png, ico</span></span>
            {if $mode ne 'create'}
                {if $site.favIcon ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$site.favIconFullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $site.favIconMeta.isImage} rel="imageviewer[site]"{/if}>
                        {if $site.favIconMeta.isImage}
                            {thumb image=$site.favIconFullPath objectid="site-`$site.id`" preset=$siteThumbPresetFavIcon tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$site.favIconMeta.size|multisitesGetFileSize:$site.favIconFullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='site' id='favIconDeleteFile' readOnly=false __title='Delete fav icon ?'}
                        {formlabel for='favIconDeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {multisitesValidationError id='favIcon' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='parametersCsvFile' __text='Parameters csv file' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='site' id='parametersCsvFile' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetParametersCsvFileVal" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="parametersCsvFileFileExtensions">csv</span></span>
            {if $mode ne 'create'}
                {if $site.parametersCsvFile ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$site.parametersCsvFileFullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $site.parametersCsvFileMeta.isImage} rel="imageviewer[site]"{/if}>
                        {if $site.parametersCsvFileMeta.isImage}
                            {thumb image=$site.parametersCsvFileFullPath objectid="site-`$site.id`" preset=$siteThumbPresetParametersCsvFile tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$site.parametersCsvFileMeta.size|multisitesGetFileSize:$site.parametersCsvFileFullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='site' id='parametersCsvFileDeleteFile' readOnly=false __title='Delete parameters csv file ?'}
                        {formlabel for='parametersCsvFileDeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {multisitesValidationError id='parametersCsvFile' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='active' __text='Active' cssClass=''}
            {formcheckbox group='site' id='active' readOnly=false __title='active ?' cssClass='' }
        </div>
    </fieldset>
    
    {include file='template/include_selectOne.tpl' group='site' alias='template' aliasReverse='sites' mandatory=false idPrefix='multisitesSite_Template' linkingItem=$site displayMode='dropdown' allowEditing=false}
    {include file='project/include_selectOne.tpl' group='site' alias='project' aliasReverse='sites' mandatory=false idPrefix='multisitesSite_Project' linkingItem=$site displayMode='dropdown' allowEditing=false}
    {if $mode ne 'create'}
        {include file='helper/include_standardfields_edit.tpl' obj=$site}
    {/if}
    
    {* include display hooks *}
    {if $mode ne 'create'}
        {assign var='hookId' value=$site.id}
        {notifydisplayhooks eventname='multisites.ui_hooks.sites.form_edit' id=$hookId assign='hooks'}
    {else}
        {notifydisplayhooks eventname='multisites.ui_hooks.sites.form_edit' id=null assign='hooks'}
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
                    {formcheckbox group='site' id='repeatCreation' readOnly=false}
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
            {gt text='Really delete this site?' assign='deleteConfirmMsg'}
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
    
        multisitesAddCommonValidationRules('site', '{{if $mode ne 'create'}}{{$site.id}}{{/if}}');
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
        multisitesInitUploadField('logo');
        multisitesInitUploadField('favIcon');
        multisitesInitUploadField('parametersCsvFile');
    });
/* ]]> */
</script>
