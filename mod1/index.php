<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche <rene@system25.de>
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


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);

require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

// Dort steckt die ModulDef-Klasse drin
include_once('../class.tx_cfcleague.php');
// Die Datenbank-Klasse
include_once('../class.tx_cfcleague_db.php');

  $LEAGUE_FUNC = array();
//  $LEAGUE_FUNC[] = new tx_cfcleague_mod_def('tx_cfcleague_match_edit', 'LLL:EXT:cfc_league/mod1/locallang.xml:edit_games');
//  $LEAGUE_FUNC[] = new tx_cfcleague_mod_def('tx_cfcleague_generator', 'LLL:EXT:cfc_league/mod1/locallang.xml:create_games');
//  $LEAGUE_FUNC[] = new tx_cfcleague_mod_def('tx_cfcleague_match_ticker', 'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker');
//  $LEAGUE_FUNC[] = new tx_cfcleague_mod_def('tx_cfcleague_profile_create', 'LLL:EXT:cfc_league/mod1/locallang.xml:create_players');
//  $LEAGUE_FUNC[] = new tx_cfcleague_mod_def('tx_cfcleague_profile_search', 'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles');

//$LEAGUE_FUNC[] = new tx_cfcleague_mod_def('tx_cfcleague_generator', 'create_games');


//require_once (PATH_t3lib.'class.tslib_content.php');

