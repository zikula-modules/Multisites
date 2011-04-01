{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{icon type='new' size='large' __alt='Create a new model'}</div>
    <h2>{gt text='Create a new model'}</h2>
    <form id="newModel" class="z-form" action="{modurl modname='Multisites' type='admin' func='createModel'}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <div class="z-formrow">
            <label for="modelname">{gt text='Model name'} <span class="mandatoryField">*</span></label>
            <input type="text" name="modelname" size="50" maxlength="150" value="{$modelname}"/>
        </div>
        <div class="z-formrow">
            <label for="modeldbtablesprefix">{gt text='Model database tables prefix'} <span class="mandatoryField">*</span></label>
            <input type="text" name="modeldbtablesprefix" size="50" maxlength="5" value="{if $modeldbtablesprefix eq ''}z{else}{$modeldbtablesprefix}{/if}"/>
        </div>
        <div class="z-formrow">
            <label for="description">{gt text='Description'}</label>
            <input type="text" name="description" size="50" maxlength="250" value="{$description}" />
        </div>
        <div class="z-formrow">
            <label for="modelFile">{gt text='Upload model file'} <span class="mandatoryField">*</span></label>
            <input type="file" name="modelFile" size="50" />
            <div class="z-informationmsg z-formnote">
                {gt text='The possible extensions are sql and txt.'}
            </div>
            <span class="z-formnote">{gt text='or select a file'}
                <select name="modelFileSelected">
                    <option value="0">{gt text='Select a file...'}</option>
                    {foreach item='modelFile' from=$modelsFiles}
                        <option value="{$modelFile}">{$modelFile}</option>
                    {/foreach}
                </select>
            </span>
        </div>
        <div class="z-formrow">
            <label for="folders">{gt text='Model folders'}</label>
            <input type="text" name="folders" size="50" maxlength="150" value="{$folders}"/>
            <div class="z-informationmsg z-formnote">
                {gt text='Write the folders that have to be created into the sites files system separated by commas. If you need a folder into another folder you can write expressions like "folder/folder".'}
            </div>
        </div>

         <div class="z-formrow">
            <label for="mandatory"><span class="mandatoryField">*</span> {gt text='Mandatory fields'}</label>
        </div>
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt='Add' __title='Add'}
            <a href="{modurl modname='Multisites' type='admin' func='main'}">
                {icon type='cancel' size='small' __alt='Cancel' __title='Cancel'}
            </a>
        </div>
    </form>
</div>