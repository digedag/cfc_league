<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Rene Nitzsche (rene@system25.de)
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

$GLOBALS['BE_USER']->modAccess($MCONF,1);

tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('tx_rnbase_mod_BaseModFunc');

/**
 * BaseModule to manage clubs, stadiums etc.
 */
class tx_cfcleague_mod1_modClubs extends tx_rnbase_mod_BaseModFunc {

	/**
	 * Method getFuncId
	 * 
	 * @return	string
	 */
	function getFuncId() {
		return 'funcclubs';
	}


	/**
	 * @param string $template
	 * @param tx_rnbase_configurations $configurations
	 * @param tx_rnbase_util_FormatUtil $formatter
	 * @param tx_rnbase_util_FormTool $formTool
	 */
	protected function getContent($template, &$configurations, &$formatter, $formTool) {
		global $LANG;

		$selector = t3lib_div::makeInstance('tx_cfcleague_selector');
		$selector->init($this->doc, $this->getModule()->getName());

		$selectorStr = '';
		$club = $selector->showClubSelector($selectorStr, $this->getModule()->getPid());
		if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
			$this->pObj->subselector = $selectorStr;
		else 
			$content .= '<div class="cfcleague_selector">'.$selectorStr.'</div><div class="cleardiv"/>';

		if(!$club) {
			$content .= '###LABEL_MSG_NOCLUBONPAGE###';
			return $content;
		}

		// Wenn ein Team gefunden ist, dann können wir das Modul schreiben
		$menu = $formTool->showTabMenu($this->getModule()->getPid(), 'clubtools', $this->getModule()->getName(),
						array('0' => $LANG->getLL('create_players'),
									'1' => $LANG->getLL('add_players'),
						));
		
		$tabs .= $menu['menu'];
		$tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

		if(tx_rnbase_util_TYPO3::isTYPO42OrHigher())
			$this->pObj->tabs = $tabs;
		else
			$content .= $tabs;


//		switch($menu['value']) {
//			case 0:
//				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_modTeamsProfileCreate');
//				$content .= $mod->handleRequest($this->getModule(), $team, $teamInfo);
//				break;
//			case 1:
//				$mod = tx_rnbase::makeInstance('tx_cfcleague_mod1_subAddProfiles');
//				$content .= $mod->handleRequest($this->getModule(), $team, $teamInfo);
//				break;
//		}
		$content .= $formTool->form->printNeededJSFunctions_top();
		$content .= $modContent;
		// Den JS-Code für Validierung einbinden
		$content .= $formTool->form->printNeededJSFunctions();
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modClubs.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_modClubs.php']);
}
?>