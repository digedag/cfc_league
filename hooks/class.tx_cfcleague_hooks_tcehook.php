<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche <rene@system25.de>
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

require_once(tx_rnbase_util_Extensions::extPath('cfc_league') . 'class.tx_cfcleague_db.php');

class tx_cfcleague_hooks_tcehook {

	/**
	 * Dieser Hook wird vor der Darstellung eines TCE-Formulars aufgerufen.
	 * Werte aus der Datenbank können vor deren Darstellung manipuliert werden.
	 */
	function getMainFields_preProcess($table, &$row, $tceform) {
		if($table == 'tx_cfcleague_team_notes') {
			$teamUid = intval(t3lib_div::_GP('team'));
			if($teamUid) $row['team'] = $teamUid;
			$typeUid = intval(t3lib_div::_GP('type'));
			if($typeUid) $row['type'] = $typeUid;
			$typeUid = intval(t3lib_div::_GP('mediatype'));
			if($typeUid) $row['mediatype'] = $typeUid;
		}
		if($table == 'tx_cfcleague_games') {
			$compUid = intval(t3lib_div::_GP('competition'));
			if($compUid) $row['competition'] = $compUid;
			$round = intval(t3lib_div::_GP('round'));
			if($round && $compUid) {
				$row['round'] = $round;
				// Den Namen aus der DB holen
				$options['where'] = 'round='.$round .' AND competition='.$compUid;
				$options['limit'] = 1;
				$rows = tx_rnbase_util_DB::doSelect('round_name', 'tx_cfcleague_games', $options);
				if(count($rows))
					$row['round_name'] = $rows[0]['round_name'];
			}
		}
		if($table == 'tx_cfcleague_profiles' && !strstr($row['uid'], 'NEW')) {
			//'2|Trainer'
			$options['where'] = 'uid_foreign='.$row['uid'];
			$options['orderby'] = 'sorting_foreign asc';
			$options['enablefieldsoff'] = 1;
			$types = array();
			$rows = tx_rnbase_util_DB::doSelect('uid_local', 'tx_cfcleague_profiletypes_mm', $options);
			foreach($rows As $type) {
				$types[] = $type['uid_local'];
			}
			$row['types'] = tx_cfcleague_tca_Lookup::getProfileTypeItems($types);
		}
		if($table == 'tx_cfcleague_club') {
			tx_rnbase::load('tx_rnbase_util_Dates');
			$row['established'] = $row['established'] ? tx_rnbase_util_Dates::datetime_mysql2tstamp($row['established']) : time();
		}
	}
	/**
	 * Wir müssen dafür sorgen, daß die neuen IDs der Teams im Wettbewerb und Spielen
	 * verwendet werden.
	 */
	public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$tcemain)  {

		if($table == 'tx_cfcleague_teams') {
			$this->checkProfiles($incomingFieldArray, 'players', $tcemain);
			$this->checkProfiles($incomingFieldArray, 'coaches', $tcemain);
			$this->checkProfiles($incomingFieldArray, 'supporters', $tcemain);
		}
		if($table == 'tx_cfcleague_competition') {
			$this->checkProfiles($incomingFieldArray, 'teams', $tcemain);
		}
		if($table == 'tx_cfcleague_games') {
			if($incomingFieldArray['arena'] > 0 && !$incomingFieldArray['stadium']) {
				$stadium = tx_rnbase::makeInstance('tx_cfcleague_models_Stadium', $incomingFieldArray['arena']);
				$incomingFieldArray['stadium'] = $stadium->getName();
			}
		}
		if($table == 'tx_cfcleague_stadiums' || $table == 'tx_cfcleague_club') {
			if($incomingFieldArray['country'] > 0 && !$incomingFieldArray['countrycode']) {
				$country = t3lib_BEfunc::getRecord('static_countries', intval($incomingFieldArray['country']));
				$incomingFieldArray['countrycode'] = $country['cn_iso_2'];
			}
		}
	}

	/**
	 * Nachbearbeitungen, unmittelbar BEVOR die Daten gespeichert werden. Das POST bezieht sich
	 * auf die Arbeit der TCE und nicht auf die Speicherung in der DB.
	 *
	 * @param string $status new oder update
	 * @param string $table Name der Tabelle
	 * @param int $id UID des Datensatzes
	 * @param array $fieldArray Felder des Datensatzes, die sich ändern
	 * @param tce_main $tcemain
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$tce) {
		if($table == 'tx_cfcleague_club') {
			if(array_key_exists('established', $fieldArray)) {
				tx_rnbase::load('tx_rnbase_util_Dates');
				$estDate = tx_rnbase_util_Dates::date_tstamp2mysql($fieldArray['established']);
				$fieldArray['established'] = $estDate;
			}
		}
	}

  /**
   * Prüft, ob im für den angegebenen Personentyp neue Personen angelegt wurden
   * und setzt die neuen UIDs.
   *
   * @param array $incomingFieldArray
   * @param string $profileType Spaltenname im Teamdatensatz (players, coaches, supporters)
   */
  function checkProfiles(&$incomingFieldArray, $profileType, &$tcemain) {
    if(strstr($incomingFieldArray[$profileType], 'NEW')) {
      $newProfileIds = t3lib_div::trimExplode(',', $incomingFieldArray[$profileType]);
      $profileUids = array();
      for($i=0; $i < count($newProfileIds); $i++) {
        if(strstr($newProfileIds[$i], 'NEW'))
          $profileUid = $tcemain->substNEWwithIDs[$newProfileIds[$i]];
        else
          $profileUid = $newProfileIds[$i];
        // Wir übernehmen nur UIDs, die gefunden werden
        if($profileUid) $profileUids[] = $profileUid;
      }
      $incomingFieldArray[$profileType] = implode($profileUids, ',');
    }
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/hooks/class.tx_cfcleague_hooks_tcehook.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/hooks/class.tx_cfcleague_hooks_tcehook.php']);
}

?>