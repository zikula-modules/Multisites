'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function zikulaMultisitesResetUploadField(fieldName)
{
    if (jQuery('#' + fieldName).size() > 0) {
        jQuery('#' + fieldName).attr('type', 'input');
        jQuery('#' + fieldName).attr('type', 'file');
    }
}

/**
 * Initialises the reset button for a certain upload input.
 */
function zikulaMultisitesInitUploadField(fieldName)
{
    var fieldNameCapitalised;

    fieldNameCapitalised = fieldName.charAt(0).toUpperCase() + fieldName.substring(1);
    if (jQuery('#reset' + fieldNameCapitalised + 'Val').size() > 0) {
        jQuery('#reset' + fieldNameCapitalised + 'Val').click( function (evt) {
            event.stopPropagation();
            zikulaMultisitesResetUploadField(fieldName);
        }).removeClass('hidden');
    }
}

