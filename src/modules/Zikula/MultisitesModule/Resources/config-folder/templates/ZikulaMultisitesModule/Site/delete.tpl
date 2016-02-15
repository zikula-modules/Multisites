{# purpose of this template: sites delete confirmation view #}
{% extends routeArea == 'admin' ? 'ZikulaMultisitesModule::adminBase.html.twig' : 'ZikulaMultisitesModule::base.html.twig' %}
{% block title __('Delete site') %}
{% block admin_page_icon 'trash-o' %}
{% block content %}
    <div class="zikulamultisitesmodule-site zikulamultisitesmodule-delete">
        <p class="alert alert-warning">{{ __f('Do you really want to delete this site: "%name%" ?', {'%name%': site.getTitleFromDisplayPattern()}) }}</p>

        {% form_theme deleteForm with [
            '@ZikulaMultisitesModule/Form/bootstrap_3.html.twig',
            '@ZikulaFormExtensionBundle/Form/form_div_layout.html.twig'
        ] %}
        {{ form_start(deleteForm) }}
        {{ form_errors(deleteForm) }}

        <fieldset>
            <legend>{{ __('Basic site data') }}</legend>

            <div class="form-group">
                <label class="control-label col-sm-3">{{ __('Name') }}</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><strong>{{ site.name }}</strong></p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{{ __('Site name') }}</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><strong>{{ site.siteName }}</strong></p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{{ __('Site dns') }}</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><strong>{{ site.siteDns }}</strong></p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">{{ __('Database name') }}</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><strong>{{ site.databaseName }}</strong></p>
                </div>
            </div>
        </fieldset>

        {# TODO: migrate options to Symfony Forms (but should nevertheless work as it is now!) #}
        <fieldset>
            <legend>{{ __('Delete options') }}</legend>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="deleteFiles" value="1" /> {{ __('Delete site files') }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="deleteDatabase" value="1" /> {{ __('Delete database') }}
                        </label>
                    </div>
                    <span class="help-block z-sub">{{ __('Expert option! Only possible if the database user has sufficient permissions.') }}</span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>{{ __('Confirmation prompt') }}</legend>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    {{ form_widget(deleteForm.delete, {attr: {class: 'btn btn-success'}, icon: 'fa-trash-o'}) }}
                    {{ form_widget(deleteForm.cancel, {attr: {class: 'btn btn-default', formnovalidate: 'formnovalidate'}, icon: 'fa-times'}) }}
                </div>
            </div>
        </fieldset>

        {{ block('display_hooks') }}
        {{ form_end(form) }}
    </div>
{% endblock %}
{% block display_hooks %}
    {% set hooks = notifyDisplayHooks(eventName='zikulamultisitesmodule.ui_hooks.sites.form_delete', id=site.id) %}
    {% if hooks is iterable and hooks|length > 0 %}
        {% for providerArea, hook in hooks %}
            <fieldset>
                {# <legend>{{ hookName }}</legend> #}
                {{ hook }}
            </fieldset>
        {% endfor %}
    {% endif %}
{% endblock %}
