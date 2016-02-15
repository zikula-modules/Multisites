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

    if (jQuery('#catid').length > 0) {
        jQuery('#catid').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#sortBy').length > 0) {
        jQuery('#sortBy').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#sortDir').length > 0) {
        jQuery('#sortDir').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#num').length > 0) {
        jQuery('#num').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
    }

    switch (objectType) {
    case 'site':
        if (jQuery('#template').length > 0) {
            jQuery('#template').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#project').length > 0) {
            jQuery('#project').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#workflowState').length > 0) {
            jQuery('#workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#active').length > 0) {
            jQuery('#active').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    case 'template':
        if (jQuery('#workflowState').length > 0) {
            jQuery('#workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    case 'siteExtension':
        if (jQuery('#site').length > 0) {
            jQuery('#site').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#workflowState').length > 0) {
            jQuery('#workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#extensionType').length > 0) {
            jQuery('#extensionType').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
        }
        break;
    case 'project':
        if (jQuery('#workflowState').length > 0) {
            jQuery('#workflowState').change(function () { zikulaMultisitesSubmitQuickNavForm(objectType); });
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
        if (state === true) {
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
