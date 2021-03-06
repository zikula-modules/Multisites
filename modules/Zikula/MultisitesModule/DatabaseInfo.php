<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @see https://modulestudio.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule;

use Zikula\MultisitesModule\SiteEntity;

/**
 * Database information object.
 */
class DatabaseInfo
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $password;
    /**
     * DatabaseInfo constructor.
     *
     * @param SiteEntity|array $dbInfo List of database parameters
     */
    public function __construct($dbInfo)
    {
        if ($dbInfo instanceof SiteEntity) {
            $this->alias = $dbInfo->getSiteAlias();
            $this->name = $dbInfo->getDatabaseName();
            $this->host = $dbInfo->getDatabaseHost();
            $this->type = $dbInfo->getDatabaseType();
            $this->userName = $dbInfo->getDatabaseUserName();
            $this->password = $dbInfo->getDatabasePassword();
        } elseif (is_array($dbInfo)) {
            $this->alias = $dbInfo['alias'];
            $this->name = $dbInfo['dbName'];
            $this->host = $dbInfo['dbHost'];
            $this->type = $dbInfo['dbType'];
            $this->userName = $dbInfo['dbUser'];
            $this->password = $dbInfo['dbPass'];
        }
    }


    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Retrieves database information array for configuration.
     *
     * @return array List of database parameters
     */
    public function getConfigData()
    {
        return [
            'alias' => $this->getAlias(),
            'dbName' => $this->getName(),
            'dbHost' => $this->getHost(),
            'dbType' => $this->getType(),
            'dbUser' => $this->getUserName(),
            'dbPass' => $this->getPassword()
        ];
    }
}
