{* purpose of this template: sites view csv view *}
{multisitesTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='Sites.csv'}
{strip}"{gt text='Name'}";"{gt text='Description'}";"{gt text='Site alias'}";"{gt text='Site name'}";"{gt text='Site description'}";"{gt text='Site admin name'}";"{gt text='Site admin password'}";"{gt text='Site admin real name'}";"{gt text='Site admin email'}";"{gt text='Site company'}";"{gt text='Site dns'}";"{gt text='Database name'}";"{gt text='Database user name'}";"{gt text='Database password'}";"{gt text='Database host'}";"{gt text='Database type'}";"{gt text='Logo'}";"{gt text='Fav icon'}";"{gt text='Allowed locales'}";"{gt text='Parameters csv file'}";"{gt text='Parameters array'}";"{gt text='Active'}";"{gt text='Workflow state'}"
;"{gt text='Template'}";"{gt text='Project'}"
;"{gt text='Extensions'}"
{/strip}
{foreach item='site' from=$items}
{strip}
    "{$site.name}";"{$site.description}";"{$site.siteAlias}";"{$site.siteName}";"{$site.siteDescription}";"{$site.siteAdminName}";"";"{$site.siteAdminRealName}";"{$site.siteAdminEmail}";"{$site.siteCompany}";"{$site.siteDns}";"{$site.databaseName}";"{$site.databaseUserName}";"";"{$site.databaseHost}";"{$site.databaseType}";"{$site.logo}";"{$site.favIcon}";"{$site.allowedLocales}";"{$site.parametersCsvFile}";"{$site.parametersArray}";"{if !$site.active}0{else}1{/if}";"{$item.workflowState|multisitesObjectState:false|lower}"
    ;"{if isset($site.Template) && $site.Template ne null}{$site.Template->getTitleFromDisplayPattern()|default:''}{/if}";"{if isset($site.Project) && $site.Project ne null}{$site.Project->getTitleFromDisplayPattern()|default:''}{/if}"
    ;"
        {if isset($site.Extensions) && $site.Extensions ne null}
            {foreach name='relationLoop' item='relatedItem' from=$site.Extensions}
            {$relatedItem->getTitleFromDisplayPattern()|default:''}{if !$smarty.foreach.relationLoop.last}, {/if}
            {/foreach}
        {/if}
    "
{/strip}
{/foreach}
