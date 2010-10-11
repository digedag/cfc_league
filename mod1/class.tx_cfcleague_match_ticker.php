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

tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('tx_rnbase_mod_BaseModFunc');


/**
 * Die Klasse stellt den MatchTicker bereit
 */
class tx_cfcleague_match_ticker extends tx_rnbase_mod_BaseModFunc {
	var $doc, $MCONF;

	/**
	 * Method getFuncId
	 * 
	 * @return	string
	 */
	function getFuncId() {
		return 'functicker';
	}

	/**
	 * Bearbeitung von Spielen. Es werden die Paaren je Spieltag angezeigt
	 */
	protected function getContent($template, &$configurations, &$formatter, $formTool) {
//	function main() {
		global $LANG;

		$this->doc = $this->getModule()->getDoc();

		// Selector-Instanz bereitstellen
		$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$this->selector->init($this->getModule()->getDoc(), $this->getModule()->getName());

		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		$content = '';

		$selector = '';
		// Anzeige der vorhandenen Ligen
		$current_league = $this->getSelector()->showLeagueSelector($selector, $this->getModule()->getPid());
		if(!$current_league)
			return $this->doc->section('Info:',$LANG->getLL('no_league_in_page'),0,1,ICON_WARN);

		if(!count($current_league->getRounds())){
			if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
				$this->pObj->subselector = $selector; // FIXME!!
			else 
				$content .= '<div class="cfcleague_selector">'.$selector.'</div><div style="clear:both"/>';
			$content .= $LANG->getLL('no_round_in_league');
			return $content;
		}
		// Jetzt den Spieltag wählen lassen
		$current_round = $this->getSelector()->showRoundSelector($selector,$this->id,$current_league);

		// Und nun das Spiel wählen
		$match = $this->getSelector()->showMatchSelector($selector,$this->id,$current_league->getGamesByRound($current_round, true));
		if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
			$this->pObj->subselector = $selector;
		else 
			$content .= '<div class="cfcleague_selector">'.$selector.'</div><div style="clear:both"/>';

		$update = t3lib_div::_GP('update');
		$data = t3lib_div::_GP('data');
		// Haben wir Daten im Request?
		if ($update && is_array($data['tx_cfcleague_match_notes'])) {
			$this->insertNotes($data);
			$content.= '<i>'.$LANG->getLL('msg_data_saved').'</i>';
			// Jetzt das Spiel nochmal laden, da sich Daten geändert haben könnten
			$match->reset();
		}

		// Wir zeigen die bisherigen Meldungen
		// Dann zeigen wir die FORM für die nächste Meldung
		$content .= $this->getInstantMessageField();
		$content .= $this->getFormHeadline();
		$arr = $this->createFormArray($match);
		$content .= $this->doc->table($arr, $this->_getTableLayoutForm());
		$content .= '<br />';
		
		// Das Form für den aktuellen Spielstand
		$content .= $this->createStandingForm($match, $current_league);
		$content .= '<br />';
		// Den Update-Button einfügen
		$content .= $this->getModule()->getFormTool()->createSubmit('update', $LANG->getLL('btn_save'));
		// Den JS-Code für Validierung einbinden
		$content  .= $this->getModule()->getFormTool()->form->JSbottom('editform');
		// Jetzt listen wir noch die zum Spiel vorhandenen Tickermeldungen auf
		$content.=$this->doc->spacer(5);
		$content.=$this->doc->divider(5);
		$content.=$this->doc->spacer(5);
		$arr = $this->createTickerArray($match, t3lib_div::_GP('showAll'));
		if($arr) {
			$tickerContent = $this->getModule()->getFormTool()->createLink('&showAll=1', $this->id, $LANG->getLL('label_showAllTickers'));
			$tickerContent .= $this->doc->table($arr);
		}
		else
			$tickerContent .= $LANG->getLL('msg_NoTicker');

		$content.=$this->doc->section($LANG->getLL('title_recent_tickers'),$tickerContent);
		return $content;
	}

