{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='edit' size='large' __alt='Edit an instance'}</div>
    <h2>{gt text='Edit an instance'}</h2>
    <form id="editInstance" class="z-form" action="{modurl modname='Multisites' type='admin' func='update'}" method="post" >
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="instanceId" value="{$site.instanceId}" />
        <div class="z-formrow">
            <label for="instanceName">{gt text='Instance name'}</label>
            <input type="text" name="instanceName" size="50" maxlength="150" value="{$site.instanceName}"/>
        </div>
        <div class="z-formrow">
            <label for="description">{gt text='Description'}</label>
            <input type="text" name="description" size="50" maxlength="255" value="{$site.description}"/>
        </div>
        <div class="z-formrow">
            <label for="siteName">{gt text='Original site name'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="siteName" size="50" maxlength="255" value="{$site.siteName}"/>
        </div>
        <div class="z-formrow">
            <label for="siteDescription">{gt text='Site description'}</label>
            <input type="text" name="siteDescription" size="50" maxlength="255" value="{$site.siteDescription}" />
        </div>
        <div class="z-formrow">
            <label for="siteAdminName">{gt text='Original site admin name'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="siteAdminName" size="15" maxlength="25" value="{$site.siteAdminName}"/>
        </div>
        <div class="z-formrow">
            <label for="siteAdminPwd">{gt text='Original site admin password'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="siteAdminPwd" size="15" maxlength="15" value="{$site.siteAdminPwd}" />
        </div>
        <div class="z-formrow">
            <label for="siteAdminRealName">{gt text='Admin real name'}</label>
            <input type="text" name="siteAdminRealName" size="30" maxlength="70" value="{$site.siteAdminRealName}" />
        </div>
        <div class="z-formrow">
            <label for="siteAdminEmail">{gt text='Admin email'}</label>
            <input type="text" name="siteAdminEmail" size="30" maxlength="30" value="{$site.siteAdminEmail}" />
        </div>
        <div class="z-formrow">
            <label for="siteCompany">{gt text='Company'}</label>
            <input type="text" name="siteCompany" size="50" maxlength="100" value="{$site.siteCompany}" />
        </div>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Site domain'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="sitedns" size="15" maxlength="60" value="{$site.sitedns}" />
        </div>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Based on model'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" value="{$site.siteInitModel}" />
        </div>
        <div class="z-formrow">
            <label for="active">{gt text='Active'}</label>
            <input type="checkbox"{if $site.active eq 1} checked="checked"{/if} name="active" value="1" />
        </div>
        <div class="z-formbuttons">
        {button src='button_ok.png' set='icons/small' __alt='Edit' __title='Edit'}
        <a href="{modurl modname='Multisites' type='admin' func='main'}">
            {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
        </a>
        </div>
    </form>
</div>