{* purpose of this template: site extensions view xml view *}
{multisitesTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<siteExtensions>
{foreach item='item' from=$items}
    {include file='siteExtension/include.xml.tpl'}
{foreachelse}
    <noSiteExtension />
{/foreach}
</siteExtensions>
