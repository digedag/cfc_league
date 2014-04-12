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


/**
 * Service for accessing stadiums
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Competition extends t3lib_svbase {

	/**
	 * Returns uids of dummy teams
	 * @param $comp
	 * @return array[int]
	 */
	public function getDummyTeamIds($comp) {
		$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
		$fields = array();
		$fields['TEAM.DUMMY'][OP_EQ_INT] = 1;
		$fields['TEAM.UID'][OP_IN_INT] = $comp->record['teams'];
		$options = array();
		$options['what'] = 'uid';
		//$options['debug'] = 1;
		$rows = $srv->searchTeams($fields, $options);
		$ret = array();
		foreach($rows As $row)
			$ret[] = $row['uid'];
		return $ret;
	}
	/**
	 * Anzahl der Spiele des/der Teams in diesem Wettbewerb
	 */
	public function getNumberOfMatches($comp, $teamIds='', $status = '0,1,2'){
		$what = 'count(uid) As matches';
		$from = 'tx_cfcleague_games';
		$options['where'] = 'status IN(' . $status . ') AND ';
		if($teamIds) {
			$options['where'] .= '( home IN(' . $teamIds . ') OR ';
			$options['where'] .= 'guest IN(' . $teamIds . ')) AND ';
		}
		$options['where'] .= 'competition = ' . $comp->uid . ' ';
		$rows = tx_rnbase_util_DB::doSelect($what,$from,$options,0);
		$ret = 0;
		if(count($rows))
			$ret = intval($rows[0]['matches']);
		return $ret;
	}
	
	/**
	 * Search database for competitions
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array of tx_cfcleague_models_Competition
	 */
	public function search($fields, $options) {
		tx_rnbase::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Competition');
		return $searcher->search($fields, $options);
	}
	public function getPointSystems($sports) {
		$srv = tx_rnbase_util_Misc::getService('t3sports_sports', $sports);
		return $srv->getTCAPointSystems();
	}
	/**
	 * Returns all available table types for a TCA select item
	 *
	 * @return array 
	 */
	public function getSports4TCA() {
		$types = array();

		// Jetzt schauen, ob noch weitere Sportarten per Service geliefert werden
		$baseType = 't3sports_sports';
		$services = tx_rnbase_util_Misc::lookupServices($baseType);
		foreach ($services As $subtype => $info) {
			$srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
			$types[] = array($srv->getTCALabel(), $subtype);
		}

		foreach($types AS $typedef) {
			$items[] = array(tx_rnbase_util_Misc::translateLLL($typedef[0]), $typedef[1]);
		}
		return $items;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Competition.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Competition.php']);
}

?>