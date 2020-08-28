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

namespace Zikula\MultisitesModule\Form\DataTransformer\Base;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\MultisitesModule\Helper\UploadHelper;

/**
 * Upload file transformer base class.
 *
 * This data transformer treats uploaded files.
 */
abstract class AbstractUploadFileTransformer implements DataTransformerInterface
{
    /**
     * @var EntityAccess
     */
    protected $entity = null;

    /**
     * @var UploadHelper
     */
    protected $uploadHelper = '';

    /**
     * @var string
     */
    protected $fieldName = '';

    public function __construct(
        EntityAccess $entity,
        UploadHelper $uploadHelper,
        $fieldName = ''
    ) {
        $this->entity = $entity;
        $this->uploadHelper = $uploadHelper;
        $this->fieldName = $fieldName;
    }

    /**
     * Transforms a filename to the corresponding upload input array.
     */
    public function transform($file)
    {
        return [
            $this->fieldName => $file,
            $this->fieldName . 'DeleteFile' => false
        ];
    }

    /**
     * Transforms a result array back to the File object
     */
    public function reverseTransform($data)
    {
        $deleteFile = false;
        $uploadedFile = null;

        if ($data instanceof UploadedFile) {
            // no file deletion checkbox has been provided
            $uploadedFile = $data;
        } else {
            $uploadedFile = isset($data[$this->fieldName]) ? $data[$this->fieldName] : null;
            $deleteFile = isset($data[$this->fieldName . 'DeleteFile'])
                ? $data[$this->fieldName . 'DeleteFile']
                : false
            ;
        }

        $entity = $this->entity;
        $objectType = $entity->get_objectType();
        $fieldName = $this->fieldName;

        $oldFile = $entity[$fieldName];

        // check if an existing file must be deleted
        $hasOldFile = !empty($oldFile);
        if ($hasOldFile && true === $deleteFile) {
            // remove old upload file
            $entity = $this->uploadHelper->deleteUploadFile($entity, $fieldName);
            // set old file to empty value as the file does not exist anymore
            $oldFile = null;
        }

        if (null === $uploadedFile || !($uploadedFile instanceof UploadedFile)) {
            // no new file has been uploaded
            return $oldFile;
        }

        // new file has been uploaded; check if there is an old one to be deleted
        if ($hasOldFile && true !== $deleteFile) {
            // remove old upload file (and image thumbnails)
            $entity = $this->uploadHelper->deleteUploadFile($entity, $fieldName);
        }

        // do the actual upload (includes validation, physical file processing and reading meta data)
        $uploadResult = $this->uploadHelper->performFileUpload($objectType, $uploadedFile, $fieldName);

        $result = null;
        $metaData = [];
        if ('' !== $uploadResult['fileName']) {
            $result = $this->uploadHelper->getFileBaseFolder($objectType, $fieldName) . $uploadResult['fileName'];
            $result = null !== $result ? new File($result) : $result;
            $metaData = $uploadResult['metaData'];
        }

        // assign the meta data
        $setter = 'set' . ucfirst($fieldName) . 'Meta';
        $entity->$setter($metaData);

        return $result;
    }
}
