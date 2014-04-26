<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2010 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_util_Misc');
tx_rnbase::load('tx_rnbase_util_DB');

/**
 * Search matches from competitions
 * We to it by showing to select boxes: one for competition and the other for round
 */
class tx_cfcleague_mod1_profilesearcher {
	private $mod;
	private $data;
	private $SEARCH_SETTINGS;

	public function tx_cfcleague_mod1_profilesearcher(&$mod, $options = array()) {
		$this->init($mod, $options);
	}

	/**
	 * 
	 * @param tx_rnbase_mod_IModule $mod
	 * @param array $options
	 */
	private function init($mod, $options) {
		$this->options = $options;
		$this->doc = $mod->getDoc();
		$this->pid = $mod->getPid();
		$this->options['pid'] = $mod->getPid();
		$this->formTool = $mod->getFormTool();
		$this->resultSize = 0;
		$this->data = t3lib_div::_GP('data');

		if(!isset($options['nopersist']))
			$this->SEARCH_SETTINGS = t3lib_BEfunc::getModuleData(array ('searchterm' => ''), $this->data, $mod->getName() );
		else
			$this->SEARCH_SETTINGS = $this->data;
	}
	/**
	 * Liefert das Suchformular. Hier die beiden Selectboxen anzeigen
	 *
	 * @param string $label Alternatives Label
	 * @return string
	 */
	public function getSearchForm($label = '') {
		$out = '';
		$out .= $GLOBALS['LANG']->getLL('label_searchterm').': ';
		$out .= $this->getFormTool()->createTxtInput('data[searchterm]', $this->SEARCH_SETTINGS['searchterm'], 20);
		// Jetzt noch zusätzlichen JavaScriptcode für Buttons auf der Seite
//		$out .= $this->getFormTool()->getJSCode($this->id);
		// Den Update-Button einfügen
		$out .= $this->getFormTool()->createSubmit('searchProfile', $GLOBALS['LANG']->getLL('btn_search'));
		return $out;
	}
	public function getResultList() {
		$content = '';
		$searchTerm = tx_rnbase_util_Misc::validateSearchString($this->SEARCH_SETTINGS['searchterm']);
		if(!$searchTerm) {
		  return $this->doc->section($GLOBALS['LANG']->getLL('message').':', $GLOBALS['LANG']->getLL('msg_searchhelp'), 0, 1, ICON_INFO);
		}

		$profiles = $this->searchProfiles($searchTerm);
		$content .= $this->showProfiles($GLOBALS['LANG']->getLL('label_searchresults'), $profiles);
		
		return $content;
	}
	private function searchProfiles($searchterm) {
		$fields = array();
		if(strlen($searchterm)) {
			$joined['value'] = trim($searchterm);
			$joined['cols'] = array('PROFILE.LAST_NAME', 'PROFILE.FIRST_NAME', 'PROFILE.UID');
			$joined['operator'] = OP_LIKE;
			$fields[SEARCH_FIELD_JOINED][] = $joined;
		}

		$options = array();
		$options['orderby']['PROFILE.LAST_NAME'] = 'ASC';
		$options['orderby']['PROFILE.FIRST_NAME'] = 'ASC';
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		$profiles = $srv->search($fields, $options);
		$this->resultSize = count($profiles);
		return $profiles;
	}
	/**
	 * Liefert die Anzahl der gefunden Datensätze.
	 * Funktioniert natürlich erst, nachdem die Ergebnisliste abgerufen wurde.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->resultSize;		
	}

	private function showProfiles($headline, &$profiles) {
		$this->options['tablename'] = 'tx_cfcleague_profiles';
		tx_rnbase::load('tx_cfcleague_mod1_decorator');
		$decor = tx_rnbase::makeInstance('tx_cfcleague_util_ProfileDecorator', $this->formTool);
		$columns = array(
			'uid' => array(),
			'last_name' => array('decorator' => $decor, 'title' => 'label_name'),
			'birthday' => array('decorator' => $decor),
		);

		if($profiles) {
			$comp = null; // PHP ist ja sowas von erbärmlich...
			$arr = tx_cfcleague_mod1_decorator::prepareTable($profiles, $columns, $this->formTool, $this->options);
			$out .= $this->doc->table($arr[0]);
		}
		else {
	  	$out = '<p><strong>'.$GLOBALS['LANG']->getLL('msg_no_matches_in_betset').'</strong></p><br/>';
		}
		return $this->doc->section($headline.':', $out, 0, 1, ICON_INFO);
	}

	/**
	 * Returns the formTool
	 * @return tx_rnbase_util_FormTool
	 */
	private function getFormTool() {
		return $this->formTool;
	}
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_profilesearcher.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_profilesearcher.php']);
}
?>