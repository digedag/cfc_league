<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche <rene@system25.de>
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




class tx_cfcleague_hooks_cmhooks {

	/**
	 * This hook is processed Bevor a commandmap is processed (delete, etc.)
	 *
	 * @param	string $command the TCE command string: localize, copy, delete etc.
	 * @param	string $table the table the data will be stored in
	 * @param	integer $id the uid of the dataset we're working on
	 * @param string $value
	 * @param	object $pObj the instance of the BE Form
	 * @return	void
	 */
	function processCmdmap_preProcess(&$command, $table, $id, $value, &$pObj)	{
		if($command == 'delete' && $table == 'tx_cfcleague_profiles') {
			// TODO: Check references
			$profile = tx_rnbase::makeInstance('tx_cfcleague_models_Profile', $id);
			$refArr = tx_cfcleague_util_ServiceRegistry::getProfileService()->checkReferences($profile);
			if(count($refArr) > 0) {
				// Abbruch
				$addInfo = '<p>';
				foreach($refArr As $table => $data) {
					$addInfo .= '<b>'.$table . ':</b> ' . count($data) . '<br />';
				}
				$addInfo .= '</p>';
				tx_rnbase::load('tx_cfcleague_util_Misc');
				tx_cfcleague_util_Misc::tceError('LLL:EXT:cfc_league/locallang_db.xml:label_msg_refError', $addInfo);
			}
		}
		elseif($command == 'delete' && $table == 'tx_cfcleague_competition') {
			$competition = tx_rnbase::makeInstance('tx_cfcleague_models_Competition', $id);
			$refArr = tx_cfcleague_util_ServiceRegistry::getCompetitionService()->checkReferences($competition);
			if(count($refArr) > 0) {
				// Abbruch
				$addInfo = '<p>';
				foreach($refArr As $table => $data) {
					$addInfo .= '<b>'.$table . ':</b> ' . $data . '<br />';
				}
				$addInfo .= '</p>';
				tx_rnbase::load('tx_cfcleague_util_Misc');
				tx_cfcleague_util_Misc::tceError('LLL:EXT:cfc_league/locallang_db.xml:label_msg_refError', $addInfo);
			}
		}
	}


	/**
	 * This hook is processed AFTER a commandmap is processed (delete, etc.)
	 *
	 * @param	array		$incomingFieldArray: the array of fields that where changed in BE (passed by reference)
	 * @param	string		$table: the table the data will be stored in
	 * @param	integer		$id: The uid of the dataset we're working on
	 * @param	object		$pObj: The instance of the BE Form
	 * @return	void
	 */
	function processCmdmap_postProcess(&$command, $table, $id, $value, &$pObj)	{
	}


	/**
	 *
	 * Prints out the error
	 * @param 	String 	$error
	 */

	function error($error)	{
		$error_doc =  tx_rnbase::makeInstance(tx_rnbase_util_Typo3Classes::getDocumentTemplateClass());
		$error_doc->backPath = '';

		$content.= $error_doc->startPage('Error Output');
		$content.= '
			<br/><br/>
			<table border="0" cellpadding="1" cellspacing="1" width="300" align="center">';

		$content.='	<tr class="bgColor5">
					<td colspan="2" align="center"><strong>'.$GLOBALS['LANG']->sL('LLL:EXT:commerce/locallang_be_errors.php:error', 1).'</strong></td>
				</tr>';

		$content.='
				<tr class="bgColor4">
					<td valign="top"><img'.Tx_Rnbase_Backend_Utility_Icons::skinImg('', 'gfx/icon_fatalerror.gif', 'width="18" height="16"').' alt="" /></td>
					<td>'.$GLOBALS['LANG']->sL($error, 0).'</td>
				</tr>';


		$content.='
				<tr>
					<td colspan="2" align="center"><br />'.

					'<form action="'.htmlspecialchars($_SERVER["HTTP_REFERER"]).'"><input type="submit" value="'.$GLOBALS['LANG']->sL('LLL:EXT:commerce/locallang_be_errors.php:continue', 1).'" onclick="document.location='.htmlspecialchars($_SERVER["HTTP_REFERER"]).'return false;" /></form>'.
					'</td>
				</tr>';

		$content.= '</table>';

		$content.= $error_doc->endPage();
		echo $content;
		exit;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/hooks/class.tx_cfcleague_hooks_cmhooks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/hooks/class.tx_cfcleague_hooks_cmhooks.php']);
}
?>