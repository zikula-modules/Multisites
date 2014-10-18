{* purpose of this template: sites view xml view *}
{multisitesTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<sites>
{foreach item='item' from=$items}
    {include file='site/include.xml.tpl'}
{foreachelse}
    <noSite />
{/foreach}
</sites>
