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


/**
 * Die Klasse wird im BE verwendet und liefert Informationen über ein Team
 */
class tx_cfcleague_util_TeamInfo {
	private $baseInfo = array();
	private $team = null;
	function __construct($team, $formTool) {
		$this->formTool = $formTool;
		$this->init($team);
	}
	private function init($team) {
		global $TCA;
		$this->team = $team;
		t3lib_div::loadTCA('tx_cfcleague_teams');
		$this->baseInfo['maxCoaches'] = intval($TCA['tx_cfcleague_teams']['columns']['coaches']['config']['maxitems']);
		$this->baseInfo['maxPlayers'] = intval($TCA['tx_cfcleague_teams']['columns']['players']['config']['maxitems']);
		$this->baseInfo['maxSupporters'] = intval($TCA['tx_cfcleague_teams']['columns']['supporters']['config']['maxitems']);

		$this->baseInfo['freePlayers'] = $this->baseInfo['maxPlayers'] - $team->getPlayerSize();
		$this->baseInfo['freeCoaches'] = $this->baseInfo['maxCoaches'] - $team->getCoachSize();
		$this->baseInfo['freeSupporters'] = $this->baseInfo['maxSupporters'] - $team->getSupporterSize();
	}
	public function refresh() {
		$this->init($this->team);
	}

	public function get($item) {
		return $this->baseInfo[$item];
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
			'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'defRow' => Array( // Format für 1. Zeile
				'tr'		=> Array('<tr class="c-headLineTable">','</tr>'),
				'defCol' => Array(tx_rnbase_util_TYPO3::isTYPO42OrHigher() ? '<td>': '<td class="c-headLineTable" style="font-weight:bold;color:white;padding:0 5px;">','</td>') // Format für jede Spalte in der 1. Zeile
			)
		);

		$arr[] = array($LANG->getLL('msg_number_of_players'), $this->baseInfo['freePlayers']);
		$arr[] = array($LANG->getLL('msg_number_of_coaches'), $this->baseInfo['freeCoaches']);
		$arr[] = array($LANG->getLL('msg_number_of_supporters'), $this->baseInfo['freeSupporters']);
		return $doc->table($arr, $tableLayout);
	}
	/**
	 * Liefert eine Tabelle mit den aktuellen Spielern, Trainern und Betreuern des Teams.
	 *
	 * @param object $doc
	 * @return string
	 */
	public function getTeamTable(&$doc) {
		global $LANG;
		$arr = Array(Array('&nbsp;',$LANG->getLL('label_firstname'),$LANG->getLL('label_lastname'),'&nbsp;'));

		$this->addProfiles($arr, $this->getTeam()->getCoachNames(), $LANG->getLL('label_profile_coach'));
		$this->addProfiles($arr, $this->getTeam()->getPlayerNames(), $LANG->getLL('label_profile_player'));
		$this->addProfiles($arr, $this->getTeam()->getSupporterNames(), $LANG->getLL('label_profile_supporter'));

		$tableProfiles = count($arr) > 1 ? $doc->table($arr, $tableLayout) : '';
		return $tableProfiles;
	}
  /**
   * Add profiles to profile list
   *
   * @param array $arr
   * @param array $profiles
   * @param string $label
   */
  private function addProfiles (&$arr, &$profileNames, $label) {
    $i = 1;
    if($profileNames) foreach($profileNames As $uid => $prof) {
    	if($i == 1)
      	$arr[] = array('','&nbsp;',''); // Leere Zeile als Trenner;
    	$row = array();
      $row[] = $i++ == 1 ? $label : '';
      $row[] = $prof[first_name];
      $row[] = $prof[last_name];
      // TODO: Button to remove a profile from team
//      $row[] = $this->formTool->createSubmit('remProfileUid['.$uid.']', 'Remove', 'Really??');
      $row[] = $this->formTool->createEditLink('tx_cfcleague_profiles', $uid);
      $arr[] = $row;
    }
  }
  /**
   * @return tx_cfcleague_models_Team
   */
  private function getTeam() {
  	return $this->team;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_TeamInfo.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_TeamInfo.php']);
}

?>