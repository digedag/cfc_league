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

require_once (PATH_t3lib.'class.t3lib_extobjbase.php');
$BE_USER->modAccess($MCONF,1);


/**
 * Die Klasse verwaltet die automatische Erstellung von Spielplänen
 */
class tx_cfcleague_generator extends t3lib_extobjbase {
  var $doc, $MCONF;

  /**
   * Initialization of the class
   *
   * @param	object		Parent Object
   * @param	array		Configuration array for the extension
   * @return	void
   */
  function init(&$pObj,$conf)	{
    parent::init($pObj,$conf);
    $this->MCONF = $pObj->MCONF;
    $this->id = $pObj->id;
  }

  /**
   * Verwaltet die Erstellung von Spielplänen von Ligen
   */
  function main() {
    global $LANG;
    // Zuerst mal müssen wir die passende Liga auswählen lassen:
    // Entweder global über die Datenbank oder die Ligen der aktuellen Seite

    $this->doc = $this->pObj->doc;

    $this->formTool = tx_div::makeInstance('tx_rnbase_util_FormTool');
    $this->formTool->init($this->doc);

    // Selector-Instanz bereitstellen
    $this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
    $this->selector->init($this->doc, $this->MCONF);
    
    $content = '';

    // Anzeige der vorhandenen Ligen
    $current_league = $this->selector->showLeagueSelector($content,$this->id);
    if($current_league) {

      // Liga darf noch keine Spiele haben
      $matchCnt = count($current_league->getGames());
    	
      if($matchCnt > 0){
        $content.=$this->doc->section($LANG->getLL('warning').':',$LANG->getLL('msg_league_generation_hasmatches'),0,1,ICON_WARN);
        $content.='<br/><br/>';
      }

      // Wir holen die Mannschaften und den GameString aus der Liga
      // Beides jagen wir durch den Generator

      $options['halfseries'] = intval(t3lib_div::_GP('option_halfseries'));
      $options['nomatch'] = $current_league->hasDummyTeam();
      $options['firstmatchday'] = $current_league->getNumberOfRounds();
      $options['firstmatchnumber'] = $current_league->getLastMatchNumber();
      
      // Zunächst mal Anzeige der Daten
      $gen = t3lib_div::makeInstance('tx_cfcleague_generator2');
      $table = $gen->main($current_league->getTeamIds(),$current_league->getGenerationKey(), $options);

      $data = t3lib_div::_GP('data');
      // Haben wir Daten im Request?
      if (is_array($data['rounds']) && t3lib_div::_GP('update')) {
//  t3lib_div::debug($data['rounds'], 'GP generator') ;
        $content .= $this->doc->section($LANG->getLL('message').':',
                          $this->createGames($data['rounds'],$table, $current_league),
                          0,1,ICON_INFO);
      }
      else
      {
//      	t3lib_div::debug(, 'tx_cfcleague_generator'); // TODO: Remove me!
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

          // Den JS-Code für Validierung einbinden
          $content  .= $this->formTool->form->JSbottom('editform');
        }
        
      }
    }
    else {
      $content.=$this->doc->section('Info:',$LANG->getLL('no_league_in_page'),0,1,ICON_WARN);
    }
    return $content;
  }

  /**
   * Erstellt die Spiele der Liga. Diese werden aus den Daten gebildet, die im Request liegen.
   */
  function createGames($rounds, $table, &$league) {
    global $LANG;

    // Aus den Spielen der $table die TCA-Datensätze erzeugen
    $data['tx_cfcleague_games'] = array();

    // Wir erstellen die Spiel je Spieltag
    foreach($rounds As $roundId => $roundData){
      // Die ID des Spieltags ermitteln
      $roundId = $roundData['round'];

      // Die Paarungen holen
      $games = $table[$roundId];

      foreach($games As $match) {
        // Die Basis des Spieldatensatzes ist $roundData
        $new_match = $roundData; // Wir arbeiten auf einer Kopie des Arrays
        $new_match['home'] = $match->home;
        $new_match['guest'] = $match->guest;
        $new_match['match_no'] = $match->nr2;
        $new_match['competition'] = $league->uid;
        $new_match['pid'] = $this->id;
        $data['tx_cfcleague_games']['NEW'.$match->nr] = $new_match;
      }
    }

    // Die neuen Notes werden jetzt gespeichert
    reset($data);
    $tce =& tx_cfcleague_db::getTCEmain($data);
    $tce->process_datamap();
    
    return $LANG->getLL('msg_matches_created');
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


    $tableLayout = Array (
      'table' => Array('<table class="typo3-dblist" style="margin:0;" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
    	'0' => Array( // Format für 1. Zeile
         'defCol' => Array('<td valign="top" class="c-headLineTable" style="font-weight:bold;padding:2px 5px;">','</td>') // Format f�r jede Spalte in der 1. Zeile
      ),
      'defRow' => Array ( // Formate für alle Zeilen
        'defCol' => Array('<td valign="top" style="padding:5px 5px 0 5px; border-bottom:solid 1px #A2AAB8;">','</td>') // Format für jede Spalte in jeder Zeile
      ),
    );
    $tableLayout2 = $tableLayout;
    $tableLayout2['defRow'] = Array ( // Formate für alle Zeilen
        'defCol' => Array('<td valign="top" style="padding:0 5px;">','</td>') // Format für jede Spalte in jeder Zeile
      );
    $tableLayout2['defRowEven'] = Array ( // Formate für alle Zeilen
        'defCol' => Array('<td valign="top" class="db_list_alt" style="padding:0 5px;">','</td>') // Format für jede Spalte in jeder Zeile
      );
    

    $arr = Array(Array('Runde','Name','Termin','Ansetzungen'));
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
   * Prüft, ob für die Liga ein Spielplan erzeugt werden kann.
   * - darf noch keine Spiele haben
   * - Mannschaften müssen da sein
   * @return 0 - Spielplan kann erstellt werden, >0 - kann nicht erstellt werden
   */
  function checkLeagueGeneration($current_league){
    return count($current_league->getGames());
  }

  /**
   * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
   */
  function createMatchTableArray(&$games, &$league) {

    $teamNames = $league->getTeamNames();
    $arr = Array(Array('Spiel-Nr.','Heim','Gast'));
    foreach($games As $match){
      $row = array();

      $row[] = $match->noMatch ? '' : str_pad($match->nr2,3,'000',STR_PAD_LEFT);
      $row[] = $teamNames[$match->home];
      $row[] = $teamNames[$match->guest];

      $arr[] = $row;
    }

    return $arr;
  }

}

/**
 * Ergebnis ist eine Liste von Spieltagen.
 * Jeder Spieltag enthält x Spiele
 *
 */
class tx_cfcleague_generator2 {
  var $errorMsg;

  /**
   * Optionen die per $options übergeben werden können:
   * halfseries - wenn != 0 wird nur die erste Halbserie erzeugt
   * nomatch - wenn ein Team als Spielfrei gewertet werden soll, dann muss es hier 
   *          übergeben werden. Es muss auch im Array $teams enthalten sein!
   * firstmatchday - Spieltag, mit dem die Zählung beginnen soll
   * firstmatchnumber - Spielnummer mit der die Zählung beginnen soll
   * 
   * @param array $teams sortiertes Array der Liga-Teams. Kann theoretisch von jedem Typ sein. Eine TeamID
   *          wäre aber gut.
   * @param string $table Spielplan-Tabelle
   * @param array $options
   * @return ein Array mit Key: Spieltag(int) und Value: Array der Spiele des Spieltags
   */
  function main($teams, $table, $options) {
  	global $LANG;
  	// In Teams müssen eigentlich nur die UIDs der Teams stehen
    $table = $this->splitTableString($table);
    if(!count($table)) {
    	$this->errors[] = $LANG->getLL('msg_no_matchkeys');
    	return array();
    }

    // Prüfen, ob die Daten stimmen
    $this->warnings = $this->checkParams($teams, $table);
    // Jetzt kann man den Spielplan aufbauen
    $ret = $this->createTable($teams, $table, $options);

    return $ret;
  }

  /**
   * Erstellt den eigentlichen Spielplan
   * @param array $teams
   * @param array $table
   * @param array $options
   */
  function createTable($teams, $table, $options) {
  	$option_halfseries = isset($options['halfseries']) ? intval($options['halfseries']) : 0;
  	$option_nomatch = isset($options['nomatch']) ? intval($options['nomatch']) : 0;
  	// Alle Elemente einen Indexplatz hochschieben, damit die Team-Nr stimmt.
    array_unshift($teams,0);
    $matchCnt = 0; // ID des Spieldatensatzes. Wird für jedes angelegte Spiel gezählt
    // Spielnummer. Spielfreie Spiele werden nicht gezählt
    $matchCnt2 = isset($options['firstmatchnumber']) ? intval($options['firstmatchnumber']) : 0; 
    // Zählung des Spieltags
    $dayCnt = isset($options['firstmatchday']) ? intval($options['firstmatchday']) : 0;

    $ret = array();
    foreach($table as $day => $matches) {
      $dayArr = array(); // Hier kommen die Spiele rein
      foreach($matches as $k => $match) {
        $teamIds = explode('-',$match);
        // Ist es ein spielfreies Spiel
        $isNoMatch = $teams[$teamIds[0]] == $option_nomatch || $teams[$teamIds[1]] == $option_nomatch;
        $dayArr[] = new Match(++$matchCnt, $isNoMatch ? '': ++$matchCnt2, $teams[$teamIds[0]], $teams[$teamIds[1]], $isNoMatch);
      }
      $ret[++$dayCnt] = $dayArr;
    }
    // die Rückspiele
    if($option_halfseries == 0) {
      foreach($table as $day => $matches) {
        $dayArr = array(); // Hier kommen die Spiele rein
        foreach($matches as $k => $match) {
          $teamIds = explode('-',$match);
          $isNoMatch = $teams[$teamIds[0]] == $option_nomatch || $teams[$teamIds[1]] == $option_nomatch;
          $dayArr[] = new Match(++$matchCnt, $isNoMatch ? '': ++$matchCnt2, $teams[$teamIds[1]], $teams[$teamIds[0]], $isNoMatch);
//          $dayArr[] = new Match(++$matchCnt, $teams[$teamIds[1]], $teams[$teamIds[0]]);
        }
        $ret[++$dayCnt] = $dayArr;
      }
    }
    return $ret;
  }
  /**
   * Prüft, ob die Spieltabelle zur Anzahl der Mannschaften passt
   * Es wird ein Array mit gefundenen Warnungen geliefert
   * @return array
   */
  function checkParams($teams, $table) {
  	global $LANG;

  	$warnings = array();
    $teamCnt = count($teams);
    // Anzahl Spieltage prüfen
    if($teamCnt-1 != count($table)) {
      $warnings[] = sprintf($LANG->getLL('msg_wrongmatchdays'), ($teamCnt-1), count($table));
    }
    // Anzahl Spiele pro Spieltag prüfen
    $matchCnt = intval($teamCnt / 2);
    foreach($table as $day => $matches) {
      if($matchCnt != count($matches)) {
      	$warnings[] = sprintf($LANG->getLL('msg_wrongmatches4matchday'), $day, count($matches), $matchCnt);
      }
      // Stimmen die Indizes?
      foreach($matches as $k => $match) {
        $matchArr = explode('-',$match);
        if(count($matchArr) != 2)
      		$warnings[] = sprintf($LANG->getLL('msg_wrongmatch_syntax'), $day, $match);
        if(intval($matchArr[0]) < 1 || intval($matchArr[0]) > $teamCnt)
      		$warnings[] = sprintf($LANG->getLL('msg_wrongmatch_homeidx'), $day, $matchArr[0]);
        //$warnings[] = "Fehler bei Spieltag $day: TeamIndex ist falsch ".$match;
        if(intval($matchArr[1]) < 1 || intval($matchArr[1]) > $teamCnt)
      		$warnings[] = sprintf($LANG->getLL('msg_wrongmatch_guestidx'), $day, $matchArr[1]);
        //$warnings[] = "Fehler bei Spieltag $day: TeamIndex ist falsch ".$match;
      }
    }
    return $warnings;
  }

  /**
   * Format: 1-4,3-2|2-1,4-3|1-3,2-4
   * Ergebnis: (1 => ('1-4','3-2'), 2=>())
   */
  function splitTableString($table) {
    $ret = array();
    if(!strlen(trim($table))) return $ret; // Kein String gesetzt
  	$days = explode('|',$table);
    foreach($days as $key => $matches) {
      $ret[$key+1] = explode(',',$matches);
    }
    return $ret;
  }

}

/**
 * Datenhalterklasse für Spiele
 */
class Match {
  var $home, $guest, $nr, $nr2, $noMatch;
  function Match($n,$n2,$h,$g, $noMatch){
    $this->nr = $n; // ID
    $this->nr2 = $n2; // Spielnummer
    $this->home = $h;
    $this->guest = $g;
    $this->noMatch = $noMatch;
  }
  function toString() {
    return 'SNr '.$this->nr.': ' . $this->home . ' - ' . $this->guest . "\n";
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_generator.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_generator.php']);
}
?>