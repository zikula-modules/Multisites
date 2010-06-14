<?php
function smarty_function_multisitesadminmenulinks()
{
    $dom = ZLanguage::getModuleDomain('Multisites');
    // set some defaults
    if (!isset($params['start'])) {
        $params['start'] = '[';
    }
    if (!isset($params['end'])) {
        $params['end'] = ']';
    }
    if (!isset($params['seperator'])) {
        $params['seperator'] = '|';
    }
    if (!isset($params['class'])) {
        $params['class'] = 'pn-menuitem-title';
    }

    $multisitesadminmenulinks = "<span class=\"" . $params['class'] . "\">" . $params['start'] . " ";

    if (SecurityUtil::checkPermission('Multisites::', "::", ACCESS_ADMIN)) {
        $multisitesadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('Multisites', 'admin', 'main')) . "\">" . __('View instances', $dom) . "</a> " . $params['seperator'];
        $multisitesadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('Multisites', 'admin', 'newInstance')) . "\">" . __('New Instance', $dom) . "</a> " . $params['seperator'];
        $multisitesadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('Multisites', 'admin', 'manageModels')) . "\">" . __('View Models', $dom) . "</a> " . $params['seperator'];
        $multisitesadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('Multisites', 'admin', 'createNewModel')) . "\">" . __('Create New Model', $dom) . "</a> " . $params['seperator'];
        $multisitesadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('Multisites', 'admin', 'actualizer')) . "\">" . __('Actualise Modules', $dom) . "</a> " . $params['seperator'];
        $multisitesadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('Multisites', 'admin', 'config')) . "\">" . __('Configuration', $dom) . "</a> ";
    }

    $multisitesadminmenulinks .= $params['end'] . "</span>\n";

    return $multisitesadminmenulinks;
}