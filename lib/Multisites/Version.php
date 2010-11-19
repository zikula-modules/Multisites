<?php
/**
 * Load the module version information
 *
 * @author      Albert Pérez Monfort (aperezm@xtec.cat)
 * @return      The version information
 */

class Multisites_Version extends Zikula_Version
{
    public function getMetaData()
    {
        $meta = array();
        $meta['version'] = '1.0.1';
        $meta['description'] = $this->__('Zikula Multisites module');
        $meta['displayname'] = $this->__('Multisites manager');
        $meta['url'] = $this->__('multisites');
        $meta['securityschema'] = array('Multisites::' => '::');
        return $meta;
    }
}