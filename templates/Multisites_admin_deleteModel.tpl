{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='view' size='large' __alt='Delete model'}</div>
    <h2>{gt text='Delete model'}</h2>
    <form id="deleteInstance" class="z-form" action="{modurl modname='Multisites' type='admin' func='deleteModel'}" method="post" >
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="confirmation" value="1" />
        <input type="hidden" name="modelid" value="{$model.modelid}" />
        <div>
            <label for="instancename">{gt text='Confirm model deletion'}</label>
        </div>
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt='Delete' __title='Delete'}
            <a href="{modurl modname='Multisites' type='admin' func='manageModels'}">
                {icon type='_cancel' size='small' __alt='Cancel' __title='Cancel'}
            </a>
        </div>
    </form>
</div>