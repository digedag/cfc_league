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

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');
require_once (PATH_t3lib.'class.t3lib_extobjbase.php');
$BE_USER->modAccess($MCONF,1);

require_once('../class.tx_cfcleague_form_tool.php');

/**
 * Die Klasse ermöglicht die Suche von Profilen unabhängig vom Seitenbaum. Das Modul
 * wurde relativ schnell runterprogrammiert und ist daher nicht auf Erweiterbarkeit
 * ausgelegt.
 */
class tx_cfcleague_profile_search extends t3lib_extobjbase {
  var $doc, $MCONF;
  /** Verstecken der Suchergebnisse */
  var $hideResults = false; 

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
    $content = '';

    $this->doc = $this->pObj->doc;

    $this->formTool = t3lib_div::makeInstance('tx_cfcleague_form_tool');
    $this->formTool->init($this->pObj->doc);

    // Selector-Instanz bereitstellen
    $this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
    $this->selector->init($this->pObj->doc, $this->MCONF);

    // Wir benötigen die $TCA, um die maximalen Spieler pro Team prüfen zu können
    t3lib_div::loadTCA('tx_cfcleague_teams');

    $data = t3lib_div::_GP('data');
//t3lib_div::debug($data, 'mod_prof_search');

    $content .= $this->doc->section($LANG->getLL('msg_search_person'),$this->createSearchForm($data), 0, 1);
    $content.=$this->doc->spacer(5);

