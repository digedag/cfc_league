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


require_once('../class.tx_cfcleague_form_tool.php');

/**
 * Die Klasse stellt Auswahlmenus zur Verfügung
 */
class tx_cfcleague_selector{
  var $doc, $MCONF;

  /**
   * Initialisiert das Objekt mit dem Template und der Modul-Config.
   */
  function init($doc, $MCONF){
    $this->doc = $doc;
    $this->MCONF = $MCONF;
    $this->formTool = t3lib_div::makeInstance('tx_cfcleague_form_tool');
    $this->formTool->init($this->doc);
  }


  /**
   * Darstellung der Select-Box mit allen Ligen der übergebenen Seite. Es wird auf die aktuelle Liga eingestellt.
   * @return den aktuellen Wettbewerb als Objekt oder 0
   */
  function showLeagueSelector(&$content,$pid,$leagues=0){
    // Wenn vorhanden, nehmen wir die übergebenen Wettbewerbe, sonst schauen wir auf der aktuellen Seite nach
    $leagues = $leagues ? $leagues : $this->findLeagues($pid);
    $this->LEAGUE_MENU = Array (
      'league' => array()
    );
    $objLeagues = array();
		foreach($leagues as $idx=>$league){
			if(is_object($league)) {
				$objLeagues[$league->uid] = $league; // Objekt merken
				$this->LEAGUE_MENU['league'][$league->uid] = $league->record['internal_name'] ? $league->record['internal_name'] : $league->record['name'];
			}
			else
				$this->LEAGUE_MENU['league'][$league['uid']] = $league['internal_name'] ? $league['internal_name'] : $league['name'];
    } 
    $this->LEAGUE_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->LEAGUE_MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
    );

