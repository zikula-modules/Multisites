<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Form\Handler\Template;

use Zikula\MultisitesModule\Form\Handler\Template\Base\EditHandler as BaseEditHandler;

use ModUtil;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * This handler class handles the page events of the Form called by the zikulaMultisitesModule_template_edit() function.
 * It aims on the template object type.
 */
class EditHandler extends BaseEditHandler
{
    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables.
     *
     * @return boolean False in case of initialisation errors, otherwise true.
     */
    public function processForm(array $templateParameters)
    {
        $result = parent::processForm($templateParameters);
    
        // set mandatory flag to false
        $this->uploadFields['sqlFile'] = false;

        return $result;
    }

    /**
     * Helper method to process upload fields.
     *
     * @param array        $formData The form input data.
     * @param EntityAccess $entity   Existing entity object.
     *
     * @return array form data after processing.
     */
    protected function handleUploads($formData, $entity)
    {
        if (!count($this->uploadFields)) {
            return $formData;
        }

        // TODO method must be reviewed and probably updated to Symfony forms

        // check if either an upload file has been provided or an existing sql file has been selected
        $hasSqlUpload = true;
        if (!$formData['sqlFile'] || $formData['sqlFile']['size'] == 0) {
            if (!isset($formData['sqlFileSelected']) || $formData['sqlFileSelected'] < 1) {
                $this->request->getSession()->getFlashBag()->add('error', $this->__('Error! Please either provide a sql file or select an existing one.'));
                return false;
            }
            $hasSqlUpload = false;
        }

        $existingObjectData = $entity->toArray();

        $objectId = ($this->templateParameters['mode'] != 'create') ? $this->idValues[0] : 0;

        // process all fields
        foreach ($this->uploadFields as $uploadField => $isMandatory) {
            if ($uploadField == 'sqlFile') {
                // check if an existing file must be deleted
                $hasOldFile = (!empty($existingObjectData[$uploadField]));
                $hasBeenDeleted = !$hasOldFile;
                if ($this->templateParameters['mode'] != 'create') {
                    if (isset($formData[$uploadField . 'DeleteFile'])) {
                        if ($hasOldFile && $formData[$uploadField . 'DeleteFile'] === true && !$entity->isSqlFileReferencedByOtherTemplates()) {
                            // remove upload file (and image thumbnails)
                            $existingObjectData = $this->uploadHandler->deleteUploadFile($this->objectType, $existingObjectData, $uploadField, $objectId);
                            if (empty($existingObjectData[$uploadField])) {
                                $entity[$uploadField] = '';
                                $entity[$uploadField . 'Meta'] = [];
                            }
                        }
                        unset($formData[$uploadField . 'DeleteFile']);
                        $hasBeenDeleted = true;
                    }
                }

                // look whether a file has been provided
                if (!$formData[$uploadField] || $formData[$uploadField]['size'] == 0) {
                    // no file has been uploaded
                    unset($formData[$uploadField]);
                    // skip to next one
                    continue;
                }

                if ($hasOldFile && $hasBeenDeleted !== true && $this->templateParameters['mode'] != 'create' && !$entity->isSqlFileReferencedByOtherTemplates()) {
                    // remove old upload file (and image thumbnails)
                    $existingObjectData = $this->uploadHandler->deleteUploadFile($this->objectType, $existingObjectData, $uploadField, $objectId);
                    if (empty($existingObjectData[$uploadField])) {
                        $entity[$uploadField] = '';
                        $entity[$uploadField . 'Meta'] = [];
                    }
                }

                if ($hasSqlUpload) {
                    // do the actual upload (includes validation, physical file processing and reading meta data)
                    $uploadResult = $this->uploadHandler->performFileUpload($this->objectType, $formData, $uploadField);
                    // assign the upload file name
                    $formData[$uploadField] = $uploadResult['fileName'];
                    // assign the meta data
                    $formData[$uploadField . 'Meta'] = $uploadResult['metaData'];
                } else {
                    // check if the selected sql file is different from the current one
                    $selectedFileTemplateId = $formData['sqlFileSelected'];
                    if ($this->templateParameters['mode'] == 'create' || $selectedFileTemplateId != $existingObjectData['id']) {
                        // update file information from original template
                        $referencedTemplate = ModUtil::apiFunc('ZikulaMultisitesModule', 'selection', 'getEntity', ['ot' => 'template', 'id' => $selectedFileTemplateId]);
                        $formData[$uploadField] = $referencedTemplate[$uploadField];
                        $formData[$uploadField . 'Meta'] = $referencedTemplate[$uploadField . 'Meta'];
                    }
                }

                // if current field is mandatory check if everything has been done
                if ($isMandatory && empty($formData[$uploadField])) {
                    // mandatory upload has not been completed successfully
                    return false;
                }
        
                // upload succeeded
            } else {
                // check if an existing file must be deleted
                $hasOldFile = (!empty($existingObjectData[$uploadField]));
                $hasBeenDeleted = !$hasOldFile;
                if ($this->templateParameters['mode'] != 'create') {
                    if (isset($formData[$uploadField . 'DeleteFile'])) {
                        if ($hasOldFile && $formData[$uploadField . 'DeleteFile'] === true) {
                            // remove upload file (and image thumbnails)
                            $existingObjectData = $this->uploadHandler->deleteUploadFile($this->objectType, $existingObjectData, $uploadField, $objectId);
                            if (empty($existingObjectData[$uploadField])) {
                                $entity[$uploadField] = '';
                                $entity[$uploadField . 'Meta'] = [];
                            }
                        }
                        unset($formData[$uploadField . 'DeleteFile']);
                        $hasBeenDeleted = true;
                    }
                }

                // look whether a file has been provided
                if (!$formData[$uploadField] || $formData[$uploadField]['size'] == 0) {
                    // no file has been uploaded
                    unset($formData[$uploadField]);
                    // skip to next one
                    continue;
                }

                if ($hasOldFile && $hasBeenDeleted !== true && $this->templateParameters['mode'] != 'create') {
                    // remove old upload file (and image thumbnails)
                    $existingObjectData = $this->uploadHandler->deleteUploadFile($this->objectType, $existingObjectData, $uploadField, $objectId);
                    if (empty($existingObjectData[$uploadField])) {
                        $entity[$uploadField] = '';
                        $entity[$uploadField . 'Meta'] = [];
                    }
                }

                // do the actual upload (includes validation, physical file processing and reading meta data)
                $uploadResult = $this->uploadHandler->performFileUpload($this->objectType, $formData, $uploadField);
                // assign the upload file name
                $formData[$uploadField] = $uploadResult['fileName'];
                // assign the meta data
                $formData[$uploadField . 'Meta'] = $uploadResult['metaData'];
        
                // if current field is mandatory check if everything has been done
                if ($isMandatory && empty($formData[$uploadField])) {
                    // mandatory upload has not been completed successfully
                    return false;
                }
        
                // upload succeeded
            }
        }

        return $formData;
    }
}
