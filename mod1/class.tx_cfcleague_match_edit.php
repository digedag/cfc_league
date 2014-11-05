<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2014 Rene Nitzsche (rene@system25.de)
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



/**
 * Die Klasse verwaltet die Bearbeitung der Spieltage
 */
class tx_cfcleague_match_edit  {


  /**
   * Bearbeitung von Spielen. Es werden die Paaren je Spieltag angezeigt
   * @param tx_rnbase_mod_IModule $module
   */
  public function main($module, $current_league) {
    global $LANG;

    $this->setModule($module);
		$pid = $module->getPid();
    $this->id = $module->getPid();
		$this->doc = $module->getDoc();

		$formTool = $module->getFormTool();
		$this->formTool = $formTool;
		$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		$content = '';
		$content.=$module->getDoc()->spacer(5);

		if(!count($current_league->getRounds())){
			$content .= $LANG->getLL('no_round_in_league');
			$content .= '<br /><br />';
			$content .= $this->getFooter($current_league, 0, $pid, $formTool);
			return $content;
		}

		$currentTeam = $this->makeTeamSelector($content, $pid, $current_league);
		// Jetzt den Spieltag wählen lassen
		if($currentTeam == null)
			$current_round = $this->getSelector()->showRoundSelector($content, $pid, $current_league);

		$content.='<div class="cleardiv"/>';
		$data = t3lib_div::_GP('data');
		// Haben wir Daten im Request?
		if (is_array($data['tx_cfcleague_games'])) {
			$this->updateMatches($data);
		}

		$matches = $this->findMatches($currentTeam, $current_round, $current_league);
		$arr = $this->createTableArray($matches, $current_league);

		$content .= $module->getDoc()->table($arr[0]);

		// Den Update-Button einfügen
		$content .= $formTool->createSubmit('update', $LANG->getLL('btn_update'), $GLOBALS['LANG']->getLL('btn_update_msgEditGames'));
//		$content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_update').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('btn_update_msgEditGames')).')">';
		if($arr[1]) { // Hat ein Team spielfrei?
			$content .= '<h3 style="margin-top:10px">'.$LANG->getLL('msg_free_of_play') . '</h3><ul>';
			foreach($arr[1] As $freeOfPlay) {
				$content .= '<li>'.$freeOfPlay['team'].$freeOfPlay['match_edit'] . '</li>';
			}
			$content .= '</ul>';
		}
		$content .= '<br /><br />';
		$content .= $this->getFooter($current_league, $current_round, $pid, $formTool);
		return $content;
	}

	/**
	 *
	 * @param tx_cfcleague_models_Team $currentTeam
	 * @param int $current_round
	 * @param tx_cfcleague_league $current_league
	 */
	private function findMatches($currentTeam, $current_round, $current_league) {
		// Mit Matchtable nach Spielen suchen
		$service = tx_cfcleague_util_ServiceRegistry::getMatchService();
		$matchTable = $service->getMatchTableBuilder();
		$matchTable->setCompetitions($current_league->uid);

		$matches = array();
		if($currentTeam == null) {
			// Nun zeigen wir die Spiele des Spieltags
			$matchTable->setRounds($current_round);
		}
		else {
			$matchTable->setTeams($currentTeam->getUid());
		}

		$fields = array();
		$options = array();
		$options['orderby']['MATCH.DATE'] = 'ASC';
		$matchTable->getFields($fields, $options);
		$matches = $service->search($fields, $options);

		return $matches;
	}
	private function makeTeamSelector(&$content, $pid, $current_league) {
    global $LANG;
		$teamOptions = array();
		$teamOptions['selectorId'] = 'teamMatchEdit';
		$teamOptions['noLinks'] = true;
		$teamOptions['firstItem']['id'] = -1;
		$teamOptions['firstItem']['label'] = $LANG->getLL('label_roundmode');
		return $this->getSelector()->showTeamSelector($content, $pid, $current_league, $teamOptions);
	}
	/**
	 * @return tx_cfcleague_selector
	 */
	private function getSelector() {
		if(!is_object($this->selector)) {
			$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
			$this->selector->init($this->getModule()->getDoc(), $this->getModule()->getName());
		}
		return $this->selector;
	}
	/**
	 * @return tx_rnbase_mod_BaseModule
	 */
	private function getModule() {
		return $this->module;
	}
	private function setModule($module) {
		$this->module = $module;
	}

