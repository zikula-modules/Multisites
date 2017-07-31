{* purpose of this template: manage site modules *}
{assign var='lct' value='admin'}
{include file="`$lct`/header.tpl"}
{pageaddvar name='javascript' value='modules/Multisites/javascript/Multisites_siteExtensions.js'}
<div class="multisites-site multisites-manageextensions">
    {gt text='Allowed extensions' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='cubes' size='small' __alt='Manage the modules for this site'}
        <h3>{$templateTitle}</h3>
    </div>

    <p>{gt text='Name'}: {$site.name}<br />
    {gt text='Site name'}: {$site.siteName}<br />
    {gt text='Site dns'}: {$site.siteDns}<br />
    {gt text='Database name'}: {$site.databaseName}</p>

    <table class="z-datatable" summary="{$templateTitle}">
        <colgroup>
            <col id="cName" />
            <col id="cVersion" />
            <col id="cDescription" />
            <col id="cActions" />
        </colgroup>
        <thead>
            <tr>
                <th id="hName" scope="row" class="z-left">{gt text='Name'}</th>
                <th id="hVersion" scope="row" class="z-left">{gt text='Version'}</th>
                <th id="hDescription" scope="row" class="z-left">{gt text='Description'}</th>
                <th id="hActions" scope="row" class="z-right">{gt text='Actions'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach item='module' from=$modules}
                <tr class="{cycle values='z-odd,z-even'}">
                    <th id="hRow{$module.id}" scope="row">{$module.name}</th>
                    <td headers="hRow{$module.id} hVersion">{$module.version}</td>
                    <td headers="hRow{$module.id} hDescription">{$module.description}</td>
                    <td headers="hRow{$module.id} hActions" width="80">
                       <div id="module_{$module.name}">
                           {$module.icons}
                       </div>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    <p><a href="javascript:history.back()" title="{gt text='Back to site list'}" class="z-icon-es-back">{gt text='Back to site list'}</a></p>
</div>
{include file="`$lct`/footer.tpl"}