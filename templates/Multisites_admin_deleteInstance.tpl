{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='view' size='large' __alt='Delete instance'}</div>
    <h2>{gt text='Delete instance'}</h2>
    <form id="deleteInstance" class="z-form" action="{modurl modname='Multisites' type='admin' func='deleteInstance'}" method="post" >
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="confirmation" value="1" />
        <input type="hidden" name="instanceid" value="{$instance.instanceid}" />
        <div>{gt text='Instance name'}: <strong>{$instance.instancename}</strong></div>
        <div>{gt text='Site name'}: <strong>{$instance.sitename}</strong></div>
        <div>{gt text='Site DNS'}: <strong>{$instance.sitedns}</strong></div>
        <div>&nbsp;</div>
        <div>
            <label for="instancename">{gt text='Confirm the instance deletion. It means delete its database and files.'}</label>
        </div>
        <div class="z-formrow">
            <label for="deleteFiles">{gt text='Delete site files'}</label>
            <input type="checkbox" name="deleteFiles" value="1" />
        </div>
        <div class="z-formrow">
            <label for="deleteDB">{gt text='Delete database (only possible if the user of the database has enough permissions)'}</label>
            <input type="checkbox" name="deleteDB" value="1" />
        </div> 
        <div class="z-formbuttons">
            <a href="javascript:document.forms['deleteInstance'].submit();">
                {icon type='ok' size='small' __alt='Delete' __title='Delete'}
            </a>
            <a href="{modurl modname='Multisites' type='admin' func='main'}">
                {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
            </a>
        </div>
    </form>
</div>