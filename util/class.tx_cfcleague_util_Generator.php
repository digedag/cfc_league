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

tx_rnbase::load('tx_cfcleague_util_GeneratorMatch');

/**
 */
class tx_cfcleague_util_Generator {
	var $errorMsg;
	
	/**
	 * Optionen die per $options übergeben werden können:
	 * halfseries - wenn != 0 wird nur die erste Halbserie erzeugt
	 * nomatch - wenn ein Team als Spielfrei gewertet werden soll, dann muss es hier 
	 *					übergeben werden. Es muss auch im Array $teams enthalten sein!
	 * firstmatchday - Spieltag, mit dem die Zählung beginnen soll
	 * firstmatchnumber - Spielnummer mit der die Zählung beginnen soll
	 * 
	 * @param array $teams sortiertes Array der Liga-Teams. Kann theoretisch von jedem Typ sein. Eine TeamID
	 *					wäre aber gut.
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
				$dayArr[] = new tx_cfcleague_util_GeneratorMatch(++$matchCnt, $isNoMatch ? '': ++$matchCnt2, $teams[$teamIds[0]], $teams[$teamIds[1]], $isNoMatch);
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
					$dayArr[] = new tx_cfcleague_util_GeneratorMatch(++$matchCnt, $isNoMatch ? '': ++$matchCnt2, $teams[$teamIds[1]], $teams[$teamIds[0]], $isNoMatch);
//					$dayArr[] = new Match(++$matchCnt, $teams[$teamIds[1]], $teams[$teamIds[0]]);
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Generator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Generator.php']);
}

?>