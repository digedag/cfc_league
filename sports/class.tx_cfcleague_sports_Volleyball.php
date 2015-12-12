<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2015 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_cfcleague_sports_ISports');
tx_rnbase::load('Tx_Rnbase_Service_Base');

/**
 */
class tx_cfcleague_sports_Volleyball extends Tx_Rnbase_Service_Base implements tx_cfcleague_sports_ISports {

	/**
	 * Get match provider
	 * @return tx_cfcleaguefe_table_ITableType or null
	 */
	public function getLeagueTable() {
		if(tx_rnbase_util_Extensions::isLoaded('cfc_league_fe')) {
			return tx_rnbase::makeInstance('tx_cfcleaguefe_table_volleyball_Table');
		}
		return null;
	}
	/**
	 * @return array
	 */
	public function getTCAPointSystems() {
		return array(
			Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2'), 0),
			Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_3'), 1)
		);
	}
	public function getTCALabel() {
		return 'Volleyball';
	}
	public function isSetBased() {
		return true;
	}

	private $matchInfo = NULL;
	/* (non-PHPdoc)
	 * @see tx_cfcleague_sports_ISports::getMatchInfo()
	 */
	public function getMatchInfo() {
		if($this->matchInfo == NULL) {
			// Bei Volleyball gibt es keine festen Zeiten
			$this->matchInfo = tx_rnbase::makeInstance('tx_cfcleague_sports_MatchInfo', array(
			));
		}

		return $this->matchInfo;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/sports/class.tx_cfcleague_sports_Volleyball.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/sports/class.tx_cfcleague_sports_Volleyball.php']);
}
