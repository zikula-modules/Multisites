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

    <div class="z-center z-bold">
        [{pagerabc posvar='letter' forwardvars='module,type,func,ot'} | <a href="{modurl modname='Multisites' type='admin' func='view' ot='site'}" title="{gt text='All letters'}">{gt text='All'}</a>]
    </div>

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
                <col id="cLogo" />
                <col id="cSiteAlias" />
                <col id="cName" />
                <col id="cSiteName" />
{*                <col id="cSiteDns" />*}
                <col id="cSiteAdminName" />
                <col id="cFeatures" />
{*                <col id="cAllowedLocales" />*}
                <col id="cActive" />
                <col id="cItemActions" />
            </colgroup>
            <thead>
            <tr>
                {if $lct eq 'admin'}
                    <th id="hSelect" scope="col" align="center" valign="middle">
                        <input type="checkbox" id="toggleSites" />
                    </th>
                {/if}
                <th id="hLogo" scope="col" class="z-left">
                    {gt text='Logo'}
                </th>
                <th id="hSiteAlias" scope="col" class="z-left">
                    {sortlink __linktext='Alias' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteAlias' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState q=$q pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hName" scope="col" class="z-left">
                    {sortlink __linktext='Name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='name' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState q=$q pageSize=$pageSize active=$active ot='site'}
                </th>
                <th id="hSiteName" scope="col" class="z-left">
                    {sortlink __linktext='Site name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteName' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState q=$q pageSize=$pageSize active=$active ot='site'}
                </th>
{*                <th id="hSiteDns" scope="col" class="z-left">
                    {sortlink __linktext='Site dns' currentsort=$sort modname='Multisites' type=$lct func='view' sort='siteDns' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState q=$q pageSize=$pageSize active=$active ot='site'}
                </th>*}
                <th id="hSiteAdminName" scope="col" class="z-left">
                    {gt text='Admin details'}
                </th>
                <th id="hFeatures" scope="col" class="z-left">
                    {gt text='Features'}
                </th>
{*                <th id="hAllowedLocales" scope="col" class="z-left">
                    {gt text='Allowed locales'}
                </th>*}
                <th id="hActive" scope="col" class="z-center">
                    {sortlink __linktext='Active' currentsort=$sort modname='Multisites' type=$lct func='view' sort='active' sortdir=$sdir all=$all own=$own template=$template project=$project workflowState=$workflowState q=$q pageSize=$pageSize active=$active ot='site'}
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
                <td headers="hSiteAlias" class="z-left">
                    {$site.siteAlias}
                </td>
                <td headers="hName" class="z-left">
                    {if $basedOnDomains eq 1}
                        <a href="http://{$site.siteDns}/" title="{gt text='Visit this site'}" class="sitelink">{$site.name|notifyfilters:'multisites.filterhook.sites'}</a>
                    {else}
                        <a href="{$wwwroot}/{$site.siteDns}/" title="{gt text='Visit this site'}" class="sitelink">{$site.name|notifyfilters:'multisites.filterhook.sites'}</a>
                    {/if}
                    {if $site.description ne ''}
                        <br /><span class="z-sub">{$site.description}</span>
                    {/if}
                </td>
                <td headers="hSiteName" class="z-left">
                    {$site.siteName}
                    {if $site.siteDescription ne ''}
                        <br /><span class="z-sub">{$site.siteDescription}</span>
                    {/if}
                </td>
{*                <td headers="hSiteDns" class="z-left">
                    {$site.siteDns}
                </td>*}
                <td headers="hSiteAdminName" class="z-left">
                    <ul class="z-sub">
                        <li>{gt text='User'}: {$site.siteAdminName}</li>
                        <li>{gt text='Real name'}: {$site.siteAdminRealName}</li>
                        <li>{gt text='Email'}: <a href="mailto:{$site.siteAdminEmail}" title="{gt text='Send an email'}">{icon type='mail' size='extrasmall' __alt='Email'}</a></li>
                        <li>{gt text='Company'}: {$site.siteCompany}</li>
                    </ul>
                </td>
                <td headers="hFeatures" class="z-left">
                    <ul class="z-sub">
                        <li>{gt text='Project'}: {if isset($site.project) && $site.project ne null}{$site.project->getTitleFromDisplayPattern()|default:''}{else}{gt text='Not set.'}{/if}</li>
                        <li>{gt text='Template'}: {if isset($site.template) && $site.template ne null}{$site.template->getTitleFromDisplayPattern()|default:''}{else}{gt text='None (decoupled)'}{/if}</li>
                        <li>{gt text='Site database'}: {$site.databaseName} ({$site.databaseType})</li>
                        <li>{gt text='Creation date'}: {$site.createdDate|dateformat}</li>
                    </ul>
                </td>
{*                <td headers="hAllowedLocales" class="z-left">
                    {if count($site.allowedLocales) gt 0}
                        <ul>
                        {foreach item='locale' from=$site.allowedLocales}
                            <li>{$locale|safetext}</li>
                        {/foreach}
                        </ul>
                    {else}
                        {gt text='All'}
                    {/if}
                </td>*}
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
              <td class="z-left" colspan="{if $lct eq 'admin'}9{else}8{/if}">
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
                    <option value="cleartemplates">{gt text='Clear all cache and compile directories' domain='zikula'}</option>
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

        $$('a.sitelink').each(function (elem) {
            elem.setAttribute('target', '_blank');
        });
    });
/* ]]> */
</script>
