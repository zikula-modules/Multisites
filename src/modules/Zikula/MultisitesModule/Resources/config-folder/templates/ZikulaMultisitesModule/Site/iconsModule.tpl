<span style="padding-right: 10px; margin-right: 10px; border-right: 2px solid #999999">
    <a href="javascript:multisitesAllowModule('{{ name }}', {{ site.id }})" title="{{ __('Toggle module state') }}">
        {% if available == true and siteModules[name].state != 6 %}
            {icon type='ok' size='extrasmall' __alt='Allowed' __title='Allowed'}
        {% elseif available == true and siteModules[name].state == 6 %}
            {icon type='cancel' size='extrasmall' __alt='Not allowed' __title='Not allowed'}
        {% elseif available != true %}
            <i class="fa fa-database" title="{{ __('Add module') }}"></i>
        {% endif %}
    </a>
</span>
{% if available == true %}
    {% if siteModules[name].state == 3 %}
        <a href="javascript:multisitesModifyModuleActivation('{{ name }}', {{ site.id }}, 2)" title="{{ __('Active, click to deactivate') }}"><i class="fa fa-circle" style="color: green"></i></a>
    {% elseif siteModules[name].state == 2 %}
        <a href="javascript:multisitesModifyModuleActivation('{{ name }}', {{ site.id }}, 3)" title="{{ __('Inactive, click to activate') }}"><i class="fa fa-circle" style="color: yellow"></i></a>
    {% elseif siteModules[name].state == 6 %}
        <a href="javascript:multisitesModifyModuleActivation('{{ name }}', {{ site.id }}, 2)" title="{{ __('Not allowed, click to deactivate') }}"><i class="fa fa-circle" style="color: yellow"></i></a>
    {% else %}
        <i class="fa fa-circle" style="color: red"></i>
    {% endif %}
{% else %}
    <img src="{{ zasset('@ZikulaMultisitesModule:images/blank.png') }}" width="16" height="16" alt="blank" title="{{ __('Not available') }}" />
{% endif %}
