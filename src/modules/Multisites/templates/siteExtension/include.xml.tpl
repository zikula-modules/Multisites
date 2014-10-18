{* purpose of this template: site extensions xml inclusion template *}
<siteextension id="{$item.id}" createdon="{$item.createdDate|dateformat}" updatedon="{$item.updatedDate|dateformat}">
    <id>{$item.id}</id>
    <name><![CDATA[{$item.name}]]></name>
    <extensionVersion><![CDATA[{$item.extensionVersion}]]></extensionVersion>
    <extensionType>{$item.extensionType|multisitesGetListEntry:'siteExtension':'extensionType'|safetext}</extensionType>
    <workflowState>{$item.workflowState|multisitesObjectState:false|lower}</workflowState>
    <site>{if isset($item.Site) && $item.Site ne null}{$item.Site->getTitleFromDisplayPattern()|default:''}{/if}</site>
</siteextension>
