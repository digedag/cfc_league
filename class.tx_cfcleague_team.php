<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('Tx_Rnbase_Utility_Strings');

define("TABLE_GAMES", "tx_cfcleague_games");
define("TABLE_LEAGUES", "tx_cfcleague_competition");
define("TABLE_TEAMS", "tx_cfcleague_teams");

/**
 * Datenobjekt für ein Spiel in der Datenbank
 * @deprecated use tx_cfcleague_models_Team
 * TODO: remove class
 */
class tx_cfcleague_team{
  var $uid;
  var $record;
  var $playerNames;
  var $coachNames;
  var $supporterNames;

  /**
   * Konstruktor erwartet eine UID der Liga
   */
  function tx_cfcleague_team($uid){
    $this->uid = $uid;
//    $this->record = t3lib_BEfunc::getRecord(TABLE_TEAMS, $uid);
    $this->refresh();
  }

  function getUid() {
  	return $this->uid;
  }
  /**
   * Lädt die Daten des Teams neu aus der Datenbank
   *
   */
  function refresh() {
	if(tx_rnbase_util_TYPO3::isTYPO76OrHigher())
		throw new Exception("deprecated\n<br/>". tx_rnbase_util_Debug::getDebugTrail());

    $this->record = t3lib_BEfunc::getRecord(TABLE_TEAMS, $this->uid);
    $this->playerNames = NULL;
    $this->coachNames = NULL;
    $this->supporterNames = NULL;
  }
  /**
   * Liefert die Namen alle Spieler des Teams als Array. Key ist die ID des Profils.
   * @param $firstEmpty wenn != 0 wird ein leerer Datensatz am Anfang eingefügt
   * @param $merge wenn != 0 werden Name und Vorname zusammengefügt
   */
  function getPlayerNames($firstEmpty=0, $merge=0) {

    if(!is_array($this->playerNames)){
      $this->playerNames = $this->retrievePlayers(
          $this->record['players'],
          $firstEmpty, $merge);
    }
    return $this->playerNames;
  }

  /**
   * Liefert die Namen der Trainer des Teams als Array. Key ist die ID des Profils.
   * @param $firstEmpty wenn != 0 wird ein leerer Datensatz am Anfang eingefügt
   * @param $merge wenn != 0 werden Name und Vorname zusammengefügt
   */
  function getCoachNames($firstEmpty=0, $merge=0) {
    if(!is_array($this->coachNames)){
      $this->coachNames = $this->retrievePlayers(
          $this->record['coaches'],
          $firstEmpty, $merge);
    }
    return $this->coachNames;
  }
  /**
   * Liefert die Namen der Betreuer des Teams als Array. Key ist die ID des Profils.
   *
   * @param int $firstEmpty wenn != 0 wird ein leerer Datensatz am Anfang eingefügt
   * @param int $merge wenn != 0 werden Name und Vorname zusammengefügt
   * @return unknown
   */
  function getSupporterNames($firstEmpty=0, $merge=0) {
    if(!is_array($this->supporterNames)){
      $this->supporterNames = $this->retrievePlayers(
          $this->record['supporters'],
          $firstEmpty, $merge);
    }
    return $this->supporterNames;
  }

	/**
	 * Lädt die Profile aus der Datenbank
	 * @param $pIds String mit den IDs der Spieler
	 * @param $firstEmpty Wenn != 0 wird am Anfang des ErgebnisArrays ein leerer Datensatz eingefügt
	 * @param $merge Wenn == 1 werden Name und Vorname zusammengesetzt
	 * @param $appendUnknown Wenn != 0 wird der DummySpieler "Unbekannt" mit der ID -1 am Ende hinzugefügt
	 * @return Array Key ist ID des Profils, Value der Name des Profils
	 */
	function retrievePlayers($pIds, $firstEmpty, $merge=1, $appendUnknown = 0) {
		$ret = array();
		if($firstEmpty != 0) $ret[0] = ''; // ggf. ein leeres Element am Anfang einfügen

		if(strlen($pIds) == 0) return $ret;

		$rows = tx_cfcleague_db::queryDB('uid, first_name, last_name', 'uid IN (' . $pIds . ')',
							'tx_cfcleague_profiles', 'last_name, first_name', '', 0);

		foreach($rows As $row) {
			if($merge)
				$ret[$row['uid']] = $row['last_name'] . ', ' . $row['first_name'];
			else
				$ret[$row['uid']] = $row;
		}

		if($appendUnknown) {
			global $LANG;
			$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');
			if($merge)
				$ret['-1'] = $LANG->getLL('tx_cfcleague.unknown');
			else
				$ret['-1'] = Array('uid' => '-1', 'last_name' => $LANG->getLL('tx_cfcleague.unknown'));
		}

		return $ret;
	}

	/**
	 * Returns all Teams where given profile is joined
	 *
	 * @param int $profileUID
	 * @deprecated use tx_cfcleague_services_Team::searchTeamsByProfile()
	 */
	function getTeams4Profile($profileUID) {
		$where = 'FIND_IN_SET(' . $profileUID . ', players) ';
		$where .= ' OR FIND_IN_SET(' . $profileUID . ', coaches) ';
		$where .= ' OR FIND_IN_SET(' . $profileUID . ', supporters) ';
		$rows = tx_cfcleague_db::queryDB('*', $where,
				'tx_cfcleague_teams', '', '', 0);
		return $rows;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_team.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_team.php']);
}
