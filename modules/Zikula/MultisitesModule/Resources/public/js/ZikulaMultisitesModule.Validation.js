'use strict';

function zikulaMultisitesValidateNoSpace(val) {
    var valStr;

    valStr = '' + val;

    return -1 === valStr.indexOf(' ');
}

function zikulaMultisitesValidateUploadExtension(val, elem) {
    var fileExtension, allowedExtensions;
    if ('' == val) {
        return true;
    }

    fileExtension = '.' + val.substr(val.lastIndexOf('.') + 1);
    allowedExtensions = jQuery('#' + elem.attr('id').replace(':', '\\:') + 'FileExtensions').text();
    allowedExtensions = '(.' + allowedExtensions.replace(/, /g, '|.').replace(/,/g, '|.') + ')$';
    allowedExtensions = new RegExp(allowedExtensions, 'i');

    return allowedExtensions.test(val);
}

/**
 * Runs special validation rules.
 */
function zikulaMultisitesExecuteCustomValidationConstraints(objectType, currentEntityId) {
    jQuery('.validate-upload').each(function () {
        if (!zikulaMultisitesValidateUploadExtension(jQuery(this).val(), jQuery(this))) {
            jQuery(this).get(0).setCustomValidity(Translator.__('Please select a valid file extension.'));
        } else {
            jQuery(this).get(0).setCustomValidity('');
        }
    });
}
