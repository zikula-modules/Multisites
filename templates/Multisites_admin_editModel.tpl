{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='edit' size='large' __alt='Edit model'}</div>
    <h2>{gt text='Edit model'}</h2>
    <form id="editModel" class="z-form" action="{modurl modname='Multisites' type='admin' func='updateModel'}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="modelid" value="{$model.modelid}" />
        <div class="z-formrow">
            <label for="modelname">{gt text='Model name'} <span class="mandatoryField">*</span></label>
            <input type="text" name="modelname" size="50" maxlength="150" value="{$model.modelname}" />
        </div>
        <div class="z-formrow">
            <label for="modeldbtablesprefix">{gt text='Model database table prefix'} <span class="mandatoryField">*</span></label>
            <input type="text" name="modeldbtablesprefix" size="50" maxlength="5" value="{$model.modeldbtablesprefix}"/>
        </div>
        <div class="z-formrow">
            <label for="description">{gt text='Description'}</label>
            <textarea name="description" cols="70" rows="5">{$model.description}</textarea>
        </div>
        <div class="z-formrow">
            <label for="modelFile">{gt text='Model file'}</label>
            <span>{$model.filename}</span>
        </div>
        <div class="z-formrow">
            <label for="folders">{gt text='Model folders'}</label>
            <input type="text" name="folders" size="50" maxlength="150" value="{$model.folders}" />
        </div>
        <div class="z-informationmsg">
            {gt text='Write the folders that have to be created into the sites files system separated by ",". If you need folder into other folders you can write expressions like "folder/folder".'}
        </div>
         <div class="z-formrow">
            <label for="mandatory"><span class="mandatoryField">*</span> {gt text='Mandatory fields'}</label>
        </div>
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt='Edit' __title='Edit'}
            <a href="{modurl modname='Multisites' type='admin' func='manageModels'}">
                {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
            </a>
        </div>
    </form>
</div>