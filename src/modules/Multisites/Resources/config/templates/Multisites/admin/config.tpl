{* purpose of this template: module configuration *}
{include file='admin/header.tpl'}
<div class="multisites-config">
    {gt text='Settings' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='config' size='small' __alt='Settings'}
        <h3>{$templateTitle}</h3>
    </div>

    {sessiongetvar name='globalAdminStatus' default='' assign='globalAdminStatus'}
    {if $globalAdminStatus ne ''}
        <div class="z-warningmsg">{$globalAdminStatus}</div>
    {/if}

    {form cssClass='z-form'}
        {* add validation summary and a <div> element for styling the form *}
        {multisitesFormFrame}
            {formsetinitialfocus inputId='modelsFolder'}
            {formtabbedpanelset}
                {gt text='General' assign='tabTitle'}
                {formtabbedpanel title=$tabTitle}
                    <fieldset>
                        <legend>{$tabTitle}</legend>
                    
                        <p class="z-confirmationmsg">{gt text='Here you can define general settings.'|nl2br}</p>
                    
                        <div class="z-formrow">
                            {formlabel for='modelsFolder' __text='Models folder' cssClass=''}
                            {formtextinput id='modelsFolder' group='config' maxLength=255 __title='Enter the models folder.'}
                        </div>
                        <div class="z-formrow">
                            {formlabel for='tempAccessFileContent' __text='Temporal folder .htaccess file content' cssClass=''}
                            {formtextinput textMode='multiline' id='tempAccessFileContent' group='config' __title='Enter the temp access file content.' rows=7}
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
                            {formtextinput textMode='password' id='globalAdminPassword' group='config' maxLength=255 __title='Enter the global admin password.'}
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
