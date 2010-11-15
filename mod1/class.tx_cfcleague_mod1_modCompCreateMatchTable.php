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

tx_rnbase::load('tx_cfcleague_mod1_decorator');
tx_rnbase::load('tx_cfcleague_models_Competition');

/**
 * Die Klasse verwaltet die Erstellung von Spielplänen für Wettbewerbe
 */
class tx_cfcleague_mod1_modCompCreateMatchTable {
	var $doc;
	private $module;


	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
   * @param tx_rnbase_mod_IModule $module
	 * @param tx_cfcleague_league $competition
	 */
	public function main($module, $competition) {
		global $LANG;

		$this->module = $module;
		$pid = $module->getPid();
		$this->doc = $module->getDoc();

		$formTool = $module->getFormTool();
		$this->formTool = $formTool;
		$comp = tx_cfcleague_models_Competition::getInstance($competition->uid);
//		$start = microtime(true);

		tx_rnbase::load('tx_cfcleague_mod1_handler_MatchCreator');
		$content .= tx_cfcleague_mod1_handler_MatchCreator::getInstance()->handleRequest($this->getModule());
		$content .= $this->handleCreateMatchTable($comp);
		if(!$content)
			$content .= $this->showMatchTable($comp);
//		t3lib_div::debug((microtime(true)-$start), 'class.tx_cfcleague_mod1_modCompCreateMatchTable.php'); // TODO: remove me

		return $content;
	}

	function handleCreateMatchTable($comp) {
  	global $LANG;
		// Haben wir Daten im Request?
  	$data = t3lib_div::_GP('data');
		if (is_array($data['rounds']) && t3lib_div::_GP('update')) {
			$result = $this->createMatches($data['rounds'], $comp);
			$content .= $this->doc->section($LANG->getLL('message').':', $result, 0,1,ICON_INFO);
			return $content; 
		}
	}
	/**
	 * Zeigt den Spielplan an
	 * @param tx_cfcleague_models_Competition $comp
	 * @return string
	 */
	private function showMatchTable($comp) {
  	global $LANG;

		$matchCnt = $comp->getNumberOfMatches(false);
		if($matchCnt > 0){
			$content.=$this->doc->section($LANG->getLL('warning').':',$LANG->getLL('msg_league_generation_hasmatches'),0,1,ICON_WARN);
			$content.='<br/><br/>';
		}

		// Hier zwischen Automatisch und Manuell unterscheiden
		$menu = $this->getFormTool()->showMenu($this->getModule()->getPid(), 't3s_mcmode', $this->getModule()->getName(), array(0=>'Auto','1'=>'Manual'));
		$content .= $menu['menu'];
		$mode = $menu['value'];
		$content .= '<br>';

		if($mode == 0)
			$content .= $this->showMatchTableAuto($comp);
		else {
			tx_rnbase::load('tx_cfcleague_mod1_handler_MatchCreator');
			$content .= tx_cfcleague_mod1_handler_MatchCreator::getInstance()->showScreen($comp, $this->getModule());
		}

		//t3lib_div::debug($table, 'tx_cfcleague_mod1_modCompCreateMatchTable :: showCreateMatchTable'); // TODO: remove me
		return $content;
	}

