{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='config' size='large' __alt='Site allowed themes'}</div>
    <h2>{gt text='Site allowed themes'}</h2>
    <div>{gt text='Site name'}: {$site.siteName}</div>
    <div>{gt text='Site DNS'}: {$site.sitedns}</div>
    <table class="z-admintable" summary="{gt text='Site allowed themes'}">
        <colgroup>
            <col id="cname" />
            <col id="cversion" />
            <col id="cdescription" />
            <col id="cactions" />
        </colgroup>
        <thead>
            <tr>
                <th id="hname" scope="row">{gt text='Name'}</th>
                <th id="hversion" scope="row">{gt text='Version'}</th>
                <th id="hdescription" scope="row">{gt text='Description'}</th>
                <th id="hactions" scope="row">{gt text='Actions'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach item='theme' from=$themes}
                <tr class="{cycle values='z-odd,z-even'}">
                    <th scope="row">{$theme.name}</th>
                    <td headers="hversion">{$theme.version}</td>
                    <td headers="hdescription">{$theme.description}</td>
                    <td headers="hactions" width="80">
                        <div id="theme_{$theme.name}">
                            {$theme.icons}
                        </div>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>