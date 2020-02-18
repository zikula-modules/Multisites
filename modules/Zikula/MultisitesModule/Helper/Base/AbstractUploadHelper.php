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

namespace Zikula\MultisitesModule\Helper\Base;

use Exception;
use Imagine\Filter\Basic\Autorotate;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\ExifMetadataReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;

/**
 * Helper base class for upload handling.
 */
abstract class AbstractUploadHelper
{
    use TranslatorTrait;
    
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var array
     */
    protected $moduleVars;
    
    /**
     * @var string
     */
    protected $dataDirectory;
    
    /**
     * @var array List of object types with upload fields
     */
    protected $allowedObjectTypes;
    
    /**
     * @var array List of file types to be considered as images
     */
    protected $imageFileTypes;
    
    /**
     * @var array List of dangerous file types to be rejected
     */
    protected $forbiddenFileTypes;
    
    public function __construct(
        TranslatorInterface $translator,
        Filesystem $filesystem,
        RequestStack $requestStack,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi,
        VariableApiInterface $variableApi,
        $dataDirectory
    ) {
        $this->setTranslator($translator);
        $this->filesystem = $filesystem;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->currentUserApi = $currentUserApi;
        $this->moduleVars = $variableApi->getAll('ZikulaMultisitesModule');
        $this->dataDirectory = $dataDirectory;
    
        $this->allowedObjectTypes = ['site', 'template'];
        $this->imageFileTypes = ['gif', 'jpeg', 'jpg', 'png'];
        $this->forbiddenFileTypes = [
            'cgi', 'pl', 'asp', 'phtml', 'php', 'php3', 'php4', 'php5',
            'exe', 'com', 'bat', 'jsp', 'cfm', 'shtml'
        ];
    }
    
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Process a file upload.
     *
     * @param string $objectType Currently treated entity type
     * @param UploadedFile $file The uploaded file
     * @param string $fieldName  Name of upload field
     *
     * @return array Resulting file name and collected meta data
     */
    public function performFileUpload($objectType, UploadedFile $file, $fieldName)
    {
        $result = [
            'fileName' => '',
            'metaData' => []
        ];
    
        // check whether uploads are allowed for the given object type
        if (!in_array($objectType, $this->allowedObjectTypes, true)) {
            return $result;
        }
    
        // perform validation
        if (!$this->validateFileUpload($objectType, $file, $fieldName)) {
            return $result;
        }
    
        // build the file name
        $fileName = $file->getClientOriginalName();
        $fileNameParts = explode('.', $fileName);
        $extension = $this->determineFileExtension($file);
        $fileNameParts[count($fileNameParts) - 1] = $extension;
        $fileName = implode('.', $fileNameParts);
    
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->hasSession() ? $request->getSession() : null;
        $flashBag = null !== $session ? $session->getFlashBag() : null;
    
        // retrieve the final file name
        try {
            $basePath = $this->getFileBaseFolder($objectType, $fieldName);
        } catch (Exception $exception) {
            if (null !== $flashBag) {
                $flashBag->add('error', $exception->getMessage());
            }
            $logArgs = [
                'app' => 'ZikulaMultisitesModule',
                'user' => $this->currentUserApi->get('uname'),
                'entity' => $objectType,
                'field' => $fieldName
            ];
            $this->logger->error(
                '{app}: User {user} could not detect upload destination path for entity {entity} and field {field}.'
                    . ' ' . $exception->getMessage(),
                $logArgs
            );
    
            return $result;
        }
        $fileName = $this->determineFileName($objectType, $fieldName, $basePath, $fileName, $extension);
    
        $destinationFilePath = $basePath . $fileName;
        $targetFile = $file->move($basePath, $fileName);
    
        // validate image file
        $isImage = in_array($extension, $this->imageFileTypes, true);
        if ($isImage) {
            $imgInfo = getimagesize($destinationFilePath);
            if (!is_array($imgInfo) || !$imgInfo[0] || !$imgInfo[1]) {
                if (null !== $flashBag) {
                    $flashBag->add('error', $this->__('Error! This file type seems not to be a valid image.'));
                }
                $this->logger->error(
                    '{app}: User {user} tried to upload a file which is seems not to be a valid image.',
                    ['app' => 'ZikulaMultisitesModule', 'user' => $this->currentUserApi->get('uname')]
                );
        
                return false;
            }
        }
    
        // collect data to return
        $result['fileName'] = $fileName;
        $result['metaData'] = $this->readMetaDataForFile($fileName, $destinationFilePath);
    
        $isImage = in_array($extension, $this->imageFileTypes, true);
        if ($isImage) {
            // fix wrong orientation and shrink too large image if needed
            @ini_set('memory_limit', '1G');
            $imagine = new Imagine();
            $image = $imagine->open($destinationFilePath);
            $autorotateFilter = new Autorotate();
            $image = $autorotateFilter->apply($image);
            $image->save($destinationFilePath);
    
            // check if shrinking functionality is enabled
            $fieldSuffix = ucfirst($objectType) . ucfirst($fieldName);
            if (
                isset($this->moduleVars['enableShrinkingFor' . $fieldSuffix])
                && true === (bool)$this->moduleVars['enableShrinkingFor' . $fieldSuffix]
            ) {
                // check for maximum size
                $maxWidth = isset($this->moduleVars['shrinkWidth' . $fieldSuffix])
                    ? $this->moduleVars['shrinkWidth' . $fieldSuffix]
                    : 800
                ;
                $maxHeight = isset($this->moduleVars['shrinkHeight' . $fieldSuffix])
                    ? $this->moduleVars['shrinkHeight' . $fieldSuffix]
                    : 600
                ;
                $thumbMode = isset($this->moduleVars['thumbnailMode' . $fieldSuffix])
                    ? $this->moduleVars['thumbnailMode' . $fieldSuffix]
                    : ImageInterface::THUMBNAIL_INSET
                ;
    
                $imgInfo = getimagesize($destinationFilePath);
                if ($imgInfo[0] > $maxWidth || $imgInfo[1] > $maxHeight) {
                    // resize to allowed maximum size
                    $imagine = new Imagine();
                    $image = $imagine->open($destinationFilePath);
                    $thumb = $image->thumbnail(new Box($maxWidth, $maxHeight), $thumbMode);
                    $thumb->save($destinationFilePath);
                }
            }
    
            // update meta data excluding EXIF
            $newMetaData = $this->readMetaDataForFile($fileName, $destinationFilePath, false);
            $result['metaData'] = array_merge($result['metaData'], $newMetaData);
        }
    
        return $result;
    }
    
