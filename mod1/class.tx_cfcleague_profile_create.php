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

require_once(PATH_site.'typo3/sysext/cms/tslib/class.tslib_content.php');

/**
 * Die Klasse ermöglicht die schnelle Erstellung von Profilen
 */
class tx_cfcleague_profile_create extends t3lib_extobjbase {
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

    parent::init($pObj,$conf);

    $this->MCONF = $pObj->MCONF;
    $this->id = $pObj->id;
  }


	function main() {
		global $LANG, $TCA;

		$this->doc = $this->pObj->doc;
		$content = '';
		
		$this->formTool = tx_div::makeInstance('tx_rnbase_util_FormTool');
		$this->formTool->init($this->pObj->doc);
		// Selector-Instanz bereitstellen
		$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$this->selector->init($this->pObj->doc, $this->MCONF);

		// Wir benötigen die $TCA, um die maximalen Spieler pro Team prüfen zu können
		t3lib_div::loadTCA('tx_cfcleague_teams');
		$baseInfo['maxCoaches'] = intval($TCA['tx_cfcleague_teams']['columns']['coaches']['config']['maxitems']);
		$baseInfo['maxPlayers'] = intval($TCA['tx_cfcleague_teams']['columns']['players']['config']['maxitems']);
		$baseInfo['maxSupporters'] = intval($TCA['tx_cfcleague_teams']['columns']['supporters']['config']['maxitems']);

		$selector = '';
		$saison = $this->selector->showSaisonSelector($selector,$this->id);
		
		if(!($saison && count($saison->getCompetitions()))) {
			if($this->pObj->isTYPO42())
				$this->pObj->subselector = $selector;
			else
				$content .= '<div class="cfcleague_selector">'.$selector.'</div><div class="cleardiv"/>';
			$content.=$this->doc->section('Info:', $saison ? $LANG->getLL('msg_NoCompetitonsFound') : $LANG->getLL('msg_NoSaisonFound'),0,1,ICON_WARN);
			return $content;
		}
		
		// Anzeige der vorhandenen Ligen
		$league = $this->selector->showLeagueSelector($selector,$this->id,$saison->getCompetitions());
		$team = $this->selector->showTeamSelector($selector,$this->id,$league);
		if($this->pObj->isTYPO42())
			$this->pObj->subselector = $selector;
		else 
			$content .= '<div class="cfcleague_selector">'.$selector.'</div><div class="cleardiv"/>';

		$data = t3lib_div::_GP('data');
		if(!$team){ // Kein Team gefunden
			$content.=$this->doc->section('Info:', $LANG->getLL('msg_no_team_found'),0,1,ICON_WARN);
			return $content;
		}
		// Wenn ein Team gefunden ist, dann können wir das Modul schreiben
		$menu = $this->selector->showTabMenu($this->id, 'teamtools', 
						array('0' => $LANG->getLL('create_players'), 
									'1' => $LANG->getLL('add_players'),
//									'2' => $LANG->getLL('manage_teamnotes'),
						));
		$tabs = $menu['menu'];
		$tabs .= '<div style="display: block; border: 1px solid #a2aab8;clear:both;" ></div>';
		
		if($this->pObj->isTYPO42())
			$this->pObj->tabs = $tabs;
		else
			$content .= $tabs;

//		$content .= $menu['menu'];
//		$content .= '<div style="display: block; border: 1px solid #a2aab8;"></div>';
		switch($menu['value']) {
			case 0:
				$content .= $this->showCreateProfiles($data, $team, $baseInfo);
				break;
			case 1:
				$content .= $this->showAddProfiles($data, $team, $baseInfo);
				break;
			case 2:
				$content .= $this->showManageTeamNotes($data, $team);
				break;
		}
		// Den JS-Code für Validierung einbinden
		$content .= $this->formTool->form->JSbottom('editform');
		return $content;
	}

	private function showManageTeamNotes($data, $team) {
		$clazzName = tx_div::makeInstanceClassname('tx_cfcleague_mod1_subTeamNotes');
		$subMod = new $clazzName($this);
		$content .= $subMod->handleRequest($team);
		return $content;
	}

	private function showAddProfiles(&$data, &$team, &$baseInfo) {
		$baseInfo['freePlayers'] = $baseInfo['maxPlayers'] - $team->getPlayerSize();
		$baseInfo['freeCoaches'] = $baseInfo['maxCoaches'] - $team->getCoachSize();
		$baseInfo['freeSupporters'] = $baseInfo['maxSupporters'] - $team->getSupporterSize();
		if($baseInfo['freePlayers'] < 1 && $baseInfo['freeCoaches'] < 1 && $baseInfo['freeSupporters'] < 1) {
			// Kann nix mehr angelegt werden
			$content .= $this->doc->section('Message:',$LANG->getLL('msg_maxPlayers'),0,1,ICON_WARN);
		}
		else {
			$content .= $this->doc->section('Message:',$this->getInfoMessage($baseInfo),0,1, ICON_INFO);
			// Einblenden der Personensuche
			$clazzName = tx_div::makeInstanceClassname('tx_cfcleague_mod1_subAddProfiles');
			$addMatches = new $clazzName($this);
			$content .= $addMatches->handleRequest($team, $baseInfo);
		}
		return $content;
	}
	
	private function showCreateProfiles(&$data, &$team, &$baseInfo) {
		global $LANG;
		$rootPage = tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'profileRootPageId');
		$goodPages = tx_cfcleague_db::getPagePath($this->id);
		if(!in_array($rootPage, $goodPages)) {
			$content = $this->doc->section('Message:',$LANG->getLL('msg_pageNotAllowed'),0,1,ICON_WARN);
			return $content;
		}

		if (is_array($data['tx_cfcleague_profiles'])) {
			$content .= $this->createProfiles($data,$team, $baseInfo);
			$team->refresh();
		}

		$baseInfo['freePlayers'] = $baseInfo['maxPlayers'] - $team->getPlayerSize();
		$baseInfo['freeCoaches'] = $baseInfo['maxCoaches'] - $team->getCoachSize();
		$baseInfo['freeSupporters'] = $baseInfo['maxSupporters'] - $team->getSupporterSize();
		if($baseInfo['freePlayers'] < 1 && $baseInfo['freeCoaches'] < 1 && $baseInfo['freeSupporters'] < 1) {
			// Kann nix mehr angelegt werden
			$content .= $this->doc->section('Message:',$LANG->getLL('msg_maxPlayers'),0,1,ICON_WARN);
		}
		else {
			$content .= $this->doc->section('Info:',$LANG->getLL('msg_checkPage') . ': <b>' . t3lib_BEfunc::getRecordPath($this->id,'',0) . '</b>' ,0,1,ICON_WARN);
			$content .= $this->doc->section('Message:',$this->getInfoMessage($baseInfo),0,1, ICON_INFO);
			// Wir zeigen 15 Zeilen mit Eingabefeldern
			$content .= $this->prepareInputTable($team);
			// Den Update-Button einfügen
			$content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_create').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('msg_CreateProfiles')).')">';
		}
		return $content;
	}
	/**
	 * Liefert die Informationen, über den Zustand des Teams.
	 *
	 */
	private function getInfoMessage($baseInfo) {
		global $LANG;
		$tableLayout = Array (
			'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'defRow' => Array( // Format für 1. Zeile
				'tr'		=> Array('<tr class="c-headLineTable">','</tr>'),
				'defCol' => Array($this->pObj->isTYPO42() ? '<td>': '<td class="c-headLineTable" style="font-weight:bold;color:white;padding:0 5px;">','</td>') // Format für jede Spalte in der 1. Zeile
			)
		);

		$arr[] = array($LANG->getLL('msg_number_of_players'), $baseInfo['freePlayers']);
		$arr[] = array($LANG->getLL('msg_number_of_coaches'), $baseInfo['freeCoaches']);
		$arr[] = array($LANG->getLL('msg_number_of_supporters'), $baseInfo['freeSupporters']);
		return $this->doc->table($arr, $tableLayout);
	}
	/**
	 * Erstellt eine Tabelle mit den schon vorhandenen Personen und den noch möglichen neuen
	 * Personen.
	 * Wenn keine Personen da sind, gibt es 15 Eingabefelder, sonst nur 5
	 * @param tx_cfcleague_team $team
	 */
	function prepareInputTable(&$team) {
		global $LANG;

		$tableLayout = Array (
			'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'0' => Array( // Format für 1. Zeile
				'tr'		=> Array('<tr class="c-headLineTable">','</tr>'),
				'defCol' => Array($this->pObj->isTYPO42() ? '<td>': '<td class="c-headLineTable" style="font-weight:bold;color:white;padding:0 5px;">','</td>') // Format für jede Spalte in der 1. Zeile
			),
			'defRow' => Array ( // Formate für alle Zeilen
				'tr'	   => Array('<tr class="db_list_normal">', '</tr>'),
				'defCol' => Array('<td>','</td>') // Format für jede Spalte in jeder Zeile
			),
			'defRowEven' => Array ( // Formate für alle Zeilen
				'tr'	   => Array('<tr class="db_list_alt">', '</tr>'),
				'defCol' => Array($this->pObj->isTYPO42() ? '<td>' : '<td class="db_list_alt">','</td>') // Format für jede Spalte in jeder Zeile
			)
		);

		// Es werden zwei Tabellen erstellt
		$arr = Array(Array('&nbsp;',$LANG->getLL('label_firstname'),$LANG->getLL('label_lastname'),'&nbsp;'));

		$this->addProfiles($arr, $team->getCoachNames(), $LANG->getLL('label_profile_coach'));
		$this->addProfiles($arr, $team->getPlayerNames(), $LANG->getLL('label_profile_player'));
		$this->addProfiles($arr, $team->getSupporterNames(), $LANG->getLL('label_profile_supporter'));

		$tableProfiles = count($arr) > 1 ? $this->doc->table($arr, $tableLayout) : '';

		$arr = Array(Array('&nbsp;',$LANG->getLL('label_firstname'),$LANG->getLL('label_lastname'),'&nbsp;'));
		$maxFields = count($team->getPlayerNames()) > 5 ? 5 : 15;
		for($i=0; $i < $maxFields; $i++){
			$row = array();
			$row[] = $i + 1;
			$row[] = $this->formTool->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][first_name]', '',10);
			$row[] = $this->formTool->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][last_name]', '',10);
			$row[] = $this->formTool->createSelectSingleByArray('data[tx_cfcleague_profiles][NEW'.$i.'][type]', '',$this->getTypeArray()) .
			$this->formTool->createHidden('data[tx_cfcleague_profiles][NEW'.$i.'][pid]', $this->id);
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

	function getTypeArray() {
    global $LANG;
    return Array(
      '1' => $LANG->getLL('label_profile_player'),
      '2' => $LANG->getLL('label_profile_coach'),
      '3' => $LANG->getLL('label_profile_supporter'),
    );
  }

  /**
   * Add profiles to profile list
   *
   * @param array $arr
   * @param array $profiles
   * @param string $label
   */
  function addProfiles (&$arr, &$profileNames, $label) {
    $i = 1;
    if($profileNames) foreach($profileNames As $uid => $prof) {
    	if($i == 1)
      	$arr[] = array('','&nbsp;',''); // Leere Zeile als Trenner;
    	$row = array();
      $row[] = $i++ == 1 ? $label : '';
      $row[] = $prof[first_name];
      $row[] = $prof[last_name];
      $row[] = $this->formTool->createEditLink('tx_cfcleague_profiles', $uid);
      $arr[] = $row;
    }
  }
  /**
   * Erstellt die angeforderten Profile
   * @param $profiles Array mit den Daten aus dem Request
   * @param $team das aktuelle Team, dem die Personen zugeordnet werden
   */
  function createProfiles(&$profiles, &$team, $baseInfo) {
    global $BE_USER, $LANG;

    $maxCoaches = $baseInfo['maxCoaches'];
    $maxPlayers = $baseInfo['maxPlayers'];
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

    // Die IDs der Trainer, Spieler und Betreuer mergen
    if(count($coachIds)) {
      $data['tx_cfcleague_teams'][$team->record['uid']]['coaches'] = implode(',',$this->mergeArrays(t3lib_div::intExplode(',',$team->record['coaches']), $coachIds));
    }
    if(count($playerIds)) {
      $data['tx_cfcleague_teams'][$team->record['uid']]['players'] = implode(',',$this->mergeArrays(t3lib_div::intExplode(',',$team->record['players']), $playerIds));
    }
    if(count($supportIds)) {
      $data['tx_cfcleague_teams'][$team->record['uid']]['supporters'] = implode(',',$this->mergeArrays(t3lib_div::intExplode(',',$team->record['supporters']), $supportIds));
    }

//    t3lib_div::debug($data, 'tx_cfcleague_profile_create');
    if(count($data)) {
      reset($data);
      $tce =& tx_cfcleague_db::getTCEmain($data);
      $tce->process_datamap();
      $content .= $LANG->getLL('msg_profiles_created'). '<br /><br />';
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
   * Zwei Arrays zusammenführen. Sollte eines der Array leer sein, dann wird es ignoriert.
   * Somit werden unnötige 0-Werte vermieden.
   */
  function mergeArrays($arr1,$arr2){
    $ret = $arr1[0] ? $arr1 : 0;
    if($ret && $arr2) {
      $ret = array_merge($ret, $arr2);
    }
    elseif($arr2)
      $ret = $arr2;
    return $ret;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_profile_create.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_profile_create.php']);
}
?>
