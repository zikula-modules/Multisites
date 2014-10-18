{* purpose of this template: show output of manage updates action in admin area *}
{include file='admin/header.tpl'}
<div class="multisites-manageupdates multisites-manageupdates">
    {gt text='Manage updates' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='utilities' size='small' __alt='Manage updates'}
        <h3>{$templateTitle}</h3>
    </div>

    <p>{gt text='This functionality is not implemented yet.'}</p>
    <p>{gt text='It is going to be added in Multisites version 2.1.0.'}</p>
    <p>{gt text='Please see this issue for more details:'} <a href="https://github.com/zikula-modules/Multisites/issues/15" title="{gt text='View issue at GitHub'}" target="_blank">#15</a></p>
</div>
{include file='admin/footer.tpl'}
