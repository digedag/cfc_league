<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2016 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_parameters');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');
tx_rnbase::load('Tx_Rnbase_Database_Connection');


/**
 * Die Klasse wird im BE verwendet und liefert Informationen über ein Team
 */
class tx_cfcleague_util_TeamInfo {
	private $baseInfo = array();
	private $team = null;
	/* @var $formTool tx_rnbase_util_FormTool */
	private $formTool;
	/**
	 *
	 * @param tx_cfcleague_models_Team $team
	 * @param tx_rnbase_util_FormTool $formTool
	 */
	public function __construct($team, $formTool) {
		$this->formTool = $formTool;
		$this->init($team);
	}
	private function init($team) {
		global $TCA;
		$this->team = $team;
        if(!tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
            Tx_Rnbase_Utility_T3General::loadTCA('tx_cfcleague_teams');
        }
		$this->baseInfo['maxCoaches'] = intval($TCA['tx_cfcleague_teams']['columns']['coaches']['config']['maxitems']);
		$this->baseInfo['maxPlayers'] = intval($TCA['tx_cfcleague_teams']['columns']['players']['config']['maxitems']);
		$this->baseInfo['maxSupporters'] = intval($TCA['tx_cfcleague_teams']['columns']['supporters']['config']['maxitems']);

		$this->baseInfo['freePlayers'] = $this->baseInfo['maxPlayers'] - $this->getPlayerSize($team);
		$this->baseInfo['freeCoaches'] = $this->baseInfo['maxCoaches'] - $this->getPlayerSize($team);
		$this->baseInfo['freeSupporters'] = $this->baseInfo['maxSupporters'] - $this->getPlayerSize($team);
	}
	public function refresh() {
		$this->init($this->team);
	}

	public function get($item) {
		return $this->baseInfo[$item];
	}
	/**
	 *
	 * @return tx_rnbase_util_FormTool
	 */
	public function getFormTool() {
		return $this->formTool;
	}
	/**
	 * Liefert true, wenn keine Personen zugeordnet werden können.
	 * @return boolean
	 */
	public function isTeamFull() {
		return ($this->baseInfo['freePlayers'] < 1 && $this->baseInfo['freeCoaches'] < 1 && $this->baseInfo['freeSupporters'] < 1);
	}