	/**
	 * Liefert ein Textfeld für eine SofortMeldung per Ajax
	 * @return string
	 */
	private function getInstantMessageField() {
		if(tx_rnbase_util_TYPO3::isTYPO3VersionOrHigher(4004000)) {
			/** @var $pageRenderer t3lib_PageRenderer */
			$pageRenderer = $this->doc->getPageRenderer();
			$pageRenderer->loadPrototype();
			$pageRenderer->loadScriptaculous('builder,effects,controls');
		}
		else {
			$this->doc->loadJavascriptLib('contrib/scriptaculous/scriptaculous.js?load=builder,effects,controls');
		}
		$ret = '';
		$ret = $this->doc->backPath;
		$ret = '<script type="text/javascript" src="js/ticker.js"></script>';
		$ret .= '<p id="instant" style="background:yellow; margin-bottom:10px; padding:3px">'.$GLOBALS['LANG']->getLL('msg_sendInstant').'</p>';
		return '<div id="t3sportsTicker">'.$ret.'</div>';
	}
  private function getFormHeadline() {
    $stop = t3lib_div::_GP('btn_watch_stop');
  	$start = t3lib_div::_GP('btn_watch_start');
  		// Daten: Startuhrzeit auf dem Client und gewünschtes offset
		$startTime = array('watch_starttime' => $stop ? 0 : t3lib_div::_GP('watch_localtime'));
		$modValues = t3lib_BEfunc::getModuleData(array ('watch_starttime' => ''), $start || $stop ? $startTime : array(),$this->getModule()->getName());
		$startTime = isset($modValues['watch_starttime']) ? $modValues['watch_starttime'] : '0';
		// Der übergebene Offset wird immer gespeichert
		$offset = array('watch_offset' => intval(t3lib_div::_GP('watch_offset')));
		$modValues = t3lib_BEfunc::getModuleData(array ('watch_offset' => ''), $offset,$this->getModule()->getName() );
		$offset = isset($modValues['watch_offset']) ? $modValues['watch_offset'] : '0';


  	$out = '<table width="100%"><tr><td style="text-align:left">';
    $out .= $this->getModule()->getFormTool()->createSubmit('update', $GLOBALS['LANG']->getLL('btn_save'));
  	$out .= '</td><td style="text-align:left">';
    
    $out .= $GLOBALS['LANG']->getLL('label_tickeroffset') . ': ';
    $out .= $this->getModule()->getFormTool()->createTxtInput('watch_offset', $offset, '2') . ' ';
    $out .= $GLOBALS['LANG']->getLL('label_tickerminute') . ': ';
    $out .= $this->getModule()->getFormTool()->createTxtInput('watch', 0, '5');
    $out .= $this->getModule()->getFormTool()->createHidden('watch_starttime', $startTime);
    $out .= $this->getModule()->getFormTool()->createHidden('watch_localtime', 0);
    $out .= $this->getModule()->getFormTool()->createHidden('watch_minute', 0);
    if($startTime > 0)
	  	$out .= $this->getModule()->getFormTool()->createSubmit('btn_watch_stop', $GLOBALS['LANG']->getLL('btn_watchstop'));
    else
	    $out .= $this->getModule()->getFormTool()->createSubmit('btn_watch_start', $GLOBALS['LANG']->getLL('btn_watchstart'));
	  $out .= '</td></tr></table>';
    
    $out .= '<script>
	function ticker() {
		form = document.forms[0];
		now = (new Date()).getTime();
		form.watch_localtime.value = now;

		start = form.watch_starttime.value;
		if(start > 0) {
			offset = trim(form.watch_offset.value);
			offset = parseInt(isNaN(offset) || offset == "" ? 0 : offset);
			diff = new Date(now - start);
			std = diff.getHours();
			min = diff.getMinutes() + ((std - 1) * 60) + offset;
			sec = diff.getSeconds();
			form.watch_minute.value = min + 1;
			form.watch.value = ((min>9) ? min : "0" + min) + ":" + ((sec>9) ? sec : "0" + sec);
		}
		setTimeout("ticker()", 1000);
	}
	function trim(str) {
		return str ? str.replace(/\s+/,"") : "";
	}
  ticker();

	function setMatchMinute(elem) {
		min = form.watch_minute.value;
		if(min == 0) return;
		line = elem.name.match(/NEW(\d+)/)[1];
		var elements = Form.getInputs(elem.form);
		for (var i = 0; i < elements.length; i++) {
			if(elements[i].name == "data[tx_cfcleague_match_notes][NEW"+line+"][minute]_hr") {
				if(Field.getValue(elements[i]) == "") {
					elements[i].value = min;
					typo3FormFieldGet("data[tx_cfcleague_match_notes][NEW"+line+"][minute]", "int", "", 0,0);
				}
			}
		}
	}
</script>
    ';
    
  	return $out;
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
      $out .= $this->getModule()->getFormTool()->createIntInput('data[tx_cfcleague_games]['.$match->uid.'][goals_home_'.$i.']', $match->record['goals_home_'.$i],2);
      $out .= ':';
      $out .= $this->getModule()->getFormTool()->createIntInput('data[tx_cfcleague_games]['.$match->uid.'][goals_guest_'.$i.']', $match->record['goals_guest_'.$i],2);
    }
		$out .= $this->getModule()->getFormTool()->createSelectSingle('data[tx_cfcleague_games]['.$match->uid.'][status]', $match->record['status'], 'tx_cfcleague_games', 'status');
		$out .= $LANG->getLL('tx_cfcleague_games.visitors') .': ';
		$out .= $this->getModule()->getFormTool()->createIntInput('data[tx_cfcleague_games]['.$match->uid.'][visitors]', $match->record['visitors'],5);
		$out .= $this->getModule()->getFormTool()->createHidden('t3matchid', $match->uid);
		$out .= '<br />';
		return $out;
	}

	/**
	 * Für das Formular benötigen wir ein spezielles Layout
	 */
	function _getTableLayoutForm() {
		$arr = Array (
			'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'0' => Array( // Format für 1. Zeile
				'tr'		=> Array('<tr class="c-headLineTable">','</tr>'),
				'defCol' => Array('<td valign="top" class="t3-row-header c-headLineTable" style="font-weight:bold;padding:2px 5px;">','</td>') // Format f�r jede Spalte in der 1. Zeile
			),
			'defRowOdd' => Array ( // Formate für alle geraden Zeilen
				'tr'	   => Array('<tr class="db_list_normal">', '</tr>'),
				'defCol' => Array('<td valign="top" style="padding:5px 5px;">','</td>') // Format für jede Spalte in jeder Zeile
			),
			'defRowEven' => Array ( // Formate für alle ungeraden Zeilen (die Textbox)
				'tr'	   => Array('<tr class="db_list_alt">', '</tr>'),
				'defCol' => Array('<td colspan="2" style="border-bottom:solid 1px #A2AAB8;">&nbsp;</td><td valign="top" align="left" colspan="2" style="padding:2px 5px;border-bottom:solid 1px #A2AAB8;">','</td>') // Format für jede Spalte in jeder Zeile
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
//		$types = array();
//		foreach($TCA['tx_cfcleague_match_notes']['columns']['type']['config']['items'] As $item){
//			$types[$item[1]] = $LANG->sL($item[0]);
//		}
		$types = $this->getTickerTypes();

		$playersHome = $match->getPlayerNamesHome();
		$playersGuest = $match->getPlayerNamesGuest();

		foreach($notes As $note){
			$row = array();

			$min = $note['minute'] . ($note['extra_time'] ? '+'. $note['extra_time'] : '' );
			$min .= $note['hidden'] ? '*' : '';
			$row[] = $min;
			$row[] = $types[$note['type']];

			$row[] = intval($note['player_home']) == -1 ? $LANG->getLL('tx_cfcleague.unknown') : $playersHome[$note['player_home']];
			$row[] = intval($note['player_guest']) == -1 ? $LANG->getLL('tx_cfcleague.unknown') : $playersGuest[$note['player_guest']];

			$row[] = $note['comment'];
			$row[] = $this->getModule()->getFormTool()->createEditLink('tx_cfcleague_match_notes', $note['uid']);
			$arr[] = $row;
		}
		return $arr;
	}

	private function getTickerTypes() {
		$srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
		$tcaTypes = $srv->getMatchNoteTypes4TCA();
		$types = array();
		foreach($tcaTypes As $typeDef) {
			$types[$typeDef[1]] = $typeDef[0];
		}
		return $types;
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
			intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['commentFieldRows']) : 3;

		$types = $this->getTickerTypes();

		// Wenn kein sinnvoller Wert vorhanden ist, bleibt der Standard bei 3
		$inputFields = $inputFields ? $inputFields : 3;
    for($i=0; $i < $inputFields; $i++){
      $row = array();

      $row[] = $this->getModule()->getFormTool()->createIntInput('data[tx_cfcleague_match_notes][NEW'.$i.'][minute]', '',2,3) . '+' . 
               $this->getModule()->getFormTool()->createIntInput('data[tx_cfcleague_match_notes][NEW'.$i.'][extra_time]', '',1,2) . 
               $this->getModule()->getFormTool()->createHidden('data[tx_cfcleague_match_notes][NEW'.$i.'][game]', $match->uid);
//			$row[] = $this->getFormTool()->createSelectSingle(
//							'data[tx_cfcleague_match_notes][NEW'.$i.'][type]', '0','tx_cfcleague_match_notes','type',array('onchange' => 'setMatchMinute(this);'));
			$row[] = $this->getModule()->getFormTool()->createSelectSingleByArray(
							'data[tx_cfcleague_match_notes][NEW'.$i.'][type]', '0', $types, array('onchange' => 'setMatchMinute(this);'));
			$row[] = $this->getModule()->getFormTool()->createSelectSingleByArray(
							'data[tx_cfcleague_match_notes][NEW'.$i.'][player_home]', '0', $match->getPlayerNamesHome(1,1),array('onchange' => 'setMatchMinute(this);'));
      $row[] = $this->getModule()->getFormTool()->createSelectSingleByArray(
                      'data[tx_cfcleague_match_notes][NEW'.$i.'][player_guest]', '0', $match->getPlayerNamesGuest(1,1),array('onchange' => 'setMatchMinute(this);'));

      $arr[] = $row;

      // Das Bemerkungsfeld kommt in die nächste Zeile
      $row = array();
      $row[] = $this->getModule()->getFormTool()->createTextArea('data[tx_cfcleague_match_notes][NEW'.$i.'][comment]', '', $cols, $rows,array('onchange' => 'setMatchMinute(this);'));
      $arr[] = $row;

    }

//    $arr[] = Array();
//    t3lib_div::debug($this->getFormTool()->form->extJSCODE);

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
  }
  /**
   * Liefert die Selector Instanz
   *
   * @return tx_cfcleague_selector
   */
  function getSelector() {
  	return $this->selector;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_match_ticker.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_match_ticker.php']);
}
?>
