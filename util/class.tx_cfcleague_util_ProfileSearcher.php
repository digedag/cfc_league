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

tx_rnbase::load('Tx_Rnbase_Utility_T3General');

/**
 * TODO: Suche von Personen im BE. Wird wohl noch nicht verwendet...
 */
class tx_cfcleague_util_ProfileSearcher {
	private $mod;
	private $data;
	private $SEARCH_SETTINGS;

	public function __construct(&$mod, $options = array()) {
		$this->init($mod, $options);
	}

	private function init($mod, $options) {
		$this->options = $options;
		$this->mod = $mod;
		$this->formTool = $this->mod->formTool;
		$this->resultSize = 0;
    $this->data = Tx_Rnbase_Utility_T3General::_GP('searchdata');

    if(!isset($options['nopersist']))
			$this->SEARCH_SETTINGS = Tx_Rnbase_Backend_Utility::getModuleData(array ('searchtermProfile' => ''), $this->data, $this->mod->MCONF['name'] );
		else
			$this->SEARCH_SETTINGS = $this->data;
	}
	/**
	 * Liefert das Suchformular
	 *
	 * @param string $label Alternatives Label
	 * @return string
	 */
	public function getSearchForm($label = '') {
    global $LANG;
    $out = '';
    $out .= (strlen($label) ? $label : $LANG->getLL('label_searchterm')).': ';
    $out .= $this->formTool->createTxtInput('searchdata[searchterm]', $this->SEARCH_SETTINGS['searchterm'], 20);
    // Den Update-Button einfügen
    $out .= '<input type="submit" name="search" value="'.$LANG->getLL('btn_search').'" />';
    // Jetzt noch zusätzlichen JavaScriptcode für Buttons auf der Seite
    $out .= $this->formTool->getJSCode($this->mod->getPid());

    return $out;
	}
	public function getResultList() {
		$content = '';
    $searchterm = $this->SEARCH_SETTINGS['searchterm'];
    if(strlen($searchterm)>0) {
    	$searchterm = trim($this->validateSearchString($searchterm));
    	if(strlen($searchterm)>2) {
		    $companies = $this->searchProfiles($searchterm);
		    $this->resultSize = count($companies);
		    $label = $this->resultSize . (($this->resultSize == 1) ? ' gefundene Person' : ' gefundene Personen');
		    $this->showCompanies($content, $label, $companies);
    	}
    	else {
    		// Suchbefriff zu kurz
    	    $content .= $this->mod->doc->section('Hinweis:', 'Der Suchbegriff sollte mindestens drei Zeichen lang sein', 0, 1, \tx_rnbase_mod_IModFunc::ICON_INFO);
    	}
    }
		return $content;
	}
	/**
	 * Liefert die Anzahl der gefunden Mitglieder.
	 * Funktioniert natürlich erst, nachdem die Ergebnisliste abgerufen wurde.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->resultSize;
	}

  function searchProfiles($searchterm) {
  	// TODO
  }


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_ProfileSearcher.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_ProfileSearcher.php']);
}
?>