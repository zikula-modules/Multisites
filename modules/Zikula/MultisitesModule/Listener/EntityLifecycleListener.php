<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.0.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Zikula\MultisitesModule\DatabaseInfo;
use Zikula\MultisitesModule\Entity\ProjectEntity;
use Zikula\MultisitesModule\Entity\SiteEntity;
use Zikula\MultisitesModule\Entity\TemplateEntity;
use Zikula\MultisitesModule\Listener\Base\AbstractEntityLifecycleListener;

/**
 * Event subscriber implementation class for entity lifecycle events.
 */
class EntityLifecycleListener extends AbstractEntityLifecycleListener
{
    /**
     * @inheritDoc
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        if ($entity instanceof SiteEntity) {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $deleteDatabase = $request->request->getBoolean('deleteDatabase', false);
            $deleteFiles = $request->request->getBoolean('deleteFiles', false);

            $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
            $flashBag = $this->container->get('session')->getFlashBag();

            if (true === $deleteDatabase) {
                // delete the database
                if (!$systemHelper->deleteDatabase(new DatabaseInfo($entity))) {
                    $flashBag->add('error', $this->container->get('translator.default')->__('Error during deleting the database.'));

                    return false;
                }
            }
            if (true === $deleteFiles) {
                // delete the site files and directories
                $dataDirectory = $this->container->getParameter('datadir') . '/' . $this->getSiteAlias();
                if (!$systemHelper->deleteDir($dataDirectory)) {
                    $flashBag->add('error', $this->container->get('translator.default')->__('Error during deleting the site files directory.'));

                    return false;
                }
            }
        }

        return parent::preRemove($args);
    }

    /**
     * @inheritDoc
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        if ($entity instanceof ProjectEntity || $entity instanceof TemplateEntity || $entity instanceof SiteEntity) {
            $systemHelper = $this->container->get('zikula_multisites_module.system_helper');

            // update subsites config removing all obsolete sites
            if (!$systemHelper->updateSubsitesConfigFile()) {
                $flashBag = $this->container->get('session')->getFlashBag();
                $flashBag->add('error', $this->container->get('translator.default')->__('Error! Updating the database configuration file failed.'));

                return false;
            }
        }

        parent::postRemove($args);
    }

    /**
     * @inheritDoc
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        if ($entity instanceof SiteEntity) {
            $systemHelper = $this->container->get('zikula_multisites_module.system_helper');
            $flashBag = $this->container->get('session')->getFlashBag();

            // update subsites config adding the new site data
            if (!$systemHelper->updateSubsitesConfigFile()) {
                $flashBag->add('error', $this->container->get('translator.default')->__('Error! Updating the database configuration file failed.'));

                return false;
            }
        }

        parent::postPersist($args);
    }

    /**
     * @inheritDoc
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        if ($entity instanceof SiteEntity) {
            $systemHelper = $this->container->get('zikula_multisites_module.system_helper');

            // update subsites config updating the site data
            if (!$systemHelper->updateSubsitesConfigFile()) {
                $flashBag = $this->container->get('session')->getFlashBag();
                $flashBag->add('error', $this->container->get('translator.default')->__('Error! Updating the database configuration file failed.'));

                return false;
            }
        }

        parent::postUpdate($args);
    }
}