    $menu = t3lib_BEfunc::getFuncMenu(
      $pid,'SET[league]',$this->LEAGUE_SETTINGS['league'],$this->LEAGUE_MENU['league']
    );
    // In den Content einbauen
    // Zusätzlich noch einen Edit-Link setzen
    if($menu) {
      $link = $this->formTool->createEditLink('tx_cfcleague_competition', $this->LEAGUE_SETTINGS['league'],'');
      $menu .= '</td><td style="width:90px; padding-left:10px;">' . $link;
      // Jetzt noch den Cache-Link
      $menu .= ' ' . $this->formTool->createLink('&clearCache=1', $pid, '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/clear_all_cache.gif','width="11" height="12"').' title="Statistik-Cache leeren" border="0" alt="" />');
    }
    $content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));

    if(t3lib_div::_GP('clearCache') && $this->LEAGUE_SETTINGS['league']) {
      if (is_object($serviceObj = t3lib_div::makeInstanceService('memento'))) {
        // Cache löschen
        $serviceObj->clear('', $this->LEAGUE_SETTINGS['league']);
      }

    }

    // Aktuellen Wert als Liga-Objekt zurückgeben
    if(count($objLeagues))
    	return $this->LEAGUE_SETTINGS['league'] ? $objLeagues[$this->LEAGUE_SETTINGS['league']] :0;
    return $this->LEAGUE_SETTINGS['league'] ? new tx_cfcleague_league($this->LEAGUE_SETTINGS['league']) :0;
  }

  /**
   * Darstellung der Select-Box mit allen Teams des übergebenen Wettbewerbs. Es wird auf das aktuelle Team eingestellt.
   * @return die aktuelle Team als Objekt
   */
  function showTeamSelector(&$content,$pid,$league){
    if(!$league)
      return 0;

    $this->TEAM_MENU = Array (
      'team' => array()
    );

    foreach($league->getTeamNames() as $id => $team_name){
      $this->TEAM_MENU['team'][$id] = $team_name;
    }

    $this->TEAM_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->TEAM_MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
    );
    $menu = t3lib_BEfunc::getFuncMenu(
      $pid,'SET[team]',$this->TEAM_SETTINGS['team'],$this->TEAM_MENU['team']
    );
    // In den Content einbauen
    // Zusätzlich noch einen Edit-Link setzen
    $link = $this->formTool->createEditLink('tx_cfcleague_teams', $this->TEAM_SETTINGS['team']);
    if($menu)
      $menu .= '</td><td style="width:90px; padding-left:10px;">' . $link;

    $content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));

    return new tx_cfcleague_team($this->TEAM_SETTINGS['team']);
  }

	/**
	 * Darstellung der Select-Box mit allen Spielrunden des übergebenen Wettbewerbs. Es wird auf die aktuelle Runde eingestellt.
	 */
	function showRoundSelector(&$content,$pid,$league){
		$this->ROUND_MENU = Array (
			'round' => array()
		);

		$objRounds = array();
		foreach($league->getRounds() as $round){
			if(is_object($round)) {
				$objRounds[$round->uid] = $round;
				$this->ROUND_MENU['round'][$round->uid] = $round->record['name'] . ( intval($round->record['finished']) ? ' *' : '' );
			}
			else
				$this->ROUND_MENU['round'][$round['round']] = $round['round_name'] . ( intval($round['max_status']) ? ' *' : '' );
		}

		$this->ROUND_SETTINGS = t3lib_BEfunc::getModuleData(
			$this->ROUND_MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
		);
		$menu = t3lib_BEfunc::getFuncMenu(
			$pid,'SET[round]',$this->ROUND_SETTINGS['round'],$this->ROUND_MENU['round']
		);
		// In den Content einbauen
		// Spielrunden sind keine Objekt, die bearbeitet werden können
		if($menu)
			$menu .= '</td><td style="width:90px; padding-left:10px;">';
		$content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));

		return count($objRounds) ? $objRounds[$this->ROUND_SETTINGS['round']] : $this->ROUND_SETTINGS['round'];
	}

  /**
   * Darstellung der Select-Box mit allen übergebenen Spielen. Es wird auf das aktuelle Spiel eingestellt.
   * @return die aktuelle Match als Objekt
   */
  function showMatchSelector(&$content,$pid,$matches){
    $this->MATCH_MENU = Array (
      'match' => array()
    );
    foreach($matches as $idx=>$match){
      $this->MATCH_MENU['match'][$match['uid']] = $match['short_name_home'] . ' - ' . $match['short_name_guest'];
    } 
    $this->MATCH_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->MATCH_MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
    );

    $menu = t3lib_BEfunc::getFuncMenu(
      $pid,'SET[match]',$this->MATCH_SETTINGS['match'],$this->MATCH_MENU['match']
    );
    // In den Content einbauen
    // Zusätzlich noch einen Edit-Link setzen
    $link = $this->formTool->createEditLink('tx_cfcleague_games', $this->MATCH_SETTINGS['match']);
    if($menu)
      $menu .= '</td><td style="width:90px; padding-left:10px;">' . $link;
    $content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));

    // Aktuellen Wert als Match-Objekt zurückgeben
    return new tx_cfcleague_match($this->MATCH_SETTINGS['match']);

  }

  /**
   * Darstellung der Select-Box mit allen Altersgruppen in der Datenbank.
   * @return die ID der aktuellen Altersgruppe
   */
  function showGroupSelector(&$content,$pid){
    // Zuerst die Gruppen ermitteln
    $groups = tx_cfcleague_db::queryDB('uid,name','uid > 0','tx_cfcleague_group','','sorting');

    $this->GROUP_MENU = Array (
      'group' => array()
    );
    foreach($groups as $idx=>$group){
      $this->GROUP_MENU['group'][$group['uid']] = $group['name'];
    } 
    $this->GROUP_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->GROUP_MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
    );

    $menu = t3lib_BEfunc::getFuncMenu(
      $pid,'SET[group]',$this->GROUP_SETTINGS['group'],$this->GROUP_MENU['group']
    );
    // In den Content einbauen
    $content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));

    // Aktuellen Wert zurückgeben
    return $this->GROUP_SETTINGS['group'];
  }

  /**
   * Darstellung der Select-Box mit allen Saisons in der Datenbank.
   * @return die aktuelle Saison als Objekt oder 0
   */
  function showSaisonSelector(&$content,$pid){
    // Zuerst die Gruppen ermitteln
    $saisons = tx_cfcleague_db::queryDB('uid,name','uid > 0','tx_cfcleague_saison','','sorting');

    $this->SAISON_MENU = Array (
      'saison' => array()
    );
    foreach($saisons as $idx=>$saison){
      $this->SAISON_MENU['saison'][$saison['uid']] = $saison['name'];
    } 
    $this->SAISON_SETTINGS = t3lib_BEfunc::getModuleData(
      $this->SAISON_MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
    );

    $menu = t3lib_BEfunc::getFuncMenu(
      $pid,'SET[saison]',$this->SAISON_SETTINGS['saison'],$this->SAISON_MENU['saison']
    );
    // In den Content einbauen
    // Wir verzichten hier auf den Link und halten nur den Abstand ein
    if($menu)
      $menu .= '</td><td style="width:90px; padding-left:10px;">';
    $content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));

    // Aktuellen Wert als Saison-Objekt zurückgeben
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
			$MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
		);

		$out = '
		<div class="typo3-dyntabmenu-tabs">
			<table class="typo3-dyntabmenu" border="0" cellpadding="0" cellspacing="0">
			<tbody><tr>';

		foreach($entries As $key => $value) {
			//$out .= '<td class="tab" onmouseover="DTM_mouseOver(this);" onmouseout="DTM_mouseOut(this);" nowrap="nowrap">';
			$out .= '
				<td class="tab'.($SETTINGS[$name] == $key ? 'Act' : '').'" nowrap="nowrap">';
			//$out .= '<a href="#" onclick="jumpToUrl(\'index.php?&amp;id='.$pid.'&amp;SET['.$name.']='. $key .',this);\'>'.$value.'<img name="DTM-307fab8d03-1-REQ" src="clear.gif" alt="" height="10" hspace="4" width="10"></a></td>';
			$out .= '<a href="#" onclick="jumpToUrl(\'index.php?&amp;id='.$pid.'&amp;SET['.$name.']='. $key .'\',this);">'.$value.'</a></td>';

		}
		$out .= '
				</tr>
			</tbody></table></div>
		';
		$ret['menu'] = $out;
		$ret['value'] = $SETTINGS[$name];
		return $ret;
		
		// jumpToUrl('index.php?&amp;id=5&amp;SET[teamtools]='+this.options[this.selectedIndex].value,this);
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
			$MENU,t3lib_div::_GP('SET'),$this->MCONF['name'] // Das ist der Name des Moduls
		);
		$ret['menu'] = t3lib_BEfunc::getFuncMenu(
			$pid,'SET['.$name.']',$SETTINGS[$name],$MENU[$name]
		);
		$ret['value'] = $SETTINGS[$name];
		return $ret;
  }

  /**
   * Liefert die Ligen der aktuellen Seite.
   * @return ein Array mit Rows
   */
  function findLeagues($pid){
    return tx_cfcleague_db::queryDB('*','pid="'.$pid.'"','tx_cfcleague_competition','','sorting');
  }


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_selector.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_selector.php']);
}


?>
