{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='view' size='large' __alt='List of instances'}</div>
    <h2>{gt text='List of instances'}</h2>
    <div>
        [{pagerabc posvar='letter' forwardvars='module,type,func'}
        | <a href="{modurl modname='Multisites' type='admin' func='main'}">{gt text='All'}</a>]
    </div>
    <form class="z-form" id="sites_view" action="{modurl modname='Multisites' type='admin' func='handleselectedsites'}" method="post">
        <div>
            <input type="hidden" name="authid" value="{insert name='generateauthkey' module='Multisites'}" />
    <table class="z-datatable">
        <colgroup>
            <col id="cselect" />
            <col id="cinstancename" />
            <col id="cdescription" />
            <col id="csitename" />
            <col id="csitedns" />
            <col id="cadmindetails" />
            <col id="cfeatures" />
            <col id="coptions" />
        </colgroup>
        <thead>
            <tr>
                <th id="hselect" scope="col" align="center" valign="middle"><input type="checkbox" id="toggle_sites" /></th>
                <th id="hinstancename" scope="col">{gt text='Instance name'}</th>
                <th id="hdescription" scope="col">{gt text='Description'}</th>
                <th id="hsitename" scope="col">{gt text='Site name'}</th>
                <th id="hsitedns" scope="col">{gt text='Site DNS'}</th>
                <th id="hadmindetails" scope="col">{gt text='Admin details'}</th>
                <th id="hfeatures" scope="col">{gt text='Features'}</th>
                <th id="hoptions" scope="col">{gt text='Options'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach item='site' from=$sites}
                <tr class="{cycle values='z-odd,z-even'}">
                    <td headers="hselect" align="center" valign="top">
                        <input type="checkbox" name="sites[]" value="{$site.instanceid}" class="sites_checkbox" />
                    </td>
                    <td headers="hinstancename" align="left" valign="top">
                    {if $based_on_domains eq 1}
                        <a href="http://{$site.sitedns}/" target="_blank">
                    {else}
                        <a href="{$wwwroot}/{$site.sitedns}/{$sitednsEndText}" target="_blank">
                    {/if}
                            {$site.instancename}
                        </a>
                    </td>
                    <td headers="hdescription" align="left" valign="top">
                        {$site.description}
                    </td>
                    <td headers="hsitename" align="left" valign="top">
                        {$site.sitename}
                    </td>
                    <td headers="hsitedns" align="left" valign="top">
                        {$site.sitedns}
                    </td>
                    <td headers="hadmindetails" align="left" valign="top">
                        <div>{gt text='Real name'}: {$site.siteadminrealname}</div>
                        <div>{gt text='Email'}: {$site.siteadminemail}</div>
                        <div>{gt text='Company'}: {$site.sitecompany}</div>
                    </td>
                    <td headers="hfeatures" align="left" valign="top">
                        <div>{gt text="Based on model"}: {$site.siteinitmodel}</div>
                        <div>{gt text="Site database"}: {$site.sitedbname}</div>
                        <div>{gt text="Creation date"}: {$site.activationdate}</div>
                        <div>{gt text="Active"}: {$site.active}</div>
                    </td>
                    <td headers="hoptions" align="right" valign="top">
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='edit' instanceid=$site.instanceid}">
                                {icon type='edit' size='extrasmall' __alt='Edit' __title='Edit'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='siteElements' instanceid=$site.instanceid}">
                                {img modname='core' src='blockdevice.gif' set='icons/extrasmall' __alt='Site allowed elements' __title='Site allowed elements'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='siteThemes' instanceid=$site.instanceid}">
                                {img modname='core' src='package_graphics.gif' set='icons/extrasmall' __alt='Site themes' __title='Site themes'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='siteTools' instanceid=$site.instanceid}">
                                {img modname='core' src='package_settings.gif' set='icons/extrasmall' __alt='Site tools' __title='Site tools'}
                            </a>
                        </div>
                        <div>
                            <a href="{modurl modname='Multisites' type='admin' func='deleteInstance' instanceid=$site.instanceid}">
                                {icon type='delete' size='extrasmall' __alt='Delete' __title='Delete'}
                            </a>
                        </div>
                    </td>
                </tr>
            {foreachelse}
                <tr class="z-admintableempty">
                    <td colspan="8" align="left">{gt text='No sites defined'}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {pager rowcount=$pager.numitems limit=$pager.itemsperpage posvar=startnum shift=1 img_prev='images/icons/extrasmall/previous.png' img_next='images/icons/extrasmall/next.png'}

            <fieldset>
                <label for="multisites_action">{gt text='With selected sites'}</label>
                <select id="multisites_action" name="action">
                    <option value="">{gt text='Choose action'}</option>
                    <option value="cleartemplates">{gt text='Clear all cache and compile directories' domain='zikula'}</option>
                </select>
                <input type="submit" value="{gt text='Submit'}" />
            </fieldset>
        </div>
    </form>
</div>

<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
        $('toggle_sites').observe('click', function(e) {
            Zikula.toggleInput('sites_view');
            e.stop()
        });
    });
/* ]]> */
</script>
