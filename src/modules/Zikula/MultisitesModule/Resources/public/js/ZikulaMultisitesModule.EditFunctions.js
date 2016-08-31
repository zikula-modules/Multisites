'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function zikulaMultisitesResetUploadField(fieldName)
{
    jQuery("input[id$='" + fieldName.toLowerCase() + "']").attr({
        type: 'input',
        type: 'file'
    });
}

/**
 * Initialises the reset button for a certain upload input.
 */
function zikulaMultisitesInitUploadField(fieldName)
{
    jQuery("a[id$='" + fieldName.toLowerCase() + "Val']").click( function (event) {
        event.stopPropagation();
        zikulaMultisitesResetUploadField(fieldName);
    }).removeClass('hidden');
}

