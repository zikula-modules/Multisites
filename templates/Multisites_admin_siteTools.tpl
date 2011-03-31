{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='package_settings.png' set='icons/large'}</div>
    <h2>{gt text='Site tools'}</h2>
    <p>{gt text='Site name'}: {$site.siteName}</p>
    <br />
    <h3>{gt text='Available tools'}</h3>
    <ul>
        <li>
            <a href="{modurl modname='Multisites' type='admin' func='executeSiteTool' tool='createAdministrator' instanceId=$site.instanceId}">
                {gt text='Create global administrator'}
            </a>
        </li>
        <li>
            <a href="{modurl modname='Multisites' type='admin' func='executeSiteTool' tool='adminSiteControl' instanceId=$site.instanceId}">
                {gt text='Recover administrators site control'}
            </a>
        </li>
    </ul>
</div>