<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2014 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_util_SearchBase');

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
	public function getMatchNoteTypes4TCA() {
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
	 * Spiele des/der Teams in einem Wettbewerb
	 * @param tx_cfcleague_models_Competition $comp
	 * @param string $teamIds
	 * @param string $status
	 * @return array[tx_cfcleague_models_Match]
	 */
	public function getMatches4Competition($comp, $teamIds='', $status = '0,1,2'){
		$fields = array();
		$options = array();
//	  $options['debug'] = 1;
		$builder = $this->getMatchTableBuilder();
		$builder->setCompetitions($comp->getUid());
		$builder->setStatus($status);
		$builder->setTeams($teamIds);
		$builder->getFields($fields, $options);
		
  	$matches = $this->search($fields, $options);
  	return $matches;
	}

	/**
	 * @return tx_cfcleague_util_MatchTableBuilder
	 */
	public function getMatchTableBuilder() {
		return tx_rnbase::makeInstance('tx_cfcleague_util_MatchTableBuilder');
	}

	/**
	 * Search database for matches
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array of tx_cfcleague_models_Match
	 */
	public function search($fields, $options) {
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Match');
		return $searcher->search($fields, $options);
	}

	/**
	 * Search database for matches
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array of tx_cfcleague_models_Match
	 */
	public function searchMatchNotes($fields, $options) {
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_MatchNote');
		return $searcher->search($fields, $options);
	}

	/**
	 * Search database for matches
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array of tx_cfcleague_models_MatchRound
	 */
	public function searchMatchRound($fields, $options) {
		tx_rnbase::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_MatchRound');
		return $searcher->search($fields, $options);
	}


	/**
	 * Ermittelt für das übergebene Spiel die MatchNotes. Wenn $types = 1 dann
	 * werden nur die Notes mit dem Typ != 100 geliefert.
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $excludeTicker
	 * @return array[tx_cfcleague_models_MatchNote]
	 */
	public function retrieveMatchNotes($match, $excludeTicker=true) {
		$options['where'] = 'game = ' .$match->getUid();
		if($excludeTicker) {
			$options['where'] .= ' AND type != 100';
		}
		$options['orderby'] = 'minute asc';
		$options['wrapperclass'] = 'tx_cfcleague_models_MatchNote';

		$matchNotes = tx_rnbase_util_DB::doSelect('*', 'tx_cfcleague_match_notes', $options);
		return $matchNotes;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Match.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Match.php']);
}

?>