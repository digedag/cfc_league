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

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');
require_once(PATH_t3lib.'class.t3lib_svbase.php');
tx_div::load('tx_rnbase_util_DB');

interface tx_cfcleague_MatchService {
  function search($fields, $options);
}

/**
 * Service for accessing match information
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Match extends t3lib_svbase implements tx_cfcleague_MatchService  {

	/**
	 * Returns all available profile types for a TCA select item
	 *
	 * @return array 
	 */
	function getMatchNoteTypes4TCA() {
		$types = array();
		// Zuerst in der Ext_Conf die BasisTypen laden
		$types = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'];

		// Jetzt schauen, ob noch weitere Typpen per Service geliefert werden
		$baseType = 't3sports_matchnotetype';
		$services = tx_rnbase_util_Misc::lookupServices($baseType);
		foreach ($services As $subtype => $info) {
			$srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
			$types = array_merge($types, $srv->getMatchNoteTypes());
		}
		foreach($types AS $typedef) {
			$items[] = array(tx_rnbase_util_Misc::translateLLL($typedef[0]), $typedef[1]);
		}
		return $items;
	}

	/**
	 * Search database for matches
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array of tx_cfcleague_models_Match
	 */
	function search($fields, $options) {
		tx_div::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Match');
		return $searcher->search($fields, $options);
	}

	/**
	 * Search database for matches
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array of tx_cfcleague_models_MatchRound
	 */
	function searchMatchRound($fields, $options) {
		tx_div::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_MatchRound');
		return $searcher->search($fields, $options);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/sv1/class.tx_cfcleague_services_Match.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/sv1/class.tx_cfcleague_services_Match.php']);
}

?>