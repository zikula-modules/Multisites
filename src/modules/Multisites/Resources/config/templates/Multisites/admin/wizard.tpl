{* purpose of this template: module configuration *}
{include file='admin/header.tpl'}
<div class="multisites-config wizard">
    {gt text='Configuration wizard' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='config' size='small' __alt='Settings'}
        <h3>{$templateTitle}</h3>
    </div>

    {if $step eq 1}
        <h3>{gt text='Step 1 - Checking the correct location of the configuration files'}</h3>
        <div class="z-informationmsg">
            {gt text='Please copy the files <strong>%1$s</strong> and <strong>%2$s</strong> into the Zikula <strong>config</strong> directory and give them writeable permissions.' tag1=$configTemplateFile tag2=$dbConfigTemplateFile}
        </div>
        <div class="z-warningmsg">
            {gt text='If you have any problem you can recover the system to the previous state at any time by deleting both files again.'}
        </div>
        <div class="z-form">
            <fieldset>
                {if !$configFileExists}
                    <div>
                        {icon type='error' size='extrasmall'}
                        {gt text='The file <strong>%s</strong> has not been found and could not be copied automatically.' tag1=$configFile}
                    </div>
                {else}
                    <div>
                        {icon type='ok' size='extrasmall'}
                        {gt text='The file <strong>%s</strong> is located in the correct place.' tag1=$configFile}
                    </div>
                    <div>
                        {if !$configFileWriteable}
                            {icon type='error' size='extrasmall'}
                            {gt text='The file <strong>%s</strong> is not writeable, but it should be.' tag1=$configFile}
                        {else}
                            {icon type='ok' size='extrasmall'}
                            {gt text='The file <strong>%s</strong> is writeable.' tag1=$configFile}
                        {/if}
                    </div>
                {/if}
                {if !$dbConfigFileExists}
                    <div>
                        {icon type='error' size='extrasmall'}
                        {gt text='The file <strong>%s</strong> has not been found and could not be copied automatically.' tag1=$dbConfigFile}
                    </div>
                {else}
                    <div>
                        {icon type='ok' size='extrasmall'}
                        {gt text='The file <strong>%s</strong> is located in the correct place.' tag1=$dbConfigFile}
                    </div>
                    <div>
                        {if !$dbConfigFileWriteable}
                            {icon type='error' size='extrasmall'}
                            {gt text='The file <strong>%s</strong> is not writeable, but it should be.' tag1=$dbConfigFile}
                        {else}
                            {icon type='ok' size='extrasmall'}
                            {gt text="The file <strong>%s</strong> is writeable." tag1=$dbConfigFile}
                        {/if}
                    </div>
                {/if}
                <br />
                <div class="z-buttons z-center">
                    <p><a href="{modurl modname='Multisites' type='admin' func='config'}" title="{gt text='Check again'}" class='z-bt-ok'>{gt text='Check again'}</a></p>
                </div>
            </fieldset>
        </div>
    {elseif $step eq 2}
        <h3>{gt text='Step 2 - Checking whether the sites files directory exists'}</h3>
        <form method="post" action="{modurl modname='Multisites' type='admin' func='config'}" class="z-form">
            <fieldset>
                <div class="z-formrow">
                    <label for="filesRealPath">{gt text='Script Real Path'}:</label>
                    <span>{$scriptRealPath}</span>
                </div>
                <div class="z-formrow">
                    <label for="filesRealPath">{gt text='Physical location of the directory in the server'}:</label>
                    <input type="text" id="filesRealPath" name="files_real_path" size="30" maxlength="80" value="{$files_real_path}" />
                    <span class="z-formnote">{gt text='This directory will be used to save the temporary and userdata files for the different sites.'}</span>
                </div>
                <div class="z-buttons z-formbuttons">
                    <input type="submit" value="{gt text='Save'}" class="z-bt-ok" />
                </div>
            </fieldset>
        </form>
    {elseif $step eq 3}
        <h3>{gt text='Step 3 - Writing correct parameters into the <strong>%s.php</strong> file' tag1=$configFile}</h3>
        <div class="z-warningmsg">
            {gt text='If you experience any problem you can delete the <strong>%1$s</strong> and <strong>%2$s</strong> files to recover the system to the previous state.' tag1=$configFile tag2=$dbConfigFile}
        </div>
        <form method="post" action="{modurl modname='Multisites' type='admin' func='config'}" class="z-form">
            <fieldset>
                <div class="z-formrow">
                    <label for="mainSiteUrl">{gt text='Main domain'}:</label>
                    <input type="text" id="mainSiteUrl" name="mainsiteurl" size="50" maxlength="50" value="{$mainSiteUrl}" />
                </div>
                <div class="z-formrow">
                    <label for="siteTempFilesFolder">{gt text='Sites temporal directory'}:</label>
                    <input type="text" id="siteTempFilesFolder" name="site_temp_files_folder" size="50" maxlength="50" value="{$siteTempFilesFolder}" />
                </div>
                <div class="z-formrow">
                    <label for="siteFilesFolder">{gt text='Sites data directory'}:</label>
                    <input type="text" id="siteFilesFolder" name="site_files_folder" size="50" maxlength="50" value="{$siteFilesFolder}" />
                </div>
                <div class="z-buttons z-formbuttons">
                    <input type="submit" value="{gt text='Save'}" class="z-bt-ok" />
                </div>
            </fieldset>
        </form>
    {elseif $step eq 4}
        <h3>{gt text='Step 4 - Check if the %s file is not writeable anymore' tag1=$configFile}</h3>
        <div class="z-form">
            <fieldset>
                <div>
                    {img modname='core' src='error.png' set='icons/extrasmall'}
                    {gt text='The <strong>%s</strong> file is writeable, but it should not be. Please set this file as not writeable.' tag1=$configFile}
                </div>
                <br />
                <div class="z-buttons z-center">
                    <p><a href="{modurl modname='Multisites' type='admin' func='config'}" title="{gt text='Check again'}" class='z-bt-ok'>{gt text='Check again'}</a></p>
                </div>
            </fieldset>
        </div>
    {/if}

</div>
{include file='admin/footer.tpl'}
