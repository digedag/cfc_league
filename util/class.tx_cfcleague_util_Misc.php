<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (rene@system25.de)
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
 * Kleine Methoden
 */
class tx_cfcleague_util_Misc {
  /**
   * Zwei Arrays zusammenführen. Sollte eines der Array leer sein, dann wird es ignoriert.
   * Somit werden unnötige 0-Werte vermieden.
   */
  public static function mergeArrays($arr1, $arr2){
    $ret = $arr1[0] ? $arr1 : 0;
    if($ret && $arr2) {
      $ret = array_merge($ret, $arr2);
    }
    elseif($arr2)
      $ret = $arr2;
    return $ret;
  }

	/**
	 * Register a new matchnote.
	 * @param string $label
	 * @param mixed $typeId
	 */
	public static function registerMatchNote($label, $typeId) {
		if(!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes']))
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'] = array();
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'][] = array($label, $typeId);
//$GLOBALS ['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'] = array(
//			Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.ticker', '100'),
	}
	/**
	 * Register a new match formation.
	 * @param string $label
	 * @param mixed $formationString
	 */
	public static function registerFormation($label, $formationString) {
		if(!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations']))
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'] = array();
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'][] = array($label, $formationString);
	}
	
	/**
	 * 
	 * Prints out the error
	 * @param 	String 	$error
	 */
	
	public static function tceError($error, $addinfo='')	{
		$error_doc = t3lib_div::makeInstance('template');
		$error_doc->backPath = '';

		$content.= $error_doc->startPage('T3sports error Output');
		$content.= '
			<br/><br/>
			<table border="0" cellpadding="1" cellspacing="1" width="300" align="center">';
	
		$content.='	<tr class="bgColor5">
					<td colspan="2" align="center"><strong>Fehler</strong></td>
				</tr>';
	
		$content.='
				<tr class="bgColor4">
					<td valign="top"><img'.t3lib_iconWorks::skinImg('', 'gfx/icon_fatalerror.gif', 'width="18" height="16"').' alt="" /></td>
					<td>'.$GLOBALS['LANG']->sL($error, 0).'</td>
				</tr>';
		if($addinfo)
			$content.='
					<tr class="bgColor4">
						<td valign="top"></td>
						<td>'.$addinfo.'</td>
					</tr>';


		$content.='
				<tr>
					<td colspan="2" align="center"><br />'.
				
					'<form action="'.htmlspecialchars($_SERVER["HTTP_REFERER"]).'"><input type="submit" value="Weiter" onclick="document.location='.htmlspecialchars($_SERVER["HTTP_REFERER"]).'return false;" /></form>'.
					'</td>
				</tr>';

		$content.= '</table>';

		$content.= $error_doc->endPage();
		echo $content;
		exit();	
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Misc.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Misc.php']);
}

?>