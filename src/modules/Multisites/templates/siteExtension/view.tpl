{* purpose of this template: site extensions list view *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
<div class="multisites-siteextension multisites-view">
    {gt text='Site extension list' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    {if $lct eq 'admin'}
        <div class="z-admin-content-pagetitle">
            {icon type='view' size='small' alt=$templateTitle}
            <h3>{$templateTitle}</h3>
        </div>
    {else}
        <h2>{$templateTitle}</h2>
    {/if}

    <p class="z-informationmsg">{gt text='A site consists of several extensions, which may be modules, themes or plugins.'}</p>

    {assign var='own' value=0}
    {if isset($showOwnEntries) && $showOwnEntries eq 1}
        {assign var='own' value=1}
    {/if}
    {assign var='all' value=0}
    {if isset($showAllEntries) && $showAllEntries eq 1}
        {gt text='Back to paginated view' assign='linkTitle'}
        <a href="{modurl modname='Multisites' type=$lct func='view' ot='siteExtension'}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
        {assign var='all' value=1}
    {else}
        {gt text='Show all entries' assign='linkTitle'}
        <a href="{modurl modname='Multisites' type=$lct func='view' ot='siteExtension' all=1}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
    {/if}

    {include file='siteExtension/view_quickNav.tpl' all=$all own=$own workflowStateFilter=false}{* see template file for available options *}

    {if $lct eq 'admin'}
    <form action="{modurl modname='Multisites' type='siteExtension' func='handleSelectedEntries' lct=$lct}" method="post" id="siteExtensionsViewForm" class="z-form">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    {/if}
        <table class="z-datatable">
            <colgroup>
                {if $lct eq 'admin'}
                    <col id="cSelect" />
                {/if}
                <col id="cName" />
                <col id="cExtensionVersion" />
                <col id="cExtensionType" />
                <col id="cSite" />
                <col id="cItemActions" />
            </colgroup>
            <thead>
            <tr>
                {if $lct eq 'admin'}
                    <th id="hSelect" scope="col" align="center" valign="middle">
                        <input type="checkbox" id="toggleSiteExtensions" />
                    </th>
                {/if}
                <th id="hName" scope="col" class="z-left">
                    {sortlink __linktext='Name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='name' sortdir=$sdir all=$all own=$own site=$site workflowState=$workflowState extensionType=$extensionType q=$q pageSize=$pageSize ot='siteExtension'}
                </th>
                <th id="hExtensionVersion" scope="col" class="z-left">
                    {sortlink __linktext='Extension version' currentsort=$sort modname='Multisites' type=$lct func='view' sort='extensionVersion' sortdir=$sdir all=$all own=$own site=$site workflowState=$workflowState extensionType=$extensionType q=$q pageSize=$pageSize ot='siteExtension'}
                </th>
                <th id="hExtensionType" scope="col" class="z-left">
                    {sortlink __linktext='Extension type' currentsort=$sort modname='Multisites' type=$lct func='view' sort='extensionType' sortdir=$sdir all=$all own=$own site=$site workflowState=$workflowState extensionType=$extensionType q=$q pageSize=$pageSize ot='siteExtension'}
                </th>
                <th id="hSite" scope="col" class="z-left">
                    {sortlink __linktext='Site' currentsort=$sort modname='Multisites' type=$lct func='view' sort='site' sortdir=$sdir all=$all own=$own site=$site workflowState=$workflowState extensionType=$extensionType q=$q pageSize=$pageSize ot='siteExtension'}
                </th>
                <th id="hItemActions" scope="col" class="z-right z-order-unsorted">{gt text='Actions'}</th>
            </tr>
            </thead>
            <tbody>
        
        {foreach item='siteExtension' from=$items}
            <tr class="{cycle values='z-odd, z-even'}">
                {if $lct eq 'admin'}
                    <td headers="hselect" align="center" valign="top">
                        <input type="checkbox" name="items[]" value="{$siteExtension.id}" class="siteextensions-checkbox" />
                    </td>
                {/if}
                <td headers="hName" class="z-left">
                    {$siteExtension.name|notifyfilters:'multisites.filterhook.siteextensions'}
                </td>
                <td headers="hExtensionVersion" class="z-left">
                    {$siteExtension.extensionVersion}
                </td>
                <td headers="hExtensionType" class="z-left">
                    {$siteExtension.extensionType|multisitesGetListEntry:'siteExtension':'extensionType'|safetext}
                </td>
                <td headers="hSite" class="z-left">
                    {if isset($siteExtension.Site) && $siteExtension.Site ne null}
                          {$siteExtension.Site->getTitleFromDisplayPattern()|default:""}
                    {else}
                        {gt text='Not set.'}
                    {/if}
                </td>
                <td id="itemActions{$siteExtension.id}" headers="hItemActions" class="z-right z-nowrap z-w02">
                    {if count($siteExtension._actions) gt 0}
                        {icon id="itemActions`$siteExtension.id`Trigger" type='options' size='extrasmall' __alt='Actions' class='z-pointer z-hide'}
                        {foreach item='option' from=$siteExtension._actions}
                            <a href="{$option.url.type|multisitesActionUrl:$option.url.func:$option.url.arguments}" title="{$option.linkTitle|safetext}"{if $option.icon eq 'preview'} target="_blank"{/if}>{icon type=$option.icon size='extrasmall' alt=$option.linkText|safetext}</a>
                        {/foreach}
                    
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            document.observe('dom:loaded', function() {
                                multisitesInitItemActions('siteExtension', 'view', 'itemActions{{$siteExtension.id}}');
                            });
                        /* ]]> */
                        </script>
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr class="z-{if $lct eq 'admin'}admin{else}data{/if}tableempty">
              <td class="z-left" colspan="{if $lct eq 'admin'}6{else}5{/if}">
            {gt text='No site extensions found.'}
              </td>
            </tr>
        {/foreach}
        
            </tbody>
        </table>
        
        {if !isset($showAllEntries) || $showAllEntries ne 1}
            {pager rowcount=$pager.numitems limit=$pager.itemsperpage display='page' modname='Multisites' type=$lct func='view' ot='siteExtension'}
        {/if}
    {if $lct eq 'admin'}
            <fieldset>
                <label for="multisitesAction">{gt text='With selected site extensions'}</label>
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
        {notifydisplayhooks eventname='multisites.ui_hooks.siteextensions.display_view' urlobject=$currentUrlObject assign='hooks'}
        {foreach key='providerArea' item='hook' from=$hooks}
            {$hook}
        {/foreach}
    {/if}
</div>
{include file="`$lct`/footer.tpl"}

<script type="text/javascript">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
        {{if $lct eq 'admin'}}
            {{* init the "toggle all" functionality *}}
            if ($('toggleSiteExtensions') != undefined) {
                $('toggleSiteExtensions').observe('click', function (e) {
                    Zikula.toggleInput('siteExtensionsViewForm');
                    e.stop()
                });
            }
        {{/if}}
    });
/* ]]> */
</script>
