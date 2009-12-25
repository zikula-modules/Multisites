<?php
/**
 * get site availability
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @return:	The siteDNS
 */
function Multisites_user_getSiteAvailability($args)
{
    $site = FormUtil::getPassedValue('site', isset($args['site']) ? $args['site'] : null, 'GET');
    $siteAvailability = pnModAPIFunc('Multisites', 'user', 'getSiteAvailability', array('site' => $site));
    print $siteAvailability;
    exit();
}
