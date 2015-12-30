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

$GLOBALS['BE_USER']->modAccess($MCONF, 1);

tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('tx_rnbase_mod_BaseModFunc');

/**
 * Die Klasse verwaltet die automatische Erstellung von Spielplänen
 */
class tx_cfcleague_mod1_modTeams extends tx_rnbase_mod_BaseModFunc {
	var $doc, $MCONF;

	/**
	 * Method getFuncId
	 *
	 * @return	string
	 */
	function getFuncId() {
		return 'functeams';
	}


	/**
	 *
	 * @return tx_cfcleague_selector
	 */
	private function getSelector() {
		if(!$this->selector) {
			$this->selector = tx_rnbase::makeInstance('tx_cfcleague_selector');
			$this->selector->init($this->doc, $this->getModule());
		}
		return $this->selector;
	}
	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
	 */
	protected function getContent($template, &$configurations, &$formatter, $formTool) {
		global $LANG;
		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		// Entweder global über die Datenbank oder die Ligen der aktuellen Seite

		$this->doc = $this->getModule()->getDoc();

		$this->formTool = $formTool;

		// Selector-Instanz bereitstellen

		// Anzeige der vorhandenen Ligen
		$selector = '';
		$saison = $this->getSelector()->showSaisonSelector($selector, $this->getModule()->getPid());
		$competitions = array();
		if($saison)
			$competitions = tx_cfcleague_util_ServiceRegistry::getCompetitionService()->getCompetitionsBySaison($saison);

		$content = '';

		if(!($saison && count($competitions))) {
			$this->getModule()->selector = $selector;
			$content.=$this->doc->section('Info:', $saison ? $LANG->getLL('msg_NoCompetitonsFound') : $LANG->getLL('msg_NoSaisonFound'), 0, 1, ICON_WARN);
			return $content;
		}

		// Anzeige der vorhandenen Ligen
		$league = $this->getSelector()->showLeagueSelector($selector, $this->getModule()->getPid(), $competitions);
		$team = $this->getSelector()->showTeamSelector($selector, $this->getModule()->getPid(), $league);
		$this->getModule()->selector = $selector;

		if(!$team){ // Kein Team gefunden
			$content.=$this->doc->section('Info:', $LANG->getLL('msg_no_team_found'), 0, 1, ICON_WARN);
			return $content;
		}
		// Wenn ein Team gefunden ist, dann können wir das Modul schreiben
		$menu = $this->selector->showTabMenu($this->getModule()->getPid(), 'teamtools',
						array('0' => $LANG->getLL('create_players'),
									'1' => $LANG->getLL('add_players'),
									'2' => $LANG->getLL('manage_teamnotes'),
						));

		$tabs .= $menu['menu'];
		$tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

		if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
			$this->pObj->tabs = $tabs;
		else
			$content .= $tabs;

		tx_rnbase::load('tx_cfcleague_util_TeamInfo');
		$teamInfo = new tx_cfcleague_util_TeamInfo($team, $this->formTool);
		$content .= $teamInfo->handleRequest();

		switch($menu['value']) {
			case 0:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_modTeamsProfileCreate');
				$content .= $mod->handleRequest($this->getModule(), $team, $teamInfo);
				break;
			case 1:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_subAddProfiles');
				$content .= $mod->handleRequest($this->getModule(), $team, $teamInfo);
				break;
			case 2:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_subTeamNotes');
				$content .= $mod->handleRequest($this->getModule(), $team, $teamInfo);
				break;
		}
		$content .= $this->formTool->form->printNeededJSFunctions_top();
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