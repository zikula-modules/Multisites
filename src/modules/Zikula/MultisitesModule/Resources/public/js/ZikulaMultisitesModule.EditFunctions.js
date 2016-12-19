'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function zikulaMultisitesResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function zikulaMultisitesInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        zikulaMultisitesResetUploadField(fieldName);
    }).removeClass('hidden');
}

