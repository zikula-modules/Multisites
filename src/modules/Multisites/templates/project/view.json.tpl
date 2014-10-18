{* purpose of this template: projects view json view *}
{multisitesTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='projects'}
    {if not $smarty.foreach.projects.first},{/if}
    {$item->toJson()}
{/foreach}
]
