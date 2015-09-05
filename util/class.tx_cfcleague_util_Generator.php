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
	static $keyStrings = array(
		2 => '1-2',
		4 => '1-3,4-2|1-2,3-4|2-3,4-1',
		6 => '1-5,3-2,6-4|2-6,4-1,5-3|1-3,4-2,6-5|1-2,3-6,5-4|2-5,4-3,6-1',
		8 => '1-7,3-4,5-2,8-6|2-3,4-8,6-1,7-5|1-5,3-7,6-4,8-2|2-6,4-1,5-3,7-8|1-3,4-2,6-7,8-5|1-2,3-8,5-6,7-4|2-7,4-5,6-3,8-1',
		10 => '1-9,3-6,5-4,7-2,10-8|2-5,4-3,6-10,8-1,9-7|1-7,3-2,5-9,8-6,10-4|2-10,4-8,6-1,7-5,9-3|1-5,3-7,6-4,8-2,10-9|2-6,4-1,5-3,7-10,9-8|1-3,4-2,6-9,8-7,10-5|1-2,3-10,5-8,7-6,9-4|2-9,4-7,6-5,8-3,10-1',
		12 => '1-11,3-8,5-6,7-4,9-2,12-10|2-7,4-5,6-3,8-12,10-1,11-9|1-9,3-4,5-2,7-11,10-8,12-6|2-3,4-12,6-10,8-1,9-7,11-5|1-7,3-11,5-9,8-6,10-4,12-2|2-10,4-8,6-1,7-5,9-3,11-12|1-5,3-7,6-4,8-2,10-11,12-9|2-6,4-1,5-3,7-12,9-10,11-8|1-3,4-2,6-11,8-9,10-7,12-5|1-2,3-12,5-10,7-8,9-6,11-4|2-11,4-9,6-7,8-5,10-3,12-1',
		14 => '1-13,3-10,5-8,7-6,9-4,11-2,14-12|2-9,4-7,6-5,8-3,10-14,12-1,13-11|1-11,3-6,5-4,7-2,9-13,12-10,14-8|2-5,4-3,6-14,8-12,10-1,11-9,13-7|1-9,3-2,5-13,7-11,10-8,12-6,14-4|2-14,4-12,6-10,8-1,9-7,11-5,13-3|1-7,3-11,5-9,8-6,10-4,12-2,14-13|2-10,4-8,6-1,7-5,9-3,11-14,13-12|1-5,3-7,6-4,8-2,10-13,12-11,14-9|2-6,4-1,5-3,7-14,9-12,11-10,13-8|1-3,4-2,6-13,8-11,10-9,12-7,14-5|1-2,3-14,5-12,7-10,9-8,11-6,13-4|2-13,4-11,6-9,8-7,10-5,12-3,14-1',
		16 => '1-15,3-12,5-10,7-8,9-6,11-4,13-2,16-14|2-11,4-9,6-7,8-5,10-3,12-16,14-1,15-13|1-13,3-8,5-6,7-4,9-2,11-15,14-12,16-10|2-7,4-5,6-3,8-16,10-14,12-1,13-11,15-9|1-11,3-4,5-2,7-15,9-13,12-10,14-8,16-6|2-3,4-16,6-14,8-12,10-1,11-9,13-7,15-5|1-9,3-15,5-13,7-11,10-8,12-6,14-4,16-2|2-14,4-12,6-10,8-1,9-7,11-5,13-3,15-16|1-7,3-11,5-9,8-6,10-4,12-2,14-15,16-13|2-10,4-8,6-1,7-5,9-3,11-16,13-14,15-12|1-5,3-7,6-4,8-2,10-15,12-13,14-11,16-9|2-6,4-1,5-3,7-16,9-14,11-12,13-10,15-8|1-3,4-2,6-15,8-13,10-11,12-9,14-7,16-5|1-2,3-16,5-14,7-12,9-10,11-8,13-6,15-4|2-15,4-13,6-11,8-9,10-7,12-5,14-3,16-1',
		18 => '1-17,3-14,5-12,7-10,9-8,11-6,13-4,15-2,18-16|2-13,4-11,6-9,8-7,10-5,12-3,14-18,16-1,17-15|1-15,3-10,5-8,7-6,9-4,11-2,13-17,16-14,18-12|2-9,4-7,6-5,8-3,10-18,12-16,14-1,15-13,17-11|1-13,3-6,5-4,7-2,9-17,11-15,14-12,16-10,18-8|2-5,4-3,6-18,8-16,10-14,12-1,13-11,15-9,17-7|1-11,3-2,5-17,7-15,9-13,12-10,14-8,16-6,18-4|2-18,4-16,6-14,8-12,10-1,11-9,13-7,15-5,17-3|1-9,3-15,5-13,7-11,10-8,12-6,14-4,16-2,18-17|2-14,4-12,6-10,8-1,9-7,11-5,13-3,15-18,17-16|1-7,3-11,5-9,8-6,10-4,12-2,14-17,16-15,18-13|2-10,4-8,6-1,7-5,9-3,11-18,13-16,15-14,17-12|1-5,3-7,6-4,8-2,10-17,12-15,14-13,16-11,18-9|2-6,4-1,5-3,7-18,9-16,11-14,13-12,15-10,17-8|1-3,4-2,6-17,8-15,10-13,12-11,14-9,16-7,18-5|1-2,3-18,5-16,7-14,9-12,11-10,13-8,15-6,17-4|2-17,4-15,6-13,8-11,10-9,12-7,14-5,16-3,18-1',
		);

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
	 * @return array mit Key: Spieltag(int) und Value: Array der Spiele des Spieltags
	 */
	public function main($teams, $table, $options) {
		global $LANG;
		// Passenden KeyString setzen
		$table = trim($table);
		if(!strlen($table))
			$table = $this->findKeyString($teams);
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
	 * Liefert einen KeyString. Wenn
	 *
	 * @param array $teams
	 * @return string
	 */
	private function findKeyString($teams) {
		$anz = count($teams);
		return array_key_exists($anz, self::$keyStrings) ? self::$keyStrings[$anz] : '';
	}

	/**
	 * Erstellt den eigentlichen Spielplan
	 * @param array $teams
	 * @param array $table
	 * @param array $options
	 */
	private function createTable($teams, $table, $options) {
		$option_halfseries = isset($options['halfseries']) ? intval($options['halfseries']) : 0;
		$option_nomatch = isset($options['nomatch']) ? intval($options['nomatch']) : 0;
		// Alle Elemente einen Indexplatz hochschieben, damit die Team-Nr stimmt.
		array_unshift($teams, 0);
		$matchCnt = 0; // ID des Spieldatensatzes. Wird für jedes angelegte Spiel gezählt
		// Spielnummer. Spielfreie Spiele werden nicht gezählt
		$matchCnt2 = isset($options['firstmatchnumber']) ? intval($options['firstmatchnumber']) : 0;
		// Zählung des Spieltags
		$dayCnt = isset($options['firstmatchday']) ? intval($options['firstmatchday']) : 0;

		$ret = array();
		// die Hinrunde hinzufügen
		if($option_halfseries != 2) {
			foreach($table as $day => $matches) {
				$dayArr = array(); // Hier kommen die Spiele rein
				foreach($matches as $k => $match) {
					$teamIds = explode('-', $match);
					// Ist es ein spielfreies Spiel
					$isNoMatch = $teams[$teamIds[0]] == $option_nomatch || $teams[$teamIds[1]] == $option_nomatch;
					$dayArr[] = new tx_cfcleague_util_GeneratorMatch(++$matchCnt, $isNoMatch ? '': ++$matchCnt2, $teams[$teamIds[0]], $teams[$teamIds[1]], $isNoMatch);
				}
				$ret[++$dayCnt] = $dayArr;
			}
		}
		// die Rückspiele hinzufügen
		if($option_halfseries == 0 || $option_halfseries == 2) {
			foreach($table as $day => $matches) {
				$dayArr = array(); // Hier kommen die Spiele rein
				foreach($matches as $k => $match) {
					$teamIds = explode('-', $match);
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
	private function checkParams($teams, $table) {
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
				$matchArr = explode('-', $match);
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
	private function splitTableString($table) {
		$ret = array();
		if(!strlen(trim($table))) return $ret; // Kein String gesetzt
		$days = explode('|', $table);
		foreach($days as $key => $matches) {
			$ret[$key+1] = explode(',', $matches);
		}
		return $ret;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Generator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Generator.php']);
}

?>