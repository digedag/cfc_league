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

tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model für einen Wettbewerb.
 */
class tx_cfcleague_models_Competition extends tx_rnbase_model_base {
	private static $instances = array();
	/** array of teams */
	private $teams;
	/**
	 * array of matches
	 * Containes retrieved matches by state
	 */
	private $matchesByState = array();
	/** array of penalties */
	private $penalties;

	function getTableName(){return 'tx_cfcleague_competition';}

	public function getSaisonUid() {
		return $this->record['saison'];
	}
	/**
	 * Liefert alle Spiele des Wettbewerbs mit einem bestimmten Status.
	 * Der Status kann sein:
	 * <ul>
	 * <li> 0 - angesetzt
	 * <li> 1 - läuft
	 * <li> 2 - beendet
	 * </ul>
	 * @param scope - 0,1,2 für alle, Hin-, Rückrunde
	 */
	public function getMatches($status, $scope=0) {
		// Sicherstellen, dass wir eine Zahl bekommen
		if((isset($status) && t3lib_div::testInt($status))) {
			$status = intval($status);
			// Wir laden die Spieldaten zunächst ohne die Teams
			// Um die Datenmenge in Grenzen zu halten
			$round = 0;
			$scope = intval($scope);
			if($scope) {
				// Feststellen wann die Hinrunde endet: Anz Teams - 1
				$round = count(t3lib_div::intExplode(',',$this->record['teams']));
				$round = ($round) ? $round - 1 : $round;
			}
			// Check if data is already cached
			if(!is_array($this->matchesByState[$status . '_' . $scope])) {
				$what = '*';
				# Die UID der Liga setzen
				$where = 'competition="'.$this->uid.'" ';
				switch($status) {
					case 1:
						$where .= ' AND status>="' . $status . '"';
						break;
					default:
						$where .= ' AND status="' . $status . '"';
				}
				if($scope && $round) {
					switch($scope) {
						case 1:
							$where .= ' AND round<="' . $round . '"';
							break;
						case 2:
							$where .= ' AND round>"' . $round . '"';
							break;
					}
				}
				$options['where'] = $where;
				$options['wrapperclass'] = 'tx_cfcleaguefe_models_match';
				// Issue 1880237: Return matches sorted by round
				$options['orderby'] = 'round, date';
				$this->matchesByState[$status . '_' . $scope] = tx_rnbase_util_DB::doSelect($what,'tx_cfcleague_games',$options, 0);
			}
			return $this->matchesByState[$status . '_' . $scope];
		}
	}

	function getName() {
		return $this->record['name'];
	}
	function getInternalName() {
		$ret = $this->record['internal_name'];
		$ret = strlen($ret) ? $ret : $this->record['short_name'];
		$ret = strlen($ret) ? $ret : $this->record['name'];
		return $ret;
	}
	/**
	 * Set matches for a state and scope
	 *
	 * @param array $matchesArr
	 * @param int $status
	 * @param int $scope
	 */
	function setMatches($matchesArr, $status, $scope = 0) {
 		$this->matchesByState[intval($status) . '_' . intval($scope)] = is_array($matchesArr) ? $matchesArr : NULL;
	}