$LANG->includeLLFile('EXT:cfc_league/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

include_once('../class.tx_cfcleague_league.php');
include_once('../class.tx_cfcleague_match.php');
include_once('../class.tx_cfcleague_team.php');
include_once('../class.tx_cfcleague_saison.php');
include_once('class.tx_cfcleague_selector.php');


/**
 * Module 'League Management' for the 'cfc_league' extension.
 *
 * @author	Rene Nitzsche <rene@system25.de>
 * @package	TYPO3
 * @subpackage	tx_cfcleague
 */
class  tx_cfcleague_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $subselector;
	var $tabs;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig() {
		global $LANG, $LEAGUE_FUNC;

		$this->MOD_MENU = Array ('function' => Array ());
		// Menu aus den definierten Plugins erstellen
		foreach($LEAGUE_FUNC As $id => $func) {
			$this->MOD_MENU['function'][$id] = $LANG->sl($func->label);
		}

		parent::menuConfig();
	}

	/**
	 * Check TYPO3 Version >= 4.2.0
	 *
	 * @return boolean
	 */
	function isTYPO42() {
		return t3lib_div::int_from_ver(TYPO3_version) >= 4002000;
	}
	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{
			$this->doc = t3lib_div::makeInstance($this->isTYPO42() ? 'template' : 'bigDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->docType = 'xhtml_trans';
			$this->doc->inDocStyles = $this->getDocStyles();
			$this->doc->tableLayout = $this->getTableLayout();
			
			if($this->isTYPO42()) {
				$this->doc->setModuleTemplate('../' . t3lib_extMgm::siteRelPath('cfc_league') .  'mod1/cfc_league.html');
				$this->doc->loadJavascriptLib('contrib/prototype/prototype.js');
			}
			$this->doc->form='<form action="index.php?id=' . $this->id . '" method="POST" name="editform">';
			
			// Selector-Instanz bereitstellen
			$this->selector = t3lib_div::makeInstance('tx_cfcleague_selector');
			$this->selector->init($this->doc, $this->MCONF);

			// JavaScript
			$this->doc->JScode .= '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
				';
        
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = ' . $this->id . ';</script>';
			if($this->isTYPO42()) {
				$this->content .= $this->moduleContent(); // Muss vor der Erstellung des Headers geladen werden
				$this->content .= $this->doc->sectionEnd();  // Zur Sicherheit eine offene Section schließen
	
				$header = $this->doc->header($LANG->getLL('title'));
				$this->content = $this->content; // ??
				// ShortCut
				if ($BE_USER->mayMakeShortcut())	{
					$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				$this->content.=$this->doc->spacer(10);
				
				// Setting up the buttons and markers for docheader
				$docHeaderButtons = $this->getButtons();
				$markers['CSH'] = $docHeaderButtons['csh'];
				$markers['HEADER'] = $header;
				$markers['SELECTOR'] = $this->subselector;
				$markers['TABS'] = $this->tabs;
				$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function']);
				$markers['CONTENT'] = $this->content;
			}
			else {
				// HeaderSection zeigt Icons und Seitenpfad
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				$this->content .= $this->moduleContent(); // Muss vor der Erstellung des Headers geladen werden
				$this->content .= $this->doc->sectionEnd();  // Zur Sicherheit einen offene Section schließen
	
				// startPage erzeugt alles bis Beginn Formular
				$header.=$this->doc->startPage($LANG->getLL('title'));
				$header.=$this->doc->header($LANG->getLL('title'));
				$header.=$this->doc->spacer(5);
				$header.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$header.=$this->doc->divider(5);
	
				$this->content = $header . $this->content;
				
				// ShortCut
				if ($BE_USER->mayMakeShortcut())	{
					$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				$this->content.=$this->doc->spacer(10);
			}

		} else {
			if($this->isTYPO42()) {
				$this->content = $this->doc->section($LANG->getLL('title'), $LANG->getLL('clickAPage_content'), 0, 1);
		
					// Setting up the buttons and markers for docheader
				$docHeaderButtons = $this->getButtons();
				$markers['CSH'] = $docHeaderButtons['csh'];
				$markers['HEADER'] = $header;
				$markers['SELECTOR'] = $this->subselector;
				$markers['TABS'] = '';
				$markers['FUNC_MENU'] = '';
				$markers['CONTENT'] = $this->content;
			}
			else {
				// If no access or if ID == zero
				$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->content.=$this->doc->startPage($LANG->getLL('title'));
				$this->content.=$this->doc->header($LANG->getLL('title'));
				$this->content.=$this->doc->spacer(5);
				$this->content.=$this->doc->spacer(10);
			}
		}
		if($this->isTYPO42()) {
			$content = $this->doc->startPage($LANG->getLL('title'));
			$content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
//			$content.= $this->doc->endPage();
			$this->content = $this->doc->insertStylesAndJS($content);
		}
	}
  
	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent() {
		global $LEAGUE_FUNC;

		$content = '';
		$plugin = $LEAGUE_FUNC[$this->MOD_SETTINGS['function']];
		if($plugin){
			$clazz = $plugin->clazz_name;
			include_once('class.' . $clazz . '.php');
			$func = t3lib_div::makeInstance($clazz);
			// Plugin initialisieren
			$func->init($this->doc, $this->MCONF,$this->id);
			// die Kontrolle weitergeben
			$content=$func->handleRequest();
		}
		else {
			$content=$this->extObjContent();
		}
		return $content;
	}

	function getDocStyles() {
		if($this->isTYPO42())
			$css = '
  				html { overflow: hidden; }
  				body {
  					padding: 0;
  					margin: 0;
  					overflow: hidden;
  					height: 100%;
  				}';
		$css .= '
				.cfcleague_selector .cfcselector div {
					float:left;
					margin: 0 5px 10px 0;
				}
				.cfcleague_content .cfcselector div {
					float:left;
					margin: 5px 5px 10px 0;
				}
				.cleardiv {clear:both;}
/*
				.cfcleague_content .cfcselector {
					margin:0;
					padding:0;
					clear:both;
				}
				.cfcleague_selector1 .cfcselector dt {
					float:right;
					text-align:left;
					width1:90px;
					padding-left1:10px;
				}
				.cfcleague_content .cfcselector dt {
					float:right;
					text-align:left;
					width:90px;
					padding-left:10px;
				}
*/
				.cfcleague_content .c-headLineTable td {
					font-weight:bold;
					color:#FFF!important;
				}
			';
	  return $css;
	}
	function getTableLayout() {
		return Array (
				'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
				'0' => Array( // Format für 1. Zeile
					'tr'		=> Array('<tr class="c-headLineTable">','</tr>'),
					'defCol' => ($this->isTYPO42() ? Array('<td>','</td>') : Array('<td class="c-headLineTable" style="font-weight:bold; color:white;">','</td>'))  // Format für jede Spalte in der 1. Zeile
				),
				'defRow' => Array ( // Formate für alle Zeilen
		//          '0' => Array('<td valign="top">','</td>'), // Format für 1. Spalte in jeder Zeile
					'tr'	   => Array('<tr class="db_list_normal">', '</tr>'),
					'defCol' => Array('<td>','</td>') // Format für jede Spalte in jeder Zeile
				),
				'defRowEven' => Array ( // Formate für alle Zeilen
					'tr'	   => Array('<tr class="db_list_alt">', '</tr>'),
					'defCol' => Array(($this->isTYPO42() ?'<td>' : '<td class="db_list_alt">'),'</td>')
//				'defCol' => Array('<td>','</td>') // Format für jede Spalte in jeder Zeile
				)
			);
	}
	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return	array	all available buttons as an assoc. array
	 */
	function getButtons()	{
		global $TCA, $LANG, $BACK_PATH, $BE_USER;

		$buttons = array(
			'csh' => '',
			'view' => '',
			'record_list' => '',
			'shortcut' => '',
		);
			// CSH
		$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_txcfcleagueM1', '', $GLOBALS['BACK_PATH'], '', TRUE);

		if($this->id && is_array($this->pageinfo)) {

				// View page
			$buttons['view'] = '<a href="#" onclick="' . htmlspecialchars(t3lib_BEfunc::viewOnClick($this->pageinfo['uid'], $BACK_PATH, t3lib_BEfunc::BEgetRootLine($this->pageinfo['uid']))) . '">' .
					'<img' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/zoom.gif') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showPage', 1) . '" hspace="3" alt="" />' .
					'</a>';

				// Shortcut
			if ($BE_USER->mayMakeShortcut())	{
				$buttons['shortcut'] = $this->doc->makeShortcutIcon('id, edit_record, pointer, new_unique_uid, search_field, search_levels, showLimit', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']);
			}

				// If access to Web>List for user, then link to that module.
			if ($BE_USER->check('modules','web_list'))	{
				$href = $BACK_PATH . 'db_list.php?id=' . $this->pageinfo['uid'] . '&returnUrl=' . rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
				$buttons['record_list'] = '<a href="' . htmlspecialchars($href) . '">' .
						'<img' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/list.gif') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showList', 1) . '" alt="" />' .
						'</a>';
			}
		}

		return $buttons;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_cfcleague_module1');
$SOBE->init();

foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);
$SOBE->checkExtObj();

$SOBE->main();
$SOBE->printContent();

?>