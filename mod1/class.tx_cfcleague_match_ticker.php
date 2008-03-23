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

require_once('../class.tx_cfcleague_form_tool.php');
require_once('../class.tx_cfcleague_db.php');

/**
 * Die Klasse stellt den MatchTicker bereit
 */
class tx_cfcleague_match_ticker extends t3lib_extobjbase {
  var $doc, $MCONF;

  /**
   * Initialization of the class
   *
   * @param	object		Parent Object
   * @param	array		Configuration array for the extension
   * @return	void
   */
  function init(&$pObj,$conf)	{
    global $BACK_PATH,$LANG;

    $this->MCONF = $pObj->MCONF;
    $this->id = $pObj->id;
    // Sprachdatei der Tabellen laden
    $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

  }

  /**
   * Bearbeitung von Spielen. Es werden die Paaren je Spieltag angezeigt
   */
  function main() {
    global $LANG;

    $this->doc = $this->pObj->doc;

    $this->formTool = t3lib_div::makeInstance('tx_cfcleague_form_tool');
    $this->formTool->init($this->pObj->doc);

    // Selector-Instanz bereitstellen
    $this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
    $this->selector->init($this->pObj->doc, $this->MCONF);

    // Zuerst mal müssen wir die passende Liga auswählen lassen:
    $content = '';

    $current_league = $this->selector->showLeagueSelector($content,$this->id);
    if($current_league) {
      // Anzeige der vorhandenen Ligen
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
      if (is_array($data['tx_cfcleague_match_notes'])) {
        $this->insertNotes($data);
        $content.= '<i>'.$LANG->getLL('msg_data_saved').'</i>';
      }


      // Und nun das Spiel wählen
      $match = $this->selector->showMatchSelector($content,$this->id,$current_league->getGamesByRound($current_round, true));
      $content.=$this->doc->spacer(5);

      // Wir zeigen die bisherigen Meldungen
      // Dann zeigen wir die FORM für die nächste Meldung
//t3lib_div::debug($match);

      $arr = $this->createFormArray($match);
      $content .= $this->doc->table($arr, $this->_getTableLayoutForm());

      $content .= '<br>';
//      t3lib_div::debug($BACK_PATH);

      // Das Form für den aktuellen Spielstand
      $content .= $this->createStandingForm($match, $current_league);

      $content .= '<br>';

      // Den Update-Button einfügen
      $content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_save').'">';

      // Den JS-Code für Validierung einbinden
      $content  .= $this->formTool->form->JSbottom('editform');

      // Jetzt listen wir noch die zum Spiel vorhandenen Tickermeldungen auf
      $content.=$this->doc->spacer(5);
      $content.=$this->doc->divider(5);
      $content.=$this->doc->spacer(5);

      
      $arr = $this->createTickerArray($match, t3lib_div::_GP('showAll'));
      if($arr) {
        $tickerContent = $this->formTool->createLink('&showAll=1', $this->id, 'Alle Meldungen anzeigen');
        $tickerContent .= $this->doc->table($arr, $this->_getTableLayoutTickerList());
      }
      else
        $tickerContent .= $LANG->getLL('msg_NoTicker');

      $content.=$this->doc->section($LANG->getLL('title_recent_tickers'),$tickerContent);


    }
    else {
      $content .= $this->doc->section('Info:',$LANG->getLL('no_league_in_page'),0,1,ICON_WARN);
    }
    return $content;

  }

  /**
   * Erstellt die Eingabemaske für den Spielstand
   * @param tx_cfcleague_match $match
   * @param tx_cfcleague_league $competition
   */
  function createStandingForm(&$match, &$competition) {
    global $LANG;

    $out = '';
//    $out .= $LANG->getLL('label_current_standings') .': ';

    $parts = $competition->getNumberOfMatchParts();
    for($i=$parts; $i > 0; $i--) {
      $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_'.$i);
      if(!$label) {
        // Prüfen ob ein default gesetzt ist
        $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_default');
        if($label) $label = $i. '. ' . $label;
      }
      $out .= $label ? $label : $i.'. part';
      $out .= ': ';
      $out .= $this->formTool->createIntInput('data[tx_cfcleague_games]['.$match->uid.'][goals_home_'.$i.']', $match->record['goals_home_'.$i],2);
      $out .= ':';
      $out .= $this->formTool->createIntInput('data[tx_cfcleague_games]['.$match->uid.'][goals_guest_'.$i.']', $match->record['goals_guest_'.$i],2);
    }
    $out .= $this->formTool->createSelectSingle('data[tx_cfcleague_games]['.$match->uid.'][status]', $match->record['status'], 'tx_cfcleague_games', 'status');
    
    $out .= '<br />';

// t3lib_div::debug($match->record, 'match');
    return $out;
  }

