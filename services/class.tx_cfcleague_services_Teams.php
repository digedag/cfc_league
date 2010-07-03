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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
require_once(PATH_t3lib.'class.t3lib_svbase.php');
tx_rnbase::load('tx_rnbase_util_SearchBase');
tx_rnbase::load('tx_rnbase_util_DB');
tx_rnbase::load('tx_cfcleague_search_Builder');


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
	public function getLogos($clubUid) {
		$fields['MEDIAREFMM.UID_FOREIGN'][OP_EQ_INT] = $clubUid;
		$fields['MEDIAREFMM.TABLENAMES'][OP_EQ] = 'tx_cfcleague_club';
		$fields['MEDIAREFMM.IDENT'][OP_EQ] = 'dam_images';
		$options['orderby']['MEDIAREFMM.SORTING_FOREIGN'] = 'asc';
		return $this->searchMedia($fields, $options);
	}

	/**
	 * Returns all team note types
	 * @return array[tx_cfcleague_models_TeamNoteType]
	 */
	public function getNoteTypes() {
		tx_rnbase::load('tx_cfcleague_models_TeamNoteType');
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
	 * Liefert die Namen der zugeordneten Teams als Array. Key ist die ID des Teams
	 * @param tx_cfcleague_models_Competition $comp
	 * @param $asArray Wenn 1 wird pro Team ein Array mit Name, Kurzname und Flag spielfrei geliefert
	 * @return array
	 */
	function getTeamNames($comp, $asArray = 0) {
		$teamNames = array();
		// Ohne zugeordnete Team, muss nicht gefragt werden
		if(!$comp->record['teams']) 
			return $teamNames;

		$fields = array();
		$fields['TEAM.UID'][OP_IN_INT] = $comp->record['teams'];
		$options = array();
		$options['what'] = 'uid,name,short_name,dummy,club';
		$rows = $this->searchTeams($fields, $options);
		foreach($rows As $row) {
			$teamNames[$row['uid']] = $asArray ? $row : $row['name'];
		}
		return $teamNames;
  }

	/**
	 * Returns the teams age group. This value is retrieved from the teams competitions. So 
	 * the first competition found, decides about the age group.
	 * @return tx_cfcleague_models_group or null
	 */
	public function getAgeGroup($team) {
		if(!is_object($team) || !$team->isValid()) return null;
		
		tx_rnbase::load('tx_cfcleague_models_Group');
		tx_rnbase::load('tx_rnbase_cache_Manager');
		$cache = tx_rnbase_cache_Manager::getCache('t3sports');
		$agegroup = $cache->get('team_'.$team->getUid());
		if(!$agegroup) {
			if(intval($team->record['agegroup']))
				$agegroup = tx_cfcleague_models_Group::getInstance($this->record['agegroup']);
			if(!$agegroup) {
				$comps = $this->getCompetitions4Team($team, true);
				for($i=0, $cnt = count($comps); $i < $cnt; $i++) {
					if(is_object($comps[$i]->getGroup())) {
						$agegroup = $comps[$i]->getGroup();
						break;
					}
				}
			}
			$cache->set('team_'.$team->getUid(), $agegroup, 600); // 10 minutes
		}
		return $agegroup;
	}

	/**
	 * Returns the competitons of this team
	 * @param tx_cfcleague_models_Team
	 * @param boolean $obligateOnly if true, only obligate competitions are returned
	 * @return array of tx_cfcleaguefe_models_competition
	 */
	public function getCompetitions4Team($team, $obligateOnly = false) {
		$fields = array();
		tx_cfcleague_search_Builder::buildCompetitionByTeam($fields, $team->getUid(),$obligateOnly);
		$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
		return $srv->search($fields, $options);
	}

	/**
	 * Search database for team notes
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array[tx_cfcleague_models_TeamNote]
	 */
	function searchTeamNotes($fields, $options) {
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_TeamNote');
		return $searcher->search($fields, $options);
	}

	/**
	 * Search database for teams
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array[tx_cfcleague_models_Team]
	 */
	function searchTeams($fields, $options) {
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Team');
		return $searcher->search($fields, $options);
	}

	/**
	 * Search database for clubs
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array[tx_cfcleague_models_Club]
	 */
	function searchClubs($fields, $options) {
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Club');
		return $searcher->search($fields, $options);
	}

	function searchMedia($fields, $options) {
		tx_rnbase::load('tx_rnbase_util_SearchBase');
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Media');
		return $searcher->search($fields, $options);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Teams.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Teams.php']);
}

?>