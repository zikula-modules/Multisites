<?php
/**
 * Multisites.
 *
 * @copyright Albert P?rez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert P?rez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Listener;

use Zikula\MultisitesModule\Listener\Base\AbstractEntityLifecycleListener;

use FormUtil;
use ServiceUtil;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;

/**
 * Event subscriber implementation class for entity lifecycle events.
 */
class EntityLifecycleListener extends AbstractEntityLifecycleListener
{
    /**
     * The preRemove event occurs for a given entity before the respective EntityManager
     * remove operation for that entity is executed. It is not called for a DQL DELETE statement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof SiteEntity) {
            $deleteDatabase = FormUtil::getPassedValue('deleteDatabase', 0, 'POST', FILTER_VALIDATE_BOOLEAN);
            $deleteFiles = FormUtil::getPassedValue('deleteFiles', 0, 'POST', FILTER_VALIDATE_BOOLEAN);

            $serviceManager = ServiceUtil::getManager();
            $systemHelper = $serviceManager->get('zikula_multisites_module.system_helper');
            $flashBag = $serviceManager->get('session')->getFlashBag();

            if ($deleteDatabase == 1) {
                // delete the database
                if (!$systemHelper->deleteDatabase($this->getDatabaseData())) {
                    $flashBag->add('error', $serviceManager->get('translator.default')->__('Error during deleting the database.'));

                    return false;
                }
            }
            if ($deleteFiles == 1) {
                // delete the site files and directories
                $msConfig = $serviceManager->getParameter('multisites');
                $siteFolder = $msConfig['files_real_path'] . '/' . $this->getSiteAlias();
                if (!$systemHelper->deleteDir($siteFolder)) {
                    $flashBag->add('error', $serviceManager->get('translator.default')->__('Error during deleting the site files directory.'));

                    return false;
                }
            }
        }

        return parent::preRemove($args);
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be
     * invoked after the database delete operations. It is not called for a DQL DELETE statement.
     *
     * Note that the postRemove event or any events triggered after an entity removal can receive
     * an uninitializable proxy in case you have configured an entity to cascade remove relations.
     * In this case, you should load yourself the proxy in the associated pre event.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProjectEntity || $entity instanceof TemplateEntity || $entity instanceof SiteEntity) {
            $serviceManager = ServiceUtil::getManager();
            $systemHelper = $serviceManager->get('zikula_multisites_module.system_helper');

            // update db config removing all obsolete databases
            if (!$systemHelper->updateDatabaseConfigFile()) {
                $flashBag = $serviceManager->get('session')->getFlashBag();
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Updating the database configuration file failed.'));

                return false;
            }
        }

        if ($entity instanceof TemplateEntity) {
            // delete sql file only if it is not referenced by any other template
            $sqlFileIsRequired = $entity->isSqlFileReferencedByOtherTemplates();
            if ($sqlFileIsRequired) {
                return;
            }
        }

        parent::postRemove($args);
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations. Generated primary key values
     * are available in the postPersist event.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof SiteEntity) {
            $serviceManager = ServiceUtil::getManager();
            $systemHelper = $serviceManager->get('zikula_multisites_module.system_helper');
            $flashBag = $serviceManager->get('session')->getFlashBag();

            // update db config adding the new database
            if (!$systemHelper->updateDatabaseConfigFile()) {
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Updating the database configuration file failed.'));

                return false;
            }

            // save the site module into the Multisites database
            $extensionHelper = $serviceManager->get('zikula_multisites_module.siteextension_helper');
            if (!$extensionHelper->saveSiteModulesIntoOwnDb($this)) {
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Storing the site modules in the Multisites database failed.'));

                return false;
            }
        }

        parent::postPersist($args);
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof SiteEntity) {
            $serviceManager = ServiceUtil::getManager();
            $systemHelper = $serviceManager->get('zikula_multisites_module.system_helper');

            // update db config adding the new database
            if (!$systemHelper->updateDatabaseConfigFile()) {
                $flashBag = $serviceManager->get('session')->getFlashBag();
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Updating the database configuration file failed.'));

                return false;
            }
        }

        parent::postUpdate($args);
    }
}