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

require_once (PATH_t3lib.'class.t3lib_extobjbase.php');
$GLOBALS['BE_USER']->modAccess($MCONF,1);


/**
 * Die Klasse verwaltet die automatische Erstellung von Spielplänen
 */
class tx_cfcleague_mod1_modCompetitions extends t3lib_extobjbase {
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

		$this->formTool = tx_div::makeInstance('tx_rnbase_util_FormTool');
		$this->formTool->init($this->doc);

		// Selector-Instanz bereitstellen
		$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$this->selector->init($this->doc, $this->MCONF);

		// Anzeige der vorhandenen Ligen
		$selector = '';
		$current_league = $this->selector->showLeagueSelector($selector,$this->id);
		$content = '';

		if($this->pObj->isTYPO42())
			$this->pObj->subselector = $selector;
		else 
			$content .= '<div class="cfcleague_selector">'.$selector.'</div><div class="cleardiv"/>';

		if(!$current_league) {
			$content.=$this->doc->section('Info:',$LANG->getLL('no_league_in_page'),0,1,ICON_WARN);
			return $content;
		}

		$menu = $this->selector->showTabMenu($this->id, 'comptools', 
			array('0' => $LANG->getLL('edit_games'),
						'1' => $LANG->getLL('mod_compteams'),
						'2' => $LANG->getLL('create_games')));

		$tabs .= $menu['menu'];
		$tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

		if($this->pObj->isTYPO42())
			$this->pObj->tabs = $tabs;
		else
			$content .= $tabs;

		switch($menu['value']) {
			case 0:
				$modContent = $this->showEditMatches($current_league);
				break;
			case 1:
				$mod = tx_div::makeInstance('tx_cfcleague_mod1_modCompTeams');
				$modContent = $mod->main($this->MCONF['name'], $this->id, $this->doc, $this->formTool, $current_league);
				break;
			case 2:
				$modContent = $this->showCreateMatchTable($current_league);
				break;
		}
		$content .= $this->formTool->form->printNeededJSFunctions_top();
		$content .= $modContent;
		// Den JS-Code für Validierung einbinden
		$content .= $this->formTool->form->printNeededJSFunctions();
//		$content  .= $this->formTool->form->JSbottom('editform');
		return $content;
	}
	private function showEditMatches($current_league) {
		require_once(t3lib_extMgm::extPath('cfc_league') . 'mod1/class.tx_cfcleague_match_edit.php');
		$subMod = t3lib_div::makeInstance('tx_cfcleague_match_edit');
		$content = $subMod->main($this->MCONF, $this->id, $this->doc, $this->formTool, $current_league);
		return $content;
  }
  
  private function showCreateMatchTable(&$current_league) {
		require_once(t3lib_extMgm::extPath('cfc_league') . 'mod1/class.tx_cfcleague_generator.php');
  	$subMod = t3lib_div::makeInstance('tx_cfcleague_generator');
  	$content = $subMod->main($this->MCONF, $this->id, $this->doc, $this->formTool, $current_league);
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompetitions.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modCompetitions.php']);
}
?>