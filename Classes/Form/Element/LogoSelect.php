<?php

namespace System25\T3sports\Form\Element;

use InvalidArgumentException;
use Sys25\RnBase\Utility\Math;
use Sys25\RnBase\Utility\TSFAL;
use tx_rnbase;
use TYPO3\CMS\Backend\Form\Element\SelectSingleElement;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 *  Copyright notice.
 *
 *  (c) 2015-2020 René Nitzsche <rene@system25.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * SelectField for club logo in team record.
 *
 * @author 			René Nitzsche <rene@system25.de>
 */
class LogoSelect extends SelectSingleElement
{
    protected function renderFieldWizard(): array
    {
        $resultArray = parent::renderFieldWizard();

        $field = $this->data['fieldName'];
        $row = $this->data['databaseRow'];

        $file = $this->getFile($this->data['databaseRow'], $field);
        if (!$file) {
            // Early return in case we do not find a file
            return $resultArray;
        }

        $arguments = [
            'image' => $file,
        ];

        /** @var StandaloneView $templateView */
        $templateView = tx_rnbase::makeInstance(StandaloneView::class);
        $templateView->setTemplateSource('
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
	xmlns:core="http://typo3.org/ns/TYPO3/CMS/Core/ViewHelpers">
	<div class="media-object" data-preview-height="80">
		<f:image image="{image}" height="80" class="thumbnail thumbnail-status"/>
	</div>
</html>
        ');

        $templateView->assignMultiple($arguments);
        $resultArray['html'] .= $templateView->render();

        return $resultArray;
    }

    /**
     * Get file object.
     *
     * @param array $row
     * @param string $fieldName
     *
     * @return File|null
     */
    protected function getFile(array $row, $fieldName)
    {
        $file = null;
        $fileRefUid = !empty($row[$fieldName]) ? $row[$fieldName] : null;
        if (is_array($fileRefUid) && isset($fileRefUid[0])) {
            $fileRefUid = $fileRefUid[0];
        }
        if (Math::isInteger($fileRefUid) && $fileRefUid) {
            try {
                $ref = TSFAL::getFileReferenceById($fileRefUid);
                if ($ref) {
                    $file = $ref->getOriginalFile();
                }
            } catch (FileDoesNotExistException $e) {
            } catch (InvalidArgumentException $e) {
            }
        }

        return $file;
    }
}
