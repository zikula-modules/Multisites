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
{if $mode ne 'create'}
    <p class="z-errormsg z-bold">{gt text='Caution: updating a site causes reapplying the template data again to it. All database tables except excluded ones will be dropped and recreated.'}</p>
{/if}
{form enctype='multipart/form-data' cssClass='z-form'}
    {* add validation summary and a <div> element for styling the form *}
    {multisitesFormFrame}
    {formsetinitialfocus inputId='name'}

    <fieldset>
        <legend>{gt text='Basic data'}</legend>
        
        <div class="z-formrow">
            {formlabel for='name' __text='Name (internal)' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='name' mandatory=true readOnly=false __title='Enter the name of the site' textMode='singleline' maxLength=150 cssClass='required' }
            {multisitesValidationError id='name' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='description' __text='Description (internal)' cssClass=''}
            {formtextinput group='site' id='description' mandatory=false readOnly=false __title='Enter the description of the site' textMode='singleline' maxLength=255 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAlias' __text='Site alias' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='siteAlias' mandatory=true readOnly=false __title='Enter the site alias of the site' textMode='singleline' maxLength=20 cssClass='required' }
            {multisitesValidationError id='siteAlias' class='required'}
            <span class="z-formnote z-sub">{gt text='The alias must be a lower case, unique string containing only letters.'}</span>
        </div>
        
        <div class="z-formrow">
            {if $mode eq 'create'}
                {formlabel for='siteName' __text='Site name' mandatorysym='1' cssClass=''}
                {formtextinput group='site' id='siteName' mandatory=true readOnly=false __title='Enter the site name of the site' textMode='singleline' maxLength=150 cssClass='required' }
            {else}
                {formlabel for='siteName' __text='Original site name' mandatorysym='1' cssClass=''}
                {formtextinput group='site' id='siteName' mandatory=true readOnly=true __title='Enter the site name of the site' textMode='singleline' maxLength=150 cssClass='required' }
            {/if}
            {multisitesValidationError id='siteName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteDescription' __text='Site description' cssClass=''}
            {formtextinput group='site' id='siteDescription' mandatory=false readOnly=false __title='Enter the site description of the site' textMode='singleline' maxLength=255 cssClass='' }
        </div>
    </fieldset>
    <fieldset>
        <legend>{gt text='Site host or folder'}</legend>

        <div class="z-formrow">
            {formlabel for='siteDns' __text='Site dns' mandatorysym='1' cssClass=''}
            {if $mode eq 'create'}
                {formtextinput group='site' id='siteDns' mandatory=true readOnly=false __title='Enter the site dns of the site' textMode='singleline' maxLength=255 cssClass='required' }
            {else}
                {formtextinput group='site' id='siteDns' mandatory=true readOnly=true __title='Enter the site dns of the site' textMode='singleline' maxLength=255 cssClass='required' }
            {/if}
            {multisitesValidationError id='siteDns' class='required'}
            <span class="z-formnote z-sub">{gt text='This is the domain or folder name under which this site should be reachable.'}</span>
        </div>

        <div class="z-formrow">
            {formlabel for='active' __text='Active' cssClass=''}
            {formcheckbox group='site' id='active' readOnly=false __title='active ?' cssClass='' }
        </div>
    </fieldset>
    <fieldset>
        <legend>{gt text='Management information'}</legend>

        <div class="z-formrow">
            {if $mode eq 'create'}
                {formlabel for='siteAdminName' __text='Site admin name' mandatorysym='1' cssClass=''}
                {formtextinput group='site' id='siteAdminName' mandatory=true readOnly=false __title='Enter the site admin name of the site' textMode='singleline' maxLength=25 cssClass='required' }
                <span class="z-formnote z-sub">{gt text='User names can contain letters, numbers, underscores, periods, or dashes.'}</span>
            {else}
                {formlabel for='siteAdminName' __text='Original site admin name' mandatorysym='1' cssClass=''}
                {formtextinput group='site' id='siteAdminName' mandatory=true readOnly=true __title='Enter the site admin name of the site' textMode='singleline' maxLength=25 cssClass='required' }
            {/if}
            {multisitesValidationError id='siteAdminName' class='required'}
        </div>
        
        <div class="z-formrow">
            {if $mode eq 'create'}
                {formlabel for='siteAdminPassword' __text='Site admin password' mandatorysym='1' cssClass=''}
                {formtextinput group='site' id='siteAdminPassword' mandatory=true readOnly=false __title='Enter the site admin password of the site' textMode='password' maxLength=15 cssClass='required' }
            {else}
                {formlabel for='siteAdminPassword' __text='Original site admin password' mandatorysym='1' cssClass=''}
                {formtextinput group='site' id='siteAdminPassword' mandatory=true readOnly=true __title='Enter the site admin password of the site' textMode='password' maxLength=15 cssClass='required' }
            {/if}
            {multisitesValidationError id='siteAdminPassword' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteAdminRealName' __text='Site admin real name' cssClass=''}
            {formtextinput group='site' id='siteAdminRealName' mandatory=false readOnly=false __title='Enter the site admin real name of the site' textMode='singleline' maxLength=70 cssClass='' }
        </div>
        
        <div class="z-formrow">
            {if $mode eq 'create'}
                {formlabel for='siteAdminEmail' __text='Site admin email' mandatorysym='1' cssClass=''}
                {formemailinput group='site' id='siteAdminEmail' mandatory=true readOnly=false __title='Enter the site admin email of the site' textMode='singleline' maxLength=40 cssClass='required validate-email' }
            {else}
                {formlabel for='siteAdminEmail' __text='Original site admin email' mandatorysym='1' cssClass=''}
                {formemailinput group='site' id='siteAdminEmail' mandatory=true readOnly=true __title='Enter the site admin email of the site' textMode='singleline' maxLength=40 cssClass='required validate-email' }
            {/if}
            {multisitesValidationError id='siteAdminEmail' class='required'}
            {multisitesValidationError id='siteAdminEmail' class='validate-email'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='siteCompany' __text='Site company' cssClass=''}
            {formtextinput group='site' id='siteCompany' mandatory=false readOnly=false __title='Enter the site company of the site' textMode='singleline' maxLength=100 cssClass='' }
        </div>
    </fieldset>
    <fieldset>
        <legend>{gt text='Database data'}</legend>

        {if $mode eq 'create'}
            <p class="z-warningmsg">{gt text='Caution: the database is emptied, so choose one which is not used by any other applications. All tables which are defined as excluded in the template are kept though.'}</p>
        {else}
            <p class="z-warningmsg">{gt text='Change these values only if database credentials actually changed.'}</p>
        {/if}

        <div class="z-formrow">
            {formlabel for='databaseType' __text='Database type' mandatorysym='1' cssClass=''}
            {formdropdownlist group='site' id='databaseType' mandatory=true readOnly=false __title='Choose the database type of the site' cssClass='required' }
            {multisitesValidationError id='databaseType' class='required'}
        </div>

        <div class="z-formrow">
            {formlabel for='databaseHost' __text='Database host' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseHost' mandatory=true readOnly=false __title='Enter the database host of the site' textMode='singleline' maxLength=25 cssClass='required' }
            {multisitesValidationError id='databaseHost' class='required'}
        </div>

        <div class="z-formrow">
            {formlabel for='databaseName' __text='Database name' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseName' mandatory=true readOnly=false __title='Enter the database name of the site' textMode='singleline' maxLength=25 cssClass='required' }
            {multisitesValidationError id='databaseName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databaseUserName' __text='Database user name' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databaseUserName' mandatory=true readOnly=false __title='Enter the database user name of the site' textMode='singleline' maxLength=25 cssClass='required' }
            {multisitesValidationError id='databaseUserName' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='databasePassword' __text='Database password' mandatorysym='1' cssClass=''}
            {formtextinput group='site' id='databasePassword' mandatory=true readOnly=false __title='Enter the database password of the site' textMode='password' maxLength=25 cssClass='required' }
            {multisitesValidationError id='databasePassword' class='required'}
        </div>

        {if $mode eq 'create'}
            <div class="z-formrow">
                {formlabel for='createNewDatabase' __text='Create database' mandatorysym='0' cssClass=''}
                {formcheckbox group='additions' id='createNewDatabase' readOnly=false __title='Check this if the database does not exist yet.' cssClass='' }
                <span class="z-formnote z-sub">{gt text='Expert option! Only possible if the database user has sufficient permissions.'}</span>
                <span class="z-formnote z-sub">{gt text='Note: the database user must exist already for this to work.'}</span>
            </div>
        {/if}
    </fieldset>
    <div{if $mode ne 'create'} class="z-hide"{/if}>
        {include file='project/include_selectOne.tpl' group='site' alias='project' aliasReverse='sites' mandatory=true idPrefix='multisitesSite_Project' linkingItem=$site displayMode='dropdown' allowEditing=false}
    </div>
    {assign var='templateMandatory' value=true}
    {if $mode ne 'create'}
        {assign var='templateMandatory' value=false}
    {/if}
    {include file='template/include_selectOne.tpl' group='site' alias='template' aliasReverse='sites' mandatory=$templateMandatory idPrefix='multisitesSite_Template' linkingItem=$site displayMode='dropdown' allowEditing=false}
    <fieldset>
        <legend>{gt text='Individualisation'}</legend>

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

        <div class="z-formrow z-hide">
            {formlabel for='allowedLocales' __text='Allowed locales' cssClass=''}
            {multisitesArrayInput group='site' id='allowedLocales' mandatory=false readOnly=false __title='Enter the allowed locales of the site' rows=7 cssClass=''}
            <span class="z-formnote z-sub">{gt text='Expert option! Per default all locales available in the system will be made available for the site.'}</span>
            <span class="z-warningmsg z-formnote">{gt text='This feature has not been implemented yet (issue #17).'}</span>
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
            <span class="z-formnote">{gt text='Note: you should use semicolon as delimiter and UTF-8 as encoding.'}</span>
            {multisitesValidationError id='parametersCsvFile' class='validate-upload'}
        </div>
        <div class="z-formrow">
            {formlabel for='parametersArray' __text='or enter them manually' mandatorySym='0' cssClass=''}
            {multisitesArrayInput group='site' id='parametersArray' mandatory=false readOnly=false __title='Enter values for the template parameters' rows=7 cssClass=''}
            <span class="z-formnote"><a id="deriveParametersFromTemplate" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Derive placeholders from template'}</a></span>
            <span class="z-informationmsg z-formnote">{gt text='Enter values for all parameters specified by the selected template separated by line breaks.'}</span>
        </div>
    </fieldset>

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
            {*gt text='Really delete this site?' assign='deleteConfirmMsg'}
            {formbutton id="btn`$actionIdCapital`" commandName=$action.id text=$actionTitle class=$action.buttonClass confirmMessage=$deleteConfirmMsg*}
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

    var parameterSpec = null;

    function filterTemplatesByProject()
    {
        var oldTemplateId = $('template').value;

        $$('#template option').each(function(elem) {
            elem.remove();
        });

        {{if $mode ne 'create'}}
            var opt = document.createElement('option');
            opt.text = '{{gt text='None (decouple site from template)'}}';
            opt.value = '';
            $('template').options.add(opt);
        {{/if}}

        if (!$('project').value) {
            return;
        }

        new Zikula.Ajax.Request(
            Zikula.Config.baseURL + 'ajax.php?module=Multisites&func=getProjectTemplates',
            {
                method: 'post',
                parameters: 'id=' + $('project').value,
                onComplete: function(req) {
                    if (!req.isSuccess()) {
                        Zikula.UI.Alert(req.getMessage(), Zikula.__('Error', 'module_multisites_js'));
                        return;
                    }
                    var data = req.getData();
                    var templates = data.templates;
                    var includesOldId = false;

                    templates.each(function (template) {
                        var opt = document.createElement('option');
                        opt.text = template.name;
                        opt.value = template.id;
                        if (template.id == oldTemplateId) {
                            includesOldId = true;
                        }
                        {{if $mode eq 'create' || $site.template eq null}}
                            $('template').options.add(opt);
                        {{else}}
                            if (template.id == oldTemplateId) {
                                $('template').options.add(opt);
                            }
                        {{/if}}

                        parameterSpec = template.parameters;
                    });

                    if (includesOldId === true) {
                        $('template').value = oldTemplateId;
                    }
                }
            }
        );

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

        $('deriveParametersFromTemplate').observe('click', function(evt) {
            evt.preventDefault();
            if (parameterSpec === null) {
                alert('{{gt text='Parameter specification is not available yet.'}}');
                return;
            }
            var spec = '';
            parameterSpec.each(function (parameterName) {
                spec += parameterName + ': Your value' + "\n";
            });
            $('parametersArray').value = spec;
        }).removeClassName('z-hide');

        $('project').observe('change', filterTemplatesByProject);
        filterTemplatesByProject();
    });
/* ]]> */
</script>
