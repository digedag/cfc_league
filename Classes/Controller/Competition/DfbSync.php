<?php

namespace System25\T3sports\Controller\Competition;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Frontend\Marker\Templates;
use Sys25\RnBase\Frontend\Request\Parameters;
use Sys25\RnBase\Utility\TYPO3;
use System25\T3sports\Dfb\Synchronizer;
use System25\T3sports\Model\Competition;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2023 Rene Nitzsche (rene@system25.de)
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
 ***************************************************************/

/**
 * Die Klasse verwaltet die Erstellung Teams für Wettbewerbe.
 */
class DfbSync
{
    protected $doc;

    /**
     * @var \TYPO3\CMS\Core\Utility\File\ExtendedFileUtility
     */
    protected $fileProcessor;

    /**
     * @var array|\TYPO3\CMS\Core\Resource\File[]
     */
    protected $uploadedFiles = [];
    protected $formTool;

    /**
     * @var Synchronizer
     */
    private $synchronizer;

    public function __construct(Synchronizer $synchronizer = null)
    {
        $this->synchronizer = $synchronizer ?: tx_rnbase::makeInstance(Synchronizer::class);
    }

    /**
     * Verwaltet die Erstellung von Spielplänen von Ligen.
     *
     * @param IModule $module
     * @param Competition $competition
     */
    public function main(IModule $module, $competition, $template)
    {
        // Zuerst mal müssen wir die passende Liga auswählen lassen:
        // Entweder global über die Datenbank oder die Ligen der aktuellen Seite
        $this->doc = $module->getDoc();

        $this->formTool = $module->getFormTool();
        $this->checkUpload();
        $markerArr = $subpartArr = $wrappedSubpartArr = [];

        $markerArr['###STATUS_INFO###'] = $this->buildInfoMessage($competition);

        $tempFolder = $this->getDefaultImportExportFolder();
        if ($tempFolder) {
            $markerArr['###TARGET_FOLDER###'] = htmlspecialchars($tempFolder->getCombinedIdentifier());
            if (Parameters::getPostOrGetParameter('_upload')) {
                if ($this->fileProcessor->internalUploadMap[1]) {
                    $markerArr['###STATUS_FILE###'] = $this->uploadedFiles[0]->getName();
                    $info = $this->synchronizer->process($this->uploadedFiles[0], $competition);

                    $markerArr['###STATUS_MATCH_UPDATED###'] = $info['match']['updated'];
                    $markerArr['###STATUS_MATCH_NEW###'] = $info['match']['new'];
                    $markerArr['###STATUS_MATCH_SKIPPED###'] = $info['match']['skipped'];
                    $markerArr['###STATUS_TEAM_NEW###'] = $info['team']['new'];
                    // FIXME: der Service sollte keinen Zustand haben
                    $markerArr['###STATUS_TIME###'] = $this->synchronizer->getStats()['total']['time'];
                } else {
                    $markerArr['###STATUS_FILE###'] = '<span class="typo3-red">###LABEL_upload_failureNoFile###</span>';
                    $markerArr['###STATUS_MATCH_UPDATED###'] = 0;
                    $markerArr['###STATUS_MATCH_NEW###'] = 0;
                    $markerArr['###STATUS_MATCH_SKIPPED###'] = 0;
                    $markerArr['###STATUS_TEAM_NEW###'] = 0;
                    $markerArr['###STATUS_TIME###'] = 0;
                }
                $wrappedSubpartArr['###SUB_UPLOAD_STATUS###'] = ['', ''];
            } else {
                $subpartArr['###SUB_UPLOAD_STATUS###'] = '';
            }
        }
        $content = Templates::substituteMarkerArrayCached($template, $markerArr, $subpartArr, $wrappedSubpartArr);

        return $content;
    }

    protected function buildInfoMessage(Competition $competition)
    {
        global $LANG;
        $key = $competition->getExtId();
        $msg = $LANG->getLL($key ? 'label_dfbsync_keyfound' : 'label_dfbsync_nokeyfound');

        return sprintf($msg, $key);
    }

    /**
     * Check if a file has been uploaded.
     *
     * @todo Define visibility
     */
    public function checkUpload()
    {
        $file = Parameters::getPostOrGetParameter('file');
        // Initializing:
        $this->fileProcessor = tx_rnbase::makeInstance('TYPO3\\CMS\\Core\\Utility\\File\\ExtendedFileUtility');
        if (TYPO3::isTYPO87OrHigher()) {
            $this->fileProcessor->setActionPermissions();
            $this->fileProcessor->setExistingFilesConflictMode(
                \TYPO3\CMS\Core\Resource\DuplicationBehavior::REPLACE
            );
        } else {
            $this->fileProcessor->init([], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
            $this->fileProcessor->setActionPermissions();
            $this->fileProcessor->dontCheckForUnique = 1;
        }
        $this->fileProcessor->start($file);
        $result = $this->fileProcessor->processData();

        if (!empty($result['upload'])) {
            foreach ($result['upload'] as $uploadedFiles) {
                $this->uploadedFiles += $uploadedFiles;
            }
        }
    }

    /**
     * Returns a \TYPO3\CMS\Core\Resource\Folder object for saving export files
     * to the server and is also used for uploading import files.
     *
     * @return \TYPO3\CMS\Core\Resource\Folder|null
     */
    protected function getDefaultImportExportFolder()
    {
        $defaultImportExportFolder = null;

        $defaultTemporaryFolder = $this->getBackendUser()->getDefaultUploadTemporaryFolder();
        if (null !== $defaultTemporaryFolder) {
            $importExportFolderName = 'importexport';
            $createFolder = !$defaultTemporaryFolder->hasFolder($importExportFolderName);
            if (true === $createFolder) {
                try {
                    $defaultImportExportFolder = $defaultTemporaryFolder->createFolder($importExportFolderName);
                } catch (\TYPO3\CMS\Core\Resource\Exception $folderAccessException) {
                }
            } else {
                $defaultImportExportFolder = $defaultTemporaryFolder->getSubfolder($importExportFolderName);
            }
        }

        return $defaultImportExportFolder;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return TYPO3::getBEUser();
    }

    /**
     * Returns the formtool.
     *
     * @return ToolBox
     */
    protected function getFormTool()
    {
        return $this->formTool;
    }
}
