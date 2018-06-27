/**
 * Modifies the state of a module in a site database.
 */
function multisitesModifyModuleActivation(moduleName, siteId, newState)
{
    jQuery('#module_' + moduleName).html('<img src="images/ajax/circle-ball-dark-antialiased.gif">');

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('zikulamultisitesmodule_ajax_modifymoduleactivation'),
        data: {
            id: siteId,
            moduleName: moduleName,
            newState: newState
        }
    }).done(function(data) {
        var data = req.getData();
        jQuery('#module_' + data.moduleName).html(data.content);
    });
}

/**
 * Creates, changes or deletes a module state in a site database depending on the module initial state.
 */
function multisitesAllowModule(moduleName, siteId)
{
    jQuery('#module_' + moduleName).html('<img src="images/ajax/circle-ball-dark-antialiased.gif">');

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('zikulamultisitesmodule_ajax_allowmodule'),
        data: {
            id: siteId,
            moduleName: moduleName
        }
    }).done(function(data) {
        var data = req.getData();
        jQuery('#module_' + data.moduleName).html(data.content);
    });
}

/**
 * Creates or deletes a theme state in a site database depending on the theme initial state.
 */
function multisitesAllowTheme(themeName, siteId)
{
    jQuery('#theme_' + themeName).html('<img src="images/ajax/circle-ball-dark-antialiased.gif">');

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('zikulamultisitesmodule_ajax_allowtheme'),
        data: {
            id: siteId,
            themeName: themeName
        }
    }).done(function(data) {
        var data = req.getData();
        jQuery('#module_' + data.themeName).html(data.content);
    });
}
