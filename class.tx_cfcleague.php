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

class tx_cfcleague_handleDataInput{

  /**
   * Liefert die Teams eines Wettbewerbs. Wird im Spiel-TCE-Dialog zur
   * Auswahl der Teams verwendet.
   */
  public function getTeams4Competition($PA, $fobj){
    // Aktuellen Wettbewerb ermitteln, wenn 0 bleiben die Felder leer

    if($PA['row']['competition'])
    {
      $teams = $this->findTeams($PA['row']['competition']);
      $PA[items] = $teams;
    }
    else
      $PA[items] = array();

//    tx_rnbase_util_Debug::debug($PA, 'cfcleague');

  }

  /**
   * Die Trainer des Heimteams ermitteln
   */
  function getCoachesHome4Match($PA, $fobj){
    if($PA['row']['home'])
    {
      $coaches = $this->findCoaches($PA['row']['home']);
      $PA[items] = $coaches;
    }
  }

  /**
   * Die Trainer des Gastteams ermitteln
   */
  function getCoachesGuest4Match($PA, $fobj){
    if($PA['row']['guest'])
    {
      $coaches = $this->findCoaches($PA['row']['guest']);
      $PA[items] = $coaches;
    }
  }

  /**
   * Die Spieler des Heimteams ermitteln
   * Used: Edit-Maske eines Spiels für Teamaufstellung und Match-Note
   */
  function getPlayersHome4Match($PA, $fobj){
    global $LANG;
    $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

// tx_rnbase_util_Debug::debug(count($PA[items]), 'items cfcleague');

    if($PA['row']['home'])
    {
      // Abfrage aus Spieldatensatz
      // Es werden alle Spieler des Teams benötigt
      $players = $this->findPlayers($PA['row']['home']);
      $PA[items] = $players;
    }
    else
      $PA[items] = array();
  }
  /**
   * Die Spieler des Gastteams ermitteln
   * Used: Edit-Maske eines Spiels für Teamaufstellung
   * @deprecated use tca_Lookup
   */
  function getPlayersGuest4Match($PA, $fobj){
    global $LANG;
    $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

    if($PA['row']['guest'])
    {
      $players = $this->findPlayers($PA['row']['guest']);
      $PA[items] = $players;
    }
    elseif($PA['row']['game']) {
      // Wenn wir die Match ID haben könne wir die Spieler auch so ermitteln
      require_once('class.tx_cfcleague_match.php');
      $match = new tx_cfcleague_match(tx_cfcleague_handleDataInput::getRowId($PA['row']['game']));
      $players = $match->getPlayerNamesGuest();
      $playerArr = array();
      foreach($players As $uid => $name) {
        $playerArr[] = Array($name, $uid);
      }
      $PA[items] = $playerArr;
      // Abschließend noch den Spieler "Unbekannt" hinzufügen!
      $PA[items][] = Array($LANG->getLL('tx_cfcleague.unknown'), '-1');
    }
    else // Ohne Daten müssen wir alle Spieler löschen
      $PA[items] = array();
  }

  /**
   * Sucht die Teams eines Wettbewerbs
   * @param complete_row wenn false wird nur Name und UID für SELECT-Box geliefert
   */
  function findTeams($competition, $complete_row = '0'){
    # build SQL for select
    $what = 'uid,name';
    $from = 'tx_cfcleague_teams';

    # WHERE
    # Finde die Teams, deren UID im übergebenen Wettbewerb vorkommt
    # Anm.: Wär das nicht auch mit einer einfach IN-Abfrage gegangen??
    # NEIN! Da der teams-String nicht richtig ausgewertet wird.
    $where = '
      FIND_IN_SET(tx_cfcleague_teams.uid,(
        SELECT tx_cfcleague_competition.teams FROM tx_cfcleague_competition
        WHERE tx_cfcleague_competition.uid =
      '.$competition . '))';

    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
      $what,
      $from,
      $where
    );


    $rows = array();
    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
      $rows[] = $complete_row ? $row : Array($row['name'], $row['uid'], );
    }
    $GLOBALS['TYPO3_DB']->sql_free_result($res);


    return $rows;
  }

	/**
	 * Liefert die Trainer (uid und name) einer Mannschaft.
	 */
	private function findCoaches($teamId) {
		$rows = array();
		if(intval($teamId) == 0) return $rows;

		$team = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $teamId);
		/* @var $profile tx_cfcleague_models_Profile */
		$profiles = $team->getCoaches();
		$rows[] = array(0, ''); // Leeres erstes Element
		foreach($profiles As $profile) {
      $rows[] = Array($profile->getName(), $profile->getUid(), );
		}

//     $coaches = $team->getCoachNames(1, 1); // firstEmpty und merge
//     foreach($coaches As $uid => $name) {
//       $rows[] = Array($name, $uid, );
//     }

		return $rows;
	}

	/**
	 * Liefert die Spieler (uid und name) einer Mannschaft.
	 */
	private function findPlayers($teamId) {
		$rows = array();
		if(intval($teamId) == 0) return $rows;

		/* @var $team tx_cfcleague_models_Team */
		$team = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $teamId);
		/* @var $profile tx_cfcleague_models_Profile */
		$profiles = $team->getPlayers();
		foreach($profiles As $profile) {
			$rows[] = Array($profile->getName(), $profile->getUid(), );
		}
// 		require_once('class.tx_cfcleague_team.php');
// 		$team = new tx_cfcleague_team($teamId);
// 		$players = $team->getPlayerNames(0, 1);
// 		foreach($players As $uid => $name) {
// 			$rows[] = Array($name, $uid, );
// 		}
		return $rows;
	}
	/**
	 * Liefert die Betreuer (uid und name) einer Mannschaft.
	 */
	private function findSupporters($teamId) {
		$rows = array();
		if(intval($teamId) == 0) return $rows;

		$team = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $teamId);
		/* @var $profile tx_cfcleague_models_Profile */
		$profiles = $team->getSupporters();
		foreach($profiles As $profile) {
			$rows[] = Array($profile->getName(), $profile->getUid(), );
		}


//     require_once('class.tx_cfcleague_team.php');
// 		$team = new tx_cfcleague_team($teamId);
// 		$players = $team->getSupporterNames(0, 1);
// 		foreach($players As $uid => $name) {
// 			$rows[] = Array($name, $uid, );
// 		}
		return $rows;
	}

  /**
   * Liefert die verschachtelte UID eines Strings der Form
   * tx_table_name_uid|valuestring
   */
  function getRowId($value) {
    $ret = Tx_Rnbase_Utility_Strings::trimExplode('|', $value);
    $ret = Tx_Rnbase_Utility_Strings::trimExplode('_', $ret[0]);
    return intval($ret[count($ret)-1]);
  }
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague.php']);
}

?>