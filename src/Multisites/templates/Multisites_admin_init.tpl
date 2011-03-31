{insert name='getstatusmsg'}
<h1>{gt text='Multiple Zikula installations'}</h1>
<div class="z-admincontainer" style="border: none">
    <div class="z-adminpageicon">{img modname='core' src='package_settings.png' set='icons/large'}</div>
    <h2>{gt text='Multisites system installation. Version RC1'}</h2>
    {if $step eq 0}
        <div>
            <h3>{gt text='The installation of Multisites requires 5 steps'}:</h3>
        </div>
        <div>
            <ol>
                <li>{gt text='Copy the files <strong>multisites_config.php</strong> and <strong>multisites_dbconfig.php</strong> in the folder config and set them to writeable (chmod770 or chmod660).'}</li>
                <li>{gt text='Create a writeable directory in your server and note down its physical path.'}</li>
{*                <li>{gt text='Write the configurable values in the files <strong>multisites_config.php</strong>.'}</li>
                <li>{gt text='Set the file <strong>multisites_config.php</strong> as read only (chmod 644 or 444).'}</li>
                <li>{gt text='Proceed with the module installation.'}</li>*}
            </ol>
        </div>
        <div class="z-warningmsg">
            {gt text='<strong>ATTENTION</strong>: If you have any problem you can recover the system to the previous state
            deleting the files <strong>config/multisites_config.php</strong> and <strong>multisites_dbconfig.php</strong> copied during the installation process.'}
        </div>
        <div class="z-informationmsg">
            {gt text='This module makes possible to run several instances of Zikula sharing the core files.
            <br />
            To run the module two special files are required: <strong>config/multisites_config.php</strong> and <strong>multisites_dbconfig.php</strong>.'}
        </div>
        <div style="margin: 10px 0 0 20px">
            <a href="{modurl modname='Multisites' type='interactiveinstaller' func='step1'}">{gt text='Go on with the installation'}</a>
        </div>
    {elseif $step eq 1}
        <div>
            <h3>{gt text='Step 1 - Checking the correct location of the files multisites_config.php and multisites_dbconfig.php'}</h3>
        </div>
        {if !$file1 or !$file2 or !$fileWriteable1 or !$fileWriteable2}
            <div class="z-informationmsg">
                {gt text='You have to locate the files <strong>config/multisites_config.php</strong> and <strong>multisites_dbconfig.php</strong> into the directory <strong>config</strong> of Zikula and give them writeable permissions.'}
            </div>
        {/if}
        <div style="margin: 20px">
            {if !$file1}
                <div>
                    {icon type='error' size='extrasmall'}
                    {gt text='The file <strong>config/multisites_config.php</strong> has not been found in the directory <strong>config</strong>.'}
                </div>
            {else}
                <div>
                    {icon type='ok' size='extrasmall'}
                    {gt text='The file <strong>config/multisites_config.php</strong> is located in the correct place.'}
                </div>
                {if !$fileWriteable1}
                    <div>
                        {icon type='error' size='extrasmall'}
                        {gt text='The file <strong>config/multisites_config.php</strong> is not writeable, but it should be.'}
                    </div>
                {else}
                    <div>
                        {icon type='ok' size='extrasmall'}
                        {gt text='The file <strong>config/multisites_config.php</strong> is writeable.'}
                    </div>
                {/if}
            {/if}
            {if !$file2}
                <div>
                    {icon type='error' size='extrasmall'}
                    {gt text='The file <strong>config/multisites_dbconfig.php</strong> has not been found in the directory <strong>config</strong>.'}
                </div>
            {else}
                <div>
                    {icon type='ok' size='extrasmall'}
                    {gt text='The file <strong>config/multisites_dbconfig.php</strong> is located in the correct place.'}
                </div>
                {if !$fileWriteable2}
                    <div>
                        {icon type='error' size='extrasmall'}
                        {gt text='The file <strong>config/multisites_dbconfig.php</strong> is not writeable, but it should be.'}
                    </div>
                {else}
                    <div>
                        {icon type='ok' size='extrasmall'}
                        {gt text="The file <strong>config/multisites_dbconfig.php</strong> is writeable."}
                    </div>
                {/if}
            {/if}
        </div>
        <div class="z-center">
            {if !$file1 || !$file2 || !$fileWriteable1 || !$fileWriteable2}
                <a href="{modurl modname='Multisites' type='interactiveinstaller' func='step1'}">
                    {gt text='Check again'}
                </a>
            {else}
                <a href="{modurl modname='Multisites' type='interactiveinstaller' func='step2'}">
                    {gt text='Go on with the step 2'}
                </a>
            {/if}
        </div>
    {elseif $step eq 2}
        <h3>{gt text='Step 2 - Checking the sites files directory existence'}</h3>
        <div class="z-informationmsg">
            {gt text='This directory will be used to save the temporal files for the different sites. If you install
            the module <em>Files</em> this directory will be used to save the particular files of the different sites.
            <br />
            We recommend to locate this directory out of the public HTTP.'}
        </div>
        <div style="margin: 20px">
            <form method="post" action="{modurl modname='Multisites' type='interactiveinstaller' func='step21'}">
                <div class="z-formrow">
                    <label for="files_real_path">{gt text='Script Real Path'}:</label>
                    <span>{$scriptRealPath}</span>
                </div>
                <div class="z-formrow">
                    <label for="files_real_path">{gt text='Write the physical location of the directory in the server'}:</label>
                    <input type="text" name="files_real_path" size="30" maxlength="50" value="{$files_real_path}" />
                </div>
                <div class="z-formbuttons">
                    <input type="submit" value="{gt text='Accept'}" />
                </div>
            </form>
        </div>
    {elseif $step eq 3}
        <h3>{gt text='Step 3 - Writing the correct parameters in the file <strong>multisites_config.php</strong>'}</h3>
        <div class="z-warningmsg">
            {gt text='If you have had any problem you can delete the
                files <strong>config/multisites_config.php</strong> and <strong>config/multisites_dbconfig.php</strong> copied during the installation process to recover the system to the previous state.'}
        </div>
        <form class="z-form" method="post" action="{modurl modname='Multisites' type='interactiveinstaller' func='step31'}">
            <div class="z-formrow">
                <label for="mainsiteurl">{gt text='Main domain'}:</label>
                <input type="text" name="mainsiteurl" size="50" maxlength="50" value="{$mainHost}"/>
            </div>
            <div class="z-formrow">
                <label for="site_temp_files_folder">{gt text='Sites temporal directory'}:</label>
                <input type="text" name="site_temp_files_folder" size="50" maxlength="50" value="{$site_temp_files_folder}"/>
            </div>
            <div class="z-formrow">
                <label for="site_files_folder">{gt text='Sites data directory'}:</label>
                <input type="text" name="site_files_folder" size="50" maxlength="50" value="Data"/>
            </div>
            <div class="z-formbuttons">
                <input type="submit" value="{gt text='Accept'}" />
            </div>
        </form>
    {elseif $step eq 4}
        <h3>{gt text='Step 4 - Check if the file multisites_config.php is not writeable'}</h3>
        <div style="margin: 20px">
            {if isset($fileWriteable) && $fileWriteable}
                <div>
                    {img modname='core' src='error.gif' set='icons/extrasmall'}
                    {gt text='The file <strong>config/multisites_config.php</strong> is writeable, but it should not be. Please set this file as not writeable.'}
                </div>
            {else}
                <div>
                    {icon type='ok' size='extrasmall'}
                    {gt text='The file <strong>config/multisites_config.php</strong> is not writeable.'}
                </div>
            {/if}
            <div style="text-align: center">
                {if !isset($fileWriteable) || !$fileWriteable}
                    <form class="z-form" action="{modurl modname='Extensions' type='admin' func='initialise'}" method="post" enctype="application/x-www-form-urlencoded">
                        <div>
                            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
                            <input type="hidden" name="activate" value="1" />
                            <input type="hidden" name="files_real_path" value="{$files_real_path}" />
                            <input type="hidden" name="usersFolder" value="{$usersFolder}" />
                            <br />
                            <div class="z-formbuttons">
                                <input name="submit" type="submit" value="{gt text='Proceed with the module installation'}" />
                            </div>
                        </div>
                    </form>
                {else}
                    <a href="{modurl modname='Multisites' type='interactiveinstaller' func='step4'}">
                        {gt text='Check again'}
                    </a>
                {/if}
            </div>
        </div>
    {/if}
</div>