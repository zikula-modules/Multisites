<?php
/**
 * Subsite.
 */

namespace Zikula\SubsiteModule;

use RuntimeException;
use Zikula\Core\AbstractExtensionInstaller;

/**
 * Subsite installer class.
 */
class SubsiteModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * Install the ZikulaSubsiteModule application.
     *
     * @return boolean True on success, or false
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the ZikulaSubsiteModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
        // update successful
        return true;
    }
    
    /**
     * Uninstall ZikulaSubsiteModule.
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        // remove all module vars
        $this->delVars();
    
        // uninstallation successful
        return true;
    }
}
