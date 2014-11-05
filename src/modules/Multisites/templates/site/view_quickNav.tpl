{* purpose of this template: sites view filter form *}
{checkpermissionblock component='Multisites:Site:' instance='::' level='ACCESS_EDIT'}
{assign var='objectType' value='site'}
<form action="{$modvars.ZConfig.entrypoint|default:'index.php'}" method="get" id="multisitesSiteQuickNavForm" class="multisites-quicknav">
    <fieldset>
        <h3>{gt text='Quick navigation'}</h3>
        <input type="hidden" name="module" value="{modgetinfo modname='Multisites' info='url'}" />
        <input type="hidden" name="type" value="{$lct}" />
        <input type="hidden" name="ot" value="site" />
        <input type="hidden" name="func" value="view" />
        <input type="hidden" name="all" value="{$all|default:0}" />
        <input type="hidden" name="own" value="{$own|default:0}" />
        {gt text='All' assign='lblDefault'}
        {if !isset($templateFilter) || $templateFilter eq true}
                <label for="template">{gt text='Templates'}</label>
                {php}
                    $mainSearchTerm = '';
                    if (isset($_GET['q'])) {
                        $mainSearchTerm = $_GET['q'];
                        unset($_GET['q']);
                    }
                {/php}
                {modapifunc modname='Multisites' type='selection' func='getEntities' ot='template' useJoins=false assign='listEntries'}
                <select id="template" name="template">
                    <option value="">{$lblDefault}</option>
                {foreach item='option' from=$listEntries}
                    {assign var='entryId' value=$option.id}
                    <option value="{$entryId}"{if $entryId eq $template} selected="selected"{/if}>{$option->getTitleFromDisplayPattern()}</option>
                {/foreach}
                </select>
                {php}
                    if (!empty($mainSearchTerm)) {
                        $_GET['q'] = $mainSearchTerm;
                    }
                {/php}
        {/if}
        {if !isset($projectFilter) || $projectFilter eq true}
                <label for="project">{gt text='Projects'}</label>
                {php}
                    $mainSearchTerm = '';
                    if (isset($_GET['q'])) {
                        $mainSearchTerm = $_GET['q'];
                        unset($_GET['q']);
                    }
                {/php}
                {modapifunc modname='Multisites' type='selection' func='getEntities' ot='project' useJoins=false assign='listEntries'}
                <select id="project" name="project">
                    <option value="">{$lblDefault}</option>
                {foreach item='option' from=$listEntries}
                    {assign var='entryId' value=$option.id}
                    <option value="{$entryId}"{if $entryId eq $project} selected="selected"{/if}>{$option->getTitleFromDisplayPattern()}</option>
                {/foreach}
                </select>
                {php}
                    if (!empty($mainSearchTerm)) {
                        $_GET['q'] = $mainSearchTerm;
                    }
                {/php}
        {/if}
        {if !isset($workflowStateFilter) || $workflowStateFilter eq true}
                <label for="workflowState">{gt text='Workflow state'}</label>
                <select id="workflowState" name="workflowState">
                    <option value="">{$lblDefault}</option>
                {foreach item='option' from=$workflowStateItems}
                <option value="{$option.value}"{if $option.title ne ''} title="{$option.title|safetext}"{/if}{if $option.value eq $workflowState} selected="selected"{/if}>{$option.text|safetext}</option>
                {/foreach}
                </select>
        {/if}
        {if !isset($searchFilter) || $searchFilter eq true}
                <label for="searchTerm">{gt text='Search'}</label>
                <input type="text" id="searchTerm" name="q" value="{$q}" />
        {/if}
        {if !isset($sorting) || $sorting eq true}
                <label for="sortBy">{gt text='Sort by'}</label>
                &nbsp;
                <select id="sortBy" name="sort">
                    <option value="id"{if $sort eq 'id'} selected="selected"{/if}>{gt text='Id'}</option>
                    <option value="name"{if $sort eq 'name'} selected="selected"{/if}>{gt text='Name'}</option>
                    <option value="description"{if $sort eq 'description'} selected="selected"{/if}>{gt text='Description'}</option>
                    <option value="siteAlias"{if $sort eq 'siteAlias'} selected="selected"{/if}>{gt text='Site alias'}</option>
                    <option value="siteName"{if $sort eq 'siteName'} selected="selected"{/if}>{gt text='Site name'}</option>
                    <option value="siteDescription"{if $sort eq 'siteDescription'} selected="selected"{/if}>{gt text='Site description'}</option>
                    <option value="siteAdminName"{if $sort eq 'siteAdminName'} selected="selected"{/if}>{gt text='Site admin name'}</option>
                    <option value="siteAdminPassword"{if $sort eq 'siteAdminPassword'} selected="selected"{/if}>{gt text='Site admin password'}</option>
                    <option value="siteAdminRealName"{if $sort eq 'siteAdminRealName'} selected="selected"{/if}>{gt text='Site admin real name'}</option>
                    <option value="siteAdminEmail"{if $sort eq 'siteAdminEmail'} selected="selected"{/if}>{gt text='Site admin email'}</option>
                    <option value="siteCompany"{if $sort eq 'siteCompany'} selected="selected"{/if}>{gt text='Site company'}</option>
                    <option value="siteDns"{if $sort eq 'siteDns'} selected="selected"{/if}>{gt text='Site dns'}</option>
                    <option value="databaseName"{if $sort eq 'databaseName'} selected="selected"{/if}>{gt text='Database name'}</option>
                    <option value="databaseUserName"{if $sort eq 'databaseUserName'} selected="selected"{/if}>{gt text='Database user name'}</option>
                    <option value="databasePassword"{if $sort eq 'databasePassword'} selected="selected"{/if}>{gt text='Database password'}</option>
                    <option value="databaseHost"{if $sort eq 'databaseHost'} selected="selected"{/if}>{gt text='Database host'}</option>
                    <option value="databaseType"{if $sort eq 'databaseType'} selected="selected"{/if}>{gt text='Database type'}</option>
                    <option value="logo"{if $sort eq 'logo'} selected="selected"{/if}>{gt text='Logo'}</option>
                    <option value="favIcon"{if $sort eq 'favIcon'} selected="selected"{/if}>{gt text='Fav icon'}</option>
                    <option value="allowedLocales"{if $sort eq 'allowedLocales'} selected="selected"{/if}>{gt text='Allowed locales'}</option>
                    <option value="parametersCsvFile"{if $sort eq 'parametersCsvFile'} selected="selected"{/if}>{gt text='Parameters csv file'}</option>
                    <option value="parametersArray"{if $sort eq 'parametersArray'} selected="selected"{/if}>{gt text='Parameters array'}</option>
                    <option value="active"{if $sort eq 'active'} selected="selected"{/if}>{gt text='Active'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdUserId"{if $sort eq 'createdUserId'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                </select>
                <select id="sortDir" name="sortdir">
                    <option value="asc"{if $sdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                    <option value="desc"{if $sdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                </select>
        {else}
            <input type="hidden" name="sort" value="{$sort}" />
            <input type="hidden" name="sdir" value="{if $sdir eq 'desc'}asc{else}desc{/if}" />
        {/if}
        {if !isset($pageSizeSelector) || $pageSizeSelector eq true}
                <label for="num">{gt text='Page size'}</label>
                <select id="num" name="num">
                    <option value="5"{if $pageSize eq 5} selected="selected"{/if}>5</option>
                    <option value="10"{if $pageSize eq 10} selected="selected"{/if}>10</option>
                    <option value="15"{if $pageSize eq 15} selected="selected"{/if}>15</option>
                    <option value="20"{if $pageSize eq 20} selected="selected"{/if}>20</option>
                    <option value="30"{if $pageSize eq 30} selected="selected"{/if}>30</option>
                    <option value="50"{if $pageSize eq 50} selected="selected"{/if}>50</option>
                    <option value="100"{if $pageSize eq 100} selected="selected"{/if}>100</option>
                </select>
        {/if}
        {if !isset($activeFilter) || $activeFilter eq true}
                <label for="active">{gt text='Active'}</label>
                <select id="active" name="active">
                    <option value="">{$lblDefault}</option>
                {foreach item='option' from=$activeItems}
                    <option value="{$option.value}"{if $option.value eq $active} selected="selected"{/if}>{$option.text|safetext}</option>
                {/foreach}
                </select>
        {/if}
        <input type="submit" name="updateview" id="quicknavSubmit" value="{gt text='OK'}" />
    </fieldset>
</form>

<script type="text/javascript">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
        multisitesInitQuickNavigation('site');
        {{if isset($searchFilter) && $searchFilter eq false}}
            {{* we can hide the submit button if we have no quick search field *}}
            $('quicknavSubmit').addClassName('z-hide');
        {{/if}}
    });
/* ]]> */
</script>
{/checkpermissionblock}
