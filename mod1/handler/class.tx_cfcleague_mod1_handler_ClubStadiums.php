<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse verwaltet Stadien eines Vereins. Es implementiert das Tab "Stadien" im Vereinsmodul.
 */
class tx_cfcleague_mod1_handler_ClubStadiums {
	/**
	 * Returns an instance
	 * @return string
	 */
	public function getTabLabel() {
		return '###LABEL_TAB_STADIUMS###';
	}
	public function handleRequest(tx_rnbase_mod_IModule $mod) {
//  	$submitted = t3lib_div::_GP('doCreateMatches');
//  	if(!$submitted) return '';
//		$tcaData = t3lib_div::_GP('data');
//
//		tx_rnbase::load('tx_rnbase_util_DB');
//		$tce =& tx_rnbase_util_DB::getTCEmain($tcaData);
//		$tce->process_datamap();
//		$content .= $mod->getDoc()->section('Message:',$GLOBALS['LANG']->getLL('msg_matches_created'),0,1, ICON_INFO);
//		return $content;
	}
	/**
	 * 
	 * @param tx_cfcleague_models_Club $club
	 * @param tx_rnbase_mod_IModule $mod
	 */
	public function showScreen($club, tx_rnbase_mod_IModule $mod) {
		global $LANG;

		$content = 'Zeige Stadien';
		return $content;
	}
	
	public function makeLink(tx_rnbase_mod_IModule $mod) {
		
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/handler/class.tx_cfcleague_mod1_handler_ClubStadiums.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/handler/class.tx_cfcleague_mod1_handler_ClubStadiums.php']);
}
?>