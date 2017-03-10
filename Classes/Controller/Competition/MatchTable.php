<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2017 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('Tx_Rnbase_Utility_T3General');


/**
 * Die Klasse verwaltet die Erstellung von Spielplänen für Wettbewerbe
 */
class Tx_Cfcleague_Controller_Competition_MatchTable {
	/* @var $doc \TYPO3\CMS\Backend\Template\BigDocumentTemplate */
	private $doc;
	private $module;
	/* @var $formTool Tx_Rnbase_Backend_Form_ToolBox */
	private $formTool;


	/**
	 * @return \TYPO3\CMS\Core\Page\PageRenderer
	 */
	private function getPageRenderer() {
		return $this->doc->getPageRenderer();
	}
	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
	 * @param tx_rnbase_mod_IModule $module
	 * @param tx_cfcleague_models_Competition $competition
	 */
	public function main($module, $competition) {
		$this->module = $module;
		$this->doc = $module->getDoc();


		if(tx_rnbase_util_TYPO3::isTYPO70OrHigher()) {
			/* @var $moduleTemplate \TYPO3\CMS\Backend\Template\ModuleTemplate */
			$moduleTemplate = tx_rnbase::makeInstance(TYPO3\CMS\Backend\Template\ModuleTemplate::class);
			$moduleTemplate->getPageRenderer()->setBackPath('./'); // ??
			$moduleTemplate->getPageRenderer()->loadJquery();
			$moduleTemplate->getPageRenderer()->addJsFile('js/matchcreate.js', 'text/javascript', FALSE, FALSE, '', TRUE);
		}
		else
			$this->getPageRenderer()->addJsFile('js/matchcreate.js', 'text/javascript', FALSE, FALSE, '', TRUE);


		$this->formTool = $module->getFormTool();
//		$start = microtime(true);

		tx_rnbase::load('Tx_Cfcleague_Handler_MatchCreator');
		// Die Neuanlage der manuellen Spiele erledigt der MatchCreator
		$content .= Tx_Cfcleague_Handler_MatchCreator::getInstance()->handleRequest($this->getModule());

		// Die Neuanlage der "automatischen" Spiele übernimmt diese Klasse
		$content .= $this->handleCreateMatchTable($competition);
		if(!$content) {
			// Ohne Submit zeigen wir das Formular
			$content .= $this->showMatchTable($competition);
		}

		return $content;
	}

	/**
	 *
	 * @param tx_cfcleague_models_Competition $comp
	 * @return string
	 */
	private function handleCreateMatchTable($comp) {
		global $LANG;
		// Haben wir Daten im Request?
		$data = Tx_Rnbase_Utility_T3General::_GP('data');
		if (is_array($data['rounds']) && Tx_Rnbase_Utility_T3General::_GP('update')) {
			$result = $this->createMatches($data['rounds'], $comp);
			$content .= $this->doc->section($LANG->getLL('message').':', $result, 0, 1, ICON_INFO);
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
			$content.=$this->doc->section($LANG->getLL('warning').':', $LANG->getLL('msg_league_generation_hasmatches'), 0, 1, ICON_WARN);
			$content.='<br/><br/>';
		}

		// Hier zwischen Automatisch und Manuell unterscheiden
		$menu = $this->getFormTool()->showMenu(
				$this->getModule()->getPid(), 't3s_mcmode',
				$this->getModule()->getName(),
				array(0=>'Auto', '1'=>'Manual'));
		$content .= $menu['menu'];
		$mode = $menu['value'];
		$content .= '<br>';

		if($mode == 0) // Automatischer Spielplan
			$content .= $this->showMatchTableAuto($comp);
		else { // Manuell Spiele anlegen
			tx_rnbase::load('Tx_Cfcleague_Handler_MatchCreator');
			$content .= Tx_Cfcleague_Handler_MatchCreator::getInstance()->showScreen($comp, $this->getModule());
		}

		return $content;
	}

