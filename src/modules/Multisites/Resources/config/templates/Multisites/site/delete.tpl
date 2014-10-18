{* purpose of this template: sites delete confirmation view *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
<div class="multisites-site multisites-delete">
    {gt text='Delete site' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    {if $lct eq 'admin'}
        <div class="z-admin-content-pagetitle">
            {icon type='delete' size='small' __alt='Delete'}
            <h3>{$templateTitle}</h3>
        </div>
    {else}
        <h2>{$templateTitle}</h2>
    {/if}

    <form class="z-form" action="{modurl modname='Multisites' type=$lct func='delete' ot='site' id=$site.id}" method="post">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input type="hidden" id="confirmation" name="confirmation" value="1" />
            <fieldset>
                <legend>{gt text='Basic site data'}</legend>

                <div class="z-formrow">
                    <label>{gt text='Name'}</label>
                    <div><strong>{$site.name}</strong></div>
                </div>

                <div class="z-formrow">
                    <label>{gt text='Site name'}</label>
                    <div><strong>{$site.siteName}</strong></div>
                </div>

                <div class="z-formrow">
                    <label>{gt text='Site dns'}</label>
                    <div><strong>{$site.siteDns}</strong></div>
                </div>

                <div class="z-formrow">
                    <label>{gt text='Database name'}</label>
                    <div><strong>{$site.databaseName}</strong></div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{gt text='Delete options'}</legend>

                <div class="z-formrow">
                    <label for="deleteFiles">{gt text='Delete site files'}</label>
                    <input type="checkbox" name="deleteFiles" value="1" />
                </div>
                <div class="z-formrow">
                    <label for="deleteDatabase">{gt text='Delete database'}</label>
                    <input type="checkbox" name="deleteDatabase" value="1" />
                    <span class="z-formnote z-sub">{gt text='Expert option! Only possible if the database user has sufficient permissions.'}</span>
                </div> 
            </fieldset>

            <fieldset>
                <legend>{gt text='Confirmation prompt'}</legend>

                <p class="z-warningmsg">{gt text='Do you really want to delete this site ?'}</p>

                <div class="z-buttons z-formbuttons">
                    {gt text='Delete' assign='deleteTitle'}
                    {button src='14_layer_deletelayer.png' set='icons/small' text=$deleteTitle title=$deleteTitle class='z-btred'}
                    <a href="{modurl modname='Multisites' type=$lct func='view' ot='site'}">{icon type='cancel' size='small' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
                </div>
            </fieldset>

            {notifydisplayhooks eventname='multisites.ui_hooks.sites.form_delete' id="`$site.id`" assign='hooks'}
            {foreach key='providerArea' item='hook' from=$hooks}
            <fieldset>
                <legend>{$hookName}</legend>
                {$hook}
            </fieldset>
            {/foreach}
        </div>
    </form>
</div>
{include file="`$lct`/footer.tpl"}
