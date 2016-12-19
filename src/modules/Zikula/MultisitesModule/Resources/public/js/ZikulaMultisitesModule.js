'use strict';

function zikulaMultisitesCapitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Submits a quick navigation form.
 */
function zikulaMultisitesSubmitQuickNavForm(objectType)
{
    jQuery('#zikulamultisitesmodule' + zikulaMultisitesCapitaliseFirstLetter(objectType) + 'QuickNavForm').submit();
}

/**
 * Initialise the quick navigation panel in list views.
 */
function zikulaMultisitesInitQuickNavigation(objectType)
{
    if (jQuery('#zikulamultisitesmodule' + zikulaMultisitesCapitaliseFirstLetter(objectType) + 'QuickNavForm').length < 1) {
        return;
    }

    var fieldPrefix = 'zikulamultisitesmodule_' + objectType.toLowerCase() + 'quicknav_';
    if (jQuery('#' + fieldPrefix + 'catid').length > 0) {
        jQuery('#' + fieldPrefix + 'catid').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'sortBy').length > 0) {
        jQuery('#' + fieldPrefix + 'sortBy').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'sortDir').length > 0) {
        jQuery('#' + fieldPrefix + 'sortDir').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'num').length > 0) {
        jQuery('#' + fieldPrefix + 'num').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }

    switch (objectType) {
    case 'site':
        if (jQuery('#' + fieldPrefix + 'template').length > 0) {
            jQuery('#' + fieldPrefix + 'template').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'project').length > 0) {
            jQuery('#' + fieldPrefix + 'project').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'active').length > 0) {
            jQuery('#' + fieldPrefix + 'active').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    case 'template':
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    case 'siteExtension':
        if (jQuery('#' + fieldPrefix + 'site').length > 0) {
            jQuery('#' + fieldPrefix + 'site').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'extensionType').length > 0) {
            jQuery('#' + fieldPrefix + 'extensionType').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    case 'project':
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    default:
        break;
    }
}

/**
 * Helper function to create new Bootstrap modal window instances.
 */
function zikulaMultisitesInitInlineWindow(containerElem, title)
{
    var newWindowId;

    // show the container (hidden for users without JavaScript)
    containerElem.removeClass('hidden');

    // define name of window
    newWindowId = containerElem.attr('id') + 'Dialog';

    containerElem.unbind('click').click(function(e) {
        e.preventDefault();

        // check if window exists already
        if (jQuery('#' + newWindowId).length < 1) {
            // create new window instance
            jQuery('<div id="' + newWindowId + '"></div>')
                .append(
                    jQuery('<iframe width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />')
                        .attr('src', containerElem.attr('href'))
                )
                .dialog({
                    autoOpen: false,
                    show: {
                        effect: 'blind',
                        duration: 1000
                    },
                    hide: {
                        effect: 'explode',
                        duration: 1000
                    },
                    title: title,
                    width: 600,
                    height: 400,
                    modal: false
                });
        }

        // open the window
        jQuery('#' + newWindowId).dialog('open');
    });

    // return the dialog selector id;
    return newWindowId;
}


/**
 * Initialise ajax-based toggle for boolean fields.
 */
function zikulaMultisitesInitToggle(objectType, fieldName, itemId)
{
    var idSuffix = zikulaMultisitesCapitaliseFirstLetter(fieldName) + itemId;
    if (jQuery('#toggle' + idSuffix).length < 1) {
        return;
    }
    jQuery('#toggle' + idSuffix).click( function() {
        zikulaMultisitesToggleFlag(objectType, fieldName, itemId);
    }).removeClass('hidden');
}


/**
 * Toggles a certain flag for a given item.
 */
function zikulaMultisitesToggleFlag(objectType, fieldName, itemId)
{
    var fieldNameCapitalised = zikulaMultisitesCapitaliseFirstLetter(fieldName);
    var params = 'ot=' + objectType + '&field=' + fieldName + '&id=' + itemId;

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('zikulamultisitesmodule_ajax_toggleflag'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var idSuffix, data;

        idSuffix = fieldName + '_' + itemId;
        data = res.data;

        /*if (data.message) {
            zikulaMultisitesSimpleAlert(jQuery('#toggle' + idSuffix), Zikula.__('Success', 'zikulamultisitesmodule_js'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
        }*/

        idSuffix = idSuffix.toLowerCase();
        var state = data.state;
        if (true === state) {
            jQuery('#no' + idSuffix).addClass('hidden');
            jQuery('#yes' + idSuffix).removeClass('hidden');
        } else {
            jQuery('#yes' + idSuffix).addClass('hidden');
            jQuery('#no' + idSuffix).removeClass('hidden');
        }
    });
}


/**
 * Simulates a simple alert using bootstrap.
 */
function zikulaMultisitesSimpleAlert(beforeElem, title, content, alertId, cssClass)
{
    var alertBox;

    alertBox = ' \
        <div id="' + alertId + '" class="alert alert-' + cssClass + ' fade"> \
          <button type="button" class="close" data-dismiss="alert">&times;</button> \
          <h4>' + title + '</h4> \
          <p>' + content + '</p> \
        </div>';

    // insert alert before the given element
    beforeElem.before(alertBox);

    jQuery('#' + alertId).delay(200).addClass('in').fadeOut(4000, function () {
        jQuery(this).remove();
    });
}
