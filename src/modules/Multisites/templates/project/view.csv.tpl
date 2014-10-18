{* purpose of this template: projects view csv view *}
{multisitesTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='Projects.csv'}
{strip}"{gt text='Name'}";"{gt text='Workflow state'}"
;"{gt text='Sites'}"
;"{gt text='Templates'}"{/strip}
{foreach item='project' from=$items}
{strip}
    "{$project.name}";"{$item.workflowState|multisitesObjectState:false|lower}"
    ;"
        {if isset($project.Sites) && $project.Sites ne null}
            {foreach name='relationLoop' item='relatedItem' from=$project.Sites}
            {$relatedItem->getTitleFromDisplayPattern()|default:''}{if !$smarty.foreach.relationLoop.last}, {/if}
            {/foreach}
        {/if}
    "
    ;"
        {if isset($project.Templates) && $project.Templates ne null}
            {foreach name='relationLoop' item='relatedItem' from=$project.Templates}
            {$relatedItem->getTitleFromDisplayPattern()|default:''}{if !$smarty.foreach.relationLoop.last}, {/if}
            {/foreach}
        {/if}
    "
{/strip}
{/foreach}
