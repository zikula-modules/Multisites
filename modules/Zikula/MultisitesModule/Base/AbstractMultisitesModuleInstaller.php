<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Base;

use Exception;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\MultisitesModule\Entity\ProjectEntity;

/**
 * Installer base class.
 */
abstract class AbstractMultisitesModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var string[]
     */
    protected $entities = [
        SiteEntity::class,
        TemplateEntity::class,
        ProjectEntity::class,
    ];

    public function install()
    {
        $logger = $this->container->get('logger');
    
        $userName = $this->container->get('zikula_users_module.current_user')->get('uname');
    
        // Check if upload directories exist and if needed create them
        try {
            $container = $this->container;
            $uploadHelper = new \Zikula\MultisitesModule\Helper\UploadHelper(
                $container->get('translator.default'),
                $container->get('filesystem'),
                $container->get('request_stack'),
                $logger,
                $container->get('zikula_users_module.current_user'),
                $container->get('zikula_extensions_module.api.variable'),
                $container->getParameter('datadir')
            );
            $uploadHelper->checkAndCreateAllUploadFolders();
        } catch (Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            $logger->error(
                '{app}: User {user} could not create upload folders during installation. Error details: {errorMessage}.',
                ['app' => 'ZikulaMultisitesModule', 'user' => $userName, 'errorMessage' => $exception->getMessage()]
            );
        }
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->entities);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error(
                '{app}: Could not create the database tables during installation. Error details: {errorMessage}.',
                ['app' => 'ZikulaMultisitesModule', 'errorMessage' => $exception->getMessage()]
            );
    
            throw $exception;
        }
    
        // set up all our vars with initial values
        $this->setVar('globalAdminName', '');
        $this->setVar('globalAdminPassword', '');
        $this->setVar('globalAdminEmail', '');
        $this->setVar('siteEntriesPerPage', 10);
        $this->setVar('templateEntriesPerPage', 10);
        $this->setVar('projectEntriesPerPage', 10);
        $this->setVar('showOnlyOwnEntries', false);
        $this->setVar('enableShrinkingForSiteLogo', false);
        $this->setVar('shrinkWidthSiteLogo', 800);
        $this->setVar('shrinkHeightSiteLogo', 600);
        $this->setVar('thumbnailModeSiteLogo', 'inset');
        $this->setVar('thumbnailWidthSiteLogoView', 32);
        $this->setVar('thumbnailHeightSiteLogoView', 24);
        $this->setVar('thumbnailWidthSiteLogoEdit', 240);
        $this->setVar('thumbnailHeightSiteLogoEdit', 180);
        $this->setVar('enableShrinkingForSiteFavIcon', false);
        $this->setVar('shrinkWidthSiteFavIcon', 800);
        $this->setVar('shrinkHeightSiteFavIcon', 600);
        $this->setVar('thumbnailModeSiteFavIcon', 'inset');
        $this->setVar('thumbnailWidthSiteFavIconView', 32);
        $this->setVar('thumbnailHeightSiteFavIconView', 24);
        $this->setVar('thumbnailWidthSiteFavIconEdit', 240);
        $this->setVar('thumbnailHeightSiteFavIconEdit', 180);
        $this->setVar('allowModerationSpecificCreatorForSite', false);
        $this->setVar('allowModerationSpecificCreationDateForSite', false);
        $this->setVar('allowModerationSpecificCreatorForTemplate', false);
        $this->setVar('allowModerationSpecificCreationDateForTemplate', false);
        $this->setVar('allowModerationSpecificCreatorForProject', false);
        $this->setVar('allowModerationSpecificCreationDateForProject', false);
    
        // initialisation successful
        return true;
    }
    
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->entities);
                } catch (Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error(
                        '{app}: Could not update the database tables during the upgrade.'
                            . ' Error details: {errorMessage}.',
                        ['app' => 'ZikulaMultisitesModule', 'errorMessage' => $exception->getMessage()]
                    );
    
                    throw $exception;
                }
        }
    */
    
        // update successful
        return true;
    }
    
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        try {
            $this->schemaTool->drop($this->entities);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error(
                '{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.',
                ['app' => 'ZikulaMultisitesModule', 'errorMessage' => $exception->getMessage()]
            );
    
            throw $exception;
        }
    
        // remove all module vars
        $this->delVars();
    
        // remind user about upload folders not being deleted
        $uploadPath = $this->container->getParameter('datadir') . '/ZikulaMultisitesModule/';
        $this->addFlash(
            'status',
            $this->__f(
                'The upload directories at "%path%" can be removed manually.',
                ['%path%' => $uploadPath]
            )
        );
    
        // uninstallation successful
        return true;
    }
}
