{* purpose of this template: sites list view *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
<div class="multisites-site multisites-view">
    {gt text='Site list' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    {if $lct eq 'admin'}
        <div class="z-admin-content-pagetitle">
            {icon type='view' size='small' alt=$templateTitle}
            <h3>{$templateTitle}</h3>
        </div>
    {else}
        <h2>{$templateTitle}</h2>
    {/if}

    <p class="z-informationmsg">{gt text='Each site is assigned to a project and instance of a certain site template.'}</p>

    {if $canBeCreated}
        {checkpermissionblock component='Multisites:Site:' instance='::' level='ACCESS_EDIT'}
            {gt text='Create site' assign='createTitle'}
            <a href="{modurl modname='Multisites' type=$lct func='edit' ot='site'}" title="{$createTitle}" class="z-icon-es-add">{$createTitle}</a>
        {/checkpermissionblock}
    {/if}
    {assign var='own' value=0}
    {if isset($showOwnEntries) && $showOwnEntries eq 1}
        {assign var='own' value=1}
    {/if}
    {assign var='all' value=0}
    {if isset($showAllEntries) && $showAllEntries eq 1}
        {gt text='Back to paginated view' assign='linkTitle'}
        <a href="{modurl modname='Multisites' type=$lct func='view' ot='site'}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
        {assign var='all' value=1}
    {else}
        {gt text='Show all entries' assign='linkTitle'}
        <a href="{modurl modname='Multisites' type=$lct func='view' ot='site' all=1}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
    {/if}

    {include file='site/view_quickNav.tpl' all=$all own=$own workflowStateFilter=false}{* see template file for available options *}

    {if $lct eq 'admin'}
    <form action="{modurl modname='Multisites' type='site' func='handleSelectedEntries' lct=$lct}" method="post" id="sitesViewForm" class="z-form">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    {/if}
        <table class="z-datatable">
            <colgroup>
                {if $lct eq 'admin'}
                    <col id="cSelect" />
                {/if}
                <col id="cName" />
                <col id="cDescription" />
                <col id="cSiteAlias" />
                <col id="cSiteName" />
                <col id="cSiteDescription" />
                <col id="cSiteAdminName" />
                <col id="cSiteAdminPassword" />
                <col id="cSiteAdminRealName" />
                <col id="cSiteAdminEmail" />
                <col id="cSiteCompany" />
                <col id="cSiteDns" />
                <col id="cDatabaseName" />
                <col id="cDatabaseUserName" />
                <col id="cDatabasePassword" />
                <col id="cDatabaseHost" />
                <col id="cDatabaseType" />
                <col id="cLogo" />
                <col id="cFavIcon" />
                <col id="cParametersCsvFile" />
                <col id="cActive" />
                <col id="cTemplate" />
                <col id="cProject" />
                <col id="cItemActions" />
            </colgroup>
            <thead>
            <tr>
                {if $lct eq 'admin'}
                    <th id="hSelect" scope="col" align="center" valign="middle">
                        <input type="checkbox" id="toggleSites" />
                    </th>
                {/if}
                <th id="hName" scope="col" class="z-left">
                    {sortlink __linktext='Name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='name' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hDescription" scope="col" class="z-left">
                    {sortlink __linktext='Description' currentsort=$sort modname='Multisites' type=$lct func='view' sort='description' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteAlias" scope="col" class="z-left">
                    {sortlink __linktext='Site alias' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteAlias' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteName" scope="col" class="z-left">
                    {sortlink __linktext='Site name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteName' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteDescription" scope="col" class="z-left">
                    {sortlink __linktext='Site description' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteDescription' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteAdminName" scope="col" class="z-left">
                    {sortlink __linktext='Site admin name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteAdminName' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteAdminPassword" scope="col" class="z-left">
                    {sortlink __linktext='Site admin password' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteAdminPassword' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteAdminRealName" scope="col" class="z-left">
                    {sortlink __linktext='Site admin real name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteAdminRealName' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteAdminEmail" scope="col" class="z-left">
                    {sortlink __linktext='Site admin email' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteAdminEmail' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteCompany" scope="col" class="z-left">
                    {sortlink __linktext='Site company' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteCompany' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteDns" scope="col" class="z-left">
                    {sortlink __linktext='Site dns' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteDns' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hDatabaseName" scope="col" class="z-left">
                    {sortlink __linktext='Database name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='databaseName' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hDatabaseUserName" scope="col" class="z-left">
                    {sortlink __linktext='Database user name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='databaseUserName' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hDatabasePassword" scope="col" class="z-left">
                    {sortlink __linktext='Database password' currentsort=$sort modname='Multisites' type=$lct func='view' sort='databasePassword' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hDatabaseHost" scope="col" class="z-left">
                    {sortlink __linktext='Database host' currentsort=$sort modname='Multisites' type=$lct func='view' sort='databaseHost' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hDatabaseType" scope="col" class="z-left">
                    {sortlink __linktext='Database type' currentsort=$sort modname='Multisites' type=$lct func='view' sort='databaseType' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hLogo" scope="col" class="z-left">
                    {sortlink __linktext='Logo' currentsort=$sort modname='Multisites' type=$lct func='view' sort='logo' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hFavIcon" scope="col" class="z-left">
                    {sortlink __linktext='Fav icon' currentsort=$sort modname='Multisites' type=$lct func='view' sort='favIcon' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hParametersCsvFile" scope="col" class="z-left">
                    {sortlink __linktext='Parameters csv file' currentsort=$sort modname='Multisites' type=$lct func='view' sort='parametersCsvFile' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hActive" scope="col" class="z-center">
                    {sortlink __linktext='Active' currentsort=$sort modname='Multisites' type=$lct func='view' sort='active' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hTemplate" scope="col" class="z-left">
                    {sortlink __linktext='Template' currentsort=$sort modname='Multisites' type=$lct func='view' sort='template' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hProject" scope="col" class="z-left">
                    {sortlink __linktext='Project' currentsort=$sort modname='Multisites' type=$lct func='view' sort='project' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hItemActions" scope="col" class="z-right z-order-unsorted">{gt text='Actions'}</th>
            </tr>
            </thead>
            <tbody>
        
        {foreach item='site' from=$items}
            <tr class="{cycle values='z-odd, z-even'}">
                {if $lct eq 'admin'}
                    <td headers="hselect" align="center" valign="top">
                        <input type="checkbox" name="items[]" value="{$site.id}" class="sites-checkbox" />
                    </td>
                {/if}
                <td headers="hName" class="z-left">
                    {$site.name|notifyfilters:'multisites.filterhook.sites'}
                </td>
                <td headers="hDescription" class="z-left">
                    {$site.description}
                </td>
                <td headers="hSiteAlias" class="z-left">
                    {$site.siteAlias}
                </td>
                <td headers="hSiteName" class="z-left">
                    {$site.siteName}
                </td>
                <td headers="hSiteDescription" class="z-left">
                    {$site.siteDescription}
                </td>
                <td headers="hSiteAdminName" class="z-left">
                    {$site.siteAdminName}
                </td>
                <td headers="hSiteAdminPassword" class="z-left">
                </td>
                <td headers="hSiteAdminRealName" class="z-left">
                    {$site.siteAdminRealName}
                </td>
                <td headers="hSiteAdminEmail" class="z-left">
                    <a href="mailto:{$site.siteAdminEmail}" title="{gt text='Send an email'}">{icon type='mail' size='extrasmall' __alt='Email'}</a>
                </td>
                <td headers="hSiteCompany" class="z-left">
                    {$site.siteCompany}
                </td>
                <td headers="hSiteDns" class="z-left">
                    {$site.siteDns}
                </td>
                <td headers="hDatabaseName" class="z-left">
                    {$site.databaseName}
                </td>
                <td headers="hDatabaseUserName" class="z-left">
                    {$site.databaseUserName}
                </td>
                <td headers="hDatabasePassword" class="z-left">
                </td>
                <td headers="hDatabaseHost" class="z-left">
                    {$site.databaseHost}
                </td>
                <td headers="hDatabaseType" class="z-left">
                    {$site.databaseType}
                </td>
                <td headers="hLogo" class="z-left">
                    {if $site.logo ne ''}
                      <a href="{$site.logoFullPathURL}" title="{$site->getTitleFromDisplayPattern()|replace:"\"":""}"{if $site.logoMeta.isImage} rel="imageviewer[site]"{/if}>
                      {if $site.logoMeta.isImage}
                          {thumb image=$site.logoFullPath objectid="site-`$site.id`" preset=$siteThumbPresetLogo tag=true img_alt=$site->getTitleFromDisplayPattern()}
                      {else}
                          {gt text='Download'} ({$site.logoMeta.size|multisitesGetFileSize:$site.logoFullPath:false:false})
                      {/if}
                      </a>
                    {else}&nbsp;{/if}
                </td>
                <td headers="hFavIcon" class="z-left">
                    {if $site.favIcon ne ''}
                      <a href="{$site.favIconFullPathURL}" title="{$site->getTitleFromDisplayPattern()|replace:"\"":""}"{if $site.favIconMeta.isImage} rel="imageviewer[site]"{/if}>
                      {if $site.favIconMeta.isImage}
                          {thumb image=$site.favIconFullPath objectid="site-`$site.id`" preset=$siteThumbPresetFavIcon tag=true img_alt=$site->getTitleFromDisplayPattern()}
                      {else}
                          {gt text='Download'} ({$site.favIconMeta.size|multisitesGetFileSize:$site.favIconFullPath:false:false})
                      {/if}
                      </a>
                    {else}&nbsp;{/if}
                </td>
                <td headers="hParametersCsvFile" class="z-left">
                    {if $site.parametersCsvFile ne ''}
                      <a href="{$site.parametersCsvFileFullPathURL}" title="{$site->getTitleFromDisplayPattern()|replace:"\"":""}"{if $site.parametersCsvFileMeta.isImage} rel="imageviewer[site]"{/if}>
                      {if $site.parametersCsvFileMeta.isImage}
                          {thumb image=$site.parametersCsvFileFullPath objectid="site-`$site.id`" preset=$siteThumbPresetParametersCsvFile tag=true img_alt=$site->getTitleFromDisplayPattern()}
                      {else}
                          {gt text='Download'} ({$site.parametersCsvFileMeta.size|multisitesGetFileSize:$site.parametersCsvFileFullPath:false:false})
                      {/if}
                      </a>
                    {else}&nbsp;{/if}
                </td>
                <td headers="hActive" class="z-center">
                    {assign var='itemid' value=$site.id}
                    <a id="toggleActive{$itemid}" href="javascript:void(0);" class="z-hide">
                    {if $site.active}
                        {icon type='ok' size='extrasmall' __alt='Yes' id="yesactive_`$itemid`" __title='This setting is enabled. Click here to disable it.'}
                        {icon type='cancel' size='extrasmall' __alt='No' id="noactive_`$itemid`" __title='This setting is disabled. Click here to enable it.' class='z-hide'}
                    {else}
                        {icon type='ok' size='extrasmall' __alt='Yes' id="yesactive_`$itemid`" __title='This setting is enabled. Click here to disable it.' class='z-hide'}
                        {icon type='cancel' size='extrasmall' __alt='No' id="noactive_`$itemid`" __title='This setting is disabled. Click here to enable it.'}
                    {/if}
                    </a>
                    <noscript><div id="noscriptActive{$itemid}">
                        {$site.active|yesno:true}
                    </div></noscript>
                </td>
                <td headers="hTemplate" class="z-left">
                    {if isset($site.Template) && $site.Template ne null}
                          {$site.Template->getTitleFromDisplayPattern()|default:""}
                    {else}
                        {gt text='Not set.'}
                    {/if}
                </td>
                <td headers="hProject" class="z-left">
                    {if isset($site.Project) && $site.Project ne null}
                          {$site.Project->getTitleFromDisplayPattern()|default:""}
                    {else}
                        {gt text='Not set.'}
                    {/if}
                </td>
                <td id="itemActions{$site.id}" headers="hItemActions" class="z-right z-nowrap z-w02">
                    {if count($site._actions) gt 0}
                        {icon id="itemActions`$site.id`Trigger" type='options' size='extrasmall' __alt='Actions' class='z-pointer z-hide'}
                        {foreach item='option' from=$site._actions}
                            <a href="{$option.url.type|multisitesActionUrl:$option.url.func:$option.url.arguments}" title="{$option.linkTitle|safetext}"{if $option.icon eq 'preview'} target="_blank"{/if}>{icon type=$option.icon size='extrasmall' alt=$option.linkText|safetext}</a>
                        {/foreach}
                    
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            document.observe('dom:loaded', function() {
                                multisitesInitItemActions('site', 'view', 'itemActions{{$site.id}}');
                            });
                        /* ]]> */
                        </script>
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr class="z-{if $lct eq 'admin'}admin{else}data{/if}tableempty">
              <td class="z-left" colspan="{if $lct eq 'admin'}24{else}23{/if}">
            {gt text='No sites found.'}
              </td>
            </tr>
        {/foreach}
        
            </tbody>
        </table>
        
        {if !isset($showAllEntries) || $showAllEntries ne 1}
            {pager rowcount=$pager.numitems limit=$pager.itemsperpage display='page' modname='Multisites' type=$lct func='view' ot='site'}
        {/if}
    {if $lct eq 'admin'}
            <fieldset>
                <label for="multisitesAction">{gt text='With selected sites'}</label>
                <select id="multisitesAction" name="action">
                    <option value="">{gt text='Choose action'}</option>
                    <option value="delete" title="{gt text='Delete content permanently.'}">{gt text='Delete'}</option>
                </select>
                <input type="submit" value="{gt text='Submit'}" />
            </fieldset>
        </div>
    </form>
    {/if}

    
    {if $lct ne 'admin'}
        {notifydisplayhooks eventname='multisites.ui_hooks.sites.display_view' urlobject=$currentUrlObject assign='hooks'}
        {foreach key='providerArea' item='hook' from=$hooks}
            {$hook}
        {/foreach}
    {/if}
</div>
{include file="`$lct`/footer.tpl"}

<script type="text/javascript">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
        {{foreach item='site' from=$items}}
            {{assign var='itemid' value=$site.id}}
            multisitesInitToggle('site', 'active', '{{$itemid}}');
        {{/foreach}}
        {{if $lct eq 'admin'}}
            {{* init the "toggle all" functionality *}}
            if ($('toggleSites') != undefined) {
                $('toggleSites').observe('click', function (e) {
                    Zikula.toggleInput('sitesViewForm');
                    e.stop()
                });
            }
        {{/if}}
    });
/* ]]> */
</script>
