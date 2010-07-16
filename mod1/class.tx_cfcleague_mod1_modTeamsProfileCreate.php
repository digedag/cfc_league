<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Rene Nitzsche (rene@system25.de)
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

/**
 * Die Klasse verwaltet die Erstellung von Spielern für Teams
 */
class tx_cfcleague_mod1_modTeamsProfileCreate extends t3lib_extobjbase {
  var $doc, $modName;


	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
	 * @param tx_cfcleague_league $competition
	 */
	function main($modName, $pid, &$doc, &$formTool, &$team, $teamInfo) {
		global $LANG;
		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		// Entweder global über die Datenbank oder die Ligen der aktuellen Seite

		$this->pid = $pid;
		$this->doc = $doc;
		$this->formTool = $formTool;
		$data = t3lib_div::_GP('data');
		
		$content = '';
		$content .= $this->showCreateProfiles($data, $team, $teamInfo);

		return $content;
	}

	/**
	 * Whether or not the given pid is inside the profile archive
	 * @param int $pid
	 * @return boolean
	 */
	public static function isProfilePage($pid) {
		$rootPage = tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'profileRootPageId');
		$goodPages = tx_cfcleague_db::getPagePath($pid);
		return in_array($rootPage, $goodPages);
	}
	private function showCreateProfiles(&$data, &$team, $teamInfo) {
		global $LANG;

		if(!self::isProfilePage($this->pid)) {
			$content = $this->doc->section('Message:',$LANG->getLL('msg_pageNotAllowed'),0,1,ICON_WARN);
			return $content;
		}

		if (is_array($data['tx_cfcleague_profiles'])) {
			$content .= $this->createProfiles($data,$team, $teamInfo);
			$team->refresh();
		}

		if($teamInfo->isTeamFull()) {
			// Kann nix mehr angelegt werden
			$content .= $this->doc->section('Message:',$LANG->getLL('msg_maxPlayers'),0,1,ICON_WARN);
		}
		else {
			$content .= $this->doc->section('Info:',$LANG->getLL('msg_checkPage') . ': <b>' . t3lib_BEfunc::getRecordPath($this->pid,'',0) . '</b>' ,0,1,ICON_WARN);
			$content .= $teamInfo->getInfoTable($this->doc);
			// Wir zeigen 15 Zeilen mit Eingabefeldern
			$content .= $this->prepareInputTable($team, $teamInfo);
			// Den Update-Button einfügen
			$content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_create').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('msg_CreateProfiles')).')">';
		}
		return $content;
	}

	/**
	 * Erstellt eine Tabelle mit den schon vorhandenen Personen und den noch möglichen neuen
	 * Personen.
	 * Wenn keine Personen da sind, gibt es 15 Eingabefelder, sonst nur 5
	 * @param tx_cfcleague_team $team
	 * @param tx_cfcleague_util_TeamInfo $teamInfo
	 */
	function prepareInputTable(&$team, $teamInfo) {
		global $LANG;
		tx_rnbase::load('tx_rnbase_util_TYPO3');
		
		// Es werden zwei Tabellen erstellt
		$tableProfiles = $teamInfo->getTeamTable($this->doc);

		$arr = Array(Array('&nbsp;',$LANG->getLL('label_firstname'),$LANG->getLL('label_lastname'),'&nbsp;'));
		$maxFields = count($team->getPlayerNames()) > 5 ? 5 : 15;
		for($i=0; $i < $maxFields; $i++){
			$row = array();
			$row[] = $i + 1;
			$row[] = $this->formTool->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][first_name]', '',10);
			$row[] = $this->formTool->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][last_name]', '',10);
			$row[] = $this->formTool->createSelectSingleByArray('data[tx_cfcleague_profiles][NEW'.$i.'][type]', '',self::getProfileTypeArray()) .
			$this->formTool->createHidden('data[tx_cfcleague_profiles][NEW'.$i.'][pid]', $this->pid);
			$arr[] = $row;
		}
		$tableForm = $this->doc->table($arr, $tableLayout);
		$tableLayout = Array (
			'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'defRow' => Array ( // Formate für alle Zeilen
				'defCol' => Array('<td valign="top" style="padding:0 5px;">','</td>') // Format für jede Spalte in jeder Zeile
			),
		);

		$content .= $this->doc->table(Array(Array($tableForm,$tableProfiles)), $tableLayout);
		return $content;
	}

	/**
	 * Liefert ein Array der Personentypen
	 *
	 * @return array
	 */
	public static function getProfileTypeArray() {
		global $LANG;
		return Array(
			'1' => $LANG->getLL('label_profile_player'),
			'2' => $LANG->getLL('label_profile_coach'),
			'3' => $LANG->getLL('label_profile_supporter'),
		);
	}
  /**
   * Erstellt die angeforderten Profile
   * @param $profiles Array mit den Daten aus dem Request
   * @param $team das aktuelle Team, dem die Personen zugeordnet werden
   * @param tx_cfcleague_util_TeamInfo $teamInfo
   */
  public static function createProfiles(&$profiles, &$team, $teamInfo) {
    global $BE_USER, $LANG;

    $maxCoaches = $teamInfo->get('maxCoaches');
    $maxPlayers = $teamInfo->get('maxPlayers');
    $profiles = $profiles['tx_cfcleague_profiles'];
    $content = '';

    $playerIds = array(); // Sammelt die UIDs der neuen Spieler
    $coachIds = array(); // Sammelt die UIDs der neuen Trainer
    $supportIds = array(); // Sammelt die UIDs der neuen Betreuer
    $warnings = array(); // Sammelt Profile die nicht angelegt werden konnten

    $data = array();
    foreach($profiles As $uid => $profile){
      // Zuerst Leerzeichen entfernen
      $profile['last_name'] = trim($profile['last_name']);
      $profile['first_name'] = trim($profile['first_name']);
    
      if(strlen($profile['last_name']) > 0) // Nachname ist Pflichtfeld
      {
        $type = $profile['type'];
        unset($profile['type']);
        // Darf dieses Profil noch angelegt werden?
        if($type == '1' && (($team->getPlayerSize() + count($playerIds)) >= $maxPlayers)) { // Spieler
          $warnings[] = $profile['last_name'] . ', ' . $profile['first_name'];
        }
        elseif($type == '2' && (($team->getCoachSize() + count($coachIds)) >= $maxCoaches)) { // Trainer
          $warnings[] = $profile['last_name'] . ', ' . $profile['first_name'];
        }
        else {

          // Jetzt das Array vorbereiten
          $data['tx_cfcleague_profiles'][$uid] = $profile;

          if($type == '1') {
            $playerIds[] = $uid;
          }
          elseif($type == '2') {
            $coachIds[] = $uid;
          }
          else {
            $supportIds[] = $uid;
          }
        }
      }        
    }

		tx_rnbase::load('tx_cfcleague_util_Misc');
    // Die IDs der Trainer, Spieler und Betreuer mergen
    if(count($coachIds)) {
      $data['tx_cfcleague_teams'][$team->record['uid']]['coaches'] = implode(',',tx_cfcleague_util_Misc::mergeArrays(t3lib_div::intExplode(',',$team->record['coaches']), $coachIds));
    }
    if(count($playerIds)) {
      $data['tx_cfcleague_teams'][$team->record['uid']]['players'] = implode(',',tx_cfcleague_util_Misc::mergeArrays(t3lib_div::intExplode(',',$team->record['players']), $playerIds));
    }
    if(count($supportIds)) {
      $data['tx_cfcleague_teams'][$team->record['uid']]['supporters'] = implode(',',tx_cfcleague_util_Misc::mergeArrays(t3lib_div::intExplode(',',$team->record['supporters']), $supportIds));
    }

//    t3lib_div::debug($data, 'tx_cfcleague_profile_create');
    if(count($data)) {
      reset($data);
      $tce =& tx_cfcleague_db::getTCEmain($data);
      $tce->process_datamap();
      $content .= count($tce->errorLog) ? $LANG->getLL('msg_tce_errors') : $LANG->getLL('msg_profiles_created');
      $content .= '<br /><br />';
    }
    else
      $content .= $LANG->getLL('msg_no_person_found'). '<br /><br />';

    if($warnings) {
      $content .= '<b>'. $LANG->getLL('msg_profiles_warnings'). '</b><br><ul><li>';
      $content .= implode('<li>',$warnings);
      $content .= '</ul>';
    }
    return $content;
  }

  /**
   * Sucht die UID des neuen Profils aus der DB. Wenn mehr als ein Datensatz gefunden wird.
   * Dann wird 0 geliefert.
   */
  function findNewProfile(&$profile) {
    # build SQL for select
    $what = 'uid';
    $from = 'tx_cfcleague_profiles';
    # WHERE
    $where = 'pid="'.$profile['pid'].'" AND tstamp="'.$profile['tstamp'].
             '" AND cruser_id="'.$profile['cruser_id'].
             '" AND last_name="' . $profile['last_name'] . '"';
    if($profile['first_name']) $where .= ' AND first_name="' . $profile['first_name'] . '"';
    
//    t3lib_div::debug($where, 'WHERE');

    $rows = tx_cfcleague_db::queryDB($what,$where,$from,'','',0);

    return count($rows) > 1 ? 0 : $rows;
  }
	
	/**
	 * Returns the formtool
	 * @return tx_rnbase_util_FormTool
	 */
	function getFormTool() {
		return $this->formTool;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modTeamsProfileCreate.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modTeamsProfileCreate.php']);
}
?>