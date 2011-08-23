{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='new' size='large' __alt='Create a new instance'}</div>
    <h2>{gt text='Create a new instance'}</h2>
    <form id="newInstance" class="z-form" action="{modurl modname='Multisites' type='admin' func='createInstance'}" method="post" >
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <div class="z-formrow">
            <label for="instancename">{gt text='Instance name'} <span class="mandatoryField">*</span></label>
            <input type="text" name="instancename" size="50" maxlength="150" value="{$instancename}"/>
        </div>
        <div class="z-formrow">
            <label for="description">{gt text='Description (internal)'}</label>
            <input type="text" name="description" size="50" maxlength="255" value="{$description}" />
        </div>
        <div class="z-formrow">
            <label for="sitename">{gt text='Site name'}</label>
            <input type="text" name="sitename" size="50" maxlength="255" value="{$sitename}"/>
        </div>
        <div class="z-formrow">
            <label for="siteDescription">{gt text='Site description'}</label>
            <input type="text" name="siteDescription" size="50" maxlength="255" value="{$siteDescription}" />
        </div>
        <div class="z-formrow">
            <label for="siteadminname">{gt text='Site admin name'} <span class="mandatoryField">*</span></label>
            <input type="text" name="siteadminname" size="15" maxlength="25" value="{if $siteadminname eq ''}admin{else}{$siteadminname}{/if}" />
        </div>
        <div class="z-formrow">
            <label for="siteadminpwd">{gt text='Site admin password'} <span class="mandatoryField">*</span></label>
            <input type="password" name="siteadminpwd" size="15" maxlength="15" />
        </div>
        <div class="z-formrow">
            <label for="siteadminrealname">{gt text='Admin real name'}</label>
            <input type="text" name="siteadminrealname" size="30" maxlength="70" value="{$siteadminrealname}"/>
        </div>
        <div class="z-formrow">
            <label for="siteadminemail">{gt text='Admin email'} <span class="mandatoryField">*</span></label>
            <input type="text" name="siteadminemail" size="30" maxlength="30" value="{$siteadminemail}"/>
        </div>
        <div class="z-formrow">
            <label for="sitecompany">{gt text='Company'}</label>
            <input type="text" name="sitecompany" size="50" maxlength="100" value="{$sitecompany}"/>
        </div>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Site domain'} <span class="mandatoryField">*</span></label>
            <input type="text" name="sitedns" size="15" maxlength="255" value="{$sitedns}" />
        </div>
        <fieldset>
            <legend>{gt text='Database information'}</legend>
            <div class="z-formrow">
                <label for="sitedbhost">{gt text='Site database type'}</label>
                <select name="sitedbtype">
                    <option value="mysql" {if $sitedbtype eq 'mysql'}selected{/if}>MySQL</option>
                    <option value="mysqli" {if $sitedbtype eq 'mysqli'}selected{/if}>MySQL Improved</option>
                    <option value="postgres" {if $sitedbtype eq 'postgres'}selected{/if}>PostgreSQL</option>
                </select>
            </div>
            <div class="z-formrow">
                <label for="sitedbhost">{gt text='Site database host'} <span class="mandatoryField">*</span></label>
                <input type="text" name="sitedbhost" size="15" maxlength="20" value="{$sitedbhost}"/>
            </div>
            <div class="z-formrow">
                <label for="sitedbname">{gt text='Site database'} <span class="mandatoryField">*</span></label>
                <input type="text" name="sitedbname" size="15" maxlength="20" value="{$sitedbname}"/>
            </div>
            <div class="z-formrow">
                <label for="sitedbuname">{gt text='Site database username'} <span class="mandatoryField">*</span></label>
                <input type="text" name="sitedbuname" size="15" maxlength="20" value="{$sitedbuname}"/>
            </div>
            <div class="z-formrow">
                <label for="sitedbpass">{gt text='Site database password'} <span class="mandatoryField">*</span></label>
                <input type="password" name="sitedbpass" size="15" maxlength="20" />
            </div>
            <div class="z-formrow">
                <label for="sitedbprefix">{gt text='Site database prefix'}</label>
                <input type="text" name="sitedbprefix" size="15" maxlength="5" value="{if $siteadminname ne ''}{$sitedbprefix}{/if}"/>
                <p class="z-formnote">{gt text='This setting is obsolete. It is recommended to use it only for legacy databases.'}</p>
            </div>        
            <div class="z-formrow">
                <label for="createDB">{gt text='Create database'}</label>
                <input type="checkbox" name="createDB" {if $createDB eq 1}checked{/if} value="1" />
            </div>
        </fieldset>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Based on model'} <span class="mandatoryField">*</span></label>
            <select name="siteinitmodel">
                <option value="">{gt text='Choose a model...'}</option>
                {foreach item="model" from=$models}
                    <option {if $siteinitmodel eq $model.modelname}selected{/if} value="{$model.modelname}">{$model.modelname}</option>
                {/foreach}
            </select>
        </div>
        <div class="z-formrow">
            <label for="active">{gt text='Active'}</label>
            <input type="checkbox" name="active" {if $active eq 1}checked{/if} value="1" />
        </div>
        <div class="z-formrow">
            <label for="mandatory"><span class="mandatoryField">*</span> {gt text='Mandatory fields'}</label>
        </div>
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt='Add' __title='Add'}
            <a href="{modurl modname='Multisites' type='admin' func='main'}">
                {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
            </a>
        </div>
    </form>
</div>