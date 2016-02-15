{* purpose of this template: view tools section *}
{assign var='lct' value='admin'}
{include file="`$lct`/header.tpl"}
<div class="multisites-site multisites-viewtools">
    {gt text='Site tools' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='options' size='small' alt=$templateTitle}
        <h3>{$templateTitle}</h3>
    </div>

    <p>{gt text='Name'}: {$site.name}<br />
    {gt text='Site name'}: {$site.siteName}<br />
    {gt text='Site dns'}: {$site.siteDns}<br />
    {gt text='Database name'}: {$site.databaseName}</p>

    <h3>{gt text='Available tools'}</h3>
    <dl>
        <dt><a href="{modurl modname='Multisites' type='site' func='executeTool' tool='createAdministrator' id=$site.id}" title="{gt text='Create global administrator'}">{gt text='Create global administrator'}</a></dt>
        <dd>{gt text='This ensures that the global administrator exists. Note that if the site admin and the global admin have the same user name, the global admin will override the original site admin.'}</dd>
        <dt><a href="{modurl modname='Multisites' type='site' func='executeTool' tool='adminSiteControl' id=$site.id}" title="{gt text='Recover administrators site control'}">{gt text='Recover administrators site control'}</a></dt>
        <dd>{gt text='This removes the first permission rule and inserts the default one instead, ensuring that the original site administrator belongs to the admin group again.'}</dd>
    </dl>

    <p><a href="javascript:history.back()" title="{gt text='Back to site list'}" class="z-icon-es-back">{gt text='Back to site list'}</a></p>
</div>
{include file="`$lct`/footer.tpl"}
