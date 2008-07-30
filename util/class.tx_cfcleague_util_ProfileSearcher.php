<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (r.nitzsche@kuehlhaus.com)
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');




/**
 * Suche von Personen im BE
 */
class tx_cfcleague_util_ProfileSearcher {
	private $mod;
	private $data;
	private $SEARCH_SETTINGS;

	public function tx_cfcleague_util_ProfileSearcher(&$mod, $options = array()) {
		$this->init($mod, $options);
	}
	
	private function init($mod, $options) {
		$this->options = $options;
		$this->mod = $mod;
		$this->formTool = $this->mod->formTool;
		$this->resultSize = 0;
    $this->data = t3lib_div::_GP('searchdata');

    if(!isset($options['nopersist']))
			$this->SEARCH_SETTINGS = t3lib_BEfunc::getModuleData(array ('searchtermProfile' => ''),$this->data,$this->mod->MCONF['name'] );
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
    $out .= $this->formTool->createTxtInput('searchdata[searchtermCompany]', $this->SEARCH_SETTINGS['searchtermCompany'], 20);
    // Den Update-Button einfügen
    $out .= '<input type="submit" name="search" value="'.$LANG->getLL('btn_search').'" />';
    // Jetzt noch zusätzlichen JavaScriptcode für Buttons auf der Seite
    $out .= $this->formTool->getJSCode($this->mod->id);

    return $out;
	}
	public function getResultList() {
		$content = '';
    $searchterm = $this->SEARCH_SETTINGS['searchtermCompany'];
    if(strlen($searchterm)>0) {
    	$searchterm = trim($this->validateSearchString($searchterm));
    	if(strlen($searchterm)>2) {
		    $companies = $this->searchCompanies($searchterm);
		    $this->resultSize = count($companies);
		    $label = $this->resultSize . (($this->resultSize == 1) ? ' gefundene Firma' : ' gefundene Firmen');
		    $this->showCompanies($content, $label, $companies);
    	}
    	else {
    		// Suchbefriff zu kurz
		    $content .= $this->mod->doc->section('Hinweis:','Der Suchbegriff sollte mindestens drei Zeichen lang sein', 0, 1,ICON_INFO);
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

  function searchCompanies($searchterm) {
  	tx_div::load('tx_dsagbase_search_Builder');
  	$fields = array();
  	$options = array();
  	$options['orderby'] = array('COMPANY.FNAME1' => 'asc');
  	// Wir müssen dafür sorgen, daß vesteckte Firmen noch angezeigt werden.
  	// Gelöscht dürfen wir nicht anzeigen, da hier keine Weiterverarbeitung möglich ist!
  	$options['enablefieldsoff'] = 1;
  	$fields['COMPANY.DELETED'][OP_EQ_INT] = 0;
  	tx_dsagbase_search_Builder::buildCompanyFreeText($fields, $searchterm);
  	// Zusätzlich im BE nach UID (DSAG-Nummer) mit suchen
  	$fields[SEARCH_FIELD_JOINED][0]['cols'][] = 'COMPANY.UID';
  	$companySrv = tx_dsagbase_util_serviceRegistry::getCompanyService();
  	$companies = $companySrv->search($fields, $options);
  	return $companies;
  }
	
  function showCompanies(&$content, $headline, &$companies) {
  	if($companies) {
		  $out = '
		  	<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">
		  	<tr>
		  	'. (isset($this->options['checkbox']) ? '<td class="c-headLineTable"></td>' : '')  .'
		  		<td class="c-headLineTable">Name</td>
		  		<td class="c-headLineTable">Informationen</td>
		  		<td class="c-headLineTable">Aktion</td></tr>';
  		$i = 0;
  		foreach($companies As $company) {
	  		$out .= tx_dsagbase_mod1_decorator::showCompanyTR($company, $this->formTool, $this->mod->pObj->id, ($i++ % 2) ? '' : 'db_list_alt',$this->options);
	  	}
	  	$out .= '</table>';
  	}
	  else
	  	$out = '-';
	  	
    $content .= $this->mod->doc->section($headline.':',$out,0,1,ICON_INFO);
  }
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_ProfileSearcher.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_ProfileSearcher.php']);
}
?>