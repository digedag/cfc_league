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

tx_rnbase::load('tx_cfcleague_models_Competition');

/**
 * Die Klasse stellt Auswahlmenus zur Verfügung
 */
class tx_cfcleague_selector{
	var $doc, $MCONF;

	/**
	 * Initialisiert das Objekt mit dem Template und der Modul-Config.
	 */
	function init($doc, $modName){
		$this->doc = $doc;
		$this->MCONF['name'] = $modName; // deprecated
		$this->modName = $modName;
	}
	/**
	 * Returns the form tool
	 * @return tx_rnbase_util_FormTool
	 */
	function getFormTool() {
		if(!$this->formTool) {
			$this->formTool = tx_rnbase::makeInstance('tx_rnbase_util_FormTool');
			$this->formTool->init($this->doc);
		}
		return $this->formTool;
	}
	/**
	 * Darstellung der Select-Box mit allen Ligen der übergebenen Seite. Es wird auf die aktuelle Liga eingestellt.
	 * @return den aktuellen Wettbewerb als Objekt oder 0
	 */
	public function showLeagueSelector(&$content, $pid, $leagues=0){
		// Wenn vorhanden, nehmen wir die übergebenen Wettbewerbe, sonst schauen wir auf der aktuellen Seite nach
		$leagues = $leagues ? $leagues : $this->findLeagues($pid);
		$LEAGUE_MENU = Array (
			'league' => array()
		);
		$objLeagues = array();
		foreach($leagues as $idx=>$league){
			if(is_object($league)) {
				$objLeagues[$league->uid] = $league; // Objekt merken
				$LEAGUE_MENU['league'][$league->uid] = $league->record['internal_name'] ? $league->record['internal_name'] : $league->record['name'];
			}
			else
				$LEAGUE_MENU['league'][$league['uid']] = $league['internal_name'] ? $league['internal_name'] : $league['name'];
		}
		// Ohne Liga-Array ist eine weitere Verarbeitung sinnlosl
		if(!count($LEAGUE_MENU['league'])) return 0;
		// TODO: auf rn_base umstellen
		$this->LEAGUE_SETTINGS = t3lib_BEfunc::getModuleData(
			$LEAGUE_MENU, t3lib_div::_GP('SET'), $this->MCONF['name'] // Das ist der Name des Moduls
		);

		$menu = (tx_rnbase_util_TYPO3::isTYPO62OrHigher() && is_array($LEAGUE_MENU['league']) && count($LEAGUE_MENU['league']) == 1) ?
				$this->buildDummyMenu('SET[league]', $LEAGUE_MENU['league']) :
				t3lib_BEfunc::getFuncMenu(
			$pid, 'SET[league]', $this->LEAGUE_SETTINGS['league'], $LEAGUE_MENU['league'], 'index.php'
		);
		// In den Content einbauen
		// Zusätzlich noch einen Edit-Link setzen
		if($menu) {
			$links = $this->getFormTool()->createEditLink('tx_cfcleague_competition', $this->LEAGUE_SETTINGS['league'], '');
			// Jetzt noch den Cache-Link
			$links .= ' ' . $this->getFormTool()->createLink('&clearCache=1', $pid, '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clear_all_cache.gif', 'width="11" height="12"').' title="Statistik-Cache leeren" border="0" alt="" />');
			$links .= $this->getFormTool()->createNewLink('tx_cfcleague_competition', $pid, '');
			$menu = '<div class="cfcselector"><div class="selector">' . $menu . '</div><div class="links">' . $links . '</div></div>';
		}
		$content.= $menu;

//		$content.=$this->doc->section('', $this->doc->funcMenu($headerSection, $menu));

		if(t3lib_div::_GP('clearCache') && $this->LEAGUE_SETTINGS['league']) {
			// Hook aufrufen
			tx_rnbase_util_Misc::callHook('cfc_league', 'clearStatistics_hook', 
				array('compUid' => $this->LEAGUE_SETTINGS['league']), $this);

//			if (is_object($serviceObj = t3lib_div::makeInstanceService('memento'))) {
//				// Cache löschen
//				$serviceObj->clear('', $this->LEAGUE_SETTINGS['league']);
//			}
		}