    // Haben wir Daten im Request?
    if (is_array($data)) {
      // Soll ein Profil bearbeitet werden?
      $content .= $this->handleProfileUpdate($data);
      $content .= $this->handleProfileMerge($data);
      
      // Wir zeigen die Liste an
      if(!$this->hideResults) {
	      $profiles = $this->searchProfiles($data);
	      $content .= $this->doc->section($LANG->getLL('msg_found_person'),$this->buildProfileTable($profiles ), 0, 1);
      }
    }
    return $content;
  }
  /**
   * Zusammenführung von zwei Profilen
   *
   * @param array $data
   */
  function handleProfileMerge(&$data) {
    global $LANG;
  	$profile1 = intval($data['merge1']);
   	$profile2 = intval($data['merge2']);
    if($data['merge_profiles']) { // Step 1
    	if(!($profile1 && $profile2) || ($profile1 == $profile2)) {
      	return $this->doc->icons(ICON_FATAL) . $LANG->getLL('msg_merge_selectprofiles');
    	}
    	$this->hideResults = true;
    	// Beide Profile nochmal anzeigen
    	// Das führende Profile muss ausgewählt werden
      $out .= $this->doc->icons(ICON_INFO) . $LANG->getLL('msg_merge_selectprofile');
    	
      $out .= $this->createProfileMergeForm($profile1, $profile2);
      
    }
    elseif ($data['merge_profiles_do']) { // Step 2
//    	$this->hideResults = true;
    	//Welches ist das führende Profil?
  		$leading = intval($data['merge']);
  		tx_div::load('tx_cfcleague_mod1_profileMerger');
  		$errors = tx_cfcleague_mod1_profileMerger::merge($leading, $leading == $profile1 ? $profile2 : $profile1);

      $out .= $this->doc->icons(ICON_OK) . $LANG->getLL('msg_merge_done');
    }
    if($out)
			$out = $this->doc->section($LANG->getLL('label_mergehead'),$out, 0, 1);
    return $out;
  }

  /**
   * Erstellt das Form für den Abgleich zweier Personen. Der Nutzer muss das führende 
   * Profil auswählen.
   *
   * @param int $uid1
   * @param int $uid2
   */
  function createProfileMergeForm($uid1, $uid2) {
    global $LANG;
    tx_div::load('tx_cfcleague_showItem');
    $info = t3lib_div::makeInstance('tx_cfcleague_showItem');

    $out = '<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">';
    $out .= '<tr><td class="c-headLineTable" style="vertical-align:top;">';
    $info->init('tx_cfcleague_profiles', $uid1);
    $out .= $this->doc->section($LANG->getLL('label_profile1').':',$info->main());
    $out .= '</td><td class="c-headLineTable" style="vertical-align:top;">';
    $info->init('tx_cfcleague_profiles', $uid2);
    $out .= $this->doc->section($LANG->getLL('label_profile2').':',$info->main());
    $out .= '</td></tr>';
    
    $out .= '<tr><td class="bgColor5">' .
            $this->formTool->createHidden('data[merge1]', $uid1) . 
            $this->formTool->createRadio('data[merge]', $uid1, true);
    $out .= '</td><td class="bgColor5">' .
    				$this->formTool->createHidden('data[merge2]', $uid2) . 
    				$this->formTool->createRadio('data[merge]', $uid2) . '</td></tr>';
    $out .= '</table>';

    $out .= $this->formTool->createSubmit('data[merge_profiles_do]', $LANG->getLL('label_merge'), $LANG->getLL('msg_merge_confirm'));
    
    return $out;
  }
  
  /**
   * Bearbeitet das interne Eingabeformular zu einer Person. Derzeit kann das Geburtsdatum
   * gesetzt werden.
   */
  function handleProfileUpdate(&$data) {
    global $LANG;
    $out = '';
    // Soll das Edit-Formular gezeigt werden?
    if($data['edit_profile']) {
    	$this->hideResults = true;
    	$uids = array_keys($data['edit_profile']);

      $profiles = $this->searchProfiles($data,$uids[0]);
        $out .= $this->doc->section($LANG->getLL('msg_edit_person'),$this->showProfileForm($profiles), 0, 1);
    }
    elseif($data['update_profile_do']) { // Wurde der Speichern-Button gedrückt?
      // Das Datum prüfen
      $out .= $this->doc->section($LANG->getLL('msg_person_saved'),$this->updateProfiles($data['update_profile']), 0, 1);
     
    }

    return $out;
  }

  /**
   * Aktualisiert die übergebenen Profile. Derzeit wird nur das Geburtsdatum gespeichert.
   */
  function updateProfiles($profiles) {
    global $LANG;
    $out = '';
    foreach($profiles As $uid => $profile) {
      // Zuerst das Datum prüfen und umwandeln
      $date = $profile['birthday'];
      list($day, $month, $year) = explode('.', $date);
      if(!checkdate($month, $day, $year)) {
        $out = $this->doc->icons(ICON_FATAL) . ' Invalid date -' . $date . '- für UID: ' . $uid;
      }
      else {
        $profile['birthday'] = mktime(0,0,0,$month,$day,$year);
        tx_cfcleague_db::updateDB('tx_cfcleague_profiles',$uid,$profile);
//t3lib_div::debug($profile, 'mod_prof_search');

        $out = $this->doc->icons(ICON_OK) . ' '.$LANG->getLL('msg_date_saved').': ' . $date;
      }
    }
    return $out;
  }

  /**
   * Anzeige der Bearbeitungsmaske für ein Profil. Hier kann das Geburtsdatum der Person
   * geändert werden. Es sind auch Werte vor 1970 möglich.
   */
  function showProfileForm(&$profiles) {
    global $LANG;
    $out = '';
    if(count($profiles) == 0) {
      $out .=  $this->doc->icons(ICON_FATAL) . ' Internal error. Sorry no profile found!';
    }
    else {
      $profile = $profiles[0];
      // Jetzt das Formular anzeigen
      $out .= $profile['last_name'];
      if($profile['first_name'])
        $out .= ', ' . $profile['first_name'];
      $out .= ' [UID: ' . $profile['uid'] . '] ';
      $out .= $this->formTool->createTxtInput('data[update_profile][' . $profile['uid'] . '][birthday]', date('j.n.Y',$profile['birthday']), 10);
      $out .= ' <input type="submit" name="data[update_profile_do]" value="'.$LANG->getLL('btn_save').'"';
    }

    return $out;
  }

  /**
   * Sucht die Profile mit den übergebenen Parametern. Entweder wird über 
   * Vor- und Zuname gesucht, oder man übergibt direkt eine UID.
   */
  function searchProfiles(&$data, $uid = 0) {
    $what = 'tx_cfcleague_profiles.uid,tx_cfcleague_profiles.pid,'.
            'last_name, first_name,birthday, '.
            "t1.short_name as 'team_name', t1.uid as 'team_uid'";

    $from = Array('tx_cfcleague_profiles ' .
              'LEFT JOIN tx_cfcleague_teams AS t1 ON FIND_IN_SET(tx_cfcleague_profiles.uid, t1.players) '
              , 'tx_cfcleague_profiles');

    $where = '';
    if($uid) {
      $where .= 'tx_cfcleague_profiles.uid = ' . intval($uid) . ' ';
    }
    else {
      if(strlen($data['searchterm'])) {
        $where .= "last_name like '%" . $data['searchterm'] . "%' ";
        $where .= "OR first_name like '%" . $data['searchterm'] . "%' ";
      }
    }
    $orderBy = 'last_name, first_name, tx_cfcleague_profiles.uid';

    $rows = tx_cfcleague_db::queryDB($what, $where, $from,'',$orderBy, 0);
//t3lib_div::debug($rows,'search');
    $cnt = count($rows);
    if(!$cnt) return $rows; // Keine Daten gefunden

    // Für jedes Team in dem die Person zugeordnet ist, erhalten wir eine Zeile
    // Diese müssen wir jetzt wieder zusammenfügen
    $lastRow = $rows[0];
    $ret = array();
    for($i=0; $i < $cnt; $i++) {
      if(intval($lastRow['uid']) != intval($rows[$i]['uid'])) {
//t3lib_div::debug($lastRow, 'last search');
        // Ein neuer Spieler, also den alten ins Ergebnisarray legen
        $ret[] = $lastRow;
        $lastRow = $rows[$i];
      }
      // Den Verein der aktuellen Row in die Liste der lastRow legen
      if($rows[$i]['team_uid']) {
        $lastRow['teams'][] = array('team_uid' => $rows[$i]['team_uid'], 'team_name' => $rows[$i]['team_name']);
      }
    }
    // Das letzte Profil noch ins Ergebnisarray legen
    $ret[] = $lastRow;

    return $ret;
  }

  /**
   * Erstellt eine Tabelle mit den gefundenen Personen
   */
  function buildProfileTable(&$profiles) {
    global $LANG;
 
//		  	<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">
//		  	<tr>
//		  		<td class="c-headLineTable">Name</td>
//		  		<td class="c-headLineTable">Informationen</td>
//		  		<td class="c-headLineTable">Aktion</td></tr>';
    
    $this->doc->tableLayout = Array (
      'table' => array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table>'),
      '0' => Array( // Format für 1. Zeile
         'defCol' => Array('<td valign="top" class="c-headLineTable" style="font-weight:bold;padding:2px 5px;">','</td>') // Format für jede Spalte in der 1. Zeile
      ),
      'defRowOdd' => Array ( // Formate für alle Zeilen
        'defCol' => Array('<td class="db_list_alt" valign="top" style="padding:2px 5px;border-bottom:1px solid gray;">','</td>') // Format für jede Spalte in jeder Zeile
      ),
      'defRowEven' => Array ( // Formate für alle Zeilen
        'defCol' => Array('<td valign="top" style="padding:2px 5px;border-bottom:1px solid gray;">','</td>') // Format für jede Spalte in jeder Zeile
      )
    );


    $out = '';
    if(!count($profiles)) {
      $out = $this->doc->icons(ICON_WARN). ' ' . $LANG->getLL('msg_no_person_found');
    }
    else {

      $arr = Array(Array($LANG->getLL('label_merge'),'UID',
      										$LANG->getLL('label_lastname'),
      										$LANG->getLL('label_firstname'),
      										$LANG->getLL('label_birthday'),
      										$LANG->getLL('label_information'),
      										'&nbsp;','&nbsp;','&nbsp;'));
      foreach($profiles As $profile) {
        $row = array();
        $row[] = $this->formTool->createRadio('data[merge1]', $profile['uid']) . $this->formTool->createRadio('data[merge2]', $profile['uid']);
        $row[] = $profile['uid'];
        $row[] = $profile['last_name'];
        $row[] = $profile['first_name'] ? $profile['first_name'] : '&nbsp;';

        $row[] = date('j.n.Y',$profile['birthday']) . ' <input type="submit" name="data[edit_profile][' . $profile['uid'] . ']" value="'.$LANG->getLL('btn_edit').'"';
        // Die Zusatzinfos zusammenstellen
        $infos = $LANG->getLL('label_page'). ': ' . t3lib_BEfunc::getRecordPath($profile['pid'],'',0) . '<br />';
        if(is_array($profile['teams']))
          foreach($profile['teams'] as $team) {
            $infos .= '&nbsp;Team: ' . $team['team_name'];
            $infos .= $this->formTool->createEditLink('tx_cfcleague_teams', $team['team_uid'],'') . '<br />';
          }

        $row[] = $infos;

        $row[] = $this->formTool->createEditLink('tx_cfcleague_profiles', $profile['uid']);
        $row[] = $this->formTool->createInfoLink('tx_cfcleague_profiles', $profile['uid']);
        $row[] = $this->formTool->createMoveLink('tx_cfcleague_profiles', $profile['uid'], $profile['pid']);
        $arr[] = $row;
      }

      $out .= $this->doc->table($arr);
      if(count($arr)) {
      	// Button für Merge einbauen
    		$out .= $this->formTool->createSubmit('data[merge_profiles]', $LANG->getLL('label_merge'));
			}
//      $out = count($profiles) . ' Personen gefunden!';
    }
    return $out;
  }

  function createSearchForm(&$data) {
    global $LANG;
    $out = '';
    $out .= $LANG->getLL('label_searchterm').': ';
    $out .= $this->formTool->createTxtInput('data[searchterm]', $data['searchterm'], 20);
    // Den Update-Button einfügen
    $out .= '<input type="submit" name="search" value="'.$LANG->getLL('btn_search').'"';
    // Jetzt noch zusätzlichen JavaScriptcode für Buttons auf der Seite
    $out .= $this->formTool->getJSCode($this->id);

    return $out;
  }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_profile_search.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_profile_search.php']);
}
?>