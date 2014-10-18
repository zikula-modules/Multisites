{* purpose of this template: templates list view *}
{assign var='lct' value='user'}
{if isset($smarty.get.lct) && $smarty.get.lct eq 'admin'}
    {assign var='lct' value='admin'}
{/if}
{include file="`$lct`/header.tpl"}
<div class="multisites-template multisites-view">
    {gt text='Template list' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    {if $lct eq 'admin'}
        <div class="z-admin-content-pagetitle">
            {icon type='view' size='small' alt=$templateTitle}
            <h3>{$templateTitle}</h3>
        </div>
    {else}
        <h2>{$templateTitle}</h2>
    {/if}

    <p class="z-informationmsg">{gt text='A site template represents a blueprint for several sites. Each template may be assigned to all or specific projects.'}</p>

    {if $canBeCreated}
        {checkpermissionblock component='Multisites:Template:' instance='::' level='ACCESS_EDIT'}
            {gt text='Create template' assign='createTitle'}
            <a href="{modurl modname='Multisites' type=$lct func='edit' ot='template'}" title="{$createTitle}" class="z-icon-es-add">{$createTitle}</a>
        {/checkpermissionblock}
    {/if}
    {assign var='own' value=0}
    {if isset($showOwnEntries) && $showOwnEntries eq 1}
        {assign var='own' value=1}
    {/if}
    {assign var='all' value=0}
    {if isset($showAllEntries) && $showAllEntries eq 1}
        {gt text='Back to paginated view' assign='linkTitle'}
        <a href="{modurl modname='Multisites' type=$lct func='view' ot='template'}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
        {assign var='all' value=1}
    {else}
        {gt text='Show all entries' assign='linkTitle'}
        <a href="{modurl modname='Multisites' type=$lct func='view' ot='template' all=1}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
    {/if}

    {include file='template/view_quickNav.tpl' all=$all own=$own workflowStateFilter=false}{* see template file for available options *}

    {if $lct eq 'admin'}
    <form action="{modurl modname='Multisites' type='template' func='handleSelectedEntries' lct=$lct}" method="post" id="templatesViewForm" class="z-form">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    {/if}
        <table class="z-datatable">
            <colgroup>
                {*if $lct eq 'admin'}
                    <col id="cSelect" />
                {/if*}
                <col id="cName" />
                <col id="cDescription" />
                <col id="cSqlFile" />
                <col id="cProjects" />
                <col id="cSites" />
                <col id="cParameters" />
                <col id="cFolders" />
                <col id="cExcludedTables" />
                <col id="cItemActions" />
            </colgroup>
            <thead>
            <tr>
                {*if $lct eq 'admin'}
                    <th id="hSelect" scope="col" align="center" valign="middle">
                        <input type="checkbox" id="toggleTemplates" />
                    </th>
                {/if*}
                <th id="hName" scope="col" class="z-left">
                    {sortlink __linktext='Name' currentsort=$sort modname='Multisites' type=$lct func='view' sort='name' sortdir=$sdir all=$all own=$own workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize ot='template'}
                </th>
                <th id="hDescription" scope="col" class="z-left">
                    {sortlink __linktext='Description' currentsort=$sort modname='Multisites' type=$lct func='view' sort='description' sortdir=$sdir all=$all own=$own workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize ot='template'}
                </th>
                <th id="hSqlFile" scope="col" class="z-left">
                    {sortlink __linktext='Sql file' currentsort=$sort modname='Multisites' type=$lct func='view' sort='sqlFile' sortdir=$sdir all=$all own=$own workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize ot='template'}
                </th>
                <th id="hProjects" scope="col" class="z-left">
                    {gt text='Projects'}
                </th>
                <th id="hSites" scope="col" class="z-left">
                    {gt text='Sites'}
                </th>
                <th id="hParameters" scope="col" class="z-left">
                    {gt text='Parameters'}
                </th>
                <th id="hFolders" scope="col" class="z-left">
                    {gt text='Folders'}
                </th>
                <th id="hExcludedTables" scope="col" class="z-left">
                    {gt text='Excluded tables'}
                </th>
                <th id="hItemActions" scope="col" class="z-right z-order-unsorted">{gt text='Actions'}</th>
            </tr>
            </thead>
            <tbody>
        
        {foreach item='template' from=$items}
            <tr class="{cycle values='z-odd, z-even'}">
                {*if $lct eq 'admin'}
                    <td headers="hselect" align="center" valign="top">
                        <input type="checkbox" name="items[]" value="{$template.id}" class="templates-checkbox" />
                    </td>
                {/if*}
                <td headers="hName" class="z-left">
                    {$template.name|notifyfilters:'multisites.filterhook.templates'}
                </td>
                <td headers="hDescription" class="z-left">
                    {$template.description}
                </td>
                <td headers="hSqlFile" class="z-left">
                      <a href="{$template.sqlFileFullPathURL}" title="{$template->getTitleFromDisplayPattern()|replace:"\"":""}"{if $template.sqlFileMeta.isImage} rel="imageviewer[template]"{/if}>
                      {if $template.sqlFileMeta.isImage}
                          {thumb image=$template.sqlFileFullPath objectid="template-`$template.id`" preset=$templateThumbPresetSqlFile tag=true img_alt=$template->getTitleFromDisplayPattern()}
                      {else}
                          {gt text='Download'} ({$template.sqlFileMeta.size|multisitesGetFileSize:$template.sqlFileFullPath:false:false})
                      {/if}
                      </a>
                </td>
                <td headers="hProjects" class="z-left">
                    {if count($template.projects) gt 0}
                        <ul>
                        {foreach item='project' from=$template.projects}
                            <li>{$project->getTitleFromDisplayPattern()}</li>
                        {/foreach}
                        </ul>
                    {else}
                        {gt text='None'}
                    {/if}
                </td>
                <td headers="hSites" class="z-left">
                    <a href="{modurl modname='Multisites' type='admin' func='view' ot='site' template=$template.id}" title="{gt text='View sites assigned to this template'}">{gt text='Site list'}</a>
                </td>
                <td headers="hParameters" class="z-left">
                    {if count($template.parameters) gt 0}
                        <ul>
                        {foreach item='parameter' from=$template.parameters}
                            <li>{$parameter|safetext}</li>
                        {/foreach}
                        </ul>
                    {else}
                        {gt text='None'}
                    {/if}
                </td>
                <td headers="hFolders" class="z-left">
                    {if count($template.folders) gt 0}
                        <ul>
                        {foreach item='folder' from=$template.folders}
                            <li>{$folder|safetext}</li>
                        {/foreach}
                        </ul>
                    {else}
                        {gt text='None'}
                    {/if}
                </td>
                <td headers="hExcludedTables" class="z-left">
                    {if count($template.excludedTables) gt 0}
                        <ul>
                        {foreach item='tableName' from=$template.excludedTables}
                            <li>{$tableName|safetext}</li>
                        {/foreach}
                        </ul>
                    {else}
                        {gt text='None'}
                    {/if}
                </td>
                <td id="itemActions{$template.id}" headers="hItemActions" class="z-right z-nowrap z-w02">
                    {if count($template._actions) gt 0}
                        {icon id="itemActions`$template.id`Trigger" type='options' size='extrasmall' __alt='Actions' class='z-pointer z-hide'}
                        {foreach item='option' from=$template._actions}
                            <a href="{$option.url.type|multisitesActionUrl:$option.url.func:$option.url.arguments}" title="{$option.linkTitle|safetext}"{if $option.icon eq 'preview'} target="_blank"{/if}>{icon type=$option.icon size='extrasmall' alt=$option.linkText|safetext}</a>
                        {/foreach}
                    
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            document.observe('dom:loaded', function() {
                                multisitesInitItemActions('template', 'view', 'itemActions{{$template.id}}');
                            });
                        /* ]]> */
                        </script>
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr class="z-{if $lct eq 'admin'}admin{else}data{/if}tableempty">
              <td class="z-left" colspan="{*if $lct eq 'admin'}10{else*}9{*/if*}">
            {gt text='No templates found.'}
              </td>
            </tr>
        {/foreach}
        
            </tbody>
        </table>
        
        {if !isset($showAllEntries) || $showAllEntries ne 1}
            {pager rowcount=$pager.numitems limit=$pager.itemsperpage display='page' modname='Multisites' type=$lct func='view' ot='template'}
        {/if}
    {if $lct eq 'admin'}
{*            <fieldset>
                <label for="multisitesAction">{gt text='With selected templates'}</label>
                <select id="multisitesAction" name="action">
                    <option value="">{gt text='Choose action'}</option>
                    <option value="delete" title="{gt text='Delete content permanently.'}">{gt text='Delete'}</option>
                </select>
                <input type="submit" value="{gt text='Submit'}" />
            </fieldset>*}
        </div>
    </form>
    {/if}

    
    {if $lct ne 'admin'}
        {notifydisplayhooks eventname='multisites.ui_hooks.templates.display_view' urlobject=$currentUrlObject assign='hooks'}
        {foreach key='providerArea' item='hook' from=$hooks}
            {$hook}
        {/foreach}
    {/if}
</div>
{include file="`$lct`/footer.tpl"}
{*
<script type="text/javascript">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
        {{if $lct eq 'admin'}}
            {{* init the "toggle all" functionality * }}
            if ($('toggleTemplates') != undefined) {
                $('toggleTemplates').observe('click', function (e) {
                    Zikula.toggleInput('templatesViewForm');
                    e.stop()
                });
            }
        {{/if}}
    });
/* ]]> */
</script>
*}
