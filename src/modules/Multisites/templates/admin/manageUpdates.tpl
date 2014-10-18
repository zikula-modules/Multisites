{* purpose of this template: show output of manage updates action in admin area *}
{include file='admin/header.tpl'}
<div class="multisites-manageupdates multisites-manageupdates">
    {gt text='Manage updates' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='options' size='small' __alt='Manage updates'}
        <h3>{$templateTitle}</h3>
    </div>

    <p>Please override this template by moving it from <em>/modules/Multisites/templates/admin/manageUpdates.tpl</em> to either your <em>/themes/YourTheme/templates/modules/Multisites/admin/manageUpdates.tpl</em> or <em>/config/templates/Multisites/admin/manageUpdates.tpl</em>.</p>
</div>
{include file='admin/footer.tpl'}
