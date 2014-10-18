{* purpose of this template: templates view json view *}
{multisitesTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='templates'}
    {if not $smarty.foreach.templates.first},{/if}
    {$item->toJson()}
{/foreach}
]
