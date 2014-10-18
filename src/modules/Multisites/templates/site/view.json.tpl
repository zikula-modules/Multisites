{* purpose of this template: sites view json view *}
{multisitesTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='sites'}
    {if not $smarty.foreach.sites.first},{/if}
    {$item->toJson()}
{/foreach}
]
