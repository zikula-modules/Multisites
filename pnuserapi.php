<?php
/**
 * Get all zikula instances that had been created
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	Returns an array with all the instances
 */
function Multisites_userapi_getAllSites($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // Security check
    if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_sites_column'];
    $where = "$c[instanceName] LIKE '$args[letter]%'";
    $orderby = "$c[instanceName]";
    // get the objects from the db
    $items = DBUtil::selectObjectArray('Multisites_sites', $where, $orderby, $args['startnum'] - 1, $args['itemsperpage'], 'instanceId');
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    // Return the items
    return $items;
}

/**
 * Get a instance information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The instance identity and the modules state
 * @return:	Returns the instance information
 */
function Multisites_userapi_getSite($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $instanceId = FormUtil::getPassedValue('instanceId', isset($args['instanceId']) ? $args['instanceId'] : null, 'POST');
    // Security check
    if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
    // Needed argument
    if ($instanceId == null || !is_numeric($instanceId)) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $items = DBUtil::selectObjectByID('Multisites_sites', $instanceId, 'instanceId');
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    // Return the items
    return $items;
}

/**
 * Get a site information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The site name
 * @return: The site information
 */
function Multisites_userapi_getSiteInfo($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $site = FormUtil::getPassedValue('site', isset($args['site']) ? $args['site'] : null, 'POST');

    // Needed argument
    if ($site == null) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }

    $pntable = pnDBGetTables();

    $c = $pntable['Multisites_sites_column'];

    $where = "$c[siteDNS] = '$site'";

    // get the objects from the db
    $items = DBUtil::selectObjectArray('Multisites_sites', $where, '', '-1', '-1', 'siteDNS');

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // Return the items
    return $items[$site];
}

/**
 * Get the siteDNS if it is available or active
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The siteDNS
 * @return: The siteDNS if it is active or nothing otherwise
 */
function Multisites_userapi_getSiteAvailability($args)
{
    $site = FormUtil::getPassedValue('site', isset($args['site']) ? $args['site'] : null, 'POST');
    // Needed argument
    if ($site == null) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_sites_column'];
    $where = "$c[siteDNS] = '$site'";
    // get the objects from the db
    $items = DBUtil::selectObjectArray('Multisites_sites', $where, '', '-1', '-1', 'siteDNS');
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    if ($items[$site]['active'] == 1) {
        return $items[$site]['siteDB'];
    } else {
        return '';
    }
}

/**
 * Get all the models available
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	An array with the models available
 */
function Multisites_userapi_getAllModels()
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // Security check
    if (!SecurityUtil::checkPermission('Multisites::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_models_column'];
    $where = '';
    $orderby = "$c[modelId]";
    // get the objects from the db
    $items = DBUtil::selectObjectArray('Multisites_models', $where, $orderby, '-1', '-1', 'modelId');
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    // Return the items
    return $items;
}

/**
 * Get a module
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model identity
 * @return:	The model name
 */
function Multisites_userapi_getModel($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelName = FormUtil::getPassedValue('modelName', isset($args['modelName']) ? $args['modelName'] : null, 'POST');
    // Needed argument
    if ($modelName == null) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }
    $pntable = pnDBGetTables();
    $c = $pntable['Multisites_models_column'];
    $where = "$c[modelName] = '$modelName'";
    // get the objects from the db
    $items = DBUtil::selectObjectArray('Multisites_models', $where, '', '-1', '-1', 'modelName');
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }
    // Return the items
    return $items[$modelName];
}

/**
 * Get a model information
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:	The model indentity
 * @return:	The model information
 */
function Multisites_userapi_getModelById($args)
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    $modelId = FormUtil::getPassedValue('modelId', isset($args['modelId']) ? $args['modelId'] : null, 'POST');

    // Needed argument
    if ($modelId == null) {
        return LogUtil::registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    }

    $pntable = pnDBGetTables();

    $c = $pntable['Multisites_models_column'];

    $where = "$c[modelId] = $modelId";

    // get the objects from the db
    $items = DBUtil::selectObjectArray('Multisites_models', $where, '', '-1', '-1', 'modelId');

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($items === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // Return the items
    return $items[$modelId];
}
