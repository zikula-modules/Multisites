{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='view' size='large' __alt='List of instances'}</div>
    <h2>{gt text='List of instances'}</h2>
    <div>
        [{pagerabc posvar='letter' forwardvars='module,type,func'}
        | <a href="{modurl modname='Multisites' type='admin' func='main'}">{gt text='All'}</a>]
    </div>
    <table class="z-admintable">
        <thead>
            <tr>
                <th>{gt text='Instance name'}</th>
                <th>{gt text='Description'}</th>
                <th>{gt text='Site name'}</th>
                <th>{gt text='Site DNS'}</th>
                <th>{gt text='Admin details'}</th>
                <th>{gt text='Features'}</th>
                <th>{gt text='Options'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach item='site' from=$sites}
                <tr class="{cycle values='z-odd,z-even'}">
                    <td align="left" valign="top">
                    {if $based_on_domains eq 1}
                        <a href="http://{$site.sitedns}/" target="_blank">
                    {else}
                        <a href="{$wwwroot}/{$site.sitedns}/{$sitednsEndText}" target="_blank">
                    {/if}
                            {$site.instanceName}
                        </a>
                    </td>
                    <td align="left" valign="top">
                        {$site.description}
                    </td>
                    <td align="left" valign="top">
                        {$site.siteName}
                    </td>
                    <td align="left" valign="top">
                        {$site.sitedns}
                    </td>
                    <td align="left" valign="top">
                        <div>{gt text='Real name'}: {$site.siteAdminRealName}</div>
                        <div>{gt text='Email'}: {$site.siteAdminEmail}</div>
                        <div>{gt text='Company'}: {$site.siteCompany}</div>
                    </td>
                    <td align="left" valign="top">
                        <div>{gt text="Based on model"}: {$site.siteInitModel}</div>
                        <div>{gt text="Site database"}: {$site.siteDBName}</div>
                        <div>{gt text="Creation date"}: {$site.activationDate}</div>
                        <div>{gt text="Active"}: {$site.active}</div>
                    </td>
                    <td align="right" valign="top">
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='edit' instanceId=$site.instanceId}">
                                {icon type='edit' size='extrasmall' __alt='Edit' __title='Edit'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='siteElements' instanceId=$site.instanceId}">
                                {img modname='core' src='blockdevice.gif' set='icons/extrasmall' __alt='Site allowed elements' __title='Site allowed elements'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='siteThemes' instanceId=$site.instanceId}">
                                {img modname='core' src='package_graphics.gif' set='icons/extrasmall' __alt='Site themes' __title='Site themes'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='siteTools' instanceId=$site.instanceId}">
                                {img modname='core' src='package_settings.gif' set='icons/extrasmall' __alt='Site tools' __title='Site tools'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='deleteInstance' instanceId=$site.instanceId}">
                                {icon type='delete' size='extrasmall' __alt='Delete' __title='Delete'}
                            </a>
                        </div>
                    </td>
                </tr>
            {foreachelse}
                <tr>
                    <td colspan="7" align="left">{gt text='No sites defined'}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {pager rowcount=$pager.numitems limit=$pager.itemsperpage posvar=startnum shift=1 img_prev='images/icons/extrasmall/previous.png' img_next='images/icons/extrasmall/next.png'}
</div>