	/**
	 * Whether or not this competition is type league
	 * @return boolean
	 */
	function isTypeLeague() {
		return $this->record['type'] == 1;
	}
	/**
	 * Whether or not this competition is type league
	 * @return boolean
	 */
	function isTypeCup() {
		return $this->record['type'] == 2;
	}
	/**
	 * Whether or not this competition is type league
	 * @return boolean
	 */
	function isTypeOther() {
		return $this->record['type'] == 0;
	}
	/**
	 * Returns the number of match parts. Default is two.
	 *
	 * @return int
   */
	public function getMatchParts() {
		$parts = intval($this->record['match_parts']);
		return $parts > 0 ? $parts : 2;
	}
	/**
	 * Whether or not the match result should be calculated from part results.
	 *
	 * @return boolean
   */
	public function isAddPartResults() {
		return intval($this->record['addparts']) > 0;
	}
	/**
	 * Liefert die Anzahl der Spielrunden
	 *
	 * @return int
	 */
	public function getNumberOfRounds() {
		return count($this->getRounds());
	}
	/**
	 * Liefert ein Array mit allen Spielrunden der Liga
	 * 
	 * @return array
	 */
	public function getRounds(){
		if(!array_key_exists('rounds', $this->cache)) {
			$srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
			# build SQL for select
			$options = array();
			$options['what'] = 'distinct round as uid,round AS number,round_name As name, max(status) As finished';
			$options['groupby'] = 'round,round_name';
			$options['orderby']['MATCHROUND.ROUND'] = 'asc';
			$fields = array();
			$fields['MATCHROUND.COMPETITION'][OP_EQ_INT] = $this->uid;
			$this->cache['rounds'] = $srv->searchMatchRound($fields, $options);
		}
		return $this->cache['rounds'];
	}
	/**
	 * Liefert die Spiele einer bestimmten Spielrunde
	 *
	 * @param int $roundId
	 */
	public function getMatchesByRound($roundId) {
		$fields = array();
		$options = array();
	  $fields['MATCH.ROUND'][OP_EQ_INT] = $roundId;
	  $fields['MATCH.COMPETITION'][OP_EQ_INT] = $this->uid;
//	  $options['debug'] = 1;
		$service = tx_cfcleague_util_ServiceRegistry::getMatchService();
  	$matches = $service->search($fields, $options);
//t3lib_div::debug($roundId, 'tx_cfcleaguefe_models_competition'); // TODO: Remove me!
  	return $matches;
  }
  /**
   * Returns the last match number
   * @return int
   */
	function getLastMatchNumber() {
		$fields = array();
	  $fields['MATCH.COMPETITION'][OP_EQ_INT] = $this->uid;
		$options = array();
		//$options['debug'] =1;
	  $options['what'] = 'max(convert(match_no,signed)) AS max_no';
		$srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
	  $arr = $srv->search($fields, $options);
		return count($arr) ? $arr[0]['max_no'] : 0;
	}

  private $cache = array();
  /**
   * Liefert ein Array mit UIDs der Dummy-Teams.
   * @return array
   */
	public function getDummyTeamIds() {
		if(!array_key_exists('dummyteamids', $this->cache)) {
			$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
			$this->cache['dummyteamids'] = $srv->getDummyTeamIds($this);
		}
		return $this->cache['dummyteamids'];
	}
	/**
	 * Liefert die Namen der zugeordneten Teams als Array. Key ist die ID des Teams
	 * @param int $asArray Wenn 1 wird pro Team ein Array mit Name, Kurzname und Flag spielfrei geliefert
	 * @return array
	 */
	public function getTeamNames($asArray = 0) {
		$key = 'teamnames'.$asArray;
		if(!array_key_exists($key, $this->cache)){
			$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
			$this->cache[$key] = $srv->getTeamNames($this, $asArray);
    }
    return $this->cache[$key];
  }

  /**
   * Anzahl der Spiele des/der Teams in diesem Wettbewerb
   */
  public function getNumberOfMatches($teamIds, $status = '0,1,2'){
		if(!array_key_exists('numofmatches', $this->cache)) {
			$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
			$this->cache['numofmatches'] = $srv->getNumberOfMatches($this, $teamIds, $status);
		}
		return $this->cache['numofmatches'];
  }

	/**
	 * Returns the first agegroup of this competition
	 *
	 * @return tx_cfcleaguefe_models_group
	 */
	public function getGroup() {
		tx_rnbase::load('tx_cfcleague_models_Group');
		$groupIds = t3lib_div::intExplode(',',$this->record['agegroup']);
		return count($groupIds) ? tx_cfcleague_models_Group::getInstance($groupIds[0]) : false;
	}
	/**
	 * Returns the uid of first agegroup of this competition
	 *
	 * @return int
	 */
	public function getFirstGroupUid() {
		tx_rnbase::load('tx_cfcleague_models_Group');
		$groupIds = t3lib_div::intExplode(',',$this->record['agegroup']);
		return count($groupIds) ? $groupIds[0] : 0;
	}
	/**
	 * Returns the agegroups of this competition
	 *
	 * @return array[tx_cfcleaguefe_models_group]
	 */
	public function getGroups() {
		tx_rnbase::load('tx_cfcleaguefe_models_group');
		$groupIds = t3lib_div::intExplode(',',$this->record['agegroup']);
		$ret = array();
		foreach($groupIds As $groupId) {
			$ret[] = tx_cfcleaguefe_models_group::getInstance($groupId);
		}
  	return $ret;
	}
	/**
	 * Returns all team participating this competition.
	 * @return array[tx_cfcleaguefe_models_team]
	 */
	public function getTeams($ignoreDummies = true) {
		if(!is_array($this->teams)) {
			$uids = $this->record['teams'];
			$options['where'] = 'uid IN (' . $uids .') ';
			if($ignoreDummies)
				$options['where'] .= ' AND dummy = 0  ';
			$options['wrapperclass'] = 'tx_cfcleaguefe_models_team';
			$options['orderby'] = 'sorting';
			$this->teams = tx_rnbase_util_DB::doSelect('*','tx_cfcleague_teams',$options, 0);
		}
		return $this->teams;
	}
	/**
	 * Returns all team ids as array
	 *
	 * @return array[int]
	 */
	function getTeamIds() {
		return t3lib_div::intExplode(',',$this->record['teams']);
	}
	/**
	 * Liefert den Generation-String für die Liga
	 */
	function getGenerationKey(){
		return $this->record['match_keys'];
	}

