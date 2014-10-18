{* purpose of this template: projects view xml view *}
{multisitesTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<projects>
{foreach item='item' from=$items}
    {include file='project/include.xml.tpl'}
{foreachelse}
    <noProject />
{/foreach}
</projects>
