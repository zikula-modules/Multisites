{* purpose of this template: templates view csv view *}
{multisitesTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='Templates.csv'}
{strip}"{gt text='Name'}";"{gt text='Description'}";"{gt text='Sql file'}";"{gt text='Parameters'}";"{gt text='Folders'}";"{gt text='Excluded tables'}";"{gt text='Workflow state'}"
;"{gt text='Projects'}"
;"{gt text='Sites'}"
{/strip}
{foreach item='template' from=$items}
{strip}
    "{$template.name}";"{$template.description}";"{$template.sqlFile}";"{$template.parameters}";"{$template.folders}";"{$template.excludedTables}";"{$item.workflowState|multisitesObjectState:false|lower}"
    ;"
        {if isset($template.Projects) && $template.Projects ne null}
            {foreach name='relationLoop' item='relatedItem' from=$template.Projects}
            {$relatedItem->getTitleFromDisplayPattern()|default:''}{if !$smarty.foreach.relationLoop.last}, {/if}
            {/foreach}
        {/if}
    "
    ;"
        {if isset($template.Sites) && $template.Sites ne null}
            {foreach name='relationLoop' item='relatedItem' from=$template.Sites}
            {$relatedItem->getTitleFromDisplayPattern()|default:''}{if !$smarty.foreach.relationLoop.last}, {/if}
            {/foreach}
        {/if}
    "
{/strip}
{/foreach}
