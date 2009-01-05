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

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');
require_once(PATH_t3lib.'class.t3lib_svbase.php');
tx_div::load('tx_rnbase_util_DB');


/**
 * Service for accessing teams
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Teams extends t3lib_svbase {

	/**
	 * Returns all stadiums for a team.
	 * This works only if a club is referenced by this team
	 *
	 * @param int $teamUid
	 * @return array[tx_cfcleague_models_stadium]
	 */
	public function getStadiums($teamUid) {
		$fields['TEAM.UID'][OP_EQ_INT] = $teamUid;
		$options['orderby']['STADIUM.NAME'] = 'asc';
		$srv = tx_cfcleague_util_ServiceRegistry::getStadiumService();
		return $srv->search($fields, $options);
	}

	/**
	 * Returns all team note types
	 * @return array[tx_cfcleague_models_TeamNoteType]
	 */
	public function getNoteTypes() {
		tx_div::load('tx_cfcleague_models_TeamNoteType');
		return tx_cfcleague_models_TeamNoteType::getAll();
	}
	/**
	 * Returns all teamnotes for with team with specific type
	 *
	 * @param tx_cfcleague_models_Team $team
	 * @param tx_cfcleague_models_TeamNoteType $type
	 */
	public function getTeamNotes($team, $type=false) {
		$fields['TEAMNOTE.TEAM'][OP_EQ_INT] = $team->getUid();
		if(is_object($type))
			$fields['TEAMNOTE.TYPE'][OP_EQ_INT] = $type->getUid();
		$options = array();
		return $this->searchTeamNotes($fields, $options);
	}
	/**
	 * Search database for team notes
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array[tx_cfcleague_models_TeamNote]
	 */
	function searchTeamNotes($fields, $options) {
		tx_div::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_TeamNote');
		return $searcher->search($fields, $options);
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Teams.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Teams.php']);
}

?>