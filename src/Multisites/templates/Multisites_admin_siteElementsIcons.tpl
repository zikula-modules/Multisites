<span style="padding-right: 10px; margin-right: 10px; border-right: 2px solid #999999">
    <a href="javascript:allowModule('{$name}', {$instanceId})">
        {if $available eq 1 && $siteModules[$name].state ne 6}
            {icon type='ok' size='extrasmall' __alt='Allowed' __title='Allowed'}
        {elseif $available eq 1 && $siteModules[$name].state eq 6}
            {icon type='cancel' size='extrasmall' __alt='Not allowed' __title='Not allowed'}
        {elseif $available eq 0}
            {img modname='core' src='db_add.png' set='icons/extrasmall' __alt='Add module' __title='Add module'}
        {/if}
    </a>
</span>
{if $available eq 1}
    {if $siteModules[$name].state eq 3}
        <a href="javascript:modifyActivation('{$name}', {$instanceId}, 2)">
            {img modname='core' src='greenled.png' set='icons/extrasmall'}
        </a>
    {elseif $siteModules[$name].state eq 2}
        <a href="javascript:modifyActivation('{$name}', {$instanceId}, 3)">
            {img modname='core' src='yellowled.png' set='icons/extrasmall'}
        </a>
    {elseif $siteModules[$name].state eq 6}
        <a href="javascript:modifyActivation('{$name}', {$instanceId}, 2)">
            {img modname='core' src='yellowled.png' set='icons/extrasmall'}
        </a>
    {else}
        {img modname='core' src='redled.png' set='icons/extrasmall'}
    {/if}
{else}
    {img modname='Multisites' src='blank.gif'}
{/if}