		// Aktuellen Wert als Liga-Objekt zurückgeben
		if(count($objLeagues))
			return $this->LEAGUE_SETTINGS['league'] ? $objLeagues[$this->LEAGUE_SETTINGS['league']] :0;
//		return $this->LEAGUE_SETTINGS['league'] ? new tx_cfcleague_league($this->LEAGUE_SETTINGS['league']) :0;
		return $this->LEAGUE_SETTINGS['league'] ? new tx_cfcleague_models_Competition($this->LEAGUE_SETTINGS['league']) :0;
	}

	private function buildDummyMenu($elementName, $menuItems) {
		// Ab T3 6.2 wird bei einem Menu-Eintrag keine Selectbox mehr erzeugt.
		// Also sieht man nicht, wo man sich befindet. Somit wird eine Dummy-Box
		// benötigt.
		$options = array();
		
		foreach ($menuItems as $value => $label) {
			$options[] = '<option value="' . htmlspecialchars($value) . '" selected="selected">' . htmlspecialchars($label, ENT_COMPAT, 'UTF-8', FALSE) . '</option>';
		}
		return '

				<!-- Function Menu of module -->
				<select name="' . $elementName . '" >
					' . implode('
					', $options) . '
				</select>
						';
	}
	/**
	 * Darstellung der Select-Box mit allen Teams des übergebenen Wettbewerbs. Es wird auf das aktuelle Team eingestellt.
	 * @return die aktuelle Team als Objekt
	 */
	public function showTeamSelector(&$content, $pid, $league, $options=array()){
		if(!$league)
			return 0;

		$selectorId = $options['selectorId'] ? $options['selectorId'] : 'team';
		$TEAM_MENU = Array (
			$selectorId => array()
		);
		if($options['firstItem']) {
			$TEAM_MENU[$selectorId][$options['firstItem']['id']] = $options['firstItem']['label'];
		}

		foreach($league->getTeamNames() as $id => $team_name){
			$TEAM_MENU[$selectorId][$id] = $team_name;
		}

		$TEAM_SETTINGS = t3lib_BEfunc::getModuleData($TEAM_MENU, t3lib_div::_GP('SET'), $this->modName);

		$menu = t3lib_BEfunc::getFuncMenu(
			$pid, 'SET['.$selectorId.']', $TEAM_SETTINGS[$selectorId], $TEAM_MENU[$selectorId]
		);
		$teamObj = null;
		if($TEAM_SETTINGS[$selectorId] > 0) {
	    tx_rnbase::load('tx_cfcleague_team');
			$teamObj = new tx_cfcleague_team($TEAM_SETTINGS[$selectorId]);
		}
		// In den Content einbauen
		// Zusätzlich noch einen Edit-Link setzen
		$noLinks = $options['noLinks'] ? true : false;
		if(!$noLinks && $menu) {
			$links = $this->getFormTool()->createEditLink('tx_cfcleague_teams', $TEAM_SETTINGS[$selectorId]);
//			$menu .= '</td><td style="width:90px; padding-left:10px;">' . $links;
			if($teamObj->record['club'])
				$links .= $this->getFormTool()->createEditLink('tx_cfcleague_club', intval($teamObj->record['club']), $GLOBALS['LANG']->getLL('label_club'));
			$menu = '<div class="cfcselector"><div class="selector">' . $menu . '</div><div class="links">' . $links . '</div></div>';
		}
		$content .= $menu;
//    $content.=$this->doc->section('', $this->doc->funcMenu($headerSection, $menu));

    return $teamObj;
  }

	/**
	 * Darstellung der Select-Box mit allen Vereinen. Es wird auf den aktuellen Verein eingestellt.
	 * @return tx_cfcleague_models_Club
	 */
	public function showClubSelector(&$content, $pid, $options=array()){
		$globalClubs = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
		$clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

		$selectorId = $options['selectorId'] ? $options['selectorId'] : 'club';
		$menuData = Array (
			$selectorId => array()
		);
		if($options['firstItem']) {
			$menuData[$selectorId][$options['firstItem']['id']] = $options['firstItem']['label'];
		}
		$fields = array();
		if(!$globalClubs)
			$fields['CLUB.PID'][OP_EQ_INT] = $pid;
		$options = array();
		if($clubOrdering)
			$options['orderby']['CLUB.CITY'] = 'asc';
		$options['orderby']['CLUB.NAME'] = 'asc';
		$clubs = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchClubs($fields, $options);

		foreach($clubs as $club){
			$label = ($clubOrdering ? $club->getCity().' - ' : '') . $club->getName();
			$menuData[$selectorId][$club->getUid()] = $label;
		}

		$menuSettings = t3lib_BEfunc::getModuleData($menuData, t3lib_div::_GP('SET'), $this->modName);

		$menu = t3lib_BEfunc::getFuncMenu(
			$pid, 'SET['.$selectorId.']', $menuSettings[$selectorId], $menuData[$selectorId]
		);
		$currItem = null;
		if($menuSettings[$selectorId] > 0) {
			$currItem = tx_rnbase::makeInstance('tx_cfcleague_models_Club', $menuSettings[$selectorId]);
		}
		// In den Content einbauen
		// Zusätzlich noch einen Edit-Link setzen
		$noLinks = $options['noLinks'] ? true : false;
		if(!$noLinks && $menu) {
			$links = $this->getFormTool()->createEditLink('tx_cfcleague_club', $menuSettings[$selectorId]);
			$links .= $this->createNewClubLink($pid);
			$menu = '<div class="cfcselector"><div class="selector">' . $menu . '</div><div class="links">' . $links . '</div></div>';
		}
		$content .= $menu;

    return $currItem;
	}
	private function createNewClubLink($pid) {
		$linker = tx_rnbase::makeInstance('tx_cfcleague_mod1_linker_NewClub');
		return $linker->makeLink(null, $this->getFormTool(), $pid, array());
	}

	/**
	 * Darstellung der Select-Box mit allen Spielrunden des übergebenen Wettbewerbs. Es wird auf die aktuelle Runde eingestellt.
	 */
	function showRoundSelector(&$content, $pid, $league){
		$entries = Array ();

		$objRounds = array();
		foreach($league->getRounds() as $round){
			if(is_object($round)) {
				$objRounds[$round->uid] = $round;
				$entries[$round->uid] = $round->record['name'] . ( intval($round->record['finished']) ? ' *' : '' );
			}
			else
				$entries[$round['round']] = $round['round_name'] . ( intval($round['max_status']) ? ' *' : '' );
		}

		$data = $this->getFormTool()->showMenu($pid, 'round', $this->MCONF['name'], $entries, 'index.php');
		// In den Content einbauen
		// Spielrunden sind keine Objekt, die bearbeitet werden können
		if($data['menu']) {
			$keys = array_flip(array_keys($entries));
			$currIdx = $keys[$data['value']];
			$keys = array_flip($keys);
			$prevIdx = ($currIdx > 0) ? $currIdx-1 : count($entries)-1;
			$nextIdx = ($currIdx < (count($entries)-1)) ? $currIdx+1 : 0;
			$prev = $this->getFormTool()->createLink('&SET[round]='.($keys[$prevIdx]), $pid, '&lt;');
			$next = $this->getFormTool()->createLink('&SET[round]='.($keys[$nextIdx]), $pid, '&gt;');
			$menu = '<div class="cfcselector"><div class="selector">' . $prev.$data['menu'].$next . '</div></div>';
		}
		$content.= $menu;

		return count($objRounds) ? $objRounds[$data['value']] : $data['value'];
	}

	/**
	 * Darstellung der Select-Box mit allen übergebenen Spielen. Es wird auf das aktuelle Spiel eingestellt.
	 * @return tx_cfcleague_match current match
	 */
	function showMatchSelector(&$content, $pid, $matches){
		$this->MATCH_MENU = Array (
			'match' => array()
		);
		foreach($matches as $idx=>$match){
			$this->MATCH_MENU['match'][$match['uid']] = $match['short_name_home'] . ' - ' . $match['short_name_guest'];
		}
		$this->MATCH_SETTINGS = t3lib_BEfunc::getModuleData(
			$this->MATCH_MENU, t3lib_div::_GP('SET'), $this->MCONF['name'] // Das ist der Name des Moduls
		);

		$menu = t3lib_BEfunc::getFuncMenu(
			$pid, 'SET[match]', $this->MATCH_SETTINGS['match'], $this->MATCH_MENU['match']
		);
		// In den Content einbauen
		// Zusätzlich noch einen Edit-Link setzen
		$links = $this->getFormTool()->createEditLink('tx_cfcleague_games', $this->MATCH_SETTINGS['match']);
		if($menu) {
			//$menu .= '</td><td style="width:90px; padding-left:10px;">' . $link;
			$menu = '<div class="cfcselector"><div class="selector">' . $menu . '</div><div class="links">' . $links . '</div></div>';
		}
		$content .= $menu;
//		$content.=$this->doc->section('', $this->doc->funcMenu($headerSection, $menu));

		// Aktuellen Wert als Match-Objekt zurückgeben
		tx_rnbase::load('tx_cfcleague_match');
		return new tx_cfcleague_match($this->MATCH_SETTINGS['match']);
	}

  /**
   * Darstellung der Select-Box mit allen Altersgruppen in der Datenbank.
   * @return die ID der aktuellen Altersgruppe
   */
  function showGroupSelector(&$content, $pid){
    // Zuerst die Gruppen ermitteln
    $groups = tx_cfcleague_db::queryDB('uid,name', 'uid > 0', 'tx_cfcleague_group', '', 'sorting');

    $this->GROUP_MENU = Array (
      'group' => array()
    );
    foreach($groups as $idx=>$group){
      $this->GROUP_MENU['group'][$group['uid']] = $group['name'];
    } 
    $this->GROUP_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->GROUP_MENU, t3lib_div::_GP('SET'), $this->MCONF['name'] // Das ist der Name des Moduls
    );

    $menu = t3lib_BEfunc::getFuncMenu(
      $pid, 'SET[group]', $this->GROUP_SETTINGS['group'], $this->GROUP_MENU['group']
    );
    // In den Content einbauen
    $content.=$this->doc->section('', $this->doc->funcMenu($headerSection, $menu));

    // Aktuellen Wert zurückgeben
    return $this->GROUP_SETTINGS['group'];
  }

  /**
   * Darstellung der Select-Box mit allen Saisons in der Datenbank.
   * @return die aktuelle Saison als Objekt oder 0
   */
  function showSaisonSelector(&$content, $pid){
    // Zuerst die Gruppen ermitteln
    $saisons = tx_cfcleague_db::queryDB('uid,name', 'uid > 0', 'tx_cfcleague_saison', '', 'sorting');

    $this->SAISON_MENU = Array (
      'saison' => array()
    );
    foreach($saisons as $idx=>$saison){
      $this->SAISON_MENU['saison'][$saison['uid']] = $saison['name'];
    } 
    $this->SAISON_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->SAISON_MENU, t3lib_div::_GP('SET'), $this->MCONF['name'] // Das ist der Name des Moduls
    );

    $menu = t3lib_BEfunc::getFuncMenu(
      $pid, 'SET[saison]', $this->SAISON_SETTINGS['saison'], $this->SAISON_MENU['saison']
    );
    // In den Content einbauen
    // Wir verzichten hier auf den Link und halten nur den Abstand ein
    if($menu) {
			$menu = '<div class="cfcselector"><div class="selector">' . $menu . '</div></div>';
//      $menu .= '</td><td style="width:90px; padding-left:10px;">';
    }
    $content.= $menu;
