{* purpose of this template: show output of multiply queries action in admin area *}
{include file='admin/header.tpl'}
<div class="multisites-multiplyqueries multisites-multiplyqueries">
    {gt text='Multiply queries' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='options' size='small' __alt='Multiply queries'}
        <h3>{$templateTitle}</h3>
    </div>

    <p class="z-warningmsg">{gt text='This is an expert function! Use it only if you know about the effects.'}</p>

    <form action="{modurl modname='Multisites' type='admin' func='multiplyQueries'}" method="post" enctype="multipart/form-data" role="form" class="z-form">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <fieldset>
                <legend>{gt text='Input data'}</legend>
                <p class="z-informationmsg">{gt text='Use <strong>`###DBNAME###`</strong> as a placeholder for the database name.'}</p>

                <div class="z-formrow">
                    <label for="inputquery">{gt text='Sql query'}</label>
                    <textarea id="inputquery" name="inputquery" class="form-control" rows="7">{$sqlInput}</textarea>
                </div>
                <div class="z-formrow">
                    <label for="queryfile">{gt text='or sql file'}</label>
                    <input type="file" id="queryfile" name="queryfile">
                </div>
            </fieldset>

            <fieldset>
                <legend>{gt text='Options'}</legend>

                <div class="z-formrow{*if count($databaseHosts) lt 2} z-hide{/if*}">
                    <label for="dbHosts">{gt text='Database hosts'}</label>
                    <select id="dbHosts" name="dbhosts[]" class="form-control" multiple="multiple">
                        {foreach item='dbHost' from=$databaseHosts}
                            <option value="{$dbHost}"{foreach item='selectedHost' from=$databaseHostsSelected}{if $dbHost eq $selectedHost} selected="selected"{/if}{/foreach}>{$dbHost}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="z-formrow{*if count($databaseTypes) lt 2} z-hide{/if*}">
                    <label for="dbTypes">{gt text='Database types'}</label>
                    <select id="dbTypes" name="dbtypes[]" class="form-control" multiple="multiple">
                        {foreach item='dbType' from=$databaseTypes}
                            <option value="{$dbType}"{foreach item='selectedType' from=$databaseTypesSelected}{if $dbType eq $selectedType} selected="selected"{/if}{/foreach}>{$dbType}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="z-formrow">
                    <label for="opMode">{gt text='Operation mode'}</label>
                    <select id="opMode" name="opmode" class="form-control">
                        <option value="show" selected="selected">{gt text='Show output sql'}</option>
                        <option value="execute">{gt text='Execute sql directly'}</option>
                    </select>
                </div>
            </fieldset>

            {if $sqlOutput ne ''}
                <fieldset>
                    <legend>{gt text='Output data'}</legend>

                    <div class="z-formrow">
                        <label for="output">{gt text='Output'}</label>
                        <textarea id="output" name="output" class="form-control" readonly="readonly" rows="7">{$sqlOutput}</textarea>
                    </div>
                </fieldset>
            {/if}

            <div class="z-buttons z-formbuttons">
                {gt text='Start' assign='startTitle'}
                {button src='button_ok.png' set='icons/small' text=$startTitle title=$startTitle class='z-btgreen'}
                <a href="{modurl modname='Multisites' type='admin' func='view' ot='site'}">{icon type='cancel' size='small' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
            </div>
        </div>
    </form>
</div>
{include file='admin/footer.tpl'}
