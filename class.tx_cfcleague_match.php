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


define("TABLE_GAMES", "tx_cfcleague_games");
define("TABLE_LEAGUES", "tx_cfcleague_competition");
define("TABLE_TEAMS", "tx_cfcleague_teams");

require_once('class.tx_cfcleague_team.php');
require_once('class.tx_cfcleague_db.php');


/**
 * Datenobjekt für eine Spiel in der Datenbank
 */
class tx_cfcleague_match{
  var $uid;
  var $record;
  var $playerNamesHome;
  var $playerNamesGuest;

  /**
   * Konstruktor erwartet eine UID der Liga
   */
  function tx_cfcleague_match($uid){
    $this->uid = $uid;
    $this->record = t3lib_BEfunc::getRecord(TABLE_GAMES,$uid);
  }

  /**
   * Liefert die vorhandenen Tickermeldungen zu diesem Spiel.
   * @param $limit
   * @param $desc
   */
  function getMatchNotes($limit = 5, $orderBy = 'minute desc, extra_time desc, uid desc') {
    $what = '*';
    
    # WHERE
    # Die UID der Liga setzen
    $where = 'game="'.$this->uid.'"';

    return tx_cfcleague_db::queryDB($what,$where,
              'tx_cfcleague_match_notes', '', $orderBy, 0, $limit);

  }

  /**
   * Liefert die Namen alle Spieler des Heimteams als Array. Key ist die ID des Profils.
   * @param $firstEmpty Wenn != 0 wird am Anfang des ErgebnisArrays ein leerer Datensatz eingefügt
   * @param $appendUnknown Wenn != 0 wird der DummySpieler "Unbekannt" mit der ID -1 am Ende hinzugefügt
   */
  function getPlayerNamesHome($firstEmpty=1, $appendUnknown=0) {

    if(!is_array($this->playerNamesHome)){
      $this->playerNamesHome = tx_cfcleague_team::retrievePlayers(
          $this->mergeIdStrings($this->record['players_home'],$this->record['substitutes_home']), 
          $firstEmpty,'1',$appendUnknown);
    }
    return $this->playerNamesHome;

  }

  /**
   * Liefert die Namen alle Spieler des Gastteams als Array. Key ist die ID des Profils.
   * @param $firstEmpty Wenn != 0 wird am Anfang des ErgebnisArrays ein leerer Datensatz eingefügt
   * @param $appendUnknown Wenn != 0 wird der DummySpieler "Unbekannt" mit der ID -1 am Ende hinzugefügt
   */
  function getPlayerNamesGuest($firstEmpty=1, $appendUnknown=0) {

    if(!is_array($this->playerNamesGuest)){
      $this->playerNamesGuest = tx_cfcleague_team::retrievePlayers(
          $this->mergeIdStrings($this->record['players_guest'],$this->record['substitutes_guest']), 
          $firstEmpty,'1',$appendUnknown);
    }
    return $this->playerNamesGuest;

  }

  // ab jetzt Private

  /**
   * Merged zwei ID-Strings zu einem gemeinsamen ID-String
   */
  function mergeIdStrings($str1, $str2){
    $arr = t3lib_div::intExplode(',',$str1);
    $arr = array_merge($arr,t3lib_div::intExplode(',',$str2));
    return implode(',',$arr);
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_match.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_match.php']);
}
?>