  /**
   * Set participating teams. This is usually not necessary, since getTeams() 
   * makes an automatic lookup in database.
   *
   * @param array $teamsArr if $teamsArr is no array the internal array is removed
   */
  function setTeams($teamsArr) {
    $this->teams = is_array($teamsArr) ? $teamsArr : NULL;
  }
  /**
   * Returns an instance of tx_cfcleague_models_competition
   * @param int $uid
   * @return tx_cfcleague_models_competition or null
   */
  public static function &getInstance($uid, $record = 0) {
    $uid = intval($uid);
    if(!array_key_exists($uid, self::$instances)) {
      $comp = new tx_cfcleague_models_Competition(is_array($record) ? $record : $uid);
      self::$instances[$uid] = $comp->isValid() ? $comp : null;
    }
    return self::$instances[$uid];
  }
  /**
   * statische Methode, die ein Array mit Instanzen dieser Klasse liefert. 
   * Es werden entweder alle oder nur bestimmte Wettkämpfe einer Saison geliefert.
   * @param string $saisonUid int einzelne UID einer Saison
   * @param string $groupUid int einzelne UID einer Altersklasse
   * @param string $uids String kommaseparierte Liste von Competition-UIDs
   * @param string $compTypes String kommaseparierte Liste von Wettkampftypen (1-Liga;2-Pokal;0-Sonstige)
   * @return Array der gefundenen Wettkämpfe
   */
  function findAll($saisonUid = '', $groupUid = '', $uids = '', $compTypes='') {
    if(is_string($uids) && strlen($uids) > 0) {
      $where = 'uid IN (' . $uids .')';
    }
    else
      $where = '1';

    if(is_numeric($saisonUid)) {
      $where .= ' AND saison = ' . $saisonUid .'';
    }

    if(is_numeric($groupUid)) {
      $where .= ' AND agegroup = ' . $groupUid .'';
    }

    if(strlen($compTypes)) {
      $where .= ' AND type IN (' . implode(t3lib_div::intExplode(',', $compTypes), ',') . ')';
    }

    /*
    SELECT * FROM tx_cfcleague_competition WHERE uid IN ($uid)
    */

    return tx_rnbase_util_DB::queryDB('*','tx_cfcleague_competition',$where,
              '','sorting','tx_cfcleaguefe_models_competition',0);
  }

  /**
   * Liefert ein Array mit den Tabellen-Markierungen
   * arr[$position] = array(markId, comment);
   */
  function getTableMarks() {
    $str = $this->record['table_marks'];
    if(!$str) return 0;

    $ret = array();
    $arr = t3lib_div::trimExplode('|',$str);
    foreach($arr As $item) {
      // Jedes Item splitten
      $mark = t3lib_div::trimExplode(';',$item);
      $positions = t3lib_div::intExplode(',',$mark[0]);
      $comments = t3lib_div::trimExplode(',',$mark[1]);
      // Jetzt das Ergebnisarray aufbauen
      foreach($positions As $position) {
        $ret[$position] = Array($comments[0], $comments[1]);
      }

    }
    return $ret;
  }

  /**
   * Liefert die verhängten Strafen für Teams des Wettbewerbs.
   */
  function getPenalties() {
    if(!is_array($this->penalties)) {
      # Die UID der Liga setzen
      $where = 'competition="'.$this->uid.'" ';
  
      $this->penalties = tx_rnbase_util_DB::queryDB('*','tx_cfcleague_competition_penalty',$where,
                     '','sorting','tx_cfcleaguefe_models_competition_penalty',0);
    }
    return $this->penalties;
  }
  /**
   * Set penalties
   *
   * @param array $penalties
   */
  function setPenalties($penalties) {
    $this->penalties = is_array($penalties) ? $penalties : NULL;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Competition.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Competition.php']);
}
?>