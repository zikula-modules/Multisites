{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='new' size='large' __alt='Create a new instance'}</div>
    <h2>{gt text='Create a new instance'}</h2>
    <div class="z-errormsg">
        {gt text='Create a new instance is not possible because the file <b>config/multisites_dbconfig.php</b> does not exists or it is not writeable.'}
    </div>
</div>