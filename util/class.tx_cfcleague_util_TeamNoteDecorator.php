<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2008 Rene Nitzsche (rene@system25.de)
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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');


/**
 * Diese Klasse ist für die Darstellung von TeamNotes im Backend verantwortlich
 */
class tx_cfcleague_util_TeamNoteDecorator {
	var $formTool;
	public function tx_cfcleague_util_TeamNoteDecorator($formTool) {
		$this->formTool = $formTool;
	}

	/**
	 * Formating team notes
	 *
	 * @param mixed $value
	 * @param string $colName
	 * @param array $record
	 * @param tx_cfcleague_models_TeamNote $item
	 * @return string
	 */
	public function format($value, $colName, $record = array(), $item=false) {
		$ret = $value;
		if(!$item) {
			$ret .= 'Error';
		}
		elseif($colName == 'mediatype') {
			switch($item->getMediaType()) {
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
		}
		elseif($colName == 'uid') {
			$ret = $item->getUid();
			$ret .= $this->formTool->createEditLink('tx_cfcleague_team_notes', $item->getUid(), '');
		}
		elseif($colName == 'value') {
			$ret = $item->getValue();
			if($item->getMediaType() == 1) {
				tx_rnbase::load('tx_cfcleague_util_DAM');
				$size = '50x50';
				$damFiles = tx_cfcleague_util_DAM::fetchFiles('tx_cfcleague_team_notes', $item->getUid(), 'media');
				$data = $damFiles['rows'];
				if(count($data)) {
					$thumbs = tx_cfcleague_util_DAM::createThumbnails($damFiles, $size, $addAttr);
					$ret = $thumbs[0];
					list($key, $file) = each($data);
					$ret .= ' ' . $file['file_name'];
//					t3lib_div::debug($file, 'tx_cfcleague_util_TeamNoteDecorator'); // TODO: remove me
					$ret .= $this->formTool->createEditLink('tx_dam', $file['uid']);
				}
			}
		}
		elseif($colName == 'profile') {
			$ret = $item->getProfile()->getName();
			$ret .= $this->formTool->createEditLink('tx_cfcleague_profiles', $item->getProfile()->getUid());
		}
		
		return $ret;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_TeamNoteDecorator.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_TeamNoteDecorator.php']);
}
?>