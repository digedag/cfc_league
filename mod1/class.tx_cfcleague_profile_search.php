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

/**
 * Die Klasse ermöglicht die Suche von Profilen unabhängig vom Seitenbaum. Das Modul
 * wurde relativ schnell runterprogrammiert und ist daher nicht auf Erweiterbarkeit
 * ausgelegt.
 */
class tx_cfcleague_profile_search extends t3lib_extobjbase {
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

      // Wir zeigen die Liste an
      $profiles = $this->searchProfiles($data);
      $content .= $this->doc->section($LANG->getLL('msg_found_person'),$this->buildProfileTable($profiles ), 0, 1);
    }
    return $content;
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
      if(strlen($data['last_name'])) {
        $where .= "last_name like '%" . $data['last_name'] . "%' ";
      }
      if(strlen($data['first_name'])) {
       if(strlen($where) > 0) $where .= 'AND ';
       $where .= "first_name like '%" . $data['first_name'] . "%' ";
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
    $this->doc->tableLayout = Array (
      '0' => Array( // Format für 1. Zeile
         'defCol' => Array('<td valign="top" style="font-weight:bold;padding:2px 5px;">','</td>') // Format für jede Spalte in der 1. Zeile
      ),
      'defRow' => Array ( // Formate für alle Zeilen
        'defCol' => Array('<td valign="top" style="padding:0 5px;border-bottom:1px solid gray;">','</td>') // Format für jede Spalte in jeder Zeile
      )
    );


    $out = '';
    if(!count($profiles)) {
      $out = $this->doc->icons(ICON_WARN). ' ' . $LANG->getLL('msg_no_person_found');
    }
    else {

      $arr = Array(Array('UID',$LANG->getLL('label_lastname'),$LANG->getLL('label_firstname'),$LANG->getLL('label_birthday'),$LANG->getLL('information')));
      foreach($profiles As $profile) {
        $row = array();
        $row[] = $profile['uid'];
        $row[] = $profile['last_name'];
        $row[] = $profile['first_name'] ? $profile['first_name'] : '&nbsp;';

        $row[] = date('j.n.Y',$profile['birthday']) . ' <input type="submit" name="data[edit_profile][' . $profile['uid'] . ']" value="'.$LANG->getLL('btn_edit').'"';
        // Die Zusatzinfos zusammenstellen
        $infos = 'Seite: ' . t3lib_BEfunc::getRecordPath($profile['pid'],'',0) . '<br />';
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

//      $out = count($profiles) . ' Personen gefunden!';
    }
    return $out;
  }

  function createSearchForm(&$data) {
    global $LANG;
    $out = '';
    $out .= 'Name: ';
    $out .= $this->formTool->createTxtInput('data[last_name]', $data['last_name'], 20);
    $out .= ' Vorname: ';
    $out .= $this->formTool->createTxtInput('data[first_name]', $data['first_name'], 20);
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
