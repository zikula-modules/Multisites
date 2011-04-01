<span style="padding-right: 10px; margin-right: 10px; border-right: 2px solid #999999">
    <a href="javascript:allowTheme('{$name}', {$instanceid})">
        {if $available eq 1 && $siteThemes[$name].state ne 6}
            {icon type='ok' size='extrasmall' __alt='Allowed' __title='Allowed'}
        {elseif $available eq 1 && $siteThemes[$name].state eq 6}
            {icon type='cancel' size='extrasmall' __alt='Not allowed' __title='Not allowed'}
        {elseif $available eq 0}
            {img modname='core' src='db_add.png' set='icons/extrasmall' __alt='Add theme' __title='Add theme'}
        {/if}
    </a>
</span>
{if $isDefaultTheme eq 1 && $available eq 1}
    {img modname='core' src='greenled.png' set='icons/extrasmall'}
{elseif $isDefaultTheme neq 1 && $available eq 1}
    <a href="{modurl modname='Multisites' type='admin' func='setThemeAsDefault' name=$name instanceid=$instanceid}">
        {img modname='core' src='redled.png' set='icons/extrasmall'}
    </a>
{else}
    {img modname='Multisites' src='blank.gif'}
{/if}