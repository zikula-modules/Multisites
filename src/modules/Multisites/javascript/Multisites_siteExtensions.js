/**
 * Modifies the state of a module in a site database.
 */
function multisitesModifyModuleActivation(moduleName, siteId, newState)
{
    var params, request;

    params = 'id=' + siteId + '&moduleName=' + moduleName + '&newState=' + newState;
    $('module_' + moduleName).update('<img src="images/ajax/circle-ball-dark-antialiased.gif">');

    request = new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=Multisites&func=modifyModuleActivation',
        {
            method: 'post',
            parameters: params,
            onComplete: function (req) {
                if (!req.isSuccess()) {
                    Zikula.showajaxerror(req.getMessage());
                    return;
                }
                var data = req.getData();
                $('module_' + data.moduleName).update(data.content);
            }
        }
    );
}

/**
 * Creates, changes or deletes a module state in a site database depending on the module initial state.
 */
function multisitesAllowModule(moduleName, siteId)
{
    var params, request;

    params = 'id=' + siteId + '&moduleName=' + moduleName;
    $('module_' + moduleName).update('<img src="images/ajax/circle-ball-dark-antialiased.gif">');

    request = new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=Multisites&func=allowModule',
        {
            method: 'post',
            parameters: params,
            onComplete: function (req) {
                if (!req.isSuccess()) {
                    Zikula.showajaxerror(req.getMessage());
                    return;
                }
                var data = req.getData();
                $('module_' + data.moduleName).update(data.content);
            }
        }
    );
}

/**
 * Creates or deletes a theme state in a site database depending on the theme initial state.
 */
function multisitesAllowTheme(themeName, siteId)
{
    var params, request;

    params = 'id=' + siteId + '&themeName=' + themeName;
    $('theme_' + themeName).update('<img src="images/ajax/circle-ball-dark-antialiased.gif">');

    request = new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=Multisites&func=allowTheme',
        {
            method: 'post',
            parameters: params,
            onComplete: function (req) {
                if (!req.isSuccess()) {
                    Zikula.showajaxerror(req.getMessage());
                    return;
                }
                var data = req.getData();
                $('theme_' + data.themeName).update(data.content);
            }
        }
    );
}
