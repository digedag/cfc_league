<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_cfcleague_mod1_decorator');

/**
 * Die Klasse verwaltet die Erstellung Teams für Wettbewerbe
 */
class tx_cfcleague_mod1_modCompTeams {
  var $doc;


	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
   * @param tx_rnbase_mod_IModule $module
	 * @param tx_cfcleague_league $competition
	 */
	public function main($module, $competition) {
		global $LANG;
		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		// Entweder global über die Datenbank oder die Ligen der aktuellen Seite
		$pid = $module->getPid();
		$this->doc = $module->getDoc();

		$formTool = $module->getFormTool();
		$this->formTool = $formTool;


		$content = '';
		// Zuerst auf neue Teams prüfen, damit sie direkt in der Teamliste angezeigt werden
		$newTeams = $this->showNewTeamForm($pid, $competition);
		$addTeams = $this->showTeamsFromPage($pid, $competition);

		$content .= $this->showCurrentTeams($competition);
		$content .= $addTeams;
		$content .= $newTeams;
		return $content;
	}

	/**
	 * Returns the formtool
	 * @return tx_rnbase_util_FormTool
	 */
	function getFormTool() {
		return $this->formTool;
	}
	/**
	 * Show all teams from current page and not part of current competition
	 * @param int $pid
	 * @param tx_cfcleague_league $competition
	 * @return string
	 */
	function showTeamsFromPage($pid, $competition) {
		global $LANG;
		// Liegen Daten im Request
		$teamIds = t3lib_div::_GP('checkEntry');
		if(t3lib_div::_GP('addteams') && is_array($teamIds) && count($teamIds)) {
			$tcaData['tx_cfcleague_competition'][$competition->record['uid']]['teams'] = implode(',', $this->mergeArrays(t3lib_div::intExplode(',', $competition->record['teams']), $teamIds));
			tx_rnbase::load('tx_rnbase_util_DB');
			$tce =& tx_rnbase_util_DB::getTCEmain($tcaData, array());
			$tce->process_datamap();
			$competition->refresh();
		}

		$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
		// Team-IDs im aktuellen Wettbewerb
		$teamIds = $competition->record['teams'];
		// Teams der Seite laden
		$fields = array();
		$fields['TEAM.PID'][OP_EQ_INT] = $pid;
		if($teamIds)
			$fields['TEAM.UID'][OP_NOTIN_INT] = $teamIds;
		$options = array();
		$options['orderby']['TEAM.NAME'] = 'asc';
//		$options['debug']=1;
		$teams = $srv->searchTeams($fields, $options);
		if(!count($teams)) return '';
		$options = array();
		$options['checkbox'] = 1;
		$columns = array(
			'uid' => array('title' => 'label_uid'),
			'name' => array('title' => 'label_teamname', 'method' => 'getName'),
		);
		$arr = tx_cfcleague_mod1_decorator::prepareTable($teams, $columns, $this->getFormTool(), $options);

		$content = '<h2>'.$LANG->getLL('label_add_teams_from_page').'</h2>';
		$content .= $this->doc->table($arr[0]); //, $this->getTableLayout()
		$content .= $this->formTool->createSubmit('addteams', $LANG->getLL('label_add_teams'), $GLOBALS['LANG']->getLL('msg_add_teams'));
		return $content;
	}
	/**
	 * Darstellung einer Tabelle mit den Teams auf der Seite und der Option diese hinzuzufügen.
	 *
	 * @param tx_cfcleague_league $competition
	 */
	function showNewTeamForm($pid, &$competition) {
		global $LANG;
		$show = intval(t3lib_div::_GP('check_newcompteam'));
		$content = '<h2>
		<input type="checkbox" name="check_newcompteam" value="1" ' . ($show ? 'checked="checked"' : '') . ' onClick="this.form.submit()">
		'.$LANG->getLL('label_create_teams').'</h2>
		';

		if(!$show) return $content;
		$content .= $this->createTeams(t3lib_div::_GP('data'), $competition);

		// Jetzt 6 Boxen mit Name und Kurzname
		$arr = Array(Array('&nbsp;', $LANG->getLL('label_teamname'), $LANG->getLL('label_teamshortname')));
		$maxFields = 6;
		for($i=0; $i < $maxFields; $i++){
			$row = array();
			$row[] = ($i + 1) . $this->getFormTool()->createHidden('data[tx_cfcleague_teams][NEW'.$i.'][pid]', $pid);
			$row[] = $this->getFormTool()->createTxtInput('data[tx_cfcleague_teams][NEW'.$i.'][name]', '', 20);
			$row[] = $this->getFormTool()->createTxtInput('data[tx_cfcleague_teams][NEW'.$i.'][short_name]', '', 10);
			$arr[] = $row;
		}
//t3lib_div::debug($teams, 'tx_cfcleague_mod1_modCompTeams'); // TODO: remove me
		$content .= $this->doc->table($arr, $this->getTableLayout());
		$content .= $this->getFormTool()->createSubmit('update', $LANG->getLL('btn_create'), $GLOBALS['LANG']->getLL('msg_create_teams'));
		return $content;
	}
	/**
	 * Creates new teams and adds to current competition
	 * @param array $data request data
	 * @param tx_cfcleague_league $competition
	 * @return string
	 */
	function createTeams($data, &$competition) {
		global $LANG;
		if (!is_array($data['tx_cfcleague_teams'])) return '';
		$tcaData = array();
		$uids = array();
		foreach($data['tx_cfcleague_teams'] As $uid => $arr) {
			if(trim($arr['name']) || trim($arr['short_name'])) {
				$tcaData['tx_cfcleague_teams'][$uid] = $arr;
				$uids[] = $uid;
			}
		}
    if(!count($uids)) return '';
		$tcaData['tx_cfcleague_competition'][$competition->record['uid']]['teams'] = implode(',', $this->mergeArrays(t3lib_div::intExplode(',', $competition->record['teams']), $uids));
		reset($tcaData);
		$tce =& tx_cfcleague_db::getTCEmain($tcaData);
		$tce->process_datamap();
		$competition->refresh();
		$content .= $this->doc->section('Message:', $LANG->getLL('msg_teams_created'), 0, 1, ICON_INFO);
		return $content;

//		t3lib_div::debug($tcaData, $uid.' - tx_cfcleague_mod1_modCompTeams'); // TODO: remove me
		
//		t3lib_div::debug($data, 'tx_cfcleague_mod1_modCompTeams'); // TODO: remove me
	}
	/**
	 * Darstellung einer Tabelle mit den aktuellen Teams
	 *
	 * @param tx_cfcleague_league $competition
	 */
	function showCurrentTeams(&$competition) {
		global $LANG;
		$content = '<h2>'.$LANG->getLL('label_current_teams').'</h2>';
		$arr[] = array('UID', 'Team', 'Info');
		$teams = $competition->getTeamNames(1);
		if(!count($teams)) {
			return $content . $this->doc->section('Message:', $LANG->getLL('msg_noteams_in_comp'), 0, 1, ICON_INFO);
		}
		foreach($teams As $teamArr) {
			$row = array();
			$row[] = $teamArr['uid'];
			$row[] = $teamArr['name'];
			$buttons = $this->formTool->createEditLink('tx_cfcleague_teams', $teamArr['uid']) .
								$this->formTool->createInfoLink('tx_cfcleague_teams', $teamArr['uid']);
			if(intval($teamArr['club']))
				$buttons .= $this->formTool->createEditLink('tx_cfcleague_club', $teamArr['club'], $LANG->getLL('label_edit_club'));
			$row[] = $buttons;
			$arr[] = $row;
		}
		$content .= $this->doc->table($arr); //, $this->getTableLayout()
		return $content;
//t3lib_div::debug($arr, 'tx_cfcleague_mod1_modCompTeams'); // TODO: remove me
	}

