<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model for a match.
 */
class tx_cfcleague_models_Match extends tx_rnbase_model_base {

	private $sets;
	private $resultInited = false;
	
	function __construct($rowOrUid) {
		parent::__construct($rowOrUid);
	}

	function getTableName(){return 'tx_cfcleague_games';}

	public function getGoalsHome($matchPart = '') {
		$this->initResult();
		$ret = $this->record['goals_home'];
		if(strlen($matchPart))
			$ret = $this->record['goals_home_'.(($matchPart == 'last') ? $this->record['matchparts'] : $matchPart) ];
		return $ret;
	}
	public function getGoalsGuest($matchPart = '') {
		$this->initResult();
		$ret = $this->record['goals_guest'];
		if(strlen($matchPart))
			$ret = $this->record['goals_guest_'.(($matchPart == 'last') ? $this->record['matchparts'] : $matchPart) ];
		return $ret;
	}

	/**
	 * Liefert den TOTO-Wert des Spiels. Als 0 für ein Unentschieden, 1 für einen Heim- 
	 * und 2 für einen Auswärstsieg.
	 * @param string $matchPart The matchpart is 1,2,3...,et,ap,last
	 */
	public function getToto($matchPart = '') {
		$goalsHome = $this->getGoalsHome($matchPart);
		$goalsGuest = $this->getGoalsGuest($matchPart);

		$goalsDiff = $goalsHome - $goalsGuest;

		if($goalsDiff == 0)
			return 0;
		return ($goalsDiff < 0) ? 2 : 1;
	}

	/**
	 * Notwendige Initialisierung für das Ergebnis des Spieldatensatzes
	 *
	 */
	public function initResult() {
		if($this->resultInited) return;

		// Um das Endergebnis zu ermitteln, muss bekannt sein, wieviele Spielabschnitte
		// es gibt. Dies steht im Wettbewerb
		$comp = $this->getCompetition();
		$this->record['matchparts'] = $comp->getMatchParts();
		if($comp->isAddPartResults())
			$this->initResultAdded($comp, $comp->getMatchParts());
		else
			$this->initResultSimple($comp, $comp->getMatchParts());
		$this->resultInited = true;
	}
	/**
	 * Init result and expect the endresult in last match part.
	 * @param tx_cfcleague_models_Competition $comp
	 * @param int $matchParts
	 */
	private function initResultSimple($comp, $matchParts) {
		$goalsHome = $this->record['goals_home_'.$matchParts];
		$goalsGuest = $this->record['goals_guest_'.$matchParts];
		// Gab es Verländerung oder Elfmeterschiessen
		if($this->isPenalty()) {
			$goalsHome = $this->record['goals_home_ap'];
			$goalsGuest = $this->record['goals_guest_ap'];
		}
		elseif($this->isExtraTime()) {
			$goalsHome = $this->record['goals_home_et'];
			$goalsGuest = $this->record['goals_guest_et'];
		}
		$this->record['goals_home'] = $goalsHome;
		$this->record['goals_guest'] = $goalsGuest;
	}
	/**
	 * Init result and add all matchpart results.
	 * @param tx_cfcleague_models_Competition $comp
	 * @param int $matchParts
	 */
	private function initResultAdded($comp, $matchParts) {
		$goalsHome = 0;
		$goalsGuest = 0;

		// Teilergebnisse holen
		$matchParts = $matchParts > 0 ? $matchParts : 1;
		for($i=1; $i<=$matchParts; $i++) {
			$goalsHome += $this->record['goals_home_'.$i];
			$goalsGuest += $this->record['goals_guest_'.$i];
		}
		// Gab es Verländerung oder Elfmeterschiessen
		if($this->isPenalty()) {
			$goalsHome += $this->record['goals_home_ap'];
			$goalsGuest += $this->record['goals_guest_ap'];
		}
		elseif($this->isExtraTime()) {
			$goalsHome += $this->record['goals_home_et'];
			$goalsGuest += $this->record['goals_guest_et'];
		}
		$this->record['goals_home'] = $goalsHome;
		$this->record['goals_guest'] = $goalsGuest;
	}
	/**
	 * 
	 * @return string
	 */
	public function getResult() {
		return $this->record['status'] > 0 ? $this->getGoalsHome() .' : ' . $this->getGoalsGuest() : '- : -';
	}
	/**
	 * Return sets if available
	 * @return array[tx_cfcleague_models_Set]
	 */
	public function getSets() {
		if(!is_array($this->sets)) {
			tx_rnbase::load('tx_cfcleague_models_Set');
			$this->sets = tx_cfcleague_models_Set::buildFromString($this->record['sets']);
			$this->sets = $this->sets ? $this->sets : array();
		}
		return $this->sets;
	}

