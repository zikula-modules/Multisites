{* purpose of this template: projects xml inclusion template *}
<project id="{$item.id}" createdon="{$item.createdDate|dateformat}" updatedon="{$item.updatedDate|dateformat}">
    <id>{$item.id}</id>
    <name><![CDATA[{$item.name}]]></name>
    <workflowState>{$item.workflowState|multisitesObjectState:false|lower}</workflowState>
    <sites>
    {if isset($item.Sites) && $item.Sites ne null}
        {foreach name='relationLoop' item='relatedItem' from=$item.Sites}
        <site>{$relatedItem->getTitleFromDisplayPattern()|default:''}</site>
        {/foreach}
    {/if}
    </sites>
    <templates>
    {if isset($item.Templates) && $item.Templates ne null}
        {foreach name='relationLoop' item='relatedItem' from=$item.Templates}
        <template>{$relatedItem->getTitleFromDisplayPattern()|default:''}</template>
        {/foreach}
    {/if}
    </templates>
</project>