  /**
   * Für das Formular benötigen wir ein spezielles Layout
   */
  function _getTableLayoutForm() {
    $arr = Array (
      'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
      '0' => Array( // Format für 1. Zeile
           'defCol' => Array('<td valign="top" class="c-headLineTable" style="font-weight:bold;padding:2px 5px;">','</td>') // Format f�r jede Spalte in der 1. Zeile
      ),
      'defRowOdd' => Array ( // Formate für alle geraden Zeilen
          'defCol' => Array('<td valign="top" style="padding:5px 5px;">','</td>') // Format für jede Spalte in jeder Zeile
      ),
      'defRowEven' => Array ( // Formate für alle ungeraden Zeilen (die Textbox)
          'defCol' => Array('<td colspan="2" style="border-bottom:solid 1px #A2AAB8;">&nbsp;</td><td valign="top" align="left" colspan="2" style="padding:2px 5px;border-bottom:solid 1px #A2AAB8;">','</td>') // Format für jede Spalte in jeder Zeile
      )
    );
    return $arr;
  }
  function _getTableLayoutTickerList() {
    $arr = Array (
      'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
      '0' => Array( // Format für 1. Zeile
           'defCol' => Array('<td valign="top" class="c-headLineTable" style="font-weight:bold;padding:2px 5px;">','</td>') // Format f�r jede Spalte in der 1. Zeile
      ),
        'defRow' => Array ( // Formate für alle Zeilen
          'defCol' => Array('<td valign="top" style="padding:0 5px;">','</td>') // Format für jede Spalte in jeder Zeile
        ),
        'defRowEven' => Array ( // Formate für alle Zeilen
          'defCol' => Array('<td valign="top" class="db_list_alt" style="padding:0 5px;">','</td>') // Format für jede Spalte in jeder Zeile
        )
    );
    return $arr;
  }
  
  /**
   * Wir listen die Tickermeldungen des Spiels auf
   *
   */
  function createTickerArray(&$match, $showAll) {
    global $LANG,$TCA;
    $notes = $match->getMatchNotes($showAll ? '' : 5);
    if(!count($notes))
      return 0;

    $arr = Array(Array(
      $LANG->getLL('tx_cfcleague_match_notes.minute'),
      $LANG->getLL('tx_cfcleague_match_notes.type'),
      $LANG->getLL('tx_cfcleague_match_notes.player_home'),
      $LANG->getLL('tx_cfcleague_match_notes.player_guest'),
      $LANG->getLL('tx_cfcleague_match_notes.comment'),
      ''));

    // Die NotesTypen laden, Wir gehen mal davon aus, daß die TCA geladen ist...
    $types = array();
    foreach($TCA['tx_cfcleague_match_notes']['columns']['type']['config']['items'] As $item){
      $types[$item[1]] = $LANG->sL($item[0]);
    }

    $playersHome = $match->getPlayerNamesHome();
    $playersGuest = $match->getPlayerNamesGuest();

    foreach($notes As $note){
      $row = array();
      $row[] = $note['minute'];
      $row[] = $types[$note['type']];

      $row[] = intval($note['player_home']) == -1 ? $LANG->getLL('tx_cfcleague.unknown') : $playersHome[$note['player_home']];
      $row[] = intval($note['player_guest']) == -1 ? $LANG->getLL('tx_cfcleague.unknown') : $playersGuest[$note['player_guest']];


      $row[] = $note['comment'];
      $row[] = $this->formTool->createEditLink('tx_cfcleague_match_notes', $note['uid']);
      $arr[] = $row;
    }
    return $arr;
  }

