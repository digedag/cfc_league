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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
require_once(PATH_t3lib.'class.t3lib_svbase.php');
tx_rnbase::load('tx_rnbase_util_DB');


/**
 * Service for accessing profiles
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Profiles extends t3lib_svbase {
	private $profiles = array();

	/**
	 * Return all instances of all requested profiles
	 * @param string $uids commaseparated uids
	 * @return array[tx_cfcleague_models_Profile]
	 */
	public function loadProfiles($uids) {
		$uids = is_array($uids) ? $uids : t3lib_div::intExplode(',', $uids);
		$ret = array();
		$toLoad = array();
		foreach($uids As $key => $uid) {
			if(array_key_exists($uid, $this->profiles))
				$ret[$key] = $this->profiles[$uid];
			else
				$toLoad[$key] = $uid;
		}

		if(!empty($toLoad)) {
			$fields['PROFILE.UID'][OP_IN_INT] = implode(',', $toLoad);
			$options = array();
			$rows = $this->search($fields,$options);
			$toLoadFlip = array_flip($toLoad);
			foreach($rows As $profile) {
				$this->profiles[$profile->getUid()] = $profile;
				$ret[$toLoadFlip[$profile->getUid()]] = $profile;
			}
		}
		return $ret;
	}
	/**
	 * Returns all team notes for a given profile
	 *
	 * @param tx_cfcleague_models_Profile $profile
	 * @return array An array with all references by table
	 */
	public function checkReferences($profile) {
		$ret = array();
		// Zuerst die Teams
		$options['what'] = 'uid';

		$fields = array();
		$fields[SEARCH_FIELD_JOINED][0] = array(
			'value' => $profile->getUid(), 
			'cols' => array('TEAM.PLAYERS', 'TEAM.SUPPORTERS', 'TEAM.COACHES'),
			'operator' => OP_INSET_INT
		);
		$result = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchTeams($fields, $options);
		if(count($result))
			$ret['tx_cfcleague_teams'] = $result;


		$fields = array();
		$fields['TEAMNOTE.PLAYER'][OP_EQ_INT] = $profile->getUid();
		$result = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchTeamNotes($fields, $options);
		if(count($result))
			$ret['tx_cfcleague_team_notes'] = $result;

		$fields = array();
		$fields[SEARCH_FIELD_JOINED][0] = array(
			'value' => $profile->getUid(), 
			'cols' => array('MATCH.REFEREE', 'MATCH.ASSISTS', 'MATCH.PLAYERS_HOME', 'MATCH.PLAYERS_GUEST', 
											'MATCH.SUBSTITUTES_HOME', 'MATCH.SUBSTITUTES_GUEST', 'MATCH.COACH_HOME', 'MATCH.COACH_GUEST'),
			'operator' => OP_INSET_INT
		);
		$result = tx_cfcleague_util_ServiceRegistry::getMatchService()->search($fields, $options);
		if(count($result))
			$ret['tx_cfcleague_games'] = $result;

		$fields = array();
		$fields[SEARCH_FIELD_JOINED][0] = array(
			'value' => $profile->getUid(), 
			'cols' => array('MATCHNOTE.PLAYER_HOME', 'MATCHNOTE.PLAYER_GUEST'),
			'operator' => OP_EQ_INT
		);
		$result = tx_cfcleague_util_ServiceRegistry::getMatchService()->searchMatchNotes($fields, $options);
		if(count($result))
			$ret['tx_cfcleague_match_notes'] = $result;

		return $ret;
	}
	/**
	 * Returns all team notes for a given profile
	 *
	 * @param int $profileUID
	 */
	function getTeamsNotes4Profile($profileUID) {
		$options['where'] = 'player = ' . $profileUID;
		return tx_rnbase_util_DB::doSelect('*','tx_cfcleague_team_notes',$options);
	}

	/**
	 * Returns all available profile types for a TCA select item
	 *
	 * @return array 
	 */
	function getProfileTypes4TCA() {
		$items = array();
		$baseType = 't3sports_profiletype';
		$services = tx_rnbase_util_Misc::lookupServices($baseType);
		foreach ($services As $subtype => $info) {
			$srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
			$types = array_merge($items, $srv->getProfileTypes());
		}
		foreach($types AS $typedef) {
			$items[] = array(tx_rnbase_util_Misc::translateLLL($typedef[0]), $typedef[1]);
		}
		return $items;
	}
	/**
	 * Find all profile types for a given array with uids.
	 *
	 * @param array $uids
	 * @return string imploded uid|label-String for TCA select fields
	 */
	function getProfileTypeItems4TCA($uids) {
		$uidArr = array();
		foreach($uids As $uid) {
			$uidArr[$uid] = '';
		}
		
		$baseType = 't3sports_profiletype';
		$services = tx_rnbase_util_Misc::lookupServices($baseType);
		foreach ($services As $subtype => $info) {
			$srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
			$srv->setProfileTypeItems($uidArr);
		}
		$items = array();
		foreach($uidArr AS $uid => $label) {
			$items[] = $uid.'|'.tx_rnbase_util_Misc::translateLLL($label);
		}
		return implode(',',$items);
	}

	/**
	 * Search database for profiles
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array[tx_cfcleague_models_Profile]
	 */
	function search($fields, $options) {
		tx_rnbase::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Profile');
		return $searcher->search($fields, $options);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Profiles.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Profiles.php']);
}

?>