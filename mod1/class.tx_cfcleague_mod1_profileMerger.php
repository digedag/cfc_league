<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2009 Rene Nitzsche <rene@system25.de>
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

require_once(t3lib_extMgm::extPath('cfc_league') . 'class.tx_cfcleague_db.php');
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
tx_rnbase::load('tx_cfcleague_team');
tx_rnbase::load('tx_cfcleague_match');
tx_rnbase::load('tx_rnbase_util_Misc');


class tx_cfcleague_mod1_profileMerger {

	/**
	 * Start merging profile. The leading profile will overtake all references
	 * of the obsolete profile. So at the end the should be no references to second
	 * profile anymore.
	 *
	 * @param int $leadingProfileUID UID of leading profile
	 * @param int $obsoleteProfileUID UID of obsolute profile
	 */
	function merge($leadingProfileUID, $obsoleteProfileUID) {
		// Alle Referenzen sollen auf das erste Profil übergehen
		// Tabellen:
		// tx_cfcleague_teams
		// tx_cfcleague_games
		// tx_cfcleague_match_notes
		// TODO: tx_cfcleague_teamnotes

		// Wir machen alles über die TCA, also das Array aufbauen
		$data = array();
		self::mergeTeams($data, $leadingProfileUID, $obsoleteProfileUID);
		self::mergeMatches($data, $leadingProfileUID, $obsoleteProfileUID);
		self::mergeMatchNotes($data, $leadingProfileUID, $obsoleteProfileUID);
		self::mergeTeamNotes($data, $leadingProfileUID, $obsoleteProfileUID);

		tx_rnbase_util_Misc::callHook('cfc_league', 'mergeProfiles_hook', 
			array('data' => &$data, 'leadingUid' => $leadingProfileUID, 'obsoleteUid' => $obsoleteProfileUID), $this);

    $tce =& tx_cfcleague_db::getTCEmain($data);
    $tce->process_datamap();

	}

	private function mergeTeamNotes(&$data, $leading, $obsolete) {
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();	
		$rows = $srv->getTeamsNotes4Profile($obsolete);
		foreach($rows As $row) {
			$data['tx_cfcleague_team_notes'][$row['uid']]['player'] = $leading;
		}
//		t3lib_div::debug($rows, 'tx_cfcleague_mod1_profileMerger'); // TODO: remove me
	}
	private function mergeMatchNotes(&$data, $leading, $obsolete) {
		$rows = tx_cfcleague_match::getMatchNotes4Profile($obsolete);
		foreach($rows As $row) {
			self::mergeField('player_home', 'tx_cfcleague_match_notes', $data, $row, $leading, $obsolete);
			self::mergeField('player_guest', 'tx_cfcleague_match_notes', $data, $row, $leading, $obsolete);
		}
	}
	
	private function mergeMatches(&$data, $leading, $obsolete) {
		$rows = tx_cfcleague_match::getMatches4Profile($obsolete);
		foreach($rows As $row) {
			self::mergeField('players_home', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('players_guest', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('substitutes_home', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('substitutes_guest', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('coach_home', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('coach_guest', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('referee', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
			self::mergeField('assists', 'tx_cfcleague_games', $data, $row, $leading, $obsolete);
		}
	}
	
	private function mergeTeams(&$data, $leading, $obsolete) {
		// Teams suchen, in denen obsolete spielt
		$teamRows = tx_cfcleague_team::getTeams4Profile($obsolete);
		foreach($teamRows As $row) {
			// Drei Felder können das Profile enthalten:
			// players
			self::mergeField('players', 'tx_cfcleague_teams', $data, $row, $leading, $obsolete);
			self::mergeField('coaches', 'tx_cfcleague_teams', $data, $row, $leading, $obsolete);
			self::mergeField('supporters', 'tx_cfcleague_teams', $data, $row, $leading, $obsolete);
		}
	}

	function mergeField($fieldName, $tableName, &$data, &$row, $leading, $obsolete) {
		$val = self::replaceUid($row[$fieldName], $leading, $obsolete);
		if(strlen($val))
			$data[$tableName][$row['uid']][$fieldName] = $val;
	}
	function replaceUid($fieldValue, $leading, $obsolete) {
		$ret = '';
		if(t3lib_div::inList($fieldValue, $obsolete)) {
			$values = t3lib_div::intExplode(',', $fieldValue);
			$idx = array_search($obsolete, $values);
			if($idx !== FALSE) {
				$values[$idx] = $leading;
			}
			$ret = implode(',', $values);
		}
		return $ret;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_profileMerger.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_profileMerger.php']);
}

?>