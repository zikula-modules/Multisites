'use strict';

function multisitesToggleShrinkSettings(fieldName) {
    var idSuffix = fieldName.replace('zikulamultisitesmodule_appsettings_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('hidden', !jQuery('#zikulamultisitesmodule_appsettings_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function() {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            multisitesToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        multisitesToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
