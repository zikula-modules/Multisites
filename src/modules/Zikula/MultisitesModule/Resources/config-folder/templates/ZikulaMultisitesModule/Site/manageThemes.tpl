{# purpose of this template: show output of manage themes action in site area #}
{% extends routeArea == 'admin' ? 'ZikulaMultisitesModule::adminBase.html.twig' : 'ZikulaMultisitesModule::base.html.twig' %}
{{ pageAddAsset('javascript', zasset('@ZikulaMultisitesModule:js/ZikulaMultisitesModule.SiteExtensions.js')) }}
{% block title %}
    {{ __('Allowed layouts') }}
{% endblock %}
{% block adminPageIcon %}paint-brush{% endblock %}
{% block content %}
    <div class="zikulamultisitesmodule-managethemes">
        <p>{{ __('Name') }}: {{ site.name }}<br />
        {{ __('Site name') }}: {{ site.siteName }}<br />
        {{ __('Site dns') }}: {{ site.siteDns }}<br />
        {{ __('Database name') }}: {{ site.databaseName }}</p>

        <table class="table table-striped table-bordered table-hover table-condensed" summary="{{ __('Manage the themes for this site') }}">
            <colgroup>
                <col id="cName" />
                <col id="cVersion" />
                <col id="cDescription" />
                <col id="cActions" />
            </colgroup>
            <thead>
                <tr>
                    <th id="hName" scope="row" class="text-left">{{ __('Name') }}</th>
                    <th id="hVersion" scope="row" class="text-left">{{ __('Version') }}</th>
                    <th id="hDescription" scope="row" class="text-left">{{ __('Description') }}</th>
                    <th id="hActions" scope="row" class="text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                {% for theme in themes %}
                    <tr>
                        <th id="hRow{{ theme.id }}" scope="row">{{ theme.name }}</th>
                        <td headers="hRow{{ theme.id }} hVersion">{{ theme.version }}</td>
                        <td headers="hRow{{ theme.id }} hDescription">{{ theme.description }}</td>
                        <td headers="hRow{{ theme.id }} hActions" class="actions nowrap z-w02">
                            <div id="theme_{{ theme.name }}">
                                {{ theme.icons }}
                            </div>
                        </td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>

        <p><a href="javascript:history.back()" title="{{ __('Back to site list') }}" class="fa fa-arrow-left">{{ __('Back to site list') }}</a></p>
    </div>
{% endblock %}
