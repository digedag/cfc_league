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
 * Die Klasse verwaltet die Erstellung Teams für Wettbewerbe
 */
class tx_cfcleague_mod1_modCompCreateMatchTable extends t3lib_extobjbase {
	var $doc, $modName;


	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
	 * @param tx_cfcleague_league $competition
	 */
	function main($modName, $pid, &$doc, &$formTool, &$competition) {
		global $LANG;
		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		// Entweder global über die Datenbank oder die Ligen der aktuellen Seite

		$this->doc = $doc;
		$this->formTool = $formTool;

		$content = '';
		// Zuerst auf neue Teams prüfen, damit sie direkt in der Teamliste angezeigt werden
		$content .= $this->showCreateMatchTable($competition);

		$content .= $addTeams;
		$content .= $newTeams;
		return $content;
	}

	private function showCreateMatchTable($current_league) {
  	global $LANG;
		$comp = tx_cfcleague_models_Competition::getInstance($current_league->uid);
		$matchCnt = $comp->getNumberOfMatches(false);
		if($matchCnt > 0){
			$content.=$this->doc->section($LANG->getLL('warning').':',$LANG->getLL('msg_league_generation_hasmatches'),0,1,ICON_WARN);
			$content.='<br/><br/>';
		}

		// Wir holen die Mannschaften und den GameString aus der Liga
		// Beides jagen wir durch den Generator
		$options['halfseries'] = intval(t3lib_div::_GP('option_halfseries'));
		$options['nomatch'] = $comp->getDummyTeamIds();
		$options['firstmatchday'] = $comp->getNumberOfRounds();
		$options['firstmatchnumber'] = $comp->getLastMatchNumber();

		// Zunächst mal Anzeige der Daten
		$gen = tx_div::makeInstance('tx_cfcleague_util_Generator');
		$table = $gen->main($comp->getTeamIds(),$comp->getGenerationKey(), $options);

		$data = t3lib_div::_GP('data');
		// Haben wir Daten im Request?
		if (is_array($data['rounds']) && t3lib_div::_GP('update')) {
//  t3lib_div::debug($data['rounds'], 'GP generator') ;
			$content .= $this->doc->section($LANG->getLL('message').':',
					$this->createGames($data['rounds'],$table, $current_league),
					0,1,ICON_INFO);
		}
		else {
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
				$content .= $this->prepareGameTable($table, $current_league,$options['halfseries']);
				// Den Update-Button einfügen
				$content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_create').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('msg_CreateGameTable')).')">';
			}
		}

		//t3lib_div::debug($table, 'tx_cfcleague_mod1_modCompCreateMatchTable :: showCreateMatchTable'); // TODO: remove me
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

		$arr = Array(Array($LANG->getLL('label_round'),$LANG->getLL('label_roundname'),
			$LANG->getLL('label_rounddate'),$LANG->getLL('label_roundset')));
		foreach($table As $round => $matchArr) {
			$row = array();

			// Die Formularfelder, die jetzt erstellt werden, wandern später direkt in die neuen Game-Records
			// Ein Hidden-Field für die Runde
			$row[] = $round . $this->formTool->createHidden('data[rounds][round_'.$round.'][round]',$round);
			// Vorschlag für den Namen des Spieltags
			$row[] = $this->formTool->createTxtInput('data[rounds][round_'.$round.'][round_name]',$round . $LANG->getLL('createGameTable_round'),10);
			$row[] = $this->formTool->createDateInput('data[rounds][round_'.$round.'][date]',time());
			// Anzeige der Paarungen
			$row[] = $this->doc->table($this->createMatchTableArray($matchArr, $league), $tableLayout2);

			$arr[] = $row;
		}
		$content .= $this->doc->table($arr, $tableLayout);
		return $content;
	}
	/**
	 * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
	 * @param $league
	 */
	function createMatchTableArray(&$games, &$league) {
		global $LANG;
		$teamNames = $league->getTeamNames();
		$arr = Array(Array($LANG->getLL('label_match_no'),$LANG->getLL('label_home'),$LANG->getLL('label_guest')));
		foreach($games As $match){
			$row = array();
			$row[] = $match->noMatch ? '' : str_pad($match->nr2,3,'000',STR_PAD_LEFT);
			$row[] = $teamNames[$match->home];
			$row[] = $teamNames[$match->guest];
			$arr[] = $row;
		}
		return $arr;
	}
	/**
	 * Returns the formtool
	 * @return tx_rnbase_util_FormTool
	 */
	function getFormTool() {
		return $this->formTool;
	}

	function getTableLayout() {
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