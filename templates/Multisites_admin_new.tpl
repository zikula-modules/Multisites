{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='new' size='large' __alt='Create a new instance'}</div>
    <h2>{gt text='Create a new instance'}</h2>
    <form id="newInstance" class="z-form" action="{modurl modname='Multisites' type='admin' func='createInstance'}" method="post" >
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <div class="z-formrow">
            <label for="instanceName">{gt text='Instance name'} <span class="mandatoryField">*</span></label>
            <input type="text" name="instanceName" size="50" maxlength="150" value="{$instanceName}"/>
        </div>
        <div class="z-formrow">
            <label for="description">{gt text='Description (internal)'}</label>
            <input type="text" name="description" size="50" maxlength="255" value="{$description}" />
        </div>
        <div class="z-formrow">
            <label for="siteName">{gt text='Site name'}</label>
            <input type="text" name="siteName" size="50" maxlength="255" value="{$siteName}"/>
        </div>
        <div class="z-formrow">
            <label for="siteDescription">{gt text='Site description'}</label>
            <input type="text" name="siteDescription" size="50" maxlength="255" value="{$siteDescription}" />
        </div>
        <div class="z-formrow">
            <label for="siteAdminName">{gt text='Site admin name'} <span class="mandatoryField">*</span></label>
            <input type="text" name="siteAdminName" size="15" maxlength="25" value="{if $siteAdminName eq ''}admin{else}{$siteAdminName}{/if}" />
        </div>
        <div class="z-formrow">
            <label for="siteAdminPwd">{gt text='Site admin password'} <span class="mandatoryField">*</span></label>
            <input type="password" name="siteAdminPwd" size="15" maxlength="15" />
        </div>
        <div class="z-formrow">
            <label for="siteAdminRealName">{gt text='Admin real name'}</label>
            <input type="text" name="siteAdminRealName" size="30" maxlength="70" value="{$siteAdminRealName}"/>
        </div>
        <div class="z-formrow">
            <label for="siteAdminEmail">{gt text='Admin email'} <span class="mandatoryField">*</span></label>
            <input type="text" name="siteAdminEmail" size="30" maxlength="30" value="{$siteAdminEmail}"/>
        </div>
        <div class="z-formrow">
            <label for="siteCompany">{gt text='Company'}</label>
            <input type="text" name="siteCompany" size="50" maxlength="100" value="{$siteCompany}"/>
        </div>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Site domain'} <span class="mandatoryField">*</span></label>
            <input type="text" name="sitedns" size="15" maxlength="20" value="{$sitedns}" />
        </div>
        <fieldset>
            <legend>{gt text='Database information'}</legend>
            <div class="z-formrow">
                <label for="siteDBHost">{gt text='Site database type'}</label>
                <select name="siteDBType">
                    <option value="mysql" {if $siteDBType eq 'mysql'}selected{/if}>MySQL</option>
                    <option value="mysqli" {if $siteDBType eq 'mysqli'}selected{/if}>MySQL Improved</option>
                    <option value="postgres" {if $siteDBType eq 'postgres'}selected{/if}>PostgreSQL</option>
                </select>
            </div>
            <div class="z-formrow">
                <label for="siteDBHost">{gt text='Site database host'} <span class="mandatoryField">*</span></label>
                <input type="text" name="siteDBHost" size="15" maxlength="20" value="{$siteDBHost}"/>
            </div>
            <div class="z-formrow">
                <label for="siteDBName">{gt text='Site database'} <span class="mandatoryField">*</span></label>
                <input type="text" name="siteDBName" size="15" maxlength="20" value="{$siteDBName}"/>
            </div>
            <div class="z-formrow">
                <label for="siteDBUname">{gt text='Site database username'} <span class="mandatoryField">*</span></label>
                <input type="text" name="siteDBUname" size="15" maxlength="20" value="{$siteDBUname}"/>
            </div>
            <div class="z-formrow">
                <label for="siteDBPass">{gt text='Site database password'} <span class="mandatoryField">*</span></label>
                <input type="password" name="siteDBPass" size="15" maxlength="20" />
            </div>
            <div class="z-formrow">
                <label for="siteDBPrefix">{gt text='Site database prefix'} <span class="mandatoryField">*</span></label>
                <input type="text" name="siteDBPrefix" size="15" maxlength="5" value="{if $siteAdminName eq ''}z{else}{$siteDBPrefix}{/if}"/>
            </div>        
            <div class="z-formrow">
                <label for="createDB">{gt text='Create database'}</label>
                <input type="checkbox" name="createDB" {if $createDB eq 1}checked{/if} value="1" />
            </div>
        </fieldset>
        <div class="z-formrow">
            <label for="sitedns">{gt text='Based on model'} <span class="mandatoryField">*</span></label>
            <select name="siteInitModel">
                <option value="">{gt text='Choose a model...'}</option>
                {foreach item="model" from=$models}
                    <option {if $siteInitModel eq $model.modelName}selected{/if} value="{$model.modelName}">{$model.modelName}</option>
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