	/**
	 * Liefert die Informationen, über den Zustand des Teams.
	 * @return string
	 */
	public function getInfoTable(&$doc) {
		global $LANG;
		tx_rnbase::load('tx_rnbase_util_TYPO3');
		$tableLayout = Array (
			'table' => Array('<table class="typo3-dblist table" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'defRow' => Array( // Format für 1. Zeile
				'tr'		=> Array('<tr class="t3-row-header">', "</tr>\n"),
				'defCol' => Array(tx_rnbase_util_TYPO3::isTYPO42OrHigher() ? '<td>': '<td class="c-headLineTable" style="font-weight:bold;color:white;padding:0 5px;">', '</td>') // Format für jede Spalte in der 1. Zeile
			)
		);

		$arr[] = array($LANG->getLL('msg_number_of_players'), $this->baseInfo['freePlayers']);
		$arr[] = array($LANG->getLL('msg_number_of_coaches'), $this->baseInfo['freeCoaches']);
		$arr[] = array($LANG->getLL('msg_number_of_supporters'), $this->baseInfo['freeSupporters']);

		$tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

		return $tables->buildTable($arr, $tableLayout);
	}
	/**
	 * Liefert eine Tabelle mit den aktuellen Spielern, Trainern und Betreuern des Teams.
	 *
	 * @param object $doc
	 * @return string
	 */
	public function getTeamTable(&$doc) {
		global $LANG;
		$arr = Array(Array('&nbsp;', $LANG->getLL('label_firstname'), $LANG->getLL('label_lastname'), '&nbsp;', '&nbsp;'));

		$this->addProfiles($arr, $this->getCoachNames($this->getTeam()), $LANG->getLL('label_profile_coach'), 'coach');
		$this->addProfiles($arr, $this->getPlayerNames($this->getTeam()), $LANG->getLL('label_profile_player'), 'player');
		$this->addProfiles($arr, $this->getSupporterNames($this->getTeam()), $LANG->getLL('label_profile_supporter'), 'supporter');

		$tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
		$tableProfiles = count($arr) > 1 ? $tables->buildTable($arr) : '';
		return $tableProfiles;
	}
	/**
	 * Bearbeitung von Anweisungen aus dem Request.
	 */
	public function handleRequest() {
		global $LANG;
		$data = tx_rnbase_parameters::getPostOrGetParameter('remFromTeam');
		if(!is_array($data)) return '';

		$fields = array('player' => 'players', 'coach'=>'coaches', 'supporter'=>'supporters');
		$team = $this->getTeam();
		$tceData = array();
		foreach($data As $type => $uid) {
			$profileUids = $team->getProperty($fields[$type]);
			if(!$profileUids) continue;

			if(Tx_Rnbase_Utility_T3General::inList($profileUids, $uid)) {
				$profileUids = Tx_Rnbase_Utility_T3General::rmFromList($uid, $profileUids);
				$tceData['tx_cfcleague_teams'][$team->getUid()][$fields[$type]] = $profileUids;
				$team->setProperty($fields[$type], $profileUids);
			}
		}

		$tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($tceData);
		$tce->process_datamap();

		return $this->getFormTool()->getDoc()->section('Info:', $LANG->getLL('msg_removedProfileFromTeam'), 0, 1, ICON_INFO);
	}
	/**
	 * Add profiles to profile list
	 *
	 * @param array $arr
	 * @param array $profiles
	 * @param string $label
	 */
	private function addProfiles (&$arr, $profileNames, $label, $type) {
		global $LANG;
		$i = 1;
		if($profileNames)
			foreach($profileNames As $uid => $prof) {
				if($i == 1)
					$arr[] = array('', '&nbsp;', '', '', ''); // Leere Zeile als Trenner;
				$row = array();
				$row[] = $i++ == 1 ? $label : '';
				$row[] = $prof[first_name];
				$row[] = $prof[last_name];
				$row[] = $this->getFormTool()->createEditLink('tx_cfcleague_profiles', $uid);
				$row[] = $this->getFormTool()->createSubmit('remFromTeam['.$type.']', $uid, $LANG->getLL('msg_remove_team_'.$type), array('icon' => 'i/be_users__h.gif', 'infomsg' => 'Remove from Team'));
				//$row[] = $this->getFormTool()->createLink('&'. 'remProfileUid['.$uid.']', 0, 'Remove from team', array('icon' => 'delete_record.gif'));
				$arr[] = $row;
			}
	}
	/**
	 * @return tx_cfcleague_models_Team
	 */
	private function getTeam() {
		return $this->team;
	}

	/**
	 * Liefert die Anzahl der zugeordneten Spieler
	 */
	public function getPlayerSize($team) {
		$value = $team->getProperty('players');
		return $value ? count(Tx_Rnbase_Utility_Strings::intExplode(',', $value)) : 0;
	}
	/**
	 * Liefert die Anzahl der zugeordneten Trainer
	 */
	public function getCoachSize() {
		$value = $team->getProperty('coaches');
		return $value ? count(Tx_Rnbase_Utility_Strings::intExplode(',', $value)) : 0;
	}

	/**
	 * Liefert die Anzahl der zugeordneten Betreuer
	 * @return int
	 */
	public function getSupporterSize() {
		$value = $team->getProperty('supporters');
		return $value ? count(Tx_Rnbase_Utility_Strings::intExplode(',', $value)) : 0;
	}
	/**
	 * Liefert die Namen alle Spieler des Teams als Array. Key ist die ID des Profils.
	 * @param tx_cfcleague_models_Team $team
	 * @return array
	 */
	protected function getPlayerNames($team) {
		$name = array();
		foreach ($team->getPlayers() As $profile) {
			$name[$profile->getUid()] = $profile->getRecord();
		}
		return $name;
	}

	/**
	 * Liefert die Namen der Trainer des Teams als Array. Key ist die ID des Profils.
	 * @param tx_cfcleague_models_Team $team
	 * @return array
	 */
	protected function getCoachNames($team) {
		$name = array();
		foreach ($team->getCoaches() As $profile) {
			$name[$profile->getUid()] = $profile->getRecord();
		}
		return $name;
	}

	/**
	 * Liefert die Namen der Betreuer des Teams als Array. Key ist die ID des Profils.
	 * @param tx_cfcleague_models_Team $team
	 * @return array
	 */
	protected function getSupporterNames($team) {
		$name = array();
		foreach ($team->getSupporters() As $profile) {
			$name[$profile->getUid()] = $profile->getRecord();
		}
		return $name;
	}
}