    /**
     * Check if an upload file meets all validation criteria.
     *
     * @param string $objectType Currently treated entity type
     * @param UploadedFile $file Reference to data of uploaded file
     * @param string $fieldName  Name of upload field
     *
     * @return bool true if file is valid else false
     */
    protected function validateFileUpload($objectType, UploadedFile $file, $fieldName)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->hasSession() ? $request->getSession() : null;
        $flashBag = null !== $session ? $session->getFlashBag() : null;
    
        // check if a file has been uploaded properly without errors
        if (UPLOAD_ERR_OK !== $file->getError()) {
            if (null !== $flashBag) {
                $flashBag->add('error', $file->getErrorMessage());
            }
            $this->logger->error(
                '{app}: User {user} tried to upload a file with errors: ' . $file->getErrorMessage(),
                ['app' => 'ZikulaMultisitesModule', 'user' => $this->currentUserApi->get('uname')]
            );
    
            return false;
        }
    
        // extract file extension
        $fileName = $file->getClientOriginalName();
        $extension = $this->determineFileExtension($file);
    
        // validate extension
        $isValidExtension = $this->isAllowedFileExtension($objectType, $fieldName, $extension);
        if (false === $isValidExtension) {
            if (null !== $flashBag) {
                $flashBag->add(
                    'error',
                    $this->__('Error! This file type is not allowed. Please choose another file format.')
                );
            }
            $logArgs = [
                'app' => 'ZikulaMultisitesModule',
                'user' => $this->currentUserApi->get('uname'),
                'extension' => $extension
            ];
            $this->logger->error(
                '{app}: User {user} tried to upload a file with a forbidden extension ("{extension}").',
                $logArgs
            );
    
            return false;
        }
    