	private function showMatchTableAuto($comp) {
  	global $LANG;
		$content = '';
		// Wir holen die Mannschaften und den GameString aus der Liga
		// Beides jagen wir durch den Generator
		$options['halfseries'] = intval(t3lib_div::_GP('option_halfseries'));
		$options['nomatch'] = $comp->getDummyTeamIds();
		$options['firstmatchday'] = $comp->getNumberOfRounds();
		$options['firstmatchnumber'] = $comp->getLastMatchNumber();
		// Zunächst mal Anzeige der Daten
		$gen = tx_rnbase::makeInstance('tx_cfcleague_util_Generator');
		$table = $gen->main($comp->getTeamIds(),$comp->getGenerationKey(), $options);

		if(count($gen->errors)) {
			// Da gibt es wohl ein Problem bei der Erzeugung der Spiele...
			$content.=$this->doc->section($LANG->getLL('error').':','<ul><li>' . implode('<li>',$gen->errors) . '</ul>',0,1,ICON_FATAL);
		}
		if(count($gen->warnings)) {
			// Da gibt es wohl ein Problem bei der Erzeugung der Spiele...
			$content.=$this->doc->section($LANG->getLL('warning').':','<ul><li>' . implode('<li>',$gen->warnings) . '</ul>',0,1,ICON_WARN);
		}
		if(count($table)) {
			// Wir zeigen alle Spieltage und fragen nach dem Termin
			$content .= $this->prepareGameTable($table, $comp,$options['halfseries']);
			// Den Update-Button einfügen
			$content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_create').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('msg_CreateGameTable')).')">';
		}
		return $content;
	}
	/**
	 * Erstellt das Vorabformular, daß für jeden Spieltag notwendige Daten abfragt.
	 */
	function prepareGameTable($table, &$league, $option_halfseries) {
		global $LANG;

		$content = '';
		// Wir benötigen eine Select-Box mit der man die Rückrunden-Option einstellen kann
		// Bei Änderung soll die Seite neu geladen werden, damit nur die Halbserie angezeigt wird.
		$content .= $this->formTool->createSelectSingleByArray('option_halfseries', $option_halfseries, Array('0' => 'Mit Rückrunde', '1' => 'Ohne Rückrunde'), array('reload'=>1));

		$content .= '<br />';

		$tableLayout = $this->doc->tableLayout;
		$tableLayout['defRow'] = Array ( // Formate für alle Zeilen
				'defCol' => Array('<td valign="top" style="padding:5px 5px 0 5px; border-bottom:solid 1px #A2AAB8;">','</td>') // Format für jede Spalte in jeder Zeile
			);
		unset($tableLayout['defRowEven']);
			
		$tableLayout2 = $tableLayout;
		$tableLayout2['defRow'] = Array ( // Formate für alle Zeilen
			'tr'	   => Array('<tr class="db_list_normal">', '</tr>'),
			'defCol' => Array('<td>','</td>') // Format für jede Spalte in jeder Zeile
		);
		$tableLayout2['defRowEven'] = Array ( // Formate für alle Zeilen
			'tr'	   => Array('<tr class="db_list_alt">', '</tr>'),
			'defCol' => Array('<td>','</td>') // Format für jede Spalte in jeder Zeile
		);
//t3lib_div::debug($table[1][0], 'class.tx_cfcleague_mod1_modCompCreateMatchTable.php'); // TODO: remove me

		$arr = Array(Array($LANG->getLL('label_roundset')));
//		$arr = Array(Array($LANG->getLL('label_round'),$LANG->getLL('label_roundname').' / '.
//			$LANG->getLL('label_rounddate'),$LANG->getLL('label_roundset')));
		foreach($table As $round => $matchArr) {
			$row = array();

			// Die Formularfelder, die jetzt erstellt werden, wandern später direkt in die neuen Game-Records
			// Ein Hidden-Field für die Runde
			$row[] = '<div>' . $this->formTool->createHidden('data[rounds][round_'.$round.'][round]',$round) . 
							$this->formTool->createTxtInput('data[rounds][round_'.$round.'][round_name]',$round . $LANG->getLL('createGameTable_round'),10) . 
							$this->formTool->createDateInput('data[rounds][round_'.$round.'][date]',time()) .'</div>'.
							// Anzeige der Paarungen
			 				$this->doc->table($this->createMatchTableArray($matchArr, $league, 'data[rounds][round_'.$round.']'), $tableLayout2);

			$arr[] = $row;
		}
		$content .= $this->doc->table($arr, $tableLayout);
		return $content;
	}
	/**
	 * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
	 * @param tx_cfcleague_models_Competition $league
	 */
	private function createMatchTableArray(&$matches, &$league, $namePrefix) {
		global $LANG;
		$teamNames = $league->getTeamNames();
		$arr = Array(Array($LANG->getLL('label_match_no'),$LANG->getLL('label_home'),$LANG->getLL('label_guest')));
		foreach($matches As $match){
			$row = array();
			$row[] = $match->noMatch ? '' : str_pad($match->nr2,3,'000',STR_PAD_LEFT);
			$row[] = $this->createSelectBox($teamNames,$match->home, $namePrefix.'[matches]['.$match->nr.'][home]');
			$row[] = $this->createSelectBox($teamNames,$match->guest,$namePrefix.'[matches]['.$match->nr.'][guest]') .
								$this->formTool->createHidden($namePrefix.'[matches]['.$match->nr.'][nr2]',$match->nr2);
//			$row[] = $teamNames[$match->home];
//			$row[] = $teamNames[$match->guest];
			$arr[] = $row;
		}

		return $arr;
	}
	private function createSelectBox($teamNames, $currentTeam, $name) {
		$ret = '<select name="'.$name.'">';
		foreach($teamNames As $key => $teamName) {
			$ret.='<option value="'.$key.'"'. ($key == $currentTeam ? ' selected="selected" ' : '') .'>'.$teamName.'</option>';
		}
		$ret .= "</select>\n";
		return $ret;
	}
	/**
	 * Returns the formtool
	 * @return tx_rnbase_util_FormTool
	 */
	function getFormTool() {
		return $this->formTool;
	}
	/**
	 * @return tx_rnbase_mod_IModule
	 */
	protected function getModule() {
		return $this->module;
	}

	/**
	 * Erstellt die Spiele der Liga. Diese werden aus den Daten gebildet, die im Request liegen.
	 */
	function createMatches($rounds, &$league) {
		global $LANG;

		// Aus den Spielen der $table die TCA-Datensätze erzeugen
		$data['tx_cfcleague_games'] = array();

		// Wir erstellen die Spiel je Spieltag
		foreach($rounds As $roundId => $roundData){
			// Die ID des Spieltags ermitteln
			$roundId = $roundData['round'];
			$matches = $roundData['matches'];
			// Die Paarungen holen
			foreach($matches As $matchId => $match) {
				// Die Basis des Spieldatensatzes ist $roundData
				$new_match = array();
				$new_match['round'] = $roundData['round'];
				$new_match['round_name'] = $roundData['round_name'];
				$new_match['date'] = $roundData['date'];
				$new_match['home'] = $match['home'];
				$new_match['guest'] = $match['guest'];
				$new_match['match_no'] = $match['nr2'];
				$new_match['competition'] = $league->uid;
				$new_match['pid'] = $league->record['pid'];
				$data['tx_cfcleague_games']['NEW'.$matchId] = $new_match;
			}
		}
		
		// Die neuen Notes werden jetzt gespeichert
		reset($data);
		$tce =& tx_cfcleague_db::getTCEmain($data);
		$tce->process_datamap();

		return $LANG->getLL('msg_matches_created');
	}

	private function getTableLayout() {
		return Array (
			'table' => Array('<table class="typo3-dblist" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'0' => Array( // Format für 1. Zeile
					'defCol' => Array('<td valign="top" class="c-headLineTable" style="font-weight:bold;padding:2px 5px;">','</td>') // Format f�r jede Spalte in der 1. Zeile
				),
			'defRow' => Array ( // Formate für alle Zeilen
					'defCol' => Array('<td valign="middle" style="padding:0px 1px;">','</td>') // Format für jede Spalte in jeder Zeile
			),
			'defRowEven' => Array ( // Formate für alle Zeilen
				'defCol' => Array('<td valign="middle" class="db_list_alt" style="padding:0px 1px;">','</td>') // Format für jede Spalte in jeder Zeile
			)
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompCreateMatchTable.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompCreateMatchTable.php']);
}
?>