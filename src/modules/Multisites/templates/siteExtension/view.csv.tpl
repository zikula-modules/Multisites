{* purpose of this template: site extensions view csv view *}
{multisitesTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='SiteExtensions.csv'}
{strip}"{gt text='Name'}";"{gt text='Extension version'}";"{gt text='Extension type'}";"{gt text='Workflow state'}"
;"{gt text='Site'}"
{/strip}
{foreach item='siteExtension' from=$items}
{strip}
    "{$siteExtension.name}";"{$siteExtension.extensionVersion}";"{$siteExtension.extensionType|multisitesGetListEntry:'siteExtension':'extensionType'|safetext}";"{$item.workflowState|multisitesObjectState:false|lower}"
    ;"{if isset($siteExtension.Site) && $siteExtension.Site ne null}{$siteExtension.Site->getTitleFromDisplayPattern()|default:''}{/if}"
{/strip}
{/foreach}
