<span style="padding-right: 10px; margin-right: 10px; border-right: 2px solid #999999">
    <a href="javascript:multisitesAllowModule('{$name}', {$site.id})" title="{gt text='Toggle module state'}">
        {if $available eq true && $siteModules[$name].state ne 6}
            {icon type='ok' size='extrasmall' __alt='Allowed' __title='Allowed'}
        {elseif $available eq true && $siteModules[$name].state eq 6}
            {icon type='cancel' size='extrasmall' __alt='Not allowed' __title='Not allowed'}
        {elseif $available eq false}
            {img modname='core' src='db_add.png' set='icons/extrasmall' __alt='Add module' __title='Add module'}
        {/if}
    </a>
</span>
{if $available eq true}
    {if $siteModules[$name].state eq 3}
        <a href="javascript:multisitesModifyModuleActivation('{$name}', {$site.id}, 2)" title="{gt text='Active, click to deactivate'}">{img modname='core' src='greenled.png' set='icons/extrasmall'}</a>
    {elseif $siteModules[$name].state eq 2}
        <a href="javascript:multisitesModifyModuleActivation('{$name}', {$site.id}, 3)" title="{gt text='Inactive, click to activate'}">{img modname='core' src='yellowled.png' set='icons/extrasmall'}</a>
    {elseif $siteModules[$name].state eq 6}
        <a href="javascript:multisitesModifyModuleActivation('{$name}', {$site.id}, 2)" title="{gt text='Not allowed, click to deactivate'}">{img modname='core' src='yellowled.png' set='icons/extrasmall'}</a>
    {else}
        {img modname='core' src='redled.png' set='icons/extrasmall'}
    {/if}
{else}
    {img src='blank.png'}
{/if}
