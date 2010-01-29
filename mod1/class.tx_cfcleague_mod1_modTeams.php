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

require_once (PATH_t3lib.'class.t3lib_extobjbase.php');
$GLOBALS['BE_USER']->modAccess($MCONF,1);


/**
 * Die Klasse verwaltet die automatische Erstellung von Spielplänen
 */
class tx_cfcleague_mod1_modTeams extends t3lib_extobjbase {
  var $doc, $MCONF;

  /**
   * Initialization of the class
   *
   * @param	object		Parent Object
   * @param	array		Configuration array for the extension
   * @return	void
   */
  function init(&$pObj,$conf)	{
    parent::init($pObj,$conf);
    $this->MCONF = $pObj->MCONF;
    $this->id = $pObj->id;
  }

	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
	 */
	function main() {
		global $LANG;
		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		// Entweder global über die Datenbank oder die Ligen der aktuellen Seite

		$this->doc = $this->pObj->doc;

		$this->formTool = tx_rnbase::makeInstance('tx_rnbase_util_FormTool');
		$this->formTool->init($this->doc);

		// Selector-Instanz bereitstellen
		$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$this->selector->init($this->doc, $this->MCONF);

		// Anzeige der vorhandenen Ligen
		$selector = '';
		$saison = $this->selector->showSaisonSelector($selector,$this->id);
		$content = '';

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
									'2' => $LANG->getLL('manage_teamnotes'),
						));
		
		$tabs .= $menu['menu'];
		$tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

		if($this->pObj->isTYPO42())
			$this->pObj->tabs = $tabs;
		else
			$content .= $tabs;

		tx_rnbase::load('tx_cfcleague_util_TeamInfo');
		$teamInfo = new tx_cfcleague_util_TeamInfo($team, $this->formTool);

		switch($menu['value']) {
			case 0:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_modTeamsProfileCreate');
				$content .= $mod->main($this->MCONF['name'], $this->id, $this->doc, $this->formTool, $team, $teamInfo);
				break;
			case 1:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_subAddProfiles', $this);
				$content .= $mod->handleRequest($team, $teamInfo);
				break;
			case 2:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_subTeamNotes',$this);
				$content .= $mod->handleRequest($team);
				break;
		}
		$content .= $this->formTool->form->printNeededJSFunctions_top();
		$content .= $modContent;
		// Den JS-Code für Validierung einbinden
		$content .= $this->formTool->form->printNeededJSFunctions();
//		$content  .= $this->formTool->form->JSbottom('editform');
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modTeams.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modTeams.php']);
}
?>