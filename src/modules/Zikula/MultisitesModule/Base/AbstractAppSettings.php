<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Base;

use Symfony\Component\Validator\Constraints as Assert;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\MultisitesModule\Validator\Constraints as MultisitesAssert;

/**
 * Application settings class for handling module variables.
 */
abstract class AbstractAppSettings
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $globalAdminName
     */
    protected $globalAdminName = '';
    
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $globalAdminPassword
     */
    protected $globalAdminPassword = '';
    
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @Assert\Email(checkMX=false, checkHost=false)
     * @var string $globalAdminEmail
     */
    protected $globalAdminEmail = '';
    
    /**
     * The amount of sites shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $siteEntriesPerPage
     */
    protected $siteEntriesPerPage = 10;
    
    /**
     * The amount of templates shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $templateEntriesPerPage
     */
    protected $templateEntriesPerPage = 10;
    
    /**
     * The amount of projects shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $projectEntriesPerPage
     */
    protected $projectEntriesPerPage = 10;
    
    /**
     * Whether only own entries should be shown on view pages by default or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showOnlyOwnEntries
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableShrinkingForSiteLogo
     */
    protected $enableShrinkingForSiteLogo = false;
    
    /**
     * The maximum image width in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $shrinkWidthSiteLogo
     */
    protected $shrinkWidthSiteLogo = 800;
    
    /**
     * The maximum image height in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $shrinkHeightSiteLogo
     */
    protected $shrinkHeightSiteLogo = 600;
    
    /**
     * Thumbnail mode (inset or outbound).
     *
     * @Assert\NotBlank()
     * @MultisitesAssert\ListEntry(entityName="appSettings", propertyName="thumbnailModeSiteLogo", multiple=false)
     * @var string $thumbnailModeSiteLogo
     */
    protected $thumbnailModeSiteLogo = 'inset';
    
    /**
     * Thumbnail width on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailWidthSiteLogoView
     */
    protected $thumbnailWidthSiteLogoView = 32;
    
    /**
     * Thumbnail height on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailHeightSiteLogoView
     */
    protected $thumbnailHeightSiteLogoView = 24;
    
    /**
     * Thumbnail width on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailWidthSiteLogoEdit
     */
    protected $thumbnailWidthSiteLogoEdit = 240;
    
    /**
     * Thumbnail height on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailHeightSiteLogoEdit
     */
    protected $thumbnailHeightSiteLogoEdit = 180;
    
    /**
     * Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableShrinkingForSiteFavIcon
     */
    protected $enableShrinkingForSiteFavIcon = false;
    
    /**
     * The maximum image width in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $shrinkWidthSiteFavIcon
     */
    protected $shrinkWidthSiteFavIcon = 800;
    
    /**
     * The maximum image height in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $shrinkHeightSiteFavIcon
     */
    protected $shrinkHeightSiteFavIcon = 600;
    
    /**
     * Thumbnail mode (inset or outbound).
     *
     * @Assert\NotBlank()
     * @MultisitesAssert\ListEntry(entityName="appSettings", propertyName="thumbnailModeSiteFavIcon", multiple=false)
     * @var string $thumbnailModeSiteFavIcon
     */
    protected $thumbnailModeSiteFavIcon = 'inset';
    
    /**
     * Thumbnail width on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailWidthSiteFavIconView
     */
    protected $thumbnailWidthSiteFavIconView = 32;
    
    /**
     * Thumbnail height on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailHeightSiteFavIconView
     */
    protected $thumbnailHeightSiteFavIconView = 24;
    
    /**
     * Thumbnail width on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailWidthSiteFavIconEdit
     */
    protected $thumbnailWidthSiteFavIconEdit = 240;
    
    /**
     * Thumbnail height on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $thumbnailHeightSiteFavIconEdit
     */
    protected $thumbnailHeightSiteFavIconEdit = 180;
    
    
    /**
     * AppSettings constructor.
     *
     * @param VariableApiInterface $variableApi VariableApi service instance
     */
    public function __construct(
        VariableApiInterface $variableApi
    ) {
        $this->variableApi = $variableApi;
    
        $this->load();
    }
    
    /**
     * Returns the global admin name.
     *
     * @return string
     */
    public function getGlobalAdminName()
    {
        return $this->globalAdminName;
    }
    
    /**
     * Sets the global admin name.
     *
     * @param string $globalAdminName
     *
     * @return void
     */
    public function setGlobalAdminName($globalAdminName)
    {
        if ($this->globalAdminName !== $globalAdminName) {
            $this->globalAdminName = isset($globalAdminName) ? $globalAdminName : '';
        }
    }
    
    /**
     * Returns the global admin password.
     *
     * @return string
     */
    public function getGlobalAdminPassword()
    {
        return $this->globalAdminPassword;
    }
    
    /**
     * Sets the global admin password.
     *
     * @param string $globalAdminPassword
     *
     * @return void
     */
    public function setGlobalAdminPassword($globalAdminPassword)
    {
        if ($this->globalAdminPassword !== $globalAdminPassword) {
            $this->globalAdminPassword = isset($globalAdminPassword) ? $globalAdminPassword : '';
        }
    }
    
    /**
     * Returns the global admin email.
     *
     * @return string
     */
    public function getGlobalAdminEmail()
    {
        return $this->globalAdminEmail;
    }
    
    /**
     * Sets the global admin email.
     *
     * @param string $globalAdminEmail
     *
     * @return void
     */
    public function setGlobalAdminEmail($globalAdminEmail)
    {
        if ($this->globalAdminEmail !== $globalAdminEmail) {
            $this->globalAdminEmail = isset($globalAdminEmail) ? $globalAdminEmail : '';
        }
    }
    
    /**
     * Returns the site entries per page.
     *
     * @return integer
     */
    public function getSiteEntriesPerPage()
    {
        return $this->siteEntriesPerPage;
    }
    
    /**
     * Sets the site entries per page.
     *
     * @param integer $siteEntriesPerPage
     *
     * @return void
     */
    public function setSiteEntriesPerPage($siteEntriesPerPage)
    {
        if (intval($this->siteEntriesPerPage) !== intval($siteEntriesPerPage)) {
            $this->siteEntriesPerPage = intval($siteEntriesPerPage);
        }
    }
    
    /**
     * Returns the template entries per page.
     *
     * @return integer
     */
    public function getTemplateEntriesPerPage()
    {
        return $this->templateEntriesPerPage;
    }
    
    /**
     * Sets the template entries per page.
     *
     * @param integer $templateEntriesPerPage
     *
     * @return void
     */
    public function setTemplateEntriesPerPage($templateEntriesPerPage)
    {
        if (intval($this->templateEntriesPerPage) !== intval($templateEntriesPerPage)) {
            $this->templateEntriesPerPage = intval($templateEntriesPerPage);
        }
    }
    
    /**
     * Returns the project entries per page.
     *
     * @return integer
     */
    public function getProjectEntriesPerPage()
    {
        return $this->projectEntriesPerPage;
    }
    
    /**
     * Sets the project entries per page.
     *
     * @param integer $projectEntriesPerPage
     *
     * @return void
     */
    public function setProjectEntriesPerPage($projectEntriesPerPage)
    {
        if (intval($this->projectEntriesPerPage) !== intval($projectEntriesPerPage)) {
            $this->projectEntriesPerPage = intval($projectEntriesPerPage);
        }
    }
    
    /**
     * Returns the show only own entries.
     *
     * @return boolean
     */
    public function getShowOnlyOwnEntries()
    {
        return $this->showOnlyOwnEntries;
    }
    
    /**
     * Sets the show only own entries.
     *
     * @param boolean $showOnlyOwnEntries
     *
     * @return void
     */
    public function setShowOnlyOwnEntries($showOnlyOwnEntries)
    {
        if (boolval($this->showOnlyOwnEntries) !== boolval($showOnlyOwnEntries)) {
            $this->showOnlyOwnEntries = boolval($showOnlyOwnEntries);
        }
    }
    
    /**
     * Returns the enable shrinking for site logo.
     *
     * @return boolean
     */
    public function getEnableShrinkingForSiteLogo()
    {
        return $this->enableShrinkingForSiteLogo;
    }
    
    /**
     * Sets the enable shrinking for site logo.
     *
     * @param boolean $enableShrinkingForSiteLogo
     *
     * @return void
     */
    public function setEnableShrinkingForSiteLogo($enableShrinkingForSiteLogo)
    {
        if (boolval($this->enableShrinkingForSiteLogo) !== boolval($enableShrinkingForSiteLogo)) {
            $this->enableShrinkingForSiteLogo = boolval($enableShrinkingForSiteLogo);
        }
    }
    
    /**
     * Returns the shrink width site logo.
     *
     * @return integer
     */
    public function getShrinkWidthSiteLogo()
    {
        return $this->shrinkWidthSiteLogo;
    }
    
    /**
     * Sets the shrink width site logo.
     *
     * @param integer $shrinkWidthSiteLogo
     *
     * @return void
     */
    public function setShrinkWidthSiteLogo($shrinkWidthSiteLogo)
    {
        if (intval($this->shrinkWidthSiteLogo) !== intval($shrinkWidthSiteLogo)) {
            $this->shrinkWidthSiteLogo = intval($shrinkWidthSiteLogo);
        }
    }
    
    /**
     * Returns the shrink height site logo.
     *
     * @return integer
     */
    public function getShrinkHeightSiteLogo()
    {
        return $this->shrinkHeightSiteLogo;
    }
    
    /**
     * Sets the shrink height site logo.
     *
     * @param integer $shrinkHeightSiteLogo
     *
     * @return void
     */
    public function setShrinkHeightSiteLogo($shrinkHeightSiteLogo)
    {
        if (intval($this->shrinkHeightSiteLogo) !== intval($shrinkHeightSiteLogo)) {
            $this->shrinkHeightSiteLogo = intval($shrinkHeightSiteLogo);
        }
    }
    
    /**
     * Returns the thumbnail mode site logo.
     *
     * @return string
     */
    public function getThumbnailModeSiteLogo()
    {
        return $this->thumbnailModeSiteLogo;
    }
    
    /**
     * Sets the thumbnail mode site logo.
     *
     * @param string $thumbnailModeSiteLogo
     *
     * @return void
     */
    public function setThumbnailModeSiteLogo($thumbnailModeSiteLogo)
    {
        if ($this->thumbnailModeSiteLogo !== $thumbnailModeSiteLogo) {
            $this->thumbnailModeSiteLogo = isset($thumbnailModeSiteLogo) ? $thumbnailModeSiteLogo : '';
        }
    }
    
    /**
     * Returns the thumbnail width site logo view.
     *
     * @return integer
     */
    public function getThumbnailWidthSiteLogoView()
    {
        return $this->thumbnailWidthSiteLogoView;
    }
    
    /**
     * Sets the thumbnail width site logo view.
     *
     * @param integer $thumbnailWidthSiteLogoView
     *
     * @return void
     */
    public function setThumbnailWidthSiteLogoView($thumbnailWidthSiteLogoView)
    {
        if (intval($this->thumbnailWidthSiteLogoView) !== intval($thumbnailWidthSiteLogoView)) {
            $this->thumbnailWidthSiteLogoView = intval($thumbnailWidthSiteLogoView);
        }
    }
    
    /**
     * Returns the thumbnail height site logo view.
     *
     * @return integer
     */
    public function getThumbnailHeightSiteLogoView()
    {
        return $this->thumbnailHeightSiteLogoView;
    }
    
    /**
     * Sets the thumbnail height site logo view.
     *
     * @param integer $thumbnailHeightSiteLogoView
     *
     * @return void
     */
    public function setThumbnailHeightSiteLogoView($thumbnailHeightSiteLogoView)
    {
        if (intval($this->thumbnailHeightSiteLogoView) !== intval($thumbnailHeightSiteLogoView)) {
            $this->thumbnailHeightSiteLogoView = intval($thumbnailHeightSiteLogoView);
        }
    }
    
    /**
     * Returns the thumbnail width site logo edit.
     *
     * @return integer
     */
    public function getThumbnailWidthSiteLogoEdit()
    {
        return $this->thumbnailWidthSiteLogoEdit;
    }
    
    /**
     * Sets the thumbnail width site logo edit.
     *
     * @param integer $thumbnailWidthSiteLogoEdit
     *
     * @return void
     */
    public function setThumbnailWidthSiteLogoEdit($thumbnailWidthSiteLogoEdit)
    {
        if (intval($this->thumbnailWidthSiteLogoEdit) !== intval($thumbnailWidthSiteLogoEdit)) {
            $this->thumbnailWidthSiteLogoEdit = intval($thumbnailWidthSiteLogoEdit);
        }
    }
    
    /**
     * Returns the thumbnail height site logo edit.
     *
     * @return integer
     */
    public function getThumbnailHeightSiteLogoEdit()
    {
        return $this->thumbnailHeightSiteLogoEdit;
    }
    
    /**
     * Sets the thumbnail height site logo edit.
     *
     * @param integer $thumbnailHeightSiteLogoEdit
     *
     * @return void
     */
    public function setThumbnailHeightSiteLogoEdit($thumbnailHeightSiteLogoEdit)
    {
        if (intval($this->thumbnailHeightSiteLogoEdit) !== intval($thumbnailHeightSiteLogoEdit)) {
            $this->thumbnailHeightSiteLogoEdit = intval($thumbnailHeightSiteLogoEdit);
        }
    }
    
    /**
     * Returns the enable shrinking for site fav icon.
     *
     * @return boolean
     */
    public function getEnableShrinkingForSiteFavIcon()
    {
        return $this->enableShrinkingForSiteFavIcon;
    }
    
    /**
     * Sets the enable shrinking for site fav icon.
     *
     * @param boolean $enableShrinkingForSiteFavIcon
     *
     * @return void
     */
    public function setEnableShrinkingForSiteFavIcon($enableShrinkingForSiteFavIcon)
    {
        if (boolval($this->enableShrinkingForSiteFavIcon) !== boolval($enableShrinkingForSiteFavIcon)) {
            $this->enableShrinkingForSiteFavIcon = boolval($enableShrinkingForSiteFavIcon);
        }
    }
    
    /**
     * Returns the shrink width site fav icon.
     *
     * @return integer
     */
    public function getShrinkWidthSiteFavIcon()
    {
        return $this->shrinkWidthSiteFavIcon;
    }
    
    /**
     * Sets the shrink width site fav icon.
     *
     * @param integer $shrinkWidthSiteFavIcon
     *
     * @return void
     */
    public function setShrinkWidthSiteFavIcon($shrinkWidthSiteFavIcon)
    {
        if (intval($this->shrinkWidthSiteFavIcon) !== intval($shrinkWidthSiteFavIcon)) {
            $this->shrinkWidthSiteFavIcon = intval($shrinkWidthSiteFavIcon);
        }
    }
    
    /**
     * Returns the shrink height site fav icon.
     *
     * @return integer
     */
    public function getShrinkHeightSiteFavIcon()
    {
        return $this->shrinkHeightSiteFavIcon;
    }
    
    /**
     * Sets the shrink height site fav icon.
     *
     * @param integer $shrinkHeightSiteFavIcon
     *
     * @return void
     */
    public function setShrinkHeightSiteFavIcon($shrinkHeightSiteFavIcon)
    {
        if (intval($this->shrinkHeightSiteFavIcon) !== intval($shrinkHeightSiteFavIcon)) {
            $this->shrinkHeightSiteFavIcon = intval($shrinkHeightSiteFavIcon);
        }
    }
    
    /**
     * Returns the thumbnail mode site fav icon.
     *
     * @return string
     */
    public function getThumbnailModeSiteFavIcon()
    {
        return $this->thumbnailModeSiteFavIcon;
    }
    
    /**
     * Sets the thumbnail mode site fav icon.
     *
     * @param string $thumbnailModeSiteFavIcon
     *
     * @return void
     */
    public function setThumbnailModeSiteFavIcon($thumbnailModeSiteFavIcon)
    {
        if ($this->thumbnailModeSiteFavIcon !== $thumbnailModeSiteFavIcon) {
            $this->thumbnailModeSiteFavIcon = isset($thumbnailModeSiteFavIcon) ? $thumbnailModeSiteFavIcon : '';
        }
    }
    
    /**
     * Returns the thumbnail width site fav icon view.
     *
     * @return integer
     */
    public function getThumbnailWidthSiteFavIconView()
    {
        return $this->thumbnailWidthSiteFavIconView;
    }
    
    /**
     * Sets the thumbnail width site fav icon view.
     *
     * @param integer $thumbnailWidthSiteFavIconView
     *
     * @return void
     */
    public function setThumbnailWidthSiteFavIconView($thumbnailWidthSiteFavIconView)
    {
        if (intval($this->thumbnailWidthSiteFavIconView) !== intval($thumbnailWidthSiteFavIconView)) {
            $this->thumbnailWidthSiteFavIconView = intval($thumbnailWidthSiteFavIconView);
        }
    }
    
    /**
     * Returns the thumbnail height site fav icon view.
     *
     * @return integer
     */
    public function getThumbnailHeightSiteFavIconView()
    {
        return $this->thumbnailHeightSiteFavIconView;
    }
    
    /**
     * Sets the thumbnail height site fav icon view.
     *
     * @param integer $thumbnailHeightSiteFavIconView
     *
     * @return void
     */
    public function setThumbnailHeightSiteFavIconView($thumbnailHeightSiteFavIconView)
    {
        if (intval($this->thumbnailHeightSiteFavIconView) !== intval($thumbnailHeightSiteFavIconView)) {
            $this->thumbnailHeightSiteFavIconView = intval($thumbnailHeightSiteFavIconView);
        }
    }
    
    /**
     * Returns the thumbnail width site fav icon edit.
     *
     * @return integer
     */
    public function getThumbnailWidthSiteFavIconEdit()
    {
        return $this->thumbnailWidthSiteFavIconEdit;
    }
    
    /**
     * Sets the thumbnail width site fav icon edit.
     *
     * @param integer $thumbnailWidthSiteFavIconEdit
     *
     * @return void
     */
    public function setThumbnailWidthSiteFavIconEdit($thumbnailWidthSiteFavIconEdit)
    {
        if (intval($this->thumbnailWidthSiteFavIconEdit) !== intval($thumbnailWidthSiteFavIconEdit)) {
            $this->thumbnailWidthSiteFavIconEdit = intval($thumbnailWidthSiteFavIconEdit);
        }
    }
    
    /**
     * Returns the thumbnail height site fav icon edit.
     *
     * @return integer
     */
    public function getThumbnailHeightSiteFavIconEdit()
    {
        return $this->thumbnailHeightSiteFavIconEdit;
    }
    
    /**
     * Sets the thumbnail height site fav icon edit.
     *
     * @param integer $thumbnailHeightSiteFavIconEdit
     *
     * @return void
     */
    public function setThumbnailHeightSiteFavIconEdit($thumbnailHeightSiteFavIconEdit)
    {
        if (intval($this->thumbnailHeightSiteFavIconEdit) !== intval($thumbnailHeightSiteFavIconEdit)) {
            $this->thumbnailHeightSiteFavIconEdit = intval($thumbnailHeightSiteFavIconEdit);
        }
    }
    
    
    /**
     * Loads module variables from the database.
     */
    protected function load()
    {
        $moduleVars = $this->variableApi->getAll('ZikulaMultisitesModule');
    
        if (isset($moduleVars['globalAdminName'])) {
            $this->setGlobalAdminName($moduleVars['globalAdminName']);
        }
        if (isset($moduleVars['globalAdminPassword'])) {
            $this->setGlobalAdminPassword($moduleVars['globalAdminPassword']);
        }
        if (isset($moduleVars['globalAdminEmail'])) {
            $this->setGlobalAdminEmail($moduleVars['globalAdminEmail']);
        }
        if (isset($moduleVars['siteEntriesPerPage'])) {
            $this->setSiteEntriesPerPage($moduleVars['siteEntriesPerPage']);
        }
        if (isset($moduleVars['templateEntriesPerPage'])) {
            $this->setTemplateEntriesPerPage($moduleVars['templateEntriesPerPage']);
        }
        if (isset($moduleVars['projectEntriesPerPage'])) {
            $this->setProjectEntriesPerPage($moduleVars['projectEntriesPerPage']);
        }
        if (isset($moduleVars['showOnlyOwnEntries'])) {
            $this->setShowOnlyOwnEntries($moduleVars['showOnlyOwnEntries']);
        }
        if (isset($moduleVars['enableShrinkingForSiteLogo'])) {
            $this->setEnableShrinkingForSiteLogo($moduleVars['enableShrinkingForSiteLogo']);
        }
        if (isset($moduleVars['shrinkWidthSiteLogo'])) {
            $this->setShrinkWidthSiteLogo($moduleVars['shrinkWidthSiteLogo']);
        }
        if (isset($moduleVars['shrinkHeightSiteLogo'])) {
            $this->setShrinkHeightSiteLogo($moduleVars['shrinkHeightSiteLogo']);
        }
        if (isset($moduleVars['thumbnailModeSiteLogo'])) {
            $this->setThumbnailModeSiteLogo($moduleVars['thumbnailModeSiteLogo']);
        }
        if (isset($moduleVars['thumbnailWidthSiteLogoView'])) {
            $this->setThumbnailWidthSiteLogoView($moduleVars['thumbnailWidthSiteLogoView']);
        }
        if (isset($moduleVars['thumbnailHeightSiteLogoView'])) {
            $this->setThumbnailHeightSiteLogoView($moduleVars['thumbnailHeightSiteLogoView']);
        }
        if (isset($moduleVars['thumbnailWidthSiteLogoEdit'])) {
            $this->setThumbnailWidthSiteLogoEdit($moduleVars['thumbnailWidthSiteLogoEdit']);
        }
        if (isset($moduleVars['thumbnailHeightSiteLogoEdit'])) {
            $this->setThumbnailHeightSiteLogoEdit($moduleVars['thumbnailHeightSiteLogoEdit']);
        }
        if (isset($moduleVars['enableShrinkingForSiteFavIcon'])) {
            $this->setEnableShrinkingForSiteFavIcon($moduleVars['enableShrinkingForSiteFavIcon']);
        }
        if (isset($moduleVars['shrinkWidthSiteFavIcon'])) {
            $this->setShrinkWidthSiteFavIcon($moduleVars['shrinkWidthSiteFavIcon']);
        }
        if (isset($moduleVars['shrinkHeightSiteFavIcon'])) {
            $this->setShrinkHeightSiteFavIcon($moduleVars['shrinkHeightSiteFavIcon']);
        }
        if (isset($moduleVars['thumbnailModeSiteFavIcon'])) {
            $this->setThumbnailModeSiteFavIcon($moduleVars['thumbnailModeSiteFavIcon']);
        }
        if (isset($moduleVars['thumbnailWidthSiteFavIconView'])) {
            $this->setThumbnailWidthSiteFavIconView($moduleVars['thumbnailWidthSiteFavIconView']);
        }
        if (isset($moduleVars['thumbnailHeightSiteFavIconView'])) {
            $this->setThumbnailHeightSiteFavIconView($moduleVars['thumbnailHeightSiteFavIconView']);
        }
        if (isset($moduleVars['thumbnailWidthSiteFavIconEdit'])) {
            $this->setThumbnailWidthSiteFavIconEdit($moduleVars['thumbnailWidthSiteFavIconEdit']);
        }
        if (isset($moduleVars['thumbnailHeightSiteFavIconEdit'])) {
            $this->setThumbnailHeightSiteFavIconEdit($moduleVars['thumbnailHeightSiteFavIconEdit']);
        }
    }
    
    /**
     * Saves module variables into the database.
     */
    public function save()
    {
        $this->variableApi->set('ZikulaMultisitesModule', 'globalAdminName', $this->getGlobalAdminName());
        $this->variableApi->set('ZikulaMultisitesModule', 'globalAdminPassword', $this->getGlobalAdminPassword());
        $this->variableApi->set('ZikulaMultisitesModule', 'globalAdminEmail', $this->getGlobalAdminEmail());
        $this->variableApi->set('ZikulaMultisitesModule', 'siteEntriesPerPage', $this->getSiteEntriesPerPage());
        $this->variableApi->set('ZikulaMultisitesModule', 'templateEntriesPerPage', $this->getTemplateEntriesPerPage());
        $this->variableApi->set('ZikulaMultisitesModule', 'projectEntriesPerPage', $this->getProjectEntriesPerPage());
        $this->variableApi->set('ZikulaMultisitesModule', 'showOnlyOwnEntries', $this->getShowOnlyOwnEntries());
        $this->variableApi->set('ZikulaMultisitesModule', 'enableShrinkingForSiteLogo', $this->getEnableShrinkingForSiteLogo());
        $this->variableApi->set('ZikulaMultisitesModule', 'shrinkWidthSiteLogo', $this->getShrinkWidthSiteLogo());
        $this->variableApi->set('ZikulaMultisitesModule', 'shrinkHeightSiteLogo', $this->getShrinkHeightSiteLogo());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailModeSiteLogo', $this->getThumbnailModeSiteLogo());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailWidthSiteLogoView', $this->getThumbnailWidthSiteLogoView());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailHeightSiteLogoView', $this->getThumbnailHeightSiteLogoView());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailWidthSiteLogoEdit', $this->getThumbnailWidthSiteLogoEdit());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailHeightSiteLogoEdit', $this->getThumbnailHeightSiteLogoEdit());
        $this->variableApi->set('ZikulaMultisitesModule', 'enableShrinkingForSiteFavIcon', $this->getEnableShrinkingForSiteFavIcon());
        $this->variableApi->set('ZikulaMultisitesModule', 'shrinkWidthSiteFavIcon', $this->getShrinkWidthSiteFavIcon());
        $this->variableApi->set('ZikulaMultisitesModule', 'shrinkHeightSiteFavIcon', $this->getShrinkHeightSiteFavIcon());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailModeSiteFavIcon', $this->getThumbnailModeSiteFavIcon());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailWidthSiteFavIconView', $this->getThumbnailWidthSiteFavIconView());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailHeightSiteFavIconView', $this->getThumbnailHeightSiteFavIconView());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailWidthSiteFavIconEdit', $this->getThumbnailWidthSiteFavIconEdit());
        $this->variableApi->set('ZikulaMultisitesModule', 'thumbnailHeightSiteFavIconEdit', $this->getThumbnailHeightSiteFavIconEdit());
    }
}
