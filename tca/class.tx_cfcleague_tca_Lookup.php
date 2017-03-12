<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2017 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_util_Misc');
tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('Tx_Rnbase_Utility_Strings');

/**
 */
class tx_cfcleague_tca_Lookup {
	/**
	 * Returns all available profile types for a TCA select item
	 *
	 * @param array $config
	 */
	function getProfileTypes(&$config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		$config['items'] = $srv->getProfileTypes4TCA();
	}
	function getProfileTypeItems($uids) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		return $srv->getProfileTypeItems4TCA($uids);
	}
	/**
	 * Liefert die vorhandenen MatchNote-Typen
	 * @param $config
	 * @return array
	 */
	public function getMatchNoteTypes(&$config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
		$config['items'] = $srv->getMatchNoteTypes4TCA();
	}
	/**
	 * Liefert die vorhandenen Liga Tabellen-Typen
	 * @param $config
	 * @return array
	 */
	public function getSportsTypes(&$config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
		$config['items'] = $srv->getSports4TCA();
	}
	/**
	 * Liefert die möglichen Spielsysteme.
	 * Das könnte man noch abhängig von der Sportart machen,
	 * aber hier reicht es erstmal, wenn wir das über die
	 * TCA erweitern können!
	 *
	 * @param $config
	 * @return array
	 */
	public function getFormations(&$config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$items = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'];
		$config['items'] = $items;
	}

	public function getPointSystems(&$config) {
		$sports = $config['row']['sports'];
		// In der 7.6 ist immer ein Array im Wert
		$sports = is_array($sports) ? ( count($sports) ? reset($sports) : FALSE ) : $sports;
		if($sports) {
			$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
			$config['items'] = $srv->getPointSystems($sports);
		}

//		$config['items'] = array(
//					Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2'), 1),
//					Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_3'), 0)
//		);
	}
	/**
	 * Set possible stadiums for a match. The stadiums are selected from home club.
	 *
	 * @param array $PA
	 * @param t3lib_TCEforms $fobj
	 */
	public function getStadium4Match($PA, $fobj){
		$current = intval($PA['row']['arena']);
		$currentAvailable = false;
		$teamId = is_array($PA['row']['home']) ? reset($PA['row']['home']) : $PA['row']['home'];
		if($teamId) {
			$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
			$stadiums = $srv->getStadiums($teamId);
			foreach ($stadiums As $stadium) {
				$currentAvailable = $currentAvailable ? $currentAvailable : ($current == $stadium->getUid() || $current == 0);
				$PA['items'][] = array($stadium->getName(), $stadium->getUid());
			}
		}
		if(!$currentAvailable) {
			// Das aktuelle Stadium ist nicht mehr im Verein gefunden worden. Es wird daher nachgeladen
			$stadium = tx_rnbase::makeInstance('tx_cfcleague_models_Stadium', $current);
			if($stadium->isValid())
				$PA['items'][] = array($stadium->getName(), $stadium->getUid());
		}
	}
	/**
	 * Set possible logos for a team. The logos are selected from club.
	 *
	 * @param array $PA
	 * @param t3lib_TCEforms $fobj
	 */
	public function getLogo4Team($PA, $fobj){
		$clubId = is_array($PA['row']['club']) ? reset($PA['row']['club']) : $PA['row']['club'];
		if($clubId) {
			$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
			// FIXME: Wenn Teams nicht global verwaltet werden, dann kommt der Verein nicht als UID
			// tx_cfcleague_club_1|M%C3%BCnchen%2C%20FC%20Bayern%20M%C3%BCnchen
			// Hier werden bei FAL Referenzen geliefert.
			// In der 7.6 wird bei Relationen nun wohl immer ein Array geliefert.
			$items = $srv->getLogos($clubId);
			// Bei FAL wird die UID der Referenz gespeichert. Damit können die zusätzlichen
			// Daten der Referenz verwendet werden.
			if(count($items))
				$PA['items'] = array();
			foreach ($items As $item) {
				//$currentAvailable = $currentAvailable ? $currentAvailable : ($current == $item->getUid() || $current == 0);
				// Je nach Pflege der Daten sind unterschiedliche Felder gefüllt.
				$label = ($item->record['title'] ? $item->record['title'] : (
						$item->record['name'] ? $item->record['name'] : $item->record['file']) );
				$PA['items'][] = array($label, $item->getUid());
			}
		}
	}

	/**
	 * Build the TCA entry for logo select-field in team record. All
	 * logos from connected club are selectable.
	 * @return array
	 */
	public static function getTeamLogoField() {
		if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
			$ret = tx_rnbase_util_TSFAL::getMediaTCA('logo', array(
				'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.logo',
				'config' => array('size' => 1, 'maxitems' => 1),
			));
			unset($ret['config']['filter']);
			foreach($ret['config'] As $key => $field) {
				if(strpos($key, 'foreign_') === 0) {
					unset($ret['config'][$key]);
				}
			}
		}
		else {
			require_once(tx_rnbase_util_Extensions::extPath('dam').'tca_media_field.php');
			$ret = txdam_getMediaTCA('image_field', 'logo');
			unset($ret['config']['MM']);
			unset($ret['config']['MM_foreign_select']);
			unset($ret['config']['MM_match_fields']);
			unset($ret['config']['MM_opposite_field']);
		}
		$ret['label'] = 'Team Logo';
		// Die Auswahlbox rendern
		// In der 7.6 einen eigenen Node-Type anmelden
		// $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']
		if(tx_rnbase_util_TYPO3::isTYPO70OrHigher()) {
			$ret['config']['type'] = 'select'; // 't3s_teamlogo';
		}
		else {
			$ret['config']['userFunc'] = 'EXT:cfc_league/tca/class.tx_cfcleague_tca_Lookup.php:&tx_cfcleague_tca_Lookup->getSingleField_teamLogo';
			$ret['config']['type'] = tx_rnbase_util_TYPO3::isTYPO60OrHigher() ? 'user' : 'select';
		}
		$ret['config']['renderType'] = 'selectSingle';
		// Die passenden Logos suchen
		$ret['config']['itemsProcFunc'] = 'tx_cfcleague_tca_Lookup->getLogo4Team';
		$ret['config']['maxitems'] = '1';
		$ret['config']['size'] = '1';
		$ret['config']['items'] = Array(Array('', '0'));
		return $ret;
	}
	/**
	 * Build a select box and an image preview of selected logo
	 * @param array $PA
	 * @param TYPO3\CMS\Backend\Form\Element\UserElement $fObj
	 */
	public function getSingleField_teamLogo($PA, $fObj)	{
		global $TYPO3_CONF_VARS;

		// In der 7.6 geht das nicht mehr...
		$tceforms = &$PA['pObj'];
		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];

		if(!$row['club'])
			return $tceforms->sL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_tca_noclubselected');
		$config = $PA['fieldConf']['config'];

		$item = $tceforms->getSingleField_typeSelect($table, $field, $row, $PA);
		if($row['logo']) {
			if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
				// Im Logo wird die UID der Referenz zwischen Verein und dem Logo gespeichert
				// Damit können die zusätzlichen Metadaten der Referenz genutzt werden
				$fileObject = tx_rnbase_util_TSFAL::getFileReferenceById($row['logo']);
				tx_rnbase::load('tx_rnbase_util_TSFAL');
				$thumbs = tx_rnbase_util_TSFAL::createThumbnails(array($fileObject));
				$item = '<table cellspacing="0" cellpadding="0" border="0">
								<tr><td style="padding-bottom:1em" colspan="2">'.$item.'</td></tr>
								<tr><td>'.$thumbs[0].'</td>
										<td style="padding-left:1em"><table cellspacing="0" cellpadding="0" border="0">
										<tr><td style="padding-right:1em">Filename: </td><td>'.$fileObject->getProperty('identifier').'</td></tr>
										<tr><td style="padding-right:1em">Size: </td><td>'. \TYPO3\CMS\Core\Utility\GeneralUtility::formatSize($fileObject->getProperty('size')).'</td></tr>
										<tr><td style="padding-right:1em">Dimension: </td><td>'. $fileObject->getProperty('width') .'x'. $fileObject->getProperty('height').' px</td></tr>
									</table></td></tr></table>';
//				$item .= ''.$thumbs[0];
			}
			else {
				// Logo anzeigen
				$currPic = t3lib_BEfunc::getRecord('tx_dam', $row['logo']);
				require_once(tx_rnbase_util_Extensions::extPath('dam').'lib/class.tx_dam_tcefunc.php');
				$tcefunc = tx_rnbase::makeInstance('tx_dam_tcefunc');
				if(!method_exists($tcefunc, 'renderFileList')) return $item;
				$tcefunc->tceforms = &$tceforms;
				$item .= $tcefunc->renderFileList(array('rows' => array($currPic)));
			}
		}
		return $item;
	}

	public static function getCountryField() {
		return Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_country',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array(' ', '0'),
				),
		    'foreign_table' => 'static_countries',
				'foreign_table_where' => ' ORDER BY static_countries.cn_short_en ',
				'size' => 1,
				'default' => 54,
				'minitems' => 0,
				'maxitems' => 1,
			)
		);
	}


	/**
	 * Die Spieler des Heimteams ermitteln
	 * Used: Edit-Maske eines Spiels für Teamaufstellung und Match-Note
	 */
	public function getPlayersHome4Match($PA, $fobj){
		global $LANG;
		$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

		// tx_rnbase_util_Debug::debug(count($PA[items]), 'items cfcleague');

		if($PA['row']['home'])
		{
			// Abfrage aus Spieldatensatz
			// Es werden alle Spieler des Teams benötigt
			$players = $this->findProfiles($PA['row']['home'], 'getPlayers');
			$PA[items] = $players;
		}
		elseif($PA['row']['game']) {
			// Abfrage aus MatchNote-Datensatz
			// Wenn wir die Match ID haben, können wir die Spieler auch so ermitteln
			// Es werden alle aufgestellten Spieler des Matches benötigt
			/* @var $match tx_cfcleague_models_Match */
			$match = tx_rnbase::makeInstance('tx_cfcleague_models_Match', $this->getRowId($PA['row']['game']));

			$players = tx_cfcleague_util_ServiceRegistry::getProfileService()->loadProfiles($match->getPlayersHome(true));
//			$players = $match->getPlayerNamesHome();
			$playerArr = [0 => '']; // empty item
			foreach($players As $player) {
				$playerArr[] = Array($player->getName(true), $player->getUid());
			}
			sort($playerArr);
			$PA[items] = $playerArr;
			// Abschließend noch den Spieler "Unbekannt" hinzufügen! Dieser ist nur in Matchnotes verfügbar
			$PA[items][] = Array($LANG->getLL('tx_cfcleague.unknown'), '-1');
		}
		else
			$PA[items] = array();
	}
	/**
	 * Die Spieler des Gastteams ermitteln
	 * Used: Edit-Maske eines Spiels für Teamaufstellung
	 */
	public function getPlayersGuest4Match($PA, $fobj){
		global $LANG;
		$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

		if($PA['row']['guest'])
		{
			$players = $this->findProfiles($PA['row']['guest'], 'getPlayers');
			$PA[items] = $players;
		}
		elseif($PA['row']['game']) {
			// Wenn wir die Match ID haben könne wir die Spieler auch so ermitteln
			/* @var $match tx_cfcleague_models_Match */
			$match = tx_rnbase::makeInstance('tx_cfcleague_models_Match', $this->getRowId($PA['row']['game']));
//			$players = $match->getPlayerNamesGuest();
			$players = tx_cfcleague_util_ServiceRegistry::getProfileService()->loadProfiles($match->getPlayersGuest(true));
			$playerArr = [0 => '']; // empty item
			foreach($players As $player) {
				$playerArr[] = Array($player->getName(true), $player->getUid());
			}
			sort($playerArr);
			$PA[items] = $playerArr;
			// Abschließend noch den Spieler "Unbekannt" hinzufügen!
			$PA[items][] = Array($LANG->getLL('tx_cfcleague.unknown'), '-1');
		}
		else // Ohne Daten müssen wir alle Spieler löschen
			$PA[items] = array();
	}

	/**
	 * Liefert die verschachtelte UID eines Strings der Form
	 * tx_table_name_uid|valuestring
	 */
	private function getRowId($value) {
		$ret = Tx_Rnbase_Utility_Strings::trimExplode('|', $value);
		$ret = Tx_Rnbase_Utility_Strings::trimExplode('_', $ret[0]);
		return intval($ret[count($ret)-1]);
	}

	/**
	 * Find player of team
	 * Used: Edit mask for team notes
	 *
	 * @param array $PA
	 * @param TYPO3\CMS\Backend\Form\Element\UserElement $fobj
	 */
	public function getPlayers4Team(&$PA, $fobj){
		global $LANG;
		$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');
		$column = 'team';
		if($PA['row'][$column]) {
			$tablename = 'tx_cfcleague_team_notes';
			$tcaFieldConf = $GLOBALS['TCA'][$tablename]['columns'][$column]['config'];
			$fieldValue = $PA['row'][$column];
			$team = is_array($fieldValue) ? $fieldValue : Tx_Rnbase_Utility_Strings::trimExplode('|', $fieldValue);
			$team = $team[0];
			if($tcaFieldConf['type'] == 'db') {
				// FIXME: funktioniert nicht in 7.6!
				if(tx_rnbase_util_TYPO3::isTYPO76OrHigher())
					throw new Exception("not implemented in 7.6\n". tx_rnbase_util_Debug::getDebugTrail());
					$dbAnalysis = tx_rnbase::makeInstance('t3lib_loadDBGroup');
					$dbAnalysis->registerNonTableValues=0;
					$dbAnalysis->start($team, $tcaFieldConf['allowed'], '', 0, $tablename, $tcaFieldConf);
					$valueArray = $dbAnalysis->getValueArray(false);
					// Abfrage aus Spieldatensatz
					// Es werden alle Spieler des Teams benötigt
					$team = $valueArray[0];
			}
			$players = $this->findProfiles($team, 'getPlayers');
			$players = array_merge($players, $this->findProfiles($team, 'getCoaches'));
			$players = array_merge($players, $this->findProfiles($team, 'getSupporters'));
			$PA[items] = $players;
		}
		else
			$PA[items] = array();
	}
	/**
	 * Liefert die Spieler (uid und name) einer Mannschaft.
	 * Die Spieler sind alphabetisch sortiert.
	 * @return []
	 */
	private function findProfiles($teamId, $getter) {
		$rows = array();
		if(intval($teamId) == 0) return $rows;

		$team = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $teamId);
		/* @var $profile tx_cfcleague_models_Profile */
		$profiles = $team->$getter();
		foreach($profiles As $profile) {
			$rows[] = Array($profile->getName(true), $profile->getUid(), );
		}
		sort($rows);

		return $rows;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/tca/class.tx_cfcleague_tca_Lookup.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/tca/class.tx_cfcleague_tca_Lookup.php']);
}
