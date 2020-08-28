<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 *
 * @see https://modulestudio.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
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
     * @Assert\NotBlank
     * @Assert\Length(min="0", max="255")
     *
     * @var string
     */
    protected $globalAdminName = '';
    
    /**
     * @Assert\NotBlank
     * @Assert\Length(min="0", max="255")
     *
     * @var string
     */
    protected $globalAdminPassword = '';
    
    /**
     * @Assert\NotBlank
     * @Assert\Length(min="0", max="255")
     * @Assert\Email(checkMX=false, checkHost=false)
     *
     * @var string
     */
    protected $globalAdminEmail = '';
    
    /**
     * The amount of sites shown per page.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $siteEntriesPerPage = 10;
    
    /**
     * The amount of templates shown per page.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $templateEntriesPerPage = 10;
    
    /**
     * The amount of projects shown per page.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $projectEntriesPerPage = 10;
    
    /**
     * Whether only own entries should be shown on view pages by default or not.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $enableShrinkingForSiteLogo = false;
    
    /**
     * The maximum image width in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $shrinkWidthSiteLogo = 800;
    
    /**
     * The maximum image height in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $shrinkHeightSiteLogo = 600;
    
    /**
     * Thumbnail mode (inset or outbound).
     *
     * @Assert\NotBlank
     * @MultisitesAssert\ListEntry(entityName="appSettings", propertyName="thumbnailModeSiteLogo", multiple=false)
     *
     * @var string
     */
    protected $thumbnailModeSiteLogo = 'inset';
    
    /**
     * Thumbnail width on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailWidthSiteLogoView = 32;
    
    /**
     * Thumbnail height on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailHeightSiteLogoView = 24;
    
    /**
     * Thumbnail width on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailWidthSiteLogoEdit = 240;
    
    /**
     * Thumbnail height on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailHeightSiteLogoEdit = 180;
    
    /**
     * Whether to enable shrinking huge images to maximum dimensions. Stores downscaled version of the original image.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $enableShrinkingForSiteFavIcon = false;
    
    /**
     * The maximum image width in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $shrinkWidthSiteFavIcon = 800;
    
    /**
     * The maximum image height in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $shrinkHeightSiteFavIcon = 600;
    
    /**
     * Thumbnail mode (inset or outbound).
     *
     * @Assert\NotBlank
     * @MultisitesAssert\ListEntry(entityName="appSettings", propertyName="thumbnailModeSiteFavIcon", multiple=false)
     *
     * @var string
     */
    protected $thumbnailModeSiteFavIcon = 'inset';
    
    /**
     * Thumbnail width on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailWidthSiteFavIconView = 32;
    
    /**
     * Thumbnail height on view pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailHeightSiteFavIconView = 24;
    
    /**
     * Thumbnail width on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailWidthSiteFavIconEdit = 240;
    
    /**
     * Thumbnail height on edit pages in pixels.
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     *
     * @var int
     */
    protected $thumbnailHeightSiteFavIconEdit = 180;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $allowModerationSpecificCreatorForSite = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $allowModerationSpecificCreationDateForSite = false;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $allowModerationSpecificCreatorForTemplate = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $allowModerationSpecificCreationDateForTemplate = false;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $allowModerationSpecificCreatorForProject = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     *
     * @var bool
     */
    protected $allowModerationSpecificCreationDateForProject = false;
    
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
     * @return int
     */
    public function getSiteEntriesPerPage()
    {
        return $this->siteEntriesPerPage;
    }
    
    /**
     * Sets the site entries per page.
     *
     * @param int $siteEntriesPerPage
     *
     * @return void
     */
    public function setSiteEntriesPerPage($siteEntriesPerPage)
    {
        if ((int) $this->siteEntriesPerPage !== (int) $siteEntriesPerPage) {
            $this->siteEntriesPerPage = (int) $siteEntriesPerPage;
        }
    }
    
    /**
     * Returns the template entries per page.
     *
     * @return int
     */
    public function getTemplateEntriesPerPage()
    {
        return $this->templateEntriesPerPage;
    }
    
    /**
     * Sets the template entries per page.
     *
     * @param int $templateEntriesPerPage
     *
     * @return void
     */
    public function setTemplateEntriesPerPage($templateEntriesPerPage)
    {
        if ((int) $this->templateEntriesPerPage !== (int) $templateEntriesPerPage) {
            $this->templateEntriesPerPage = (int) $templateEntriesPerPage;
        }
    }
    
    /**
     * Returns the project entries per page.
     *
     * @return int
     */
    public function getProjectEntriesPerPage()
    {
        return $this->projectEntriesPerPage;
    }
    
    /**
     * Sets the project entries per page.
     *
     * @param int $projectEntriesPerPage
     *
     * @return void
     */
    public function setProjectEntriesPerPage($projectEntriesPerPage)
    {
        if ((int) $this->projectEntriesPerPage !== (int) $projectEntriesPerPage) {
            $this->projectEntriesPerPage = (int) $projectEntriesPerPage;
        }
    }
    
    /**
     * Returns the show only own entries.
     *
     * @return bool
     */
    public function getShowOnlyOwnEntries()
    {
        return $this->showOnlyOwnEntries;
    }
    
    /**
     * Sets the show only own entries.
     *
     * @param bool $showOnlyOwnEntries
     *
     * @return void
     */
    public function setShowOnlyOwnEntries($showOnlyOwnEntries)
    {
        if ((bool) $this->showOnlyOwnEntries !== (bool) $showOnlyOwnEntries) {
            $this->showOnlyOwnEntries = (bool) $showOnlyOwnEntries;
        }
    }
    
    /**
     * Returns the enable shrinking for site logo.
     *
     * @return bool
     */
    public function getEnableShrinkingForSiteLogo()
    {
        return $this->enableShrinkingForSiteLogo;
    }
    
    /**
     * Sets the enable shrinking for site logo.
     *
     * @param bool $enableShrinkingForSiteLogo
     *
     * @return void
     */
    public function setEnableShrinkingForSiteLogo($enableShrinkingForSiteLogo)
    {
        if ((bool) $this->enableShrinkingForSiteLogo !== (bool) $enableShrinkingForSiteLogo) {
            $this->enableShrinkingForSiteLogo = (bool) $enableShrinkingForSiteLogo;
        }
    }
    
    /**
     * Returns the shrink width site logo.
     *
     * @return int
     */
    public function getShrinkWidthSiteLogo()
    {
        return $this->shrinkWidthSiteLogo;
    }
    
    /**
     * Sets the shrink width site logo.
     *
     * @param int $shrinkWidthSiteLogo
     *
     * @return void
     */
    public function setShrinkWidthSiteLogo($shrinkWidthSiteLogo)
    {
        if ((int) $this->shrinkWidthSiteLogo !== (int) $shrinkWidthSiteLogo) {
            $this->shrinkWidthSiteLogo = (int) $shrinkWidthSiteLogo;
        }
    }
    
    /**
     * Returns the shrink height site logo.
     *
     * @return int
     */
    public function getShrinkHeightSiteLogo()
    {
        return $this->shrinkHeightSiteLogo;
    }
    
    /**
     * Sets the shrink height site logo.
     *
     * @param int $shrinkHeightSiteLogo
     *
     * @return void
     */
    public function setShrinkHeightSiteLogo($shrinkHeightSiteLogo)
    {
        if ((int) $this->shrinkHeightSiteLogo !== (int) $shrinkHeightSiteLogo) {
            $this->shrinkHeightSiteLogo = (int) $shrinkHeightSiteLogo;
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
     * @return int
     */
    public function getThumbnailWidthSiteLogoView()
    {
        return $this->thumbnailWidthSiteLogoView;
    }
    
    /**
     * Sets the thumbnail width site logo view.
     *
     * @param int $thumbnailWidthSiteLogoView
     *
     * @return void
     */
    public function setThumbnailWidthSiteLogoView($thumbnailWidthSiteLogoView)
    {
        if ((int) $this->thumbnailWidthSiteLogoView !== (int) $thumbnailWidthSiteLogoView) {
            $this->thumbnailWidthSiteLogoView = (int) $thumbnailWidthSiteLogoView;
        }
    }
    
    /**
     * Returns the thumbnail height site logo view.
     *
     * @return int
     */
    public function getThumbnailHeightSiteLogoView()
    {
        return $this->thumbnailHeightSiteLogoView;
    }
    
    /**
     * Sets the thumbnail height site logo view.
     *
     * @param int $thumbnailHeightSiteLogoView
     *
     * @return void
     */
    public function setThumbnailHeightSiteLogoView($thumbnailHeightSiteLogoView)
    {
        if ((int) $this->thumbnailHeightSiteLogoView !== (int) $thumbnailHeightSiteLogoView) {
            $this->thumbnailHeightSiteLogoView = (int) $thumbnailHeightSiteLogoView;
        }
    }
    
    /**
     * Returns the thumbnail width site logo edit.
     *
     * @return int
     */
    public function getThumbnailWidthSiteLogoEdit()
    {
        return $this->thumbnailWidthSiteLogoEdit;
    }
    
    /**
     * Sets the thumbnail width site logo edit.
     *
     * @param int $thumbnailWidthSiteLogoEdit
     *
     * @return void
     */
    public function setThumbnailWidthSiteLogoEdit($thumbnailWidthSiteLogoEdit)
    {
        if ((int) $this->thumbnailWidthSiteLogoEdit !== (int) $thumbnailWidthSiteLogoEdit) {
            $this->thumbnailWidthSiteLogoEdit = (int) $thumbnailWidthSiteLogoEdit;
        }
    }
    
    /**
     * Returns the thumbnail height site logo edit.
     *
     * @return int
     */
    public function getThumbnailHeightSiteLogoEdit()
    {
        return $this->thumbnailHeightSiteLogoEdit;
    }
    
    /**
     * Sets the thumbnail height site logo edit.
     *
     * @param int $thumbnailHeightSiteLogoEdit
     *
     * @return void
     */
    public function setThumbnailHeightSiteLogoEdit($thumbnailHeightSiteLogoEdit)
    {
        if ((int) $this->thumbnailHeightSiteLogoEdit !== (int) $thumbnailHeightSiteLogoEdit) {
            $this->thumbnailHeightSiteLogoEdit = (int) $thumbnailHeightSiteLogoEdit;
        }
    }
    
    /**
     * Returns the enable shrinking for site fav icon.
     *
     * @return bool
     */
    public function getEnableShrinkingForSiteFavIcon()
    {
        return $this->enableShrinkingForSiteFavIcon;
    }
    
    /**
     * Sets the enable shrinking for site fav icon.
     *
     * @param bool $enableShrinkingForSiteFavIcon
     *
     * @return void
     */
    public function setEnableShrinkingForSiteFavIcon($enableShrinkingForSiteFavIcon)
    {
        if ((bool) $this->enableShrinkingForSiteFavIcon !== (bool) $enableShrinkingForSiteFavIcon) {
            $this->enableShrinkingForSiteFavIcon = (bool) $enableShrinkingForSiteFavIcon;
        }
    }
    
    /**
     * Returns the shrink width site fav icon.
     *
     * @return int
     */
    public function getShrinkWidthSiteFavIcon()
    {
        return $this->shrinkWidthSiteFavIcon;
    }
    
    /**
     * Sets the shrink width site fav icon.
     *
     * @param int $shrinkWidthSiteFavIcon
     *
     * @return void
     */
    public function setShrinkWidthSiteFavIcon($shrinkWidthSiteFavIcon)
    {
        if ((int) $this->shrinkWidthSiteFavIcon !== (int) $shrinkWidthSiteFavIcon) {
            $this->shrinkWidthSiteFavIcon = (int) $shrinkWidthSiteFavIcon;
        }
    }
    
    /**
     * Returns the shrink height site fav icon.
     *
     * @return int
     */
    public function getShrinkHeightSiteFavIcon()
    {
        return $this->shrinkHeightSiteFavIcon;
    }
    
    /**
     * Sets the shrink height site fav icon.
     *
     * @param int $shrinkHeightSiteFavIcon
     *
     * @return void
     */
    public function setShrinkHeightSiteFavIcon($shrinkHeightSiteFavIcon)
    {
        if ((int) $this->shrinkHeightSiteFavIcon !== (int) $shrinkHeightSiteFavIcon) {
            $this->shrinkHeightSiteFavIcon = (int) $shrinkHeightSiteFavIcon;
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
     * @return int
     */
    public function getThumbnailWidthSiteFavIconView()
    {
        return $this->thumbnailWidthSiteFavIconView;
    }
    
    /**
     * Sets the thumbnail width site fav icon view.
     *
     * @param int $thumbnailWidthSiteFavIconView
     *
     * @return void
     */
    public function setThumbnailWidthSiteFavIconView($thumbnailWidthSiteFavIconView)
    {
        if ((int) $this->thumbnailWidthSiteFavIconView !== (int) $thumbnailWidthSiteFavIconView) {
            $this->thumbnailWidthSiteFavIconView = (int) $thumbnailWidthSiteFavIconView;
        }
    }
    
    /**
     * Returns the thumbnail height site fav icon view.
     *
     * @return int
     */
    public function getThumbnailHeightSiteFavIconView()
    {
        return $this->thumbnailHeightSiteFavIconView;
    }
    
    /**
     * Sets the thumbnail height site fav icon view.
     *
     * @param int $thumbnailHeightSiteFavIconView
     *
     * @return void
     */
    public function setThumbnailHeightSiteFavIconView($thumbnailHeightSiteFavIconView)
    {
        if ((int) $this->thumbnailHeightSiteFavIconView !== (int) $thumbnailHeightSiteFavIconView) {
            $this->thumbnailHeightSiteFavIconView = (int) $thumbnailHeightSiteFavIconView;
        }
    }
    
    /**
     * Returns the thumbnail width site fav icon edit.
     *
     * @return int
     */
    public function getThumbnailWidthSiteFavIconEdit()
    {
        return $this->thumbnailWidthSiteFavIconEdit;
    }
    
    /**
     * Sets the thumbnail width site fav icon edit.
     *
     * @param int $thumbnailWidthSiteFavIconEdit
     *
     * @return void
     */
    public function setThumbnailWidthSiteFavIconEdit($thumbnailWidthSiteFavIconEdit)
    {
        if ((int) $this->thumbnailWidthSiteFavIconEdit !== (int) $thumbnailWidthSiteFavIconEdit) {
            $this->thumbnailWidthSiteFavIconEdit = (int) $thumbnailWidthSiteFavIconEdit;
        }
    }
    
    /**
     * Returns the thumbnail height site fav icon edit.
     *
     * @return int
     */
    public function getThumbnailHeightSiteFavIconEdit()
    {
        return $this->thumbnailHeightSiteFavIconEdit;
    }
    
    /**
     * Sets the thumbnail height site fav icon edit.
     *
     * @param int $thumbnailHeightSiteFavIconEdit
     *
     * @return void
     */
    public function setThumbnailHeightSiteFavIconEdit($thumbnailHeightSiteFavIconEdit)
    {
        if ((int) $this->thumbnailHeightSiteFavIconEdit !== (int) $thumbnailHeightSiteFavIconEdit) {
            $this->thumbnailHeightSiteFavIconEdit = (int) $thumbnailHeightSiteFavIconEdit;
        }
    }
    
    /**
     * Returns the allow moderation specific creator for site.
     *
     * @return bool
     */
    public function getAllowModerationSpecificCreatorForSite()
    {
        return $this->allowModerationSpecificCreatorForSite;
    }
    
    /**
     * Sets the allow moderation specific creator for site.
     *
     * @param bool $allowModerationSpecificCreatorForSite
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForSite($allowModerationSpecificCreatorForSite)
    {
        if ((bool) $this->allowModerationSpecificCreatorForSite !== (bool) $allowModerationSpecificCreatorForSite) {
            $this->allowModerationSpecificCreatorForSite = (bool) $allowModerationSpecificCreatorForSite;
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for site.
     *
     * @return bool
     */
    public function getAllowModerationSpecificCreationDateForSite()
    {
        return $this->allowModerationSpecificCreationDateForSite;
    }
    
    /**
     * Sets the allow moderation specific creation date for site.
     *
     * @param bool $allowModerationSpecificCreationDateForSite
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForSite($allowModerationSpecificCreationDateForSite)
    {
        if ((bool) $this->allowModerationSpecificCreationDateForSite !== (bool) $allowModerationSpecificCreationDateForSite) {
            $this->allowModerationSpecificCreationDateForSite = (bool) $allowModerationSpecificCreationDateForSite;
        }
    }
    
    /**
     * Returns the allow moderation specific creator for template.
     *
     * @return bool
     */
    public function getAllowModerationSpecificCreatorForTemplate()
    {
        return $this->allowModerationSpecificCreatorForTemplate;
    }
    
    /**
     * Sets the allow moderation specific creator for template.
     *
     * @param bool $allowModerationSpecificCreatorForTemplate
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForTemplate($allowModerationSpecificCreatorForTemplate)
    {
        if ((bool) $this->allowModerationSpecificCreatorForTemplate !== (bool) $allowModerationSpecificCreatorForTemplate) {
            $this->allowModerationSpecificCreatorForTemplate = (bool) $allowModerationSpecificCreatorForTemplate;
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for template.
     *
     * @return bool
     */
    public function getAllowModerationSpecificCreationDateForTemplate()
    {
        return $this->allowModerationSpecificCreationDateForTemplate;
    }
    
    /**
     * Sets the allow moderation specific creation date for template.
     *
     * @param bool $allowModerationSpecificCreationDateForTemplate
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForTemplate($allowModerationSpecificCreationDateForTemplate)
    {
        if ((bool) $this->allowModerationSpecificCreationDateForTemplate !== (bool) $allowModerationSpecificCreationDateForTemplate) {
            $this->allowModerationSpecificCreationDateForTemplate = (bool) $allowModerationSpecificCreationDateForTemplate;
        }
    }
    
    /**
     * Returns the allow moderation specific creator for project.
     *
     * @return bool
     */
    public function getAllowModerationSpecificCreatorForProject()
    {
        return $this->allowModerationSpecificCreatorForProject;
    }
    
    /**
     * Sets the allow moderation specific creator for project.
     *
     * @param bool $allowModerationSpecificCreatorForProject
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForProject($allowModerationSpecificCreatorForProject)
    {
        if ((bool) $this->allowModerationSpecificCreatorForProject !== (bool) $allowModerationSpecificCreatorForProject) {
            $this->allowModerationSpecificCreatorForProject = (bool) $allowModerationSpecificCreatorForProject;
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for project.
     *
     * @return bool
     */
    public function getAllowModerationSpecificCreationDateForProject()
    {
        return $this->allowModerationSpecificCreationDateForProject;
    }
    
    /**
     * Sets the allow moderation specific creation date for project.
     *
     * @param bool $allowModerationSpecificCreationDateForProject
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForProject($allowModerationSpecificCreationDateForProject)
    {
        if ((bool) $this->allowModerationSpecificCreationDateForProject !== (bool) $allowModerationSpecificCreationDateForProject) {
            $this->allowModerationSpecificCreationDateForProject = (bool) $allowModerationSpecificCreationDateForProject;
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
        if (isset($moduleVars['allowModerationSpecificCreatorForSite'])) {
            $this->setAllowModerationSpecificCreatorForSite($moduleVars['allowModerationSpecificCreatorForSite']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForSite'])) {
            $this->setAllowModerationSpecificCreationDateForSite($moduleVars['allowModerationSpecificCreationDateForSite']);
        }
        if (isset($moduleVars['allowModerationSpecificCreatorForTemplate'])) {
            $this->setAllowModerationSpecificCreatorForTemplate($moduleVars['allowModerationSpecificCreatorForTemplate']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForTemplate'])) {
            $this->setAllowModerationSpecificCreationDateForTemplate($moduleVars['allowModerationSpecificCreationDateForTemplate']);
        }
        if (isset($moduleVars['allowModerationSpecificCreatorForProject'])) {
            $this->setAllowModerationSpecificCreatorForProject($moduleVars['allowModerationSpecificCreatorForProject']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForProject'])) {
            $this->setAllowModerationSpecificCreationDateForProject($moduleVars['allowModerationSpecificCreationDateForProject']);
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
        $this->variableApi->set('ZikulaMultisitesModule', 'allowModerationSpecificCreatorForSite', $this->getAllowModerationSpecificCreatorForSite());
        $this->variableApi->set('ZikulaMultisitesModule', 'allowModerationSpecificCreationDateForSite', $this->getAllowModerationSpecificCreationDateForSite());
        $this->variableApi->set('ZikulaMultisitesModule', 'allowModerationSpecificCreatorForTemplate', $this->getAllowModerationSpecificCreatorForTemplate());
        $this->variableApi->set('ZikulaMultisitesModule', 'allowModerationSpecificCreationDateForTemplate', $this->getAllowModerationSpecificCreationDateForTemplate());
        $this->variableApi->set('ZikulaMultisitesModule', 'allowModerationSpecificCreatorForProject', $this->getAllowModerationSpecificCreatorForProject());
        $this->variableApi->set('ZikulaMultisitesModule', 'allowModerationSpecificCreationDateForProject', $this->getAllowModerationSpecificCreationDateForProject());
    }
}
