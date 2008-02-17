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

/**
 * Datenobjekt für eine Liga in der Datenbank
 */
class tx_cfcleague_league{
  var $uid;
  var $record;
  var $teamNames;
  /**
   * Konstruktor erwartet eine UID der Liga
   */
  function tx_cfcleague_league($uid){
    $this->uid = $uid;
    $this->record = t3lib_BEfunc::getRecord(TABLE_LEAGUES,$uid);
  }

  /**
   * Liefert die IDs der zugeordneten Teams als Array
   */
  function getTeamIds(){
    return t3lib_div::intExplode(',',$this->record['teams']);
  }
  /**
   * Liefert den Generation-String für die Liga
   */
  function getGenerationKey(){
    return $this->record['match_keys'];
  }

  /**
   * Liefert die Anzahl der Spielabschnitte in diesem Wettbewerb
   *
   * @return int
   */
  function getNumberOfMatchParts(){
    return intval($this->record['match_parts']) ? intval($this->record['match_parts']) : 2;
  }
  
  /**
   * Liefert die Namen der zugeordneten Teams als Array. Key ist die ID des Teams
   * @param $asArray Wenn 1 wird pro Team ein Array mit Name, Kurzname und Flag spielfrei geliefert
   */
  function getTeamNames($asArray = 0) {

    if(!is_array($this->teamNames[$asArray])){
// t3lib_div::debug('Erstelle neue Teams');
			// Ohne zugeordnete Team, muss nicht gefragt werden
			if($this->record['teams']) {
	      $rows = 
	         tx_cfcleague_db::queryDB('uid,name,short_name,dummy','uid IN (' . $this->record['teams'] . ')',
	              'tx_cfcleague_teams');
	      $this->teamNames[$asArray]= array();
	      foreach($rows As $row) {
	        $this->teamNames[$asArray][$row['uid']] = $asArray ? $row : $row['name'];
	      }
			}
			else
				$this->teamNames[$asArray] = array();
    }
    return $this->teamNames[$asArray];
  }
  /**
   * Wenn vorhanden, wird die ID des Spielfrei-Teams geliefert.
   * TODO: Mehrere spielfreie Teams pro Wettbewerb unterstützen
   * @return ID des Spielfrei-Teams oder 0
   */
  function hasDummyTeam() {
    $teams = $this->getTeamNames(1);
    foreach ($teams as $team) {
    	if($team['dummy']) return $team['uid'];
    }
    return 0;
  }
  /**
   * Liefert ein Array mit allen Spielrunden der Liga
   * return array
   */
  function getRounds(){
    # build SQL for select
    $what = 'round,round_name, max(status) AS max_status';
    
    # WHERE
    # Die UID der Liga setzen
    $where = 'competition="'.$this->uid.'"';
    $groupby = 'round,round_name';
    $orderby = 'round asc';
    
    return tx_cfcleague_db::queryDB($what,$where,
              TABLE_GAMES, $groupby,$orderby,0);
  }
  /**
   * Liefert die Anzahl der angesetzten Spielrunden
   *
   * @return int
   */
  function getNumberOfRounds() {
  	return count($this->getRounds());
  }
  function getLastMatchNumber() {
    $what = 'max(match_no) AS max_no';
    $where = 'competition="'.$this->uid.'"';
    $arr = tx_cfcleague_db::queryDB($what,$where,
              TABLE_GAMES, '','',0);
    return count($arr) ? $arr[0]['max_no'] : 0;
  }

  /**
   * Liefert ein Array mit Spielen der Liga. Wird round übergeben, dann werden nur die
   * Spiele dieses Spieltags geliefert
   */
  function getGames($round = ''){

    # build SQL for select
    $what = '*';
//    $from = TABLE_GAMES;
    
    # WHERE
    # Die UID der Liga setzen
    $where = 'competition="'.$this->uid.'"';
    if($round)
      $where .= ' AND round='.$round;

    return tx_cfcleague_db::queryDB($what,$where,
              TABLE_GAMES);
  }

  /**
   * Diese Funktion ermittelt die Spiele eines Spieltags. Die Namen der Teams werden aufgelöst.
   * @param int $round
   * @param boolean $ignoreFreeOfPlay
   */
  function getGamesByRound($round, $ignoreFreeOfPlay = false){
    $what = 'tx_cfcleague_games.uid,home,guest, t1.name AS name_home, t2.name AS name_guest, '.
            't1.short_name AS short_name_home, t1.dummy AS no_match_home, t2.short_name AS short_name_guest, t2.dummy AS no_match_guest, '.
            'goals_home_1,goals_guest_1,goals_home_2,goals_guest_2, '.
            'goals_home_3,goals_guest_3,goals_home_4,goals_guest_4, '.
    'goals_home_et,goals_guest_et,goals_home_ap,goals_guest_ap, visitors,date,status';
    $from = Array('tx_cfcleague_games ' .
              'INNER JOIN tx_cfcleague_teams t1 ON (home= t1.uid) ' . 
              'INNER JOIN tx_cfcleague_teams t2 ON (guest= t2.uid) ' 
              , 'tx_cfcleague_games');

              
    $where = 'competition="'.$this->uid.'"';
    $where .= ' AND round='.$round;
    if($ignoreFreeOfPlay) { // keine spielfreien Spiele laden
      $where .= ' AND t1.dummy = 0 AND t2.dummy = 0 ';
    }

    return tx_cfcleague_db::queryDB($what,$where,
              $from,'','',0);

/*
SELECT tx_cfcleague_games.uid, t1.name, t2.name, goals_home_1,goals_guest_1 
FROM `tx_cfcleague_games` 
INNER JOIN tx_cfcleague_teams AS t1 
INNER JOIN tx_cfcleague_teams AS t2
ON home= t1.uid
ON guest= t2.uid

$res = $TYPO3_DB->exec_SELECTquery(
	'sys_language.uid',
	'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode=static_languages.uid',
	'static_languages.lg_typo3='.$TYPO3_DB->fullQuoteStr($LANG->lang,'static_languages').
		t3lib_BEfunc::BEenableFields('sys_language').
		t3lib_BEfunc::deleteClause('sys_language').
		t3lib_BEfunc::deleteClause('static_languages')
				);
*/

  }

  function toString(){
    return "Liga mit der UID: " . $this->uid . ' Spiele: '. count($this->getGames());
  }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_league.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_league.php']);
}
?>
