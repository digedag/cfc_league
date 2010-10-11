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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_rnbase_mod_BaseModFunc');

tx_rnbase::load('tx_rnbase_util_Templates');
tx_rnbase::load('tx_rnbase_util_BaseMarker');
tx_rnbase::load('tx_rnbase_util_TYPO3');

//require_once (PATH_t3lib.'class.t3lib_extobjbase.php');
//$GLOBALS['BE_USER']->modAccess($MCONF,1);


/**
 * Die Klasse ist die Einstiegsklasse für das Modul "Wettbewerbe verwalten"
 */
class tx_cfcleague_mod1_modCompetitions extends tx_rnbase_mod_BaseModFunc {
	var $doc, $MCONF;

	/**
	 * Method getFuncId
	 * 
	 * @return	string
	 */
	function getFuncId() {
		return 'funccompetitions';
	}

  /**
   * Initialization of the class
   *
   * @param	object		Parent Object
   * @param	array		Configuration array for the extension
   * @return	void
   */
//  function init(&$pObj,$conf)	{
//    parent::init($pObj,$conf);
//    $this->MCONF = $pObj->MCONF;
//    $this->id = $pObj->id;
//  }

	/**
	 * Verwaltet die Erstellung von Spielplänen von Ligen
	 * @param 	string $template
	 * @param 	tx_rnbase_configurations $configurations
	 * @param 	tx_rnbase_util_FormatUtil $formatter
	 * @param 	tx_rnbase_util_FormTool $formTool
	 * @return 	string
	 */
	protected function getContent($template, &$configurations, &$formatter, $formTool) {
		global $LANG;
		// Zuerst mal müssen wir die passende Liga auswählen lassen:
		// Entweder global über die Datenbank oder die Ligen der aktuellen Seite

		
		// Selector-Instanz bereitstellen
		$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$this->selector->init($this->getModule()->getDoc(), $this->getModule()->getName());

		// Anzeige der vorhandenen Ligen
		$selector = '';
		$current_league = $this->selector->showLeagueSelector($selector,$this->getModule()->getPid());
		$content = '';

		if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
			$this->pObj->subselector = $selector;
		else 
			$content .= '<div class="cfcleague_selector">'.$selector.'</div><div class="cleardiv"/>';

		if(!$current_league) {
			$content.=$this->getModule()->getDoc()->section('Info:',$LANG->getLL('no_league_in_page'),0,1,ICON_WARN);
			$content .= '<p style="margin-top:5px; font-weight:bold;">'.$formTool->createNewLink('tx_cfcleague_competition', $this->pObj->id,$LANG->getLL('msg_create_new_competition')).'</p>';
			return $content;
		}

		$menu = $this->selector->showTabMenu($this->getModule()->getPid(), 'comptools', 
			array('0' => $LANG->getLL('edit_games'),
						'1' => $LANG->getLL('mod_compteams'),
//						'2' => $LANG->getLL('create_games'),
						'3' => $LANG->getLL('create_games')));

		$tabs .= $menu['menu'];
		$tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

		if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
			$this->pObj->tabs = $tabs;
		else
			$content .= $tabs;

		switch($menu['value']) {
			case 0:
				$modContent = $this->showEditMatches($current_league, $this->getModule());
				break;
			case 1:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_modCompTeams');
				$modContent = $mod->main($this->getModule(), $current_league);
				break;
			case 2:
				$modContent = $this->showCreateMatchTable($current_league, $this->getModule());
				break;
			case 3:
				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_modCompCreateMatchTable');
				$modContent = $mod->main($this->getModule(), $current_league);
				break;
		}
		$content .= $formTool->form->printNeededJSFunctions_top();
		$content .= $modContent;
		// Den JS-Code für Validierung einbinden
		$content .= $formTool->form->printNeededJSFunctions();
//		$content  .= $this->formTool->form->JSbottom('editform');
		return $content;
	}

	private function showEditMatches($current_league, $module) {
		require_once(t3lib_extMgm::extPath('cfc_league') . 'mod1/class.tx_cfcleague_match_edit.php');
		$subMod = t3lib_div::makeInstance('tx_cfcleague_match_edit');
		$content = $subMod->main($module, $current_league);
		return $content;
  }
  
  private function showCreateMatchTable(&$current_league, $module) {
		require_once(t3lib_extMgm::extPath('cfc_league') . 'mod1/class.tx_cfcleague_generator.php');
  	$subMod = t3lib_div::makeInstance('tx_cfcleague_generator');
  	$content = $subMod->main($module, $current_league);
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompetitions.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompetitions.php']);
}
?>