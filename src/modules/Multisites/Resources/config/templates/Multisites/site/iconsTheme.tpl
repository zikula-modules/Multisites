<span style="padding-right: 10px; margin-right: 10px; border-right: 2px solid #999999">
    <a href="javascript:multisitesAllowTheme('{$name}', {$site.id})" title="{gt text='Toggle theme state'}">
        {if $available eq true && $siteThemes[$name].state ne 6}
            {icon type='ok' size='extrasmall' __alt='Allowed' __title='Allowed'}
        {elseif $available eq true && $siteThemes[$name].state eq 6}
            {icon type='cancel' size='extrasmall' __alt='Not allowed' __title='Not allowed'}
        {elseif $available eq false}
            {img modname='core' src='db_add.png' set='icons/extrasmall' __alt='Add theme' __title='Add theme'}
        {/if}
    </a>
</span>
{if $isDefaultTheme eq 1 && $available eq true}
    {img modname='core' src='greenled.png' set='icons/extrasmall' __title='Default theme'}
{elseif $isDefaultTheme ne 1 && $available eq true}
    <a href="{modurl modname='Multisites' type='site' func='setThemeAsDefault' id=$site.id name=$name}" title="{gt text='Set as default theme'}">{img modname='core' src='redled.png' set='icons/extrasmall'}</a>
{else}
    {img src='blank.png'}
{/if}
