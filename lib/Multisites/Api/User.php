<?php

class Multisites_Api_User extends Zikula_AbstractApi
{

    /**
     * Get all zikula instances that had been created
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: Returns an array with all the instances
     */
    public function getAllSites($args)
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        $table = DBUtil::getTables();
        $c = $table['multisitessites_column'];
        $where = (isset($args['letter'])) ? "$c[instancename] LIKE '$args[letter]%'" : "";
        $startnum = (isset($args['startnum'])) ? $args['startnum'] : 0;
        $itemsperpage = (isset($args['itemsperpage'])) ? $args['itemsperpage'] : -1;
        $orderby = "$c[instancename]";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('multisitessites', $where, $orderby, $startnum - 1, $itemsperpage, 'instanceid');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    /**
     * Get a instance information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The instance identity and the modules state
     * @return: Returns the instance information
     */
    public function getSite($args)
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        $instanceid = FormUtil::getPassedValue('instanceid', isset($args['instanceid']) ? $args['instanceid'] : null, 'POST');

        // Needed argument
        if ($instanceid == null || !is_numeric($instanceid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $items = DBUtil::selectObjectByID('multisitessites', $instanceid, 'instanceid');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    /**
     * Get a site information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The site name
     * @return: The site information
     */
    public function getSiteInfo($args)
    {
        $site = FormUtil::getPassedValue('site', isset($args['site']) ? $args['site'] : null, 'POST');

        // Needed argument
        if ($site == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $table = DBUtil::getTables();

        $c = $table['multisitessites_column'];

        $where = "$c[sitedns] = '$site'";

        // get the objects from the db
        $items = DBUtil::selectObjectArray('multisitessites', $where, '', '-1', '-1', 'sitedns');

        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // Return the items
        return $items;
    }

    /**
     * Get the sitedns if it is available or active
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The sitedns
     * @return: The sitedns if it is active or nothing otherwise
     */
    public function getSiteAvailability($args)
    {
        $site = FormUtil::getPassedValue('site', isset($args['site']) ? $args['site'] : null, 'POST');
        // Needed argument
        if ($site == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $table = DBUtil::getTables();
        $c = $table['multisitessites_column'];
        $where = "$c[sitedns] = '$site'";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('multisitessites', $where, '', '-1', '-1', 'sitedns');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        if ($items[$site]['active'] == 1) {
            return $items[$site]['siteDB'];
        } else {
            return '';
        }
    }

    /**
     * Get all the models available
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @return: An array with the models available
     */
    public function getAllModels()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        $table = DBUtil::getTables();
        $c = $table['multisitesmodels_column'];
        $where = '';
        $orderby = "$c[modelid]";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('multisitesmodels', $where, $orderby, '-1', '-1', 'modelid');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    /**
     * Get a module
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model identity
     * @return: The model name
     */
    public function getModel($args)
    {
        $modelname = FormUtil::getPassedValue('modelname', isset($args['modelname']) ? $args['modelname'] : null, 'POST');
        // Needed argument
        if ($modelname == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $table = DBUtil::getTables();
        $c = $table['multisitesmodels_column'];
        $where = "$c[modelname] = '$modelname'";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('multisitesmodels', $where, '', '-1', '-1', 'modelname');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items[$modelname];
    }

    /**
     * Get a model information
     * @author: Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:  The model indentity
     * @return: The model information
     */
    public function getModelById($args)
    {
        $modelid = FormUtil::getPassedValue('modelid', isset($args['modelid']) ? $args['modelid'] : null, 'POST');

        // Needed argument
        if ($modelid == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $table = DBUtil::getTables();

        $c = $table['multisitesmodels_column'];

        $where = "$c[modelid] = $modelid";

        // get the objects from the db
        $items = DBUtil::selectObjectArray('multisitesmodels', $where, '', '-1', '-1', 'modelid');

        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // Return the items
        return $items[$modelid];
    }
}