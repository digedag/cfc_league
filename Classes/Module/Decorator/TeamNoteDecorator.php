<?php

namespace System25\T3sports\Module\Decorator;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Utility\TSFAL;
use System25\T3sports\Model\TeamNote;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2021 Rene Nitzsche (rene@system25.de)
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
 * Diese Klasse ist für die Darstellung von TeamNotes im Backend verantwortlich.
 */
class TeamNoteDecorator
{
    public $formTool;

    public function __construct(ToolBox $formTool)
    {
        $this->formTool = $formTool;
    }

    /**
     * Formating team notes.
     *
     * @param mixed $value
     * @param string $colName
     * @param array $record
     * @param TeamNote $item
     *
     * @return string
     */
    public function format($value, $colName, $record = [], TeamNote $item = null)
    {
        $ret = $value;
        if (!$item) {
            $ret .= 'Error';
        } elseif ('mediatype' == $colName) {
            switch ($item->getMediaType()) {
                case 0:
                    $ret = $GLOBALS['LANG']->getLL('tx_cfcleague_team_notes.mediatype.text');

                    break;
                case 2:
                    $ret = $GLOBALS['LANG']->getLL('tx_cfcleague_team_notes.mediatype.number');

                    break;
                case 1:
                    $ret = $GLOBALS['LANG']->getLL('tx_cfcleague_team_notes.mediatype.media');

                    break;
                default:
                    $ret = 'unknown';
            }
        } elseif ('uid' == $colName) {
            $ret = $item->getUid();
            $ret .= $this->formTool->createEditLink('tx_cfcleague_team_notes', $item->getUid(), '');
        } elseif ('value' == $colName) {
            $ret = $item->getValue();
            if (1 == $item->getMediaType()) {
                $ret .= $this->showMediaFAL($item);
            }
        } elseif ('profile' == $colName) {
            $ret = $item->getProfile()->getName();
            $ret .= $this->formTool->createEditLink('tx_cfcleague_profiles', $item->getProfile()->getUid());
        }

        return $ret;
    }

    private function showMediaFAL($item)
    {
        $fileReference = TSFAL::getFirstReference('tx_cfcleague_team_notes', $item->getUid(), 'media');
        if ($fileReference) {
            $thumbs = TSFAL::createThumbnails([$fileReference], ['width' => 50, 'height' => 50]);

            return ''.$thumbs[0];
        }

        return '';
    }
}
