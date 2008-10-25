<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (rene@system25.de)
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
  var $doc, $MCONF;

  /**
   * Initialisiert das Objekt mit dem Template und der Modul-Config.
   */
  /**
   * Initialization of the class
   *
   * @param	object		Parent Object
   * @param	array		Configuration array for the extension
   * @return	void
   */
  function init(&$pObj,$conf)	{
    global $BACK_PATH,$LANG;

    parent::init($pObj,$conf);

    $this->MCONF = $pObj->MCONF;
    $this->id = $pObj->id;
    // Sprachdatei der Tabellen laden
    $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

  }

  /**
   * Bearbeitung von Spielen. Es werden die Paaren je Spieltag angezeigt
   */
  function main(&$MCONF,$pid, $doc, &$formTool, &$current_league) {
    global $LANG;

		$this->MCONF = $MCONF;
		$this->id = $pid;
		$this->doc = $doc;

		$this->formTool = $formTool;
		$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');
    
		// Selector-Instanz bereitstellen
		$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$this->selector->init($this->doc, $this->MCONF);

		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		$content = '';
		$content.=$this->doc->spacer(5);

		if(!count($current_league->getRounds())){
			$content .= $LANG->getLL('no_round_in_league');
			return $content;
		}
		// Jetzt den Spieltag wählen lassen
		$current_round = $this->selector->showRoundSelector($content,$this->id,$current_league);
		$content.=$this->doc->spacer(5);
		$data = t3lib_div::_GP('data');
		// Haben wir Daten im Request?
		if (is_array($data['tx_cfcleague_games'])) {
			$this->updateMatches($data);
			// Wir löschen auch die Mementos für diesen Wettbewerb
			if (is_object($serviceObj = t3lib_div::makeInstanceService('memento'))) {
				// Memento über den SuperKey (Wettbewerb) löschen
				$serviceObj->clear('', $current_league->uid);
			}
		}

		// Nun zeigen wir die Spiele des Spieltags
		$games = $current_league->getGamesByRound($current_round);
		$arr = $this->createTableArray($games, $current_league);

		$content .= $this->doc->table($arr[0]);

		// Den Update-Button einfügen
		$content .= $this->formTool->createSubmit('update',$LANG->getLL('btn_update'), $GLOBALS['LANG']->getLL('btn_update_msgEditGames'));
//		$content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_update').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('btn_update_msgEditGames')).')">';
		if($arr[1]) { // Hat ein Team spielfrei?
			$content .= '<h3 style="margin-top:10px">'.$LANG->getLL('msg_free_of_play') . '</h3><ul>';
			foreach($arr[1] As $freeOfPlay) {
				$content .= '<li>'.$freeOfPlay['team'].$freeOfPlay['match_edit'] . '</li>';
			}
			$content .= '</ul>';
		}
		return $content;
  }

  /**
   * Liefert die passenden Überschrift für die Tabelle
   *
   * @param int $parts
   * @return array
   */
  function getHeadline($parts) {
    global $LANG;
    $arr = array( '',
      $LANG->getLL('tx_cfcleague_games.date'),
      $LANG->getLL('tx_cfcleague_games.status'),
      $LANG->getLL('tx_cfcleague_games.home'),
      $LANG->getLL('tx_cfcleague_games.guest'));

    // Hier je Spielart die Überschrift setzen
    for($i=$parts; $i > 0; $i--) {
      $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_'.$i);
      if(!$label) {
        // Prüfen ob ein default gesetzt ist
        $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_default');
        if($label) $label = $i. '. ' . $label;
      }
      $arr[] = $label ? $label : $i.'. part';
    }
    $arr[] = $LANG->getLL('tx_cfcleague_games.visitors');
    $arr[] = '';
    return $arr;
  }
  /**
   * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
   * @param array $matches
   * @param tx_cfcleague_league $competition
   * @return array mit zwei Elementen: Idx 0 enthält Array für Darstellung als Tabelle, Idx 1
   *         enthält, falls vorhanden den Namen des spielfreien Teams
   */
  function createTableArray($matches, &$competition) {
    global $LANG;

    $parts = $competition->getNumberOfMatchParts();
    $arr = Array( 0 => Array( $this->getHeadline($parts) ));
    

    foreach($matches As $game){
      $row = array();

      $isNoMatch = $game['no_match_home'] || $game['no_match_guest'];

      $table = 'tx_cfcleague_games';
			if(!$isNoMatch) {
				$row[] = $game['uid'];
				$dataArr = $this->formTool->getTCEFormArray($table, $game['uid']);
				$row[] = $this->formTool->form->getSoloField($table,$dataArr[$table.'_'.$game['uid']],'date');

				$row[] = $this->formTool->form->getSoloField($table,$dataArr[$table.'_'.$game['uid']],'status');
				$row[] = $this->formTool->createEditLink('tx_cfcleague_teams',$game['home'],$game['short_name_home']);
				$row[] = $this->formTool->createEditLink('tx_cfcleague_teams',$game['guest'],$game['short_name_guest']);

				// Jetzt die Spielabschitte einbauen, wobei mit dem letzten begonnen wird
				for($i=$parts; $i > 0; $i--) {
					$row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][goals_home_'.$i.']',$game['goals_home_'.$i],2) . ' : ' . $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][goals_guest_'.$i.']',$game['goals_guest_'.$i],2);
				}
        
//        $row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][goals_home_2]',$game['goals_home_2'],2) . ' : ' . $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][goals_guest_2]',$game['goals_guest_2'],2);
//        $row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][goals_home_1]',$game['goals_home_1'],2) . ' : ' . $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][goals_guest_1]',$game['goals_guest_1'],2);

        $row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$game['uid'].'][visitors]',$game['visitors'],6);
        $row[] = $this->formTool->createEditLink('tx_cfcleague_games',$game['uid']);
        $arr[0][] = $row;
      }
      else {
        $row = array();
        $row['team'] = $game['no_match_home'] ? $game['name_guest'] : $game['name_home'];
        $row['team_edit'] = $this->formTool->createEditLink('tx_cfcleague_teams', 
                                     ($game['no_match_home'] ? $game['guest'] : $game['home']), 
                                     ($game['no_match_home'] ? $game['short_name_guest'] :$game['short_name_home']));
        $row['match_edit'] = $this->formTool->createEditLink('tx_cfcleague_games',$game['uid']);
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
