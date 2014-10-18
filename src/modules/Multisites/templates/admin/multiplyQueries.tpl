{* purpose of this template: show output of multiply queries action in admin area *}
{include file='admin/header.tpl'}
<div class="multisites-multiplyqueries multisites-multiplyqueries">
    {gt text='Multiply queries' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='options' size='small' __alt='Multiply queries'}
        <h3>{$templateTitle}</h3>
    </div>

    <p>Please override this template by moving it from <em>/modules/Multisites/templates/admin/multiplyQueries.tpl</em> to either your <em>/themes/YourTheme/templates/modules/Multisites/admin/multiplyQueries.tpl</em> or <em>/config/templates/Multisites/admin/multiplyQueries.tpl</em>.</p>
</div>
{include file='admin/footer.tpl'}
