{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='edit' size='large' __alt='Edit an instance'}</div>
    <h2>{gt text='Edit an instance'}</h2>
    <form id="editInstance" class="z-form" action="{modurl modname='Multisites' type='admin' func='update'}" method="post" >
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="instanceid" value="{$site.instanceid}" />
        <div class="z-formrow">
            <label for="instancename">{gt text='Instance name'}</label>
            <input type="text" name="instancename" size="50" maxlength="150" value="{$site.instancename}"/>
        </div>
        <div class="z-formrow">
            <label for="description">{gt text='Description (internal)'}</label>
            <input type="text" name="description" size="50" maxlength="255" value="{$site.description}"/>
        </div>
        <div class="z-formrow">
            <label for="alias">{gt text='Alias'}</label>
            <input type="text" name="alias" size="50" maxlength="10" value="{$site.alias}" />
            <p class="z-formnote">{gt text='The alias should be a lower case, unique string containing only letters.'}</p>
        </div>
        <div class="z-formrow">
            <label for="sitename">{gt text='Original site name'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="sitename" size="50" maxlength="255" value="{$site.sitename}"/>
        </div>
        <div class="z-formrow">
            <label for="siteDescription">{gt text='Site description'}</label>
            <input type="text" name="siteDescription" size="50" maxlength="255" value="{$site.siteDescription}" />
        </div>
        <div class="z-formrow">
            <label for="siteadminname">{gt text='Original site admin name'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="siteadminname" size="15" maxlength="25" value="{$site.siteadminname}"/>
        </div>
        <div class="z-formrow">
            <label for="siteadminpwd">{gt text='Original site admin password'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="siteadminpwd" size="15" maxlength="15" value="{$site.siteadminpwd}" />
        </div>
        <div class="z-formrow">
            <label for="siteadminrealname">{gt text='Admin real name'}</label>
            <input type="text" name="siteadminrealname" size="30" maxlength="70" value="{$site.siteadminrealname}" />
        </div>
        <div class="z-formrow">
            <label for="siteadminemail">{gt text='Admin email'}</label>
            <input type="text" name="siteadminemail" size="30" maxlength="30" value="{$site.siteadminemail}" />
        </div>
        <div class="z-formrow">
            <label for="sitecompany">{gt text='Company'}</label>
            <input type="text" name="sitecompany" size="50" maxlength="100" value="{$site.sitecompany}" />
        </div>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Site domain'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" name="sitedns" size="15" maxlength="255" value="{$site.sitedns}" />
        </div>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Based on model'}</label>
            <input class="notEditableField" onfocus="blur()" type="text" value="{$site.siteinitmodel}" />
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