//    $content.=$this->doc->section('', $this->doc->funcMenu($headerSection, $menu));

    // Aktuellen Wert als Saison-Objekt zurückgeben
    tx_rnbase::load('tx_cfcleague_saison');
    return $this->SAISON_SETTINGS['saison'] ? new tx_cfcleague_saison($this->SAISON_SETTINGS['saison']) : 0;
  }

  /**
   * Zeigt ein TabMenu
   *
   * @param int $pid
   * @param string $name
   * @param array $entries
   * @return array with keys 'menu' and 'value'
   */
	public function showTabMenu($pid, $name, $entries) {
		$MENU = Array (
			$name => $entries
		);
		$SETTINGS = t3lib_BEfunc::getModuleData(
			$MENU, t3lib_div::_GP('SET'), $this->MCONF['name'] // Das ist der Name des Moduls
		);

		$out = '
		<div class="typo3-dyntabmenu-tabs">
			<table class="typo3-dyntabmenu" border="0" cellpadding="0" cellspacing="0">
			<tbody><tr>';

		foreach($entries As $key => $value) {
			//$out .= '<td class="tab" onmouseover="DTM_mouseOver(this);" onmouseout="DTM_mouseOut(this);" nowrap="nowrap">';
			$out .= '
				<td class="tab'.($SETTINGS[$name] == $key ? 'Act' : '').'" nowrap="nowrap">';
			//$out .= '<a href="#" onclick="jumpToUrl(\'index.php?&amp;id='.$pid.'&amp;SET['.$name.']='. $key .', this);\'>'.$value.'<img name="DTM-307fab8d03-1-REQ" src="clear.gif" alt="" height="10" hspace="4" width="10"></a></td>';
			$out .= '<a href="#" onclick="jumpToUrl(\'index.php?&amp;id='.$pid.'&amp;SET['.$name.']='. $key .'\', this);">'.$value.'</a></td>';

		}
		$out .= '
				</tr>
			</tbody></table></div>
		';
		$ret['menu'] = $out;
		$ret['value'] = $SETTINGS[$name];
		return $ret;
		
		// jumpToUrl('index.php?&amp;id=5&amp;SET[teamtools]='+this.options[this.selectedIndex].value, this);
	}
	/**
	 * Zeigt eine Art Tab-Menu
	 *
	 */
	public function showMenu($pid, $name, $entries) {
		$MENU = Array (
			$name => $entries
		);
		$SETTINGS = t3lib_BEfunc::getModuleData(
			$MENU, t3lib_div::_GP('SET'), $this->MCONF['name'] // Das ist der Name des Moduls
		);
		$ret['menu'] = t3lib_BEfunc::getFuncMenu(
			$pid, 'SET['.$name.']', $SETTINGS[$name], $MENU[$name]
		);
		$ret['value'] = $SETTINGS[$name];
		return $ret;
  }

  /**
   * Liefert die Ligen der aktuellen Seite.
   * @return ein Array mit Rows
   */
  private function findLeagues($pid){
    return tx_cfcleague_db::queryDB('*', 'pid="'.$pid.'"', 'tx_cfcleague_competition', '', 'sorting');
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_selector.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_selector.php']);
}

