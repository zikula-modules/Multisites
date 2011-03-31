{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='utilities' size='large' __alt='Upgrade extensions'}</div>
    <h2>{gt text='Upgrade extensions'}</h2>
    <div style="height: 15px">&nbsp;</div>
    <div name="content" id="content">
        <form id="actualize" action="{modurl modname='Multisites' type='admin' func='actualizeModules'}" method="post" >
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <h3>{gt text='Modules folder'}</h3>
            <table class="z-admintable">
                <tbody>
                    {if $upgradeNeeded}
                        {foreach item='module' from=$modules}
                            {if $module.numberOfSites gt 0}
                                <tr class="{cycle values='z-odd,z-even'}">
                                    <td>{$module.name}</td>
                                    <td>{$module.description}</td>
                                    <td>{$module.version}</td>
                                    <td>{$module.numberOfSites}</td>
                                    <td>
                                        <a href="{modurl modname='Multisites' type='admin' func='actualizeModule' moduleName=$module.name}">
                                            {icon type='regenerate' size='extrasmall' __alt='Upgrade sites' __title='Upgrade sites'}
                                        </a>
                                    </td>
                                </tr>
                            {/if}
                        {/foreach}
                    {else}
                       <tr>
                              <td>
                                  {gt text='All the sites and modules are upgraded to the current versions'}
                              </td>
                       </tr>
                    {/if}
                </tbody>
            </table>
            <div style="clear: both"></div>
            <div class="z-formbuttons">
                <a href="javascript:actualize()">
                    {icon type='ok' size='small' __alt='Upgrade modules' __title='Upgrade modules'}
                </a>
                <a href="{modurl modname='Multisites' type='admin' func='main'}">
                   {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
                </a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var notModulesSelected = '{{gt text='Not modules selected'}}';
</script>