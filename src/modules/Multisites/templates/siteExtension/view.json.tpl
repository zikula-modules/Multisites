{* purpose of this template: site extensions view json view *}
{multisitesTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='siteExtensions'}
    {if not $smarty.foreach.siteExtensions.first},{/if}
    {$item->toJson()}
{/foreach}
]