	function getTableLayout() {
		return Array (
			'table' => Array('<table class="typo3-dblist" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'0' => Array( // Format für 1. Zeile
					'defCol' => Array('<td valign="top" class="c-headLineTable" style="font-weight:bold;padding:2px 5px;">', '</td>') // Format für jede Spalte in der 1. Zeile
				),
			'defRow' => Array ( // Formate für alle Zeilen
					'defCol' => Array('<td valign="middle" style="padding:0px 1px;">', '</td>') // Format für jede Spalte in jeder Zeile
			),
			'defRowEven' => Array ( // Formate für alle Zeilen
				'defCol' => Array('<td valign="middle" class="db_list_alt" style="padding:0px 1px;">', '</td>') // Format für jede Spalte in jeder Zeile
			)
		);
	}
	/**
	 * Zwei Arrays zusammenführen. Sollte eines der Array leer sein, dann wird es ignoriert.
	 * Somit werden unnötige 0-Werte vermieden.
	 */
	function mergeArrays($arr1, $arr2){
		$ret = $arr1[0] ? $arr1 : 0;
		if($ret && $arr2) {
			$ret = array_merge($ret, $arr2);
		}
		elseif($arr2)
			$ret = $arr2;
		return $ret;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompTeams.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompTeams.php']);
}
?>