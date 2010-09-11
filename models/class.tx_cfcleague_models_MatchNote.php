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
 * Model for a match note/event.
 */
class tx_cfcleague_models_MatchNote extends tx_rnbase_model_base {

	function getTableName(){return 'tx_cfcleague_match_notes';}

	/**
	 * Liefert die UID des Profils, des an der Aktion beteiligten Spielers der Heimmannschaft.
	 * @return int
	 */
	public function getPlayerHome() {
		return intval($this->record['player_home']);
	}

	/**
	 * Liefert die UID des Profils, des an der Aktion beteiligten Spielers der Gastmannschaft
	 * @return int
	 */
	public function getPlayerGuest() {
		return intval($this->record['player_guest']);
	}

	/**
	 * Liefert die UID des Spielers, dem diese Meldung zugeordnet ist.
	 * @return int
	 */
	public function getPlayer() {
		if($this->isHome())
			return $this->getPlayerHome();
		if($this->isGuest())
			return $this->getPlayerGuest();
		return 0;
	}

	/**
	 * Liefert den Typ der Meldung
	 * @return int den Typ der Meldung
	 */
	public function getType() {
		return intval($this->record['type']);
	}
	/**
	 * Liefert die Minute der Meldung
	 * @return int
	 */
	public function getMinute() {
		return $this->record['minute'];
	}
	/**
	 * Liefert true wenn die Aktion dem Heimteam zugeordnet ist
	 * @return boolean
	 */
	public function isHome() {
		return $this->record['player_home'] > 0 || $this->record['player_home'] == -1;
	}

	/**
	 * Liefert true wenn die Aktion dem Gastteam zugeordnet ist
	 * @return boolean
	 */
	public function isGuest() {
		return $this->record['player_guest'] > 0 || $this->record['player_guest'] == -1;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_MatchNote.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_MatchNote.php']);
}

?>