	function getFooter($current_league, $current_round, $pid, $formTool) {
		$params['params'] = '&competition='.$current_league->uid;
		if($current_round)
			$params['params'] .= '&round='.($current_round);
		$params['title'] = $GLOBALS['LANG']->getLL('label_create_match');
		$content = $formTool->createNewLink('tx_cfcleague_games', $pid, $GLOBALS['LANG']->getLL('label_create_match'), $params);
		return $content;
	}
  /**
   * Liefert die passenden Überschrift für die Tabelle
   *
   * @param int $parts
   * @param tx_cfcleague_models_Competition $competition
   * @return array
   */
  private function getHeadline($parts, $competition) {
    global $LANG;
    $arr = array( '',
      $LANG->getLL('tx_cfcleague_games.date'),
      $LANG->getLL('tx_cfcleague_games.status'),
      $LANG->getLL('tx_cfcleague_games.home'),
      $LANG->getLL('tx_cfcleague_games.guest'));

		if($competition->isAddPartResults() || $parts == 1)
			$arr[] = $LANG->getLL('tx_cfcleague_games.endresult');
		// Hier je Spielart die Überschrift setzen
		if($parts > 1)
			for($i=$parts; $i > 0; $i--) {
				$label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_'.$i);
				if(!$label) {
					// Prüfen ob ein default gesetzt ist
					$label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_default');
					if($label) $label = $i. '. ' . $label;
				}
				$arr[] = $label ? $label : $i.'. part';
			}

		$sports = $competition->getSportsService();
		if($sports->isSetBased()) {
			$arr[] = $LANG->getLL('tx_cfcleague_games_sets');
		}

		$arr[] = $LANG->getLL('tx_cfcleague_games.visitors');
//    $arr[] = '';
		return $arr;
	}
  /**
   * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
   * @param array[tx_cfcleague_models_Match] $matches
   * @param tx_cfcleague_models_Competition $competition
   * @return array mit zwei Elementen: Idx 0 enthält Array für Darstellung als Tabelle, Idx 1
   *         enthält, falls vorhanden den Namen des spielfreien Teams
   */
	private function createTableArray($matches, $competition) {
		global $LANG;

		$parts = $competition->getNumberOfMatchParts();
		$arr = Array( 0 => Array( $this->getHeadline($parts, $competition) ));

		foreach($matches As $game) {
			$row = array();

			$isNoMatch = $game->isDummy();
//      $isNoMatch = $game['no_match_home'] || $game['no_match_guest'];

			$table = 'tx_cfcleague_games';
			if(!$isNoMatch) {
				$row[] = $game->getUid().$this->formTool->createEditLink('tx_cfcleague_games', $game->getUid(), '');
				$dataArr = $this->formTool->getTCEFormArray($table, $game->getUid());
				$row[] = $this->formTool->form->getSoloField($table, $dataArr[$table.'_'.$game->getUid()], 'date');

				$row[] = $this->formTool->form->getSoloField($table, $dataArr[$table.'_'.$game->getUid()], 'status');
				$row[] = $this->formTool->createEditLink('tx_cfcleague_teams', $game->record['home'], $game->getHome()->getNameShort());
				$row[] = $this->formTool->createEditLink('tx_cfcleague_teams', $game->record['guest'], $game->getGuest()->getNameShort());

				if($competition->isAddPartResults() && $parts != 1) {
					$row[] = $game->getResult();
				}
				// Jetzt die Spielabschitte einbauen, wobei mit dem letzten begonnen wird
				for($i=$parts; $i > 0; $i--) {
					$row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game->getUid().'][goals_home_'.$i.']', $game->record['goals_home_'.$i], 2) . ' : ' . $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game->getUid().'][goals_guest_'.$i.']', $game->record['goals_guest_'.$i], 2);
				}

				$sports = $competition->getSportsService();
				if($sports->isSetBased()) {
        	$row[] = $this->formTool->createTxtInput('data[tx_cfcleague_games]['.$game->getUid().'][sets]', $game->record['sets'], 12);
				}

        $row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game->getUid().'][visitors]', $game->record['visitors'], 6);
        $arr[0][] = $row;
      }
      else {
        $row = array();
        $isHomeDummy = $game->getHome()->isDummy();
        $row['team'] = $isHomeDummy ? $game->getGuest()->getName() : $game->getHome()->getName();
        $row['team_edit'] = $this->formTool->createEditLink('tx_cfcleague_teams',
                                     ($isHomeDummy ? $game->record['guest'] : $game->record['home']),
                                     ($isHomeDummy ? $game->getGuest()->getNameShort() : $game->getHome()->getNameShort()));
        $row['match_edit'] = $this->formTool->createEditLink('tx_cfcleague_games', $game->getUid());
        $arr[1][] = $row;
      }
    }
//    t3lib_div::debug($this->formTool->form->extJSCODE);

    return $arr;
  }

  /**
   * Aktualisiert die Spiele mit den Daten aus dem Request
   */
  function updateMatches($data) {
    require_once('../class.tx_cfcleague.php');

    $tce =& tx_cfcleague_db::getTCEmain($data);
    $tce->process_datamap();
//t3lib_div::debug($data, 'medit');
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_match_edit.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_match_edit.php']);
}
?>
