{* purpose of this template: templates xml inclusion template *}
<template id="{$item.id}" createdon="{$item.createdDate|dateformat}" updatedon="{$item.updatedDate|dateformat}">
    <id>{$item.id}</id>
    <name><![CDATA[{$item.name}]]></name>
    <description><![CDATA[{$item.description}]]></description>
    <sqlFile{if $item.sqlFile ne ''} extension="{$item.sqlFileMeta.extension}" size="{$item.sqlFileMeta.size}" isImage="{if $item.sqlFileMeta.isImage}true{else}false{/if}"{if $item.sqlFileMeta.isImage} width="{$item.sqlFileMeta.width}" height="{$item.sqlFileMeta.height}" format="{$item.sqlFileMeta.format}"{/if}{/if}>{$item.sqlFile}</sqlFile>
    <parameters>{$item.parameters}</parameters>
    <folders>{$item.folders}</folders>
    <excludedTables>{$item.excludedTables}</excludedTables>
    <workflowState>{$item.workflowState|multisitesObjectState:false|lower}</workflowState>
    <projects>
    {if isset($item.Projects) && $item.Projects ne null}
        {foreach name='relationLoop' item='relatedItem' from=$item.Projects}
        <project>{$relatedItem->getTitleFromDisplayPattern()|default:''}</project>
        {/foreach}
    {/if}
    </projects>
    <sites>
    {if isset($item.Sites) && $item.Sites ne null}
        {foreach name='relationLoop' item='relatedItem' from=$item.Sites}
        <site>{$relatedItem->getTitleFromDisplayPattern()|default:''}</site>
        {/foreach}
    {/if}
    </sites>
</template>