        return true;
    }
    
    /**
     * Read meta data from a certain file.
     *
     * @param string $fileName Name of file to be processed
     * @param string $filePath Path to file to be processed
     * @param bool $includeExif Whether to read out EXIF data or not
     *
     * @return array Collected meta data
     */
    public function readMetaDataForFile($fileName, $filePath, $includeExif = true)
    {
        $meta = [];
        if (empty($fileName)) {
            return $meta;
        }
    
        $extensionarr = explode('.', $fileName);
        $meta['extension'] = strtolower($extensionarr[count($extensionarr) - 1]);
        $meta['size'] = filesize($filePath);
        $meta['isImage'] = in_array($meta['extension'], $this->imageFileTypes, true);
    
        if (!$meta['isImage']) {
            return $meta;
        }
    
        if ('swf' === $meta['extension']) {
            $meta['isImage'] = false;
        }
    
        $imgInfo = getimagesize($filePath);
        if (!is_array($imgInfo)) {
            return $meta;
        }
    
        $meta['width'] = $imgInfo[0];
        $meta['height'] = $imgInfo[1];
    
        if ($imgInfo[1] < $imgInfo[0]) {
            $meta['format'] = 'landscape';
        } elseif ($imgInfo[1] > $imgInfo[0]) {
            $meta['format'] = 'portrait';
        } else {
            $meta['format'] = 'square';
        }
    
        if (!$includeExif || 'jpg' !== $meta['extension']) {
            return $meta;
        }
    
        // add EXIF data
        $exifData = $this->readExifData($filePath);
        $meta = array_merge($meta, $exifData);
    
        return $meta;
    }
    
    /**
     * Read EXIF data from a certain file.
     *
     * @param string $filePath Path to file to be processed
     *
     * @return array Collected meta data
     */
    protected function readExifData($filePath)
    {
        $imagine = new Imagine();
        $image = $imagine
            ->setMetadataReader(new ExifMetadataReader())
            ->open($filePath)
        ;
    
        $exifData = $image->metadata()->toArray();
    
        // strip non-utf8 chars to bypass firmware bugs (e.g. Samsung)
        foreach ($exifData as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $kk => $vv) {
                    $exifData[$k][$kk] = mb_convert_encoding($vv, 'UTF-8', 'UTF-8');
                    if (false !== strpos($exifData[$k][$kk], '????')) {
                        unset($exifData[$k][$kk]);
                    }
                }
            } else {
                $exifData[$k] = mb_convert_encoding($v, 'UTF-8', 'UTF-8');
                if (false !== strpos($exifData[$k], '????')) {
                    unset($exifData[$k]);
                }
            }
        }
    
        return $exifData;
    }
    
    /**
     * Determines the allowed file extensions for a given object type and field.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     *
     * @return string[] List of allowed file extensions
     */
    public function getAllowedFileExtensions($objectType, $fieldName)
    {
        // determine the allowed extensions
        $allowedExtensions = [];
        switch ($objectType) {
            case 'site':
                switch ($fieldName) {
                    case 'logo':
                        $allowedExtensions = ['gif', 'jpeg', 'jpg', 'png'];
                        break;
                    case 'favIcon':
                        $allowedExtensions = ['png', 'ico'];
                        break;
                    case 'parametersCsvFile':
                        $allowedExtensions = ['csv'];
                        break;
                }
                break;
            case 'template':
                $allowedExtensions = ['sql', 'txt'];
                break;
        }
    
        return $allowedExtensions;
    }
    
    /**
     * Determines whether a certain file extension is allowed for a given object type and field.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName Name of upload field
     * @param string $extension Input file extension
     *
     * @return bool True if given extension is allowed, false otherwise
     */
    protected function isAllowedFileExtension($objectType, $fieldName, $extension)
    {
        // determine the allowed extensions
        $allowedExtensions = $this->getAllowedFileExtensions($objectType, $fieldName);
    
        if (count($allowedExtensions) > 0 && '*' !== $allowedExtensions[0]) {
            if (!in_array($extension, $allowedExtensions, true)) {
                return false;
            }
        }
    
        return !in_array($extension, $this->forbiddenFileTypes, true);
    }
    
    /**
     * Determines the extension for a given file.
     *
     * @param UploadedFile $file Reference to data of uploaded file
     *
     * @return string the file extension
     */
    protected function determineFileExtension(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();
        $fileNameParts = explode('.', $fileName);
        $extension = null !== $file->guessExtension() ? $file->guessExtension() : $file->guessClientExtension();
        if (in_array($extension, ['bin', 'mpga'])) {
            // fallback to given extension for mp3
            $extension = strtolower($fileNameParts[count($fileNameParts) - 1]);
        }
        if (null === $extension) {
            $extension = strtolower($fileNameParts[count($fileNameParts) - 1]);
        }
    
        return str_replace('jpeg', 'jpg', $extension);
    }
    
    /**
     * Determines the final filename for a given input filename.
     * It considers different strategies for computing the result.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName Name of upload field
     * @param string $basePath Base path for file storage
     * @param string $fileName Input file name
     * @param string $extension Input file extension
     *
     * @return string the resulting file name
     */
    protected function determineFileName($objectType, $fieldName, $basePath, $fileName, $extension)
    {
        $namingScheme = 0;
        switch ($objectType) {
            case 'site':
                switch ($fieldName) {
                    case 'logo':
                        $namingScheme = 0;
                        break;
                    case 'favIcon':
                        $namingScheme = 0;
                        break;
                    case 'parametersCsvFile':
                        $namingScheme = 0;
                        break;
                }
                break;
            case 'template':
                $namingScheme = 0;
                break;
        }
    
        if (0 === $namingScheme || 3 === $namingScheme) {
            // clean the given file name
            $fileNameCharCount = strlen($fileName);
            for ($y = 0; $y < $fileNameCharCount; $y++) {
                if (preg_match('/[^0-9A-Za-z_\.]/', $fileName[$y])) {
                    $fileName[$y] = '_';
                }
            }
        }
        $backupFileName = $fileName;
    
        $iterIndex = -1;
        do {
            if (0 === $namingScheme || 3 === $namingScheme) {
                // original (0) or user defined (3) file name with counter
                if (0 < $iterIndex) {
                    // strip off extension
                    $fileName = str_replace('.' . $extension, '', $backupFileName);
                    // append incremented number
                    $fileName .= (string) ++$iterIndex;
                    // readd extension
                    $fileName .= '.' . $extension;
                } else {
                    $iterIndex++;
                }
            } elseif (1 === $namingScheme) {
                // md5 name
                $fileName = md5(uniqid(mt_rand(), true)) . '.' . $extension;
            } elseif (2 === $namingScheme) {
                // prefix with random number
                $fileName = $fieldName . mt_rand(1, 999999) . '.' . $extension;
            }
        } while (file_exists($basePath . $fileName)); // repeat until we have a new name
    
        // return the final file name
        return $fileName;
    }
    
    /**
     * Deletes an existing upload file.
     *
     * @param EntityAccess $entity Currently treated entity
     * @param string $fieldName Name of upload field
     *
     * @return mixed Updated entity on success, else false
     */
    public function deleteUploadFile(EntityAccess $entity, $fieldName)
    {
        $objectType = $entity->get_objectType();
        if (!in_array($objectType, $this->allowedObjectTypes, true)) {
            return false;
        }
    
        if (empty($entity[$fieldName])) {
            return $entity;
        }
    
        // remove the file
        if (is_array($entity[$fieldName]) && isset($entity[$fieldName][$fieldName])) {
            $entity[$fieldName] = $entity[$fieldName][$fieldName];
        }
        $filePath = $entity[$fieldName] instanceof File ? $entity[$fieldName]->getPathname() : $entity[$fieldName];
        if (file_exists($filePath) && !unlink($filePath)) {
            return false;
        }
    
        $entity[$fieldName] = null;
    
        return $entity;
    }
    
    /**
     * Retrieve the base path for given object type and upload field combination.
     *
     * @param string $objectType Name of treated entity type
     * @param string $fieldName Name of upload field
     * @param bool $ignoreCreate Whether to ignore the creation of upload folders on demand or not
     *
     * @return string
     *
     * @throws Exception If an invalid object type is used
     */
    public function getFileBaseFolder($objectType, $fieldName = '', $ignoreCreate = false)
    {
        $basePath = $this->dataDirectory . '/ZikulaMultisitesModule/';
    
        switch ($objectType) {
            case 'site':
                $basePath .= 'sites/';
                if ('' !== $fieldName) {
                    switch ($fieldName) {
                        case 'logo':
                            $basePath .= 'logo/';
                            break;
                        case 'favIcon':
                            $basePath .= 'favicon/';
                            break;
                        case 'parametersCsvFile':
                            $basePath .= 'parameterscsvfile/';
                            break;
                    }
                }
                break;
            case 'template':
                $basePath .= 'templates/';
                if ('' !== $fieldName) {
                    $basePath .= 'sqlfile/';
                }
                break;
            default:
                throw new Exception($this->__('Error! Invalid object type received.'));
        }
    
        $result = $basePath;
        if ('/' !== substr($result, -1, 1)) {
            // reappend the removed slash
            $result .= '/';
        }
    
        if (!is_dir($result) && !$ignoreCreate) {
            $this->checkAndCreateAllUploadFolders();
        }
    
        return $result;
    }
    
    /**
     * Creates all required upload folders for this application.
     *
     * @return bool Whether everything went okay or not
     */
    public function checkAndCreateAllUploadFolders()
    {
        $result = true;
    
        $result = $result && $this->checkAndCreateUploadFolder('site', 'logo', 'gif, jpeg, jpg, png');
        $result = $result && $this->checkAndCreateUploadFolder('site', 'favIcon', 'png, ico');
        $result = $result && $this->checkAndCreateUploadFolder('site', 'parametersCsvFile', 'csv');
    
        $result = $result && $this->checkAndCreateUploadFolder('template', 'sqlFile', 'sql, txt');
    
        return $result;
    }
    
    /**
     * Creates an upload folder and a .htaccess file within it.
     *
     * @param string $objectType Name of treated entity type
     * @param string $fieldName Name of upload field
     * @param string $allowedExtensions String with list of allowed file extensions (separated by ", ")
     *
     * @return bool Whether everything went okay or not
     */
    protected function checkAndCreateUploadFolder($objectType, $fieldName, $allowedExtensions = '')
    {
        $uploadPath = $this->getFileBaseFolder($objectType, $fieldName, true);
    
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->hasSession() ? $request->getSession() : null;
        $flashBag = null !== $session ? $session->getFlashBag() : null;
    
        // Check if directory exist and try to create it if needed
        if (!$this->filesystem->exists($uploadPath)) {
            try {
                $this->filesystem->mkdir($uploadPath, 0777);
            } catch (IOExceptionInterface $exception) {
                if (null !== $flashBag) {
                    $flashBag->add(
                        'error',
                        $this->__f(
                            'The upload directory "%path%" does not exist and could not be created. Try to create it yourself and make sure that this folder is accessible via the web and writable by the webserver.',
                            ['%path%' => $exception->getPath()]
                        )
                    );
                }
                if (null !== $this->logger) {
                    $this->logger->error(
                        '{app}: The upload directory {directory} does not exist and could not be created.',
                        ['app' => 'ZikulaMultisitesModule', 'directory' => $uploadPath]
                    );
                }
    
                return false;
            }
        }
    
        // Check if directory is writable and change permissions if needed
        if (!is_writable($uploadPath)) {
            try {
                $this->filesystem->chmod($uploadPath, 0777);
            } catch (IOExceptionInterface $exception) {
                if (null !== $flashBag) {
                    $flashBag->add(
                        'warning',
                        $this->__f(
                            'Warning! The upload directory at "%path%" exists but is not writable by the webserver.',
                            ['%path%' => $exception->getPath()]
                        )
                    );
                }
                $this->logger->error(
                    '{app}: The upload directory {directory} exists but is not writable by the webserver.',
                    ['app' => 'ZikulaMultisitesModule', 'directory' => $uploadPath]
                );
    
                return false;
            }
        }
    
        // Write a htaccess file into the upload directory
        $htaccessFilePath = $uploadPath . '/.htaccess';
        $htaccessFileTemplate = 'modules/Zikula/MultisitesModule/Resources/docs/htaccessTemplate';
        if (!$this->filesystem->exists($htaccessFilePath) && $this->filesystem->exists($htaccessFileTemplate)) {
            try {
                $extensions = str_replace(',', '|', str_replace(' ', '', $allowedExtensions));
                $htaccessContent = str_replace(
                    '__EXTENSIONS__',
                    $extensions,
                    file_get_contents($htaccessFileTemplate, false)
                );
                $this->filesystem->dumpFile($htaccessFilePath, $htaccessContent);
            } catch (IOExceptionInterface $exception) {
                if (null !== $flashBag) {
                    $flashBag->add(
                        'error',
                        $this->__f(
                            'An error occured during creation of the .htaccess file in directory "%path%".',
                            ['%path%' => $exception->getPath()]
                        )
                    );
                }
                $this->logger->error(
                    '{app}: An error occured during creation of the .htaccess file in directory {directory}.',
                    ['app' => 'ZikulaMultisitesModule', 'directory' => $uploadPath]
                );
    
                return false;
            }
        }
    
        return true;
    }
}
