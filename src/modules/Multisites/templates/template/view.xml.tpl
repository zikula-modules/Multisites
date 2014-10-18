{* purpose of this template: templates view xml view *}
{multisitesTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<templates>
{foreach item='item' from=$items}
    {include file='template/include.xml.tpl'}
{foreachelse}
    <noTemplate />
{/foreach}
</templates>
