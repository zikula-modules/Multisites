{# purpose of this template: sites list view #}
{% extends routeArea == 'admin' ? 'ZikulaMultisitesModule::adminBase.html.twig' : 'ZikulaMultisitesModule::base.html.twig' %}
{% block title __('Site list') %}
{% block admin_page_icon 'list-alt' %}
{% block content %}
<div class="zikulamultisitesmodule-site zikulamultisitesmodule-view">

    <p class="alert alert-info">{{ __('Each site is assigned to a project and instance of a certain site template.') }}</p>

    {{ block('page_nav_links') }}

    {# TODO enable again when pagerabc is available for Twig
    <div class="text-center bold">
        [{{ pagerabc(posvar='letter', forwardvars='module,type,func') }} | <a href="{{ path('zikulamultisitesmodule_site_adminview') }}" title="{{ __('All letters') }}">{{ __('All') }}</a>]
    </div>#}

    {{ include('@ZikulaMultisitesModule/Site/viewQuickNav.html.twig', { all: all, own: own, workflowStateFilter: false }) }}{# see template file for available options #}

    {% if routeArea == 'admin' %}
    <form action="{{ path('zikulamultisitesmodule_site_' ~ routeArea ~ 'handleselectedentries') }}" method="post" id="sitesViewForm" class="form-horizontal" role="form">
        <div>
    {/if}
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover table-condensed">
            <colgroup>
                {% if routeArea == 'admin' %}
                    <col id="cSelect" />
                {% endif %}
                <col id="cLogo" />
                <col id="cSiteAlias" />
                <col id="cName" />
                <col id="cSiteName" />
{#                <col id="cSiteDns" />#}
                <col id="cSiteAdminName" />
                <col id="cFeatures" />
{#                <col id="cAllowedLocales" />#}
                <col id="cActive" />
                <col id="cItemActions" />
            </colgroup>
            <thead>
            <tr>
                {% if routeArea == 'admin' %}
                    <th id="hSelect" scope="col" align="center" valign="middle">
                        <input type="checkbox" id="toggleSites" />
                    </th>
                {% endif %}
                <th id="hLogo" scope="col" class="text-left">
                    <a href="{{ sort.logo.url }}" title="{{ __f('Sort by %s', 'logo') }}" class="{{ sort.logo.class }}">{{ __('Logo') }}</a>
                </th>
                <th id="hSiteAlias" scope="col" class="text-left">
                    <a href="{{ sort.siteAlias.url }}" title="{{ __f('Sort by %s', 'site alias') }}" class="{{ sort.siteAlias.class }}">{{ __('Site alias') }}</a>
                </th>
                <th id="hName" scope="col" class="text-left">
                    <a href="{{ sort.name.url }}" title="{{ __f('Sort by %s', 'name') }}" class="{{ sort.name.class }}">{{ __('Name') }}</a>
                </th>
                <th id="hSiteName" scope="col" class="text-left">
                    <a href="{{ sort.siteName.url }}" title="{{ __f('Sort by %s', 'site name') }}" class="{{ sort.siteName.class }}">{{ __('Site name') }}</a>
                </th>
{#                <th id="hSiteDns" scope="col" class="text-left">
                    <a href="{{ sort.siteDns.url }}" title="{{ __f('Sort by %s', 'site dns') }}" class="{{ sort.siteDns.class }}">{{ __('Site dns') }}</a>
                </th>#}
                <th id="hSiteAdminName" scope="col" class="text-left">
                    {{ __('Admin details') }}
                </th>
                <th id="hFeatures" scope="col" class="text-left">
                    {{ __('Features') }}
                </th>
{#                <th id="hAllowedLocales" scope="col" class="text-left">
                    {{ __('Allowed locales') }}
                </th>#}
                <th id="hActive" scope="col" class="text-center">
                    <a href="{{ sort.active.url }}" title="{{ __f('Sort by %s', 'active') }}" class="{{ sort.active.class }}">{{ __('Active') }}</a>
                </th>
                <th id="hItemActions" scope="col" class="z-order-unsorted">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
        
        {% for site in items %}
            <tr>
                {% if routeArea == 'admin' %}
                    <td headers="hselect" align="center" valign="top">
                        <input type="checkbox" name="items[]" value="{{ site.id }}" class="sites-checkbox" />
                    </td>
                {% endif %}
                <td headers="hLogo" class="text-left">
                    {% if site.logo is not empty %}
                    <a href="{{ site.logoFullPathURL }}" title="{{ site.getTitleFromDisplayPattern()|e('html_attr') }}"{% if site.logoMeta.isImage %} class="lightbox"{% endif %}>
                    {% if site.logoMeta.isImage %}
                        {{ zikulamultisitesmodule_thumb({ image: site.logoFullPath, objectid: 'site-' ~ site.id ~ '', preset: siteThumbPresetLogo, tag: true, img_alt: site.getTitleFromDisplayPattern(), img_class: 'img-thumbnail' }) }}
                    {% else %}
                        {{ __('Download') }} ({{ site.logoMeta.size|zikulamultisitesmodule_fileSize(site.logoFullPath, false, false) }})
                    {% endif %}
                    </a>
                    {% else %}&nbsp;{% endif %}
                </td>
                <td headers="hSiteAlias" class="text-left">
                    {{ site.siteAlias }}
                </td>
                <td headers="hName" class="text-left">
                    {% if basedOnDomains == 1 %}
                        <a href="http://{{ site.siteDns }}/" title="{{ __('Visit this site') }}" class="sitelink">{{ site.name|notifyFilters(zikulamultisitesmodule.filterhook.sites') }}</a>
                    {% else %}
                        <a href="{{ wwwroot }}/{{ site.siteDns }}/" title="{{ __('Visit this site') }}" class="sitelink">{{ site.namenotifyFilters(zikulamultisitesmodule.filterhook.sites') }}</a>
                    {/if}
                    {% if site.description not empty %}
                        <br /><span class="z-sub">{{ site.description }}</span>
                    {% endif %}
                </td>
                <td headers="hSiteName" class="text-left">
                    {{ site.siteName }}
                    {% if site.siteDescription not empty %}
                        <br /><span class="z-sub">{{ site.siteDescription }}</span>
                    {% endif %}
                </td>
{#                <td headers="hSiteDns" class="text-left">
                    {{ site.siteDns }}
                </td>#}
                <td headers="hSiteAdminName" class="text-left">
                    <ul class="z-sub">
                        <li>{{ __('User') }}: {{ site.siteAdminName }}</li>
                        <li>{{ __('Real name') }}: {{ site.siteAdminRealName }}</li>
                        <li>{{ __('Email') }}: <a href="mailto:{{ site.siteAdminEmail }}" title="{{ __('Send an email') }}" class="fa fa-envelope"></a></li>
                        <li>{{ __('Company') }}: {{ site.siteCompany }}</li>
                    </ul>
                </td>
                <td headers="hFeatures" class="text-left">
                    <ul class="z-sub">
                        <li>{{ __('Project') }}: {% if site.project|default %}{{ site.project.getTitleFromDisplayPattern() }}{% else %}{{ __('Not set.') }}{% endif %}</li>
                        <li>{{ __('Template') }}: {% if site.template|default %}{{ site.template.getTitleFromDisplayPattern() }}{% else %}{{ __('None (decoupled)') }}{% endif %}</li>
                        <li>{{ __('Site database') }}: {{ site.databaseName }} ({{ site.databaseType }})</li>
                        <li>{{ __('Creation date') }}: {{ site.createdDate|localizeddate('medium', 'short') }}</li>
                    </ul>
                </td>
{*                <td headers="hAllowedLocales" class="text-left">
                    {% if site.allowedLocales|length > 0 %}
                        <ul>
                        {% for locale in site.allowedLocales %}
                            <li>{{ locale }}</li>
                        {% endfor %}
                        </ul>
                    {% else %}
                        {{ __('All') }}
                    {% endif %}
                </td>*}
                <td headers="hActive" class="z-center">
                    {% set itemid = site.id %}
                    <a id="toggleActive{{ itemid }}" href="javascript:void(0);" class="hidden">
                    {% if site.active %}
                        <span class="cursor-pointer fa fa-check" id="yesactive_{{ itemid }}" title="{{ __('This setting is enabled. Click here to disable it.') }}"></span>
                        <span class="cursor-pointer fa fa-times hidden" id="noactive_{{ itemid }}" title="{{ __('This setting is disabled. Click here to enable it.') }}"></span>
                    {% else %}
                        <span class="cursor-pointer fa fa-check hidden" id="yesactive_{{ itemid }}" title="{{ __('This setting is enabled. Click here to disable it.') }}"></span>
                        <span class="cursor-pointer fa fa-times" id="noactive_{{ itemid }}" title="{{ __('This setting is disabled. Click here to enable it.') }}"></span>
                    {% endif %}
                    </a>
                    <noscript><div id="noscriptActive{{ itemid }}">
                        {{ site.active|yesno(true) }}
                    </div></noscript>
                </td>
                <td id="itemActions{{ site.id }}" headers="hItemActions" class="actions nowrap z-w02">
                    {% if site._actions|length > 0 %}
                        <div class="dropdown">
                            <a id="itemActions{{ site.id }}DropDownToggle" role="button" data-toggle="dropdown" data-target="#" href="javascript:void(0);" class="dropdown-toggle"><i class="fa fa-tasks"></i> <span class="caret"></span></a>
                            
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="itemActions{{ site.id }}DropDownToggle">
                                {% for option in site._actions %}
                                    <li role="presentation"><a href="{{ option.url.type|zikulamultisitesmodule_actionUrl(option.url.func, option.url.arguments) }}" title="{{ option.linkTitle|e('html_attr') }}" role="menuitem" tabindex="-1" class="fa fa-{{ option.icon }}">{{ option.linkText }}</a></li>
                                    
                                {% endfor %}
                            </ul>
                        </div>
                    {% endif %}
                </td>
            </tr>
        {% else %}
            <tr class="z-{{ routeArea == 'admin' ? 'admin' : 'data' }}tableempty">
                <td class="text-left" colspan="{% if routeArea == 'admin' %}9{% else %}8{% endif %}">
            {{ __('No sites found.') }}
              </td>
            </tr>
        {% endfor %}
        
            </tbody>
        </table>
        </div>
        
        {% if all != 1 %}
            {{ pager({ rowcount: pager.numitems, limit: pager.itemsperpage, display: 'page', route: 'zikulamultisitesmodule_site_' ~ routeArea ~ 'view'}) }}
        {% endif %}
    {% if routeArea == 'admin' %}
            <fieldset>
                <label for="zikulaMultisitesModuleAction" class="col-sm-3 control-label">{{ __('With selected sites') }}</label>
                <div class="col-sm-6">
                    <select id="zikulaMultisitesModuleAction" name="action" class="form-control input-sm">
                        <option value="">{{ __('Choose action') }}</option>
                        <option value="cleartemplates">{{ __('Clear all cache and compile directories', 'zikula') }}</option>
                        <option value="delete" title="{{ __('Delete content permanently.') }}">{{ __('Delete') }}</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="submit" value="{{ __('Submit') }}" class="btn btn-default btn-sm" />
                </div>
            </fieldset>
        </div>
    </form>
    {% endif %}

    {{ block('display_hooks') }}
</div>
{% endblock %}
{% block page_nav_links %}
    {% if canBeCreated %}
        {% if hasPermission('ZikulaMultisitesModule:Site:', '::', 'ACCESS_EDIT') %}
            {% set createTitle = __('Create site') %}
            <a href="{{ path('zikulamultisitesmodule_site_' ~ routeArea ~ 'edit') }}" title="{{ createTitle|e('html_attr') }}" class="fa fa-plus">{{ createTitle }}</a>
        {% endif %}
    {% endif %}
    {% set own = showOwnEntries is defined and showOwnEntries == 1 ? 1 : 0 %}
    {% set all = showAllEntries is defined and showAllEntries == 1 ? 1 : 0 %}
    {% all == 1 %}
        {% set linkTitle = __('Back to paginated view') %}
        <a href="{{ path('zikulamultisitesmodule_site_' ~ routeArea ~ 'view') }}" title="{{ linkTitle|e('html_attr') }}" class="fa fa-table">{{ linkTitle }}</a>
    {% else %}
        {% set linkTitle = __('Show all entries') %}
        <a href="{{ path('zikulamultisitesmodule_site_' ~ routeArea ~ 'view', { all: 1 }) }}" title="{{ linkTitle|e('html_attr') }}" class="fa fa-table">{{ linkTitle }}</a>
    {% endif %}
{% endblock %}
{% block display_hooks %}
    
    {# here you can activate calling display hooks for the view page if you need it #}
    {# % if routeArea != 'admin' %}
        {% set hooks = notifyDisplayHooks(eventName='zikulamultisitesmodule.ui_hooks.sites.display_view', urlObject=currentUrlObject) %}
        {% for providerArea, hook in hooks %}
            {{ hook }}
        {% endfor %}
    {% endif % #}
{% endblock %}
{% block footer %}
    {{ parent() }}

<script type="text/javascript">
/* <![CDATA[ */
    ( function($) {
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
            $('a.fa-zoom-in').attr('target', '_blank');
            {% for site in items %}
                {% set itemid = site.id %}
                zikulaMultisitesInitToggle('site', 'active', '{{ itemid|e('js') }}');
            {% endfor %}
            {% if routeArea == 'admin' %}
                {# init the "toggle all" functionality #}
                if ($('#toggleSites').length > 0) {
                    $('#toggleSites').on('click', function (e) {
                        Zikula.toggleInput('sitesViewForm');
                        e.preventDefault();
                    });
                }

                $('a.sitelink').attr('target', '_blank');
            {% endif %}
        });
    })(jQuery);
/* ]]> */
</script>
{% endblock %}
