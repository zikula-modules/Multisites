{* purpose of this template: module configuration *}
{include file='admin/header.tpl'}
<div class="multisites-config">
    {gt text='Settings' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='config' size='small' __alt='Settings'}
        <h3>{$templateTitle}</h3>
    </div>

    {form cssClass='z-form'}
        {* add validation summary and a <div> element for styling the form *}
        {multisitesFormFrame}
            {formsetinitialfocus inputId='tempAccessFileContent'}
            {formtabbedpanelset}
                {gt text='General' assign='tabTitle'}
                {formtabbedpanel title=$tabTitle}
                    <fieldset>
                        <legend>{$tabTitle}</legend>
                    
                        <p class="z-confirmationmsg">{gt text='Here you can define general settings.'|nl2br}</p>
                    
                        <div class="z-formrow">
                            {formlabel for='tempAccessFileContent' __text='Temp access file content' cssClass=''}
                                {formtextinput id='tempAccessFileContent' group='config' maxLength=255 __title='Enter the temp access file content.'}
                        </div>
                    </fieldset>
                {/formtabbedpanel}
                {gt text='Security settings' assign='tabTitle'}
                {formtabbedpanel title=$tabTitle}
                    <fieldset>
                        <legend>{$tabTitle}</legend>
                    
                        <p class="z-confirmationmsg">{gt text='Here you can define security-related settings.'|nl2br}</p>
                    
                        <div class="z-formrow">
                            {formlabel for='globalAdminName' __text='Global admin name' cssClass=''}
                                {formtextinput id='globalAdminName' group='config' maxLength=255 __title='Enter the global admin name.'}
                        </div>
                        <div class="z-formrow">
                            {formlabel for='globalAdminPassword' __text='Global admin password' cssClass=''}
                                {formtextinput id='globalAdminPassword' group='config' maxLength=255 __title='Enter the global admin password.'}
                        </div>
                        <div class="z-formrow">
                            {formlabel for='globalAdminEmail' __text='Global admin email' cssClass=''}
                                {formtextinput id='globalAdminEmail' group='config' maxLength=255 __title='Enter the global admin email.'}
                        </div>
                    </fieldset>
                {/formtabbedpanel}
            {/formtabbedpanelset}

            <div class="z-buttons z-formbuttons">
                {formbutton commandName='save' __text='Update configuration' class='z-bt-save'}
                {formbutton commandName='cancel' __text='Cancel' class='z-bt-cancel'}
            </div>
        {/multisitesFormFrame}
    {/form}
</div>
{include file='admin/footer.tpl'}