	/**
	 * Liefert die Spieler des Heimteams der Startelf 
	 * @param $all wenn true werden auch die Ersatzspieler mit geliefert
	 * @return string comma separated uids
	 */
	public function getPlayersHome($all = false) {
		$ids = $this->record['players_home'];
		if($all &&  strlen($this->record['substitutes_home']) > 0){
			// Auch Ersatzspieler anhängen
			if(strlen($ids) > 0)
				$ids = $ids . ',' . $this->record['substitutes_home'];
		}
		return $ids;
	}
	/**
	 * Liefert die Spieler des Gastteams der Startelf 
	 * @param $all wenn true werden auch die Ersatzspieler mit geliefert
	 * @return string comma separated uids
	 */
	public function getPlayersGuest($all = false) {
		$ids = $this->record['players_guest'];
		if($all &&  strlen($this->record['substitutes_guest']) > 0){
			// Auch Ersatzspieler anhängen
			if(strlen($ids) > 0)
				$ids = $ids . ',' . $this->record['substitutes_guest'];
		}
		return $ids;
	}
	/**
	 * Returns the competition
 	 *
	 * @return tx_cfcleague_models_Competition
	 */
	public function getCompetition() {
		if(!$this->competition) {
			tx_rnbase::load('tx_cfcleague_models_Competition');
			$this->competition = tx_cfcleague_models_Competition::getInstance($this->record['competition']);
		}
		return $this->competition;
	}
	public function setCompetition($competition) {
		$this->competition = $competition;
	}

	/**
	 * Liefert das Heim-Team als Objekt
	 * @return tx_cfcleague_models_Team
	 */
	public function getHome() {
		if(!$this->_teamHome) {
			$this->_teamHome = $this->getTeam($this->record['home']);
		}
		return $this->_teamHome;
	}

	/**
	 * Setzt das Heim-Team
	 */
	public function setHome($team) {
		$this->_teamHome = $team;
	}

	/**
	 * Liefert das Gast-Team als Objekt
	 * @return tx_cfcleague_models_Team
	 */
	public function getGuest() {
		if(!$this->_teamGuest) {
			$this->_teamGuest = $this->getTeam($this->record['guest']);
		}
		return $this->_teamGuest;
	}
	/**
	 * Setzt das Gast-Team
	 */
	public function setGuest($team) {
		$this->_teamGuest = $team;
	}
	/**
	 * Liefert das Team als Objekt
	 * @return tx_cfcleague_models_Team
	 */
	private function getTeam($uid) {
		if(!$uid) throw new Exception('Invalid match with uid ' . $this->getUid() . ': At least one team is not set.');
		tx_rnbase::load('tx_cfcleague_models_Team');
		$team = tx_cfcleague_models_Team::getInstance($uid);
		return $team;
	}
	public function getHomeNameShort() {
		return $this->getHome()->getNameShort();
	}
	public function getGuestNameShort() {
		return $this->getGuest()->getNameShort();
	}
	/**
	 * Returns true if match is finished
	 *
	 * @return boolean
	 */
	public function isFinished(){
		return intval($this->record['status']) == 2;
	}
	/**
	 * Returns true if match is running
	 *
	 * @return boolean
	 */
	public function isRunning() {
		return intval($this->record['status']) == 1;
	}
	/**
	 * Returns true if match has extra time
	 *
	 * @return boolean
	 */
	public function isExtraTime() {
		return intval($this->record['is_extratime']) == 1;
	}
	/**
	 * Returns true if match has extra time
	 *
	 * @return boolean
	 */
	public function isPenalty() {
		return intval($this->record['is_penalty']) == 1;
	}
	/**
	 * Returns true of match is a dummy (free of play).
	 *
	 * @return boolean
	 */
	public function isDummy() {
		return $this->getHome()->isDummy() || $this->getGuest()->isDummy();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Match.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Match.php']);
}

?>