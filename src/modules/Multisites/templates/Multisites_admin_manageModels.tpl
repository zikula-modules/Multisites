{include file='Multisites_admin_menu.tpl'}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='blockdevice.png' set='icons/large' __alt='List of models'}</div>
    <h2>{gt text='List of models'}</h2>
    <table class="z-datatable">
        <colgroup>
            <col id="cname" />
            <col id="cdescription" />
            <col id="cmodelfile" />
            <col id="cmodelfolders" />
            <col id="cdbprefix" />
            <col id="coptions" />
        </colgroup>
        <thead>
            <tr>
                <th scope="col" id="hname">{gt text='Model name'}</th>
                <th scope="col" id="hdescription">{gt text='Description'}</th>
                <th scope="col" id="hmodelfile">{gt text='Model file name'}</th>
                <th scope="col" id="hmodelfolders">{gt text='Model folders'}</th>
                <th scope="col" id="hdbprefix">{gt text='DB tables prefix'}</th>
                <th scope="col" id="hoptions">{gt text='Options'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach item='model' from=$modelsArray}
            <tr class="{cycle values='z-odd,z-even'}">
                <td headers="hname" align="left" valign="top">{$model.modelname}</td>
                <td headers="hdescription" align="left" valign="top">{$model.description}</td>
                <td headers="hmodelfile" align="left" valign="top">{$model.filename}</td>
                <td headers="hmodelfolders" align="left" valign="top">{$model.folders|replace:',':'<br />'}</td>
                <td headers="hdbprefix" align="left" valign="top">{$model.modeldbtablesprefix}</td>
                <td headers="hoptions" align="right" valign="top">
                    <div>
                        <a href="{modurl modname='Multisites' type='admin' func='editModel' modelid=$model.modelid}">
                            {icon type='edit' size='extrasmall' __alt='Edit' __title='Edit'}
                        </a>
                    </div>
                    <div>
                        <a href="{modurl modname='Multisites' type='admin' func='deleteModel' modelid=$model.modelid}">
                            {icon type='delete' size='extrasmall' __alt='Delete' __title='Delete'}
                        </a>
                    </div>
                </td>
            </tr>
        {foreachelse}
            <tr>
                <td colspan="6">{gt text='No models available. You must define models before create instances.'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>