'use strict';

function zikulaMultisitesCapitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Initialise the quick navigation form in list views.
 */
function zikulaMultisitesInitQuickNavigation() {
    var quickNavForm;
    var objectType;

    if (jQuery('.zikulamultisitesmodule-quicknav').length < 1) {
        return;
    }

    quickNavForm = jQuery('.zikulamultisitesmodule-quicknav').first();
    objectType = quickNavForm.attr('id').replace('zikulaMultisitesModule', '').replace('QuickNavForm', '');

    quickNavForm.find('select').change(function (event) {
        quickNavForm.submit();
    });

    var fieldPrefix = 'zikulamultisitesmodule_' + objectType.toLowerCase() + 'quicknav_';
    // we can hide the submit button if we have no visible quick search field
    if (jQuery('#' + fieldPrefix + 'q').length < 1 || jQuery('#' + fieldPrefix + 'q').parent().parent().hasClass('hidden')) {
        jQuery('#' + fieldPrefix + 'updateview').addClass('hidden');
    }
}

/**
 * Toggles a certain flag for a given item.
 */
function zikulaMultisitesToggleFlag(objectType, fieldName, itemId) {
    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('zikulamultisitesmodule_ajax_toggleflag'),
        data: {
            ot: objectType,
            field: fieldName,
            id: itemId
        },
        success: function (data) {
            var idSuffix;
            var toggleLink;

            idSuffix = zikulaMultisitesCapitaliseFirstLetter(fieldName) + itemId;
            toggleLink = jQuery('#toggle' + idSuffix);

            if (data.message) {
                zikulaMultisitesSimpleAlert(toggleLink, Translator.__('Success'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
            }

            toggleLink.find('.fa-check').toggleClass('hidden', true !== data.state);
            toggleLink.find('.fa-times').toggleClass('hidden', true === data.state);
        }
    });
}

/**
 * Initialise ajax-based toggle for all affected boolean fields on the current page.
 */
function zikulaMultisitesInitAjaxToggles() {
    jQuery('.zikulamultisites-ajax-toggle').click(function (event) {
        var objectType;
        var fieldName;
        var itemId;

        event.preventDefault();
        objectType = jQuery(this).data('object-type');
        fieldName = jQuery(this).data('field-name');
        itemId = jQuery(this).data('item-id');

        zikulaMultisitesToggleFlag(objectType, fieldName, itemId);
    }).removeClass('hidden');
}

/**
 * Simulates a simple alert using bootstrap.
 */
function zikulaMultisitesSimpleAlert(anchorElement, title, content, alertId, cssClass) {
    var alertBox;

    alertBox = ' \
        <div id="' + alertId + '" class="alert alert-' + cssClass + ' fade"> \
          <button type="button" class="close" data-dismiss="alert">&times;</button> \
          <h4>' + title + '</h4> \
          <p>' + content + '</p> \
        </div>';

    // insert alert before the given anchor element
    anchorElement.before(alertBox);

    jQuery('#' + alertId).delay(200).addClass('in').fadeOut(4000, function () {
        jQuery(this).remove();
    });
}

/**
 * Initialises the mass toggle functionality for admin view pages.
 */
function zikulaMultisitesInitMassToggle() {
    if (jQuery('.zikulamultisites-mass-toggle').length > 0) {
        jQuery('.zikulamultisites-mass-toggle').unbind('click').click(function (event) {
            jQuery('.zikulamultisites-toggle-checkbox').prop('checked', jQuery(this).prop('checked'));
        });
    }
}

/**
 * Creates a dropdown menu for the item actions.
 */
function zikulaMultisitesInitItemActions(context) {
    var containerSelector;
    var containers;
    
    containerSelector = '';
    if (context == 'view') {
        containerSelector = '.zikulamultisitesmodule-view';
    } else if (context == 'display') {
        containerSelector = 'h2, h3';
    }
    
    if (containerSelector == '') {
        return;
    }
    
    containers = jQuery(containerSelector);
    if (containers.length < 1) {
        return;
    }
    
    containers.find('.dropdown > ul').removeClass('list-inline').addClass('list-unstyled dropdown-menu');
    containers.find('.dropdown > ul a i').addClass('fa-fw');
    containers.find('.dropdown-toggle').removeClass('hidden').dropdown();
}

/**
 * Initialises image viewing behaviour.
 */
function zikulaMultisitesInitImageViewer() {
    if (typeof(magnificPopup) === 'undefined') {
        return;
    }
    jQuery('a.image-link').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        image: {
            titleSrc: 'title',
            verticalFit: true
        },
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
            tPrev: Translator.__('Previous (Left arrow key)'),
            tNext: Translator.__('Next (Right arrow key)'),
            tCounter: '<span class="mfp-counter">%curr% ' + Translator.__('of') + ' %total%</span>'
        },
        zoom: {
            enabled: true,
            duration: 300,
            easing: 'ease-in-out'
        }
    });
}

jQuery(document).ready(function () {
    var isViewPage;
    var isDisplayPage;

    isViewPage = jQuery('.zikulamultisitesmodule-view').length > 0;
    isDisplayPage = jQuery('.zikulamultisitesmodule-display').length > 0;

    zikulaMultisitesInitImageViewer();

    if (isViewPage) {
        zikulaMultisitesInitQuickNavigation();
        zikulaMultisitesInitMassToggle();
        zikulaMultisitesInitItemActions('view');
        zikulaMultisitesInitAjaxToggles();
    } else if (isDisplayPage) {
        zikulaMultisitesInitItemActions('display');
        zikulaMultisitesInitAjaxToggles();
    }
});
