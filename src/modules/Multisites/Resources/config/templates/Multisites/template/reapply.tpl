{* purpose of this template: template reapplication confirmation view *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
<div class="multisites-template multisites-reapply">
    {gt text='Reapply template' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    {if $lct eq 'admin'}
        <div class="z-admin-content-pagetitle">
            {icon type='regenerate' size='small' __alt='Reapply'}
            <h3>{$templateTitle}</h3>
        </div>
    {else}
        <h2>{$templateTitle}</h2>
    {/if}

    <form class="z-form" action="{modurl modname='Multisites' type=$lct func='reapply' ot='template' id=$template.id}" method="post">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input type="hidden" id="confirmation" name="confirmation" value="1" />

            <fieldset>
                <legend>{gt text='Template'}</legend>

                <div class="z-formrow">
                    <label>{gt text='Name'}</label>
                    <div><strong>{$template.name}</strong></div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{gt text='Confirmation prompt'}</legend>

                <p class="z-warningmsg">{gt text='Do you really want to reapply this template ?'}</p>

                <p class="z-errormsg z-bold">{gt text='Caution: this reapplies the template data again to all assigned sites. All database tables except excluded ones will be dropped and recreated.'}</p>

                <div class="z-buttons z-formbuttons">
                    {gt text='Reapply' assign='reapplyTitle'}
                    {button src='filesave.png' set='icons/small' text=$reapplyTitle title=$reapplyTitle class='z-btgreen'}
                    <a href="{modurl modname='Multisites' type=$lct func='view' ot='template'}">{icon type='cancel' size='small' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
                </div>
            </fieldset>
        </div>
    </form>
</div>
{include file="`$lct`/footer.tpl"}