  /**
   * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages
   */
  function createFormArray(&$match) {
    global $LANG;
    
    $arr = Array(Array(
      $LANG->getLL('tx_cfcleague_match_notes.minute'),
      $LANG->getLL('tx_cfcleague_match_notes.type'),
      $LANG->getLL('tx_cfcleague_match_notes.player_home'),
      $LANG->getLL('tx_cfcleague_match_notes.player_guest'),
//      $LANG->getLL('tx_cfcleague_match_notes.comment'),
      ));

    // TS-Config der aktuellen Seite laden, um die Anzahl der Felder zu ermitteln
		$pageTSconfig = t3lib_BEfunc::getPagesTSconfig($this->id);
		$inputFields = (is_array($pageTSconfig) && is_array($pageTSconfig['tx_cfcleague.']['matchTickerCfg.'])) ?
		  intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['numberOfInputFields']) : 3;
		$cols = (is_array($pageTSconfig) && is_array($pageTSconfig['tx_cfcleague.']['matchTickerCfg.'])) ?
		  intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['commentFieldCols']) : 30;
		$rows = (is_array($pageTSconfig) && is_array($pageTSconfig['tx_cfcleague.']['matchTickerCfg.'])) ?
		  intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['commentFieldRows']) : 5;
		  
		// Wenn kein sinnvoller Wert vorhanden ist, bleibt der Standard bei 3
		$inputFields = $inputFields ? $inputFields : 3;
    for($i=0; $i < $inputFields; $i++){
      $row = array();

      $row[] = $this->formTool->createIntInput('data[tx_cfcleague_match_notes][NEW'.$i.'][minute]', '',2,3) . '+' . 
               $this->formTool->createIntInput('data[tx_cfcleague_match_notes][NEW'.$i.'][extra_time]', '',1,2) . 
               $this->formTool->createHidden('data[tx_cfcleague_match_notes][NEW'.$i.'][game]', $match->uid);
      $row[] = $this->formTool->createSelectSingle('data[tx_cfcleague_match_notes][NEW'.$i.'][type]', '0','tx_cfcleague_match_notes','type');
      $row[] = $this->formTool->createSelectSingleByArray(
                      'data[tx_cfcleague_match_notes][NEW'.$i.'][player_home]', '0', $match->getPlayerNamesHome(1,1));
      $row[] = $this->formTool->createSelectSingleByArray(
                      'data[tx_cfcleague_match_notes][NEW'.$i.'][player_guest]', '0', $match->getPlayerNamesGuest(1,1));

      $arr[] = $row;

      // Das Bemerkungsfeld kommt in die nächste Zeile
      $row = array();
      $row[] = $this->formTool->createTextArea('data[tx_cfcleague_match_notes][NEW'.$i.'][comment]', '', $cols, $rows);
      $arr[] = $row;

    }

//    $arr[] = Array();
//    t3lib_div::debug($this->formTool->form->extJSCODE);

    return $arr;
  }

  /**
   * Erstellt eine neue Spielaktion mit den Daten aus dem Request
   */
  function insertNotes($data) {
    global $BE_USER;
    $notes = $data['tx_cfcleague_match_notes'];

    $tstamp = time();
    foreach($notes As $noteId => $note){
      $playerOk = !(intval($note['player_home']) != 0 && intval($note['player_guest']) != 0);

      // Ohne Minute (Feld ist leer) wird nix gespeichert
      // kleinste Minute ist -1 für versteckte Meldungen
      if(strlen($note['minute']) > 0 && intval($note['minute']) >= -1 && $playerOk) { // Minute ist Pflichtfeld
        $data['tx_cfcleague_match_notes'][$noteId]['pid'] = $this->id;
//        $data['tx_cfcleague_match_notes'][$noteId]['tstamp'] = $tstamp;
//        $data['tx_cfcleague_match_notes'][$noteId]['sorting'] = intval($note['game']) * 100 + intval($note['minute']) ;
//        $data['tx_cfcleague_match_notes'][$noteId]['cruser_id'] = $BE_USER->user['uid'];
        $data['tx_cfcleague_match_notes'][$noteId]['comment'] = nl2br($data['tx_cfcleague_match_notes'][$noteId]['comment']);
      }
      else {
        unset($data['tx_cfcleague_match_notes'][$noteId]);
      }
    }
    if (!count($data['tx_cfcleague_match_notes'])) {
        unset($data['tx_cfcleague_match_notes']);
    }
    // Die neuen Notes werden jetzt gespeichert
    reset($data);
    $tce =& tx_cfcleague_db::getTCEmain($data);
    $tce->process_datamap();
//    t3lib_div::debug($data, 'Saved Note!!');

  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_match_ticker.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_match_ticker.php']);
}
?>
