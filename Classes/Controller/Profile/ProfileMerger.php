<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2016 Rene Nitzsche <rene@system25.de>
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

tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
tx_rnbase::load('tx_rnbase_util_Misc');
tx_rnbase::load('Tx_Rnbase_Utility_Strings');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');


class Tx_Cfcleague_Controller_Profile_ProfileMerger {

	/**
	 * Start merging profile. The leading profile will overtake all references
	 * of the obsolete profile. So at the end the should be no references to second
	 * profile anymore.
	 *
	 * @param int $leadingProfileUID UID of leading profile
	 * @param int $obsoleteProfileUID UID of obsolute profile
	 */
	public function merge($leadingProfileUID, $obsoleteProfileUID) {
		// Alle Referenzen sollen auf das erste Profil übergehen
		// Tabellen:
		// tx_cfcleague_teams
		// tx_cfcleague_games
		// tx_cfcleague_match_notes
		// TODO: tx_cfcleague_teamnotes

		// Wir machen alles über die TCA, also das Array aufbauen
		$data = array();
		$this->mergeTeams($data, $leadingProfileUID, $obsoleteProfileUID);
		$this->mergeMatches($data, $leadingProfileUID, $obsoleteProfileUID);
		$this->mergeMatchNotes($data, $leadingProfileUID, $obsoleteProfileUID);
		$this->mergeTeamNotes($data, $leadingProfileUID, $obsoleteProfileUID);

		tx_rnbase_util_Misc::callHook('cfc_league', 'mergeProfiles_hook',
			array('data' => &$data, 'leadingUid' => $leadingProfileUID, 'obsoleteUid' => $obsoleteProfileUID), $this);

		tx_rnbase::load('Tx_Rnbase_Database_Connection');
		$tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
		$tce->process_datamap();
	}

	private function mergeTeamNotes(&$data, $leading, $obsolete) {
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		$rows = $srv->getTeamsNotes4Profile($obsolete);
		foreach($rows As $row) {
			$data['tx_cfcleague_team_notes'][$row['uid']]['player'] = $leading;
		}
	}
	private function mergeMatchNotes(&$data, $leading, $obsolete) {
		$rows = tx_cfcleague_util_ServiceRegistry::getMatchService()->searchMatchNotesByProfile($obsolete);
		foreach($rows As $matchNote) {
			$this->mergeField('player_home', 'tx_cfcleague_match_notes', $data, $matchNote->getRecord(), $leading, $obsolete);
			$this->mergeField('player_guest', 'tx_cfcleague_match_notes', $data, $matchNote->getRecord(), $leading, $obsolete);
		}
	}

	private function mergeMatches(&$data, $leading, $obsolete) {
		$rows = tx_cfcleague_util_ServiceRegistry::getMatchService()->searchMatchesByProfile($obsolete);
		foreach($rows As $match) {
			$this->mergeField('players_home', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('players_guest', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('substitutes_home', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('substitutes_guest', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('coach_home', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('coach_guest', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('referee', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
			$this->mergeField('assists', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
		}
	}

	private function mergeTeams(&$data, $leading, $obsolete) {
		// Teams suchen, in denen obsolete spielt
		$teamRows = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchTeamsByProfile($obsolete);
		foreach($teamRows As $team) {
			// Drei Felder können das Profile enthalten:
			// players
			$this->mergeField('players', 'tx_cfcleague_teams', $data, $team->getRecord(), $leading, $obsolete);
			$this->mergeField('coaches', 'tx_cfcleague_teams', $data, $team->getRecord(), $leading, $obsolete);
			$this->mergeField('supporters', 'tx_cfcleague_teams', $data, $team->getRecord(), $leading, $obsolete);
		}
	}

	private function mergeField($fieldName, $tableName, &$data, $row, $leading, $obsolete) {
		$val = $this->replaceUid($row[$fieldName], $leading, $obsolete);
		if(strlen($val))
			$data[$tableName][$row['uid']][$fieldName] = $val;
	}
	private function replaceUid($fieldValue, $leading, $obsolete) {
		$ret = '';
		if(Tx_Rnbase_Utility_T3General::inList($fieldValue, $obsolete)) {
			$values = Tx_Rnbase_Utility_Strings::intExplode(',', $fieldValue);
			$idx = array_search($obsolete, $values);
			if($idx !== FALSE) {
				$values[$idx] = $leading;
			}
			$ret = implode(',', $values);
		}
		return $ret;
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_profileMerger.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_profileMerger.php']);
}