	/**
	 * Automatische Erzeugung eines Spielplans.
	 * @param tx_cfcleague_models_Competition $comp
	 * @return string
	 */
	private function showMatchTableAuto($comp) {
  	global $LANG;
		$content = '';
		// Wir holen die Mannschaften und den GameString aus der Liga
		// Beides jagen wir durch den Generator
		$options = array();
		$options['halfseries'] = intval(Tx_Rnbase_Utility_T3General::_GP('option_halfseries'));
		$options['nomatch'] = $comp->getDummyTeamIds();
		$options['firstmatchday'] = $comp->getNumberOfRounds();
		$options['firstmatchnumber'] = $comp->getLastMatchNumber();
		// Zunächst mal Anzeige der Daten
		/* @var $gen tx_cfcleague_util_Generator */
		$gen = tx_rnbase::makeInstance('tx_cfcleague_util_Generator');
		$table = $gen->main($comp->getTeamIds(), $comp->getGenerationKey(), $options);

		if(count($gen->errors)) {
			// Da gibt es wohl ein Problem bei der Erzeugung der Spiele...
			$content.=$this->doc->section($LANG->getLL('error').':', '<ul><li>' . implode('<li>', $gen->errors) . '</ul>', 0, 1, ICON_FATAL);
		}
		if(count($gen->warnings)) {
			// Da gibt es wohl ein Problem bei der Erzeugung der Spiele...
			$content.=$this->doc->section($LANG->getLL('warning').':', '<ul><li>' . implode('<li>', $gen->warnings) . '</ul>', 0, 1, ICON_WARN);
		}
		if(count($table)) {
			// Wir zeigen alle Spieltage und fragen nach dem Termin
			$content .= $this->prepareMatchTable($table, $comp, $options['halfseries']);
			// Den Update-Button einfügen
			$content .= $this->formTool->createSubmit('update', $LANG->getLL('btn_create'), $GLOBALS['LANG']->getLL('msg_CreateGameTable'));
		}
		return $content;
	}
	/**
	 * Erstellt das Vorabformular, daß für jeden Spieltag notwendige Daten abfragt.
	 */
	private function prepareMatchTable($table, &$league, $option_halfseries) {
		global $LANG;

		$content = '';
		// Wir benötigen eine Select-Box mit der man die Rückrunden-Option einstellen kann
		// Bei Änderung soll die Seite neu geladen werden, damit nur die Halbserie angezeigt wird.
		$content .= $this->formTool->createSelectByArray('option_halfseries', $option_halfseries, Array('0' => '###LABEL_CREATE_SAISON###', '1' => '###LABEL_CREATE_FIRSTHALF###', '2' => '###LABEL_CREATE_SECONDHALF###'), array('reload'=>1));
		$content .= '<br />';

		// Führende 0 für Spieltag im einstelligen Bereich
		$content .= $this->formTool->createCheckbox('option_leadingZero', '1', FALSE, 't3sMatchCreator.prependZero(this);');
		$content .= '###LABEL_LEADING_ZERO###';
		$content .= '<br />';

		$tableLayout = $this->doc->tableLayout;
		$tableLayout['defRow'] = Array ( // Formate für alle Zeilen
				'defCol' => Array('<td valign="top" style="padding:5px 5px 0 5px; border-bottom:solid 1px #A2AAB8;">', '</td>') // Format für jede Spalte in jeder Zeile
			);
		unset($tableLayout['defRowEven']);

		$tableLayout2 = $tableLayout;
		$tableLayout2['defRow'] = Array ( // Formate für alle Zeilen
			'tr'	   => Array('<tr class="db_list_normal">', '</tr>'),
			'defCol' => Array('<td>', '</td>') // Format für jede Spalte in jeder Zeile
		);
		$tableLayout2['defRowEven'] = Array ( // Formate für alle Zeilen
			'tr'	   => Array('<tr class="db_list_alt">', '</tr>'),
			'defCol' => Array('<td>', '</td>') // Format für jede Spalte in jeder Zeile
		);

		$arr = Array(Array($LANG->getLL('label_roundset')));
//		$arr = Array(Array($LANG->getLL('label_round'), $LANG->getLL('label_roundname').' / '.
//			$LANG->getLL('label_rounddate'), $LANG->getLL('label_roundset')));
		$tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

		foreach($table As $round => $matchArr) {
			$row = array();

			// Die Formularfelder, die jetzt erstellt werden, wandern später direkt in die neuen Game-Records
			// Ein Hidden-Field für die Runde
			$row[] = '<div>' . $this->formTool->createHidden('data[rounds][round_'.$round.'][round]', $round) .
						$this->formTool->createTxtInput('data[rounds][round_'.$round.'][round_name]', $round . $LANG->getLL('createGameTable_round'), 10, array('class'=>'roundname')) .
						$this->formTool->createDateInput('data[rounds][round_'.$round.'][date]', time()) .'</div>'.
						// Anzeige der Paarungen
						$tables->buildTable($this->createMatchTableArray($matchArr, $league, 'data[rounds][round_'.$round.']'));

			$arr[] = $row;
		}
		$content .= $tables->buildTable($arr);
		return $content;
	}
	/**
	 * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
	 * @param tx_cfcleague_models_Competition $league
	 */
	private function createMatchTableArray(&$matches, &$league, $namePrefix) {
		global $LANG;
		$teamNames = $league->getTeamNames();
		$arr = Array(Array($LANG->getLL('label_match_no'), $LANG->getLL('label_home'), $LANG->getLL('label_guest')));
		foreach($matches As $match){
			$row = array();
			$row[] = $match->noMatch ? '' : str_pad($match->nr2, 3, '000', STR_PAD_LEFT);
			$row[] = $this->createSelectBox($teamNames, $match->home, $namePrefix.'[matches]['.$match->nr.'][home]');
			$row[] = $this->createSelectBox($teamNames, $match->guest, $namePrefix.'[matches]['.$match->nr.'][guest]') .
					$this->formTool->createHidden($namePrefix.'[matches]['.$match->nr.'][nr2]', $match->nr2);
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
	protected function getFormTool() {
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
	function createMatches($rounds, $league) {
		global $LANG;

		// Aus den Spielen der $table die TCA-Datensätze erzeugen
		$data = array('tx_cfcleague_games' => array());

		// Wir erstellen die Spiel je Spieltag
		foreach($rounds As $roundData){
			// Die ID des Spieltags ermitteln
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
				$new_match['competition'] = $league->getUid();
				$new_match['pid'] = $league->getPid();
				$data['tx_cfcleague_games']['NEW'.$matchId] = $new_match;
			}
		}

		// Die neuen Notes werden jetzt gespeichert
		reset($data);

		tx_rnbase::load('Tx_Rnbase_Database_Connection');
		$tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
		$tce->process_datamap();

		return $LANG->getLL('msg_matches_created');
	}
}
