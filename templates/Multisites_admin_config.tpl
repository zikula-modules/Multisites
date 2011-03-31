{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='config' size='large' __alt='Module configuration'}</div>
    <h2>{gt text='Module configuration'}</h2>
    <form name="config" id="config" class="z-form" action="{modurl modname='Multisites' type='admin' func='updateConfig'}" method="post">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <div class="z-formrow">
            <label for="modelsFolder">{gt text='Model files folder'}</label>
            <input type="text" name="modelsFolder" size="50" maxlength="150" value="{$modelsFolder}"/>
        </div>
        <div class="z-formrow">
            <label for="tempAccessFileContent">{gt text='Temporal folder .htaccess file content'}</label>
            <textarea name="tempAccessFileContent" rows="7" cols="50">{$tempAccessFileContent}</textarea>
        </div>
        <div class="z-formrow">
            <label for="globalAdminName">{gt text='Global admin name'}</label>
            <input type="text" name="globalAdminName" size="20" maxlength="20" value="{$globalAdminName}"/>
        </div>
        <div class="z-formrow">
            <label for="globalAdminPassword">{gt text='Global admin password'}</label>
            <input type="text" name="globalAdminPassword" size="20" maxlength="20" value="{$globalAdminPassword}"/>
        </div>
        <div class="z-formrow">
            <label for="globalAdminemail">{gt text='Global admin e-mail'}</label>
            <input type="text" name="globalAdminemail" size="20" maxlength="20" value="{$globalAdminemail}"/>
        </div>
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt='Modify' __title='Modify'}
            <a href="{modurl modname='Multisites' type='admin' func='main'}">
                {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
            </a>
        </div>
    </form>
</div>