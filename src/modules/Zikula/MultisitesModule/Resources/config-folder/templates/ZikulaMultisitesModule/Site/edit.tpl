{# purpose of this template: build the form to edit an instance of site #}
{% extends routeArea == 'admin' ? 'ZikulaMultisitesModule::adminBase.html.twig' : 'ZikulaMultisitesModule::base.html.twig' %}
{{ pageAddAsset('javascript', zasset('@ZikulaMultisitesModule:js/ZikulaMultisitesModule.EditFunctions.js')) }}
{{ pageAddAsset('javascript', zasset('@ZikulaMultisitesModule:js/ZikulaMultisitesModule.Validation.js')) }}
{{ pageAddAsset('javascript', 'web/typeahead.js/dist/typeahead.bundle.min.js') }}

{% if mode != 'create' %}
    {% block title __('Edit site') %}
    {% block admin_page_icon 'pencil-square-o' %}
{% elseif mode == 'create' %}
    {% block title __('Create site') %}
    {% block admin_page_icon 'plus' %}
{% endif %}
{% block content %}
    <div class="zikulamultisitesmodule-site zikulamultisitesmodule-edit">
{% if mode != 'create' %}
    <p class="alert alert-danger bold">{{ __('Caution: updating a site causes reapplying the template data again to it. All database tables except excluded ones will be dropped and recreated.') }}</p>
{% endif %}
{% form_theme form with [
    '@ZikulaMultisitesModule/Form/bootstrap_3.html.twig',
    '@ZikulaFormExtensionBundle/Form/form_div_layout.html.twig'
] %}
{{ form_start(form, {attr: {id: 'siteEditForm'}}) }}
{{ form_errors(form) }}
<fieldset>
    <legend>{{ __('Basic data') }}</legend>
    
    {{ form_row(form.name, { label: __('Name (internal)') }) }}
    
    {{ form_row(form.description, { label: __('Description (internal)') }) }}
    
    {{ form_row(form.siteAlias, { help: __('The alias must be a lower case, unique string containing only letters.') }) }}
    
    {% if mode == 'create' %}
        {{ form_row(form.siteName) }}
    {% else %}
        {{ form_row(form.siteName, { label: __('Original site name'), disabled: true }) }}
    {% endif %}
    
    {{ form_row(form.siteDescription) }}
</fieldset>
<fieldset>
    <legend>{{ __('Site host or folder') }}</legend>

    {% if mode == 'create' %}
        {{ form_row(form.siteDns, { help: __('This is the domain or folder name under which this site should be reachable.') }) }}
    {% else %}
        {{ form_row(form.siteDns, { help: __('This is the domain or folder name under which this site should be reachable.'), disabled: true }) }}
    {% endif %}
    
    {{ form_row(form.active) }}
</fieldset>
<fieldset>
    <legend>{{ __('Management information') }}</legend>

    {% if mode == 'create' %}
        {{ form_row(form.siteAdminName, { help: __('User names can contain letters, numbers, underscores, periods, or dashes.') }) }}

        {{ form_row(form.siteAdminPassword) }}
    {% else %}
        {{ form_row(form.siteAdminName, { label: __('Original site admin name'), disabled: true }) }}

        {{ form_row(form.siteAdminPassword, { label: __('Original site admin password'), disabled: true }) }}
    {% endif %}
    
    {{ form_row(form.siteAdminRealName) }}

    {% if mode == 'create' %}
        {{ form_row(form.siteAdminEmail) }}
    {% else %}
        {{ form_row(form.siteAdminEmail, { label: __('Original site admin email'), disabled: true }) }}
    {% endif %}

    {{ form_row(form.siteCompany) }}
</fieldset>
<fieldset>
    <legend>{{ __('Database data') }}</legend>

    {% if mode == 'create' %}
        <p class="alert alert-warning">{{ __('Caution: the database is emptied, so choose one which is not used by any other applications. All tables which are defined as excluded in the template are kept though.') }}</p>
    {% else %}
        <p class="alert alert-warning">{{ __('Change these values only if database credentials actually changed.') }}</p>
    {% endif %}

    {{ form_row(form.databaseType) }}
    
    {{ form_row(form.databaseHost) }}
    
    {{ form_row(form.databaseName) }}
    
    {{ form_row(form.databaseUserName) }}
    
    {{ form_row(form.databasePassword) }}

    {% if mode == 'create' %}
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="createNewDatabase" value="1" title="{{ __('Check this if the database does not exist yet.') }}" /> {{ __('Create database') }}
                    </label>
                </div>
                <span class="help-block z-sub">{{ __('Expert option! Only possible if the database user has sufficient permissions.') }}</span>
                <span class="help-block z-sub">{{ __('Note: the database user must exist already for this to work.') }}</span>
            </div>
        </div>
    {/if}
</fieldset>
<div{% if mode != 'create' %} class="hidden"{% endif %}>
    {{ include(
        '@ZikulaMultisitesModule/Project/includeSelectOne.html.twig',
        { group: 'site', alias: 'project', aliasReverse: 'sites', mandatory: true, idPrefix: 'multisitesSite_Project', linkingItem: site, displayMode: 'choices', allowEditing: false }
    ) }}
</div>
{% set templateMandatory = (mode == 'create' ? true : false) %}
{{ include(
    '@ZikulaMultisitesModule/Template/includeSelectOne.html.twig',
    { group: 'site', alias: 'template', aliasReverse: 'sites', mandatory: templateMandatory, idPrefix: 'multisitesSite_Template', linkingItem: site, displayMode: 'choices', allowEditing: false }
) }}
<fieldset>
    <legend>{{ __('Individualisation') }}</legend>

    {{ form_row(form.logo) }}

    {{ form_row(form.favIcon) }}

    <div class="hidden">
        {{ form_row(form.allowedLocales) }}
    </div>

    {{ form_row(form.parametersCsvFile, { help: __('Note: you should use semicolon as delimiter and UTF-8 as encoding.') }) }}

    {{ form_row(form.parametersArray) }}
</fieldset>

{% if mode != 'create' %}
    {{ include('Helper/includeStandardFieldsEdit.html.twig', { obj: site }) }}
{% endif %}

{# include display hooks #}
{% if mode != 'create' %}
    {% set hookId = site.id %}
    {% set hooks = notifyDisplayHooks(eventName='zikulamultisitesmodule.ui_hooks.sites.form_edit', id=hookId) %}
{% else %}
    {% set hooks = notifyDisplayHooks(eventName='zikulamultisitesmodule.ui_hooks.sites.form_edit', id=null) %}
{% endif %}
{% if hooks is iterable and hooks|length > 0 %}
    {% for providerArea, hook in hooks %}
        {% if providerArea != 'provider.scribite.ui_hooks.editor' %}{# fix for #664 #}
            <fieldset>
                {{ hook }}
            </fieldset>
        {% endif %}
    {% endfor %}
{% endif %}


{# include return control #}
{% if mode == 'create' %}
    <fieldset>
        <legend>{{ __('Return control') }}</legend>
        {{ form_row(form.repeatCreation) }}
    </fieldset>
{/if}

{# include possible submit actions #}
<div class="form-group form-buttons">
    <div class="col-sm-offset-3 col-sm-9">
        {% for action in actions %}
            {{ form_widget(attribute(form, action.id), {attr: {class: action.buttonClass}, icon: action.id == 'delete' ? 'fa-trash-o' : '') }}
        {% endfor %}
        {{ form_widget(form.reset, {attr: {class: 'btn btn-default', formnovalidate: 'formnovalidate'}, icon: 'fa-refresh'}) }}
        {{ form_widget(form.cancel, {attr: {class: 'btn btn-default', formnovalidate: 'formnovalidate'}, icon: 'fa-times'}) }}
    </div>
</div>
{{ form_end(form) }}
</div>
{% endblock %}
{% block footer %}
    {{ parent() }}

    % set editImage = '<span class="fa fa-pencil-square-o"></span>' %}
    % set deleteImage = '<span class="fa fa-trash-o"></span>' %}
    
    
    <script type="text/javascript">
    /* <![CDATA[ */
        
                var formButtons;
        
                function executeCustomValidationConstraints()
                {
                    zikulaMultisitesPerformCustomValidationRules('site', '{% if mode != 'create' %}{{ site.id }}{% endif %}');
                }
        
                function triggerFormValidation()
                {
                    executeCustomValidationConstraints();
                    if (!document.getElementById('siteEditForm').checkValidity()) {
                        // This does not really submit the form,
                        // but causes the browser to display the error message
                        jQuery('#siteEditForm').find(':submit').not(jQuery('#btnDelete')).first().click();
                    }
                }
        
                function handleFormSubmit (event) {
                    triggerFormValidation();
                    if (!document.getElementById('siteEditForm').checkValidity()) {
                        event.preventDefault();
                        return false;
                    }
        
                    // hide form buttons to prevent double submits by accident
                    formButtons.each(function (index) {
                        jQuery(this).addClass('hidden');
                    });
        
                    return true;
                }

                var parameterSpec = null;

                function filterTemplatesByProject()
                {
                    var oldTemplateId = $('#template').val();

                    $('#template option').remove();

                    {% if mode != 'create' %}
                        var opt = document.createElement('option');
                        opt.text = '{{ __('None (decouple site from template)') }}';
                        opt.value = '';
                        $('#template').options.add(opt);
                    {% endif %}

                    if (!$('#project').val()) {
                        return;
                    }

                    jQuery.ajax({
                        type: 'POST',
                        url: Routing.generate('zikulamultisitesmodule_ajax_getprojecttemplates'),
                        data: 'id=' + $('#project').val()
                    }).done(function(res) {
                        var data = res.data;
                        var templates = data.templates;
                        var includesOldId = false;

                        templates.each(function() {
                            var template = $(this);
                            var opt = document.createElement('option');
                            opt.text = template.name;
                            opt.value = template.id;
                            if (template.id == oldTemplateId) {
                                includesOldId = true;
                            }
                            {% if mode == 'create' || site.template is null %}
                                $('#template').options.add(opt);
                            {% else %}
                                if (template.id == oldTemplateId) {
                                    $('#template').options.add(opt);
                                }
                            {% endif %}

                            parameterSpec = template.parameters;
                        });

                        if (includesOldId === true) {
                            $('#template').value = oldTemplateId;
                        }
                    });
                }

                ( function($) {
                    $(document).ready(function() {
        
                        var allFormFields = $('#siteEditForm input, #siteEditForm select, #siteEditForm textarea');
                        allFormFields.change(executeCustomValidationConstraints);
        
                        formButtons = $('#siteEditForm .form-buttons input');
                        $('#btnDelete').bind('click keypress', function (e) {
                            if (!window.confirm('{{ __('Really delete this site?') }}')) {
                                e.preventDefault();
                            }
                        });
                        $('#siteEditForm').submit(handleFormSubmit);
        
                        {% if mode != 'create' %}
                            triggerFormValidation();
                        {% endif %}
        
                        $('#siteEditForm label').tooltip();
                        zikulaMultisitesInitUploadField('logo');
                        zikulaMultisitesInitUploadField('favIcon');
                        zikulaMultisitesInitUploadField('parametersCsvFile');

                        $('#deriveParametersFromTemplate').click(function(evt) {
                            evt.preventDefault();
                            if (null === parameterSpec) {
                                alert('{{ __('Parameter specification is not available yet.') }}');
                                return;
                            }
                            var spec = '';
                            parameterSpec.each(function() {
                                spec += $(this) + ': Your value' + "\n";
                            });
                            $('#parametersArray').val(spec);
                        }).removeClassName('hidden');

                        $('#project').change(filterTemplatesByProject);
                        filterTemplatesByProject();
                    });
                })(jQuery);
    /* ]]> */
    </script>
{% endblock %}
