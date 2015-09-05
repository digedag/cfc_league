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


/**
 * Die Klasse ermöglicht die manuelle Erstellung von Spielplänen
 */
class tx_cfcleague_mod1_handler_MatchCreator {
	/**
	 * Returns an instance
	 * @return tx_cfcleague_mod1_handler_MatchCreator
	 */
	public static function getInstance() {
		return tx_rnbase::makeInstance(get_class());
	}
	/**
	 * Neuanlage von Spielen über die TCE
	 * @param tx_rnbase_mod_IModule $mod
	 * @return string
	 */
	public function handleRequest(tx_rnbase_mod_IModule $mod) {
  	$submitted = t3lib_div::_GP('doCreateMatches');
  	if(!$submitted) return '';
		$tcaData = t3lib_div::_GP('data');

		tx_rnbase::load('tx_rnbase_util_DB');
		$tce =& tx_rnbase_util_DB::getTCEmain($tcaData);
		$tce->process_datamap();
		$content = $mod->getDoc()->section('Message:', $GLOBALS['LANG']->getLL('msg_matches_created'), 0, 1, ICON_INFO);
		return $content;
	}
	/**
	 *
	 * @param tx_cfcleague_models_Competition $competition
	 * @param tx_rnbase_mod_IModule $mod
	 */
	public function showScreen($competition, tx_rnbase_mod_IModule $mod) {
		global $LANG;
		$LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

		$items = array();
		for($i=1; $i<33; $i++)
			$items[$i] = $i .($i==1 ? ' ###LABEL_MATCH###' : ' ###LABEL_MATCHES###' );
		$menu = $mod->getFormTool()->showMenu($mod->getPid(), 'matchs3create', $mod->getName(), $items, 'index.php');
		$content .= $menu['menu'];
		$maxMatches = $menu['value'];

		$table = 'tx_cfcleague_games';
		// Jetzt 6 Boxen mit Name und Kurzname
		$arr = Array(0=>array(
			$LANG->getLL('tx_cfcleague_games.round'),
			$LANG->getLL('tx_cfcleague_games.date'),
			$LANG->getLL('tx_cfcleague_games.status'),
			$LANG->getLL('tx_cfcleague_games.home'),
			$LANG->getLL('tx_cfcleague_games.guest'))
		);

		$dataArr = array('pid' => $mod->getPid(),
			'competition' => $competition->getUid(),
			'date' => time(),
			'round' => $competition->getNumberOfRounds(),
			'round_name' => $competition->getNumberOfRounds().$LANG->getLL('createGameTable_round'),
		);


		for($i=0; $i < $maxMatches; $i++){
			$row = array();
			$dataArr['uid'] = 'NEW_'.$i;
			$row[] = $mod->getFormTool()->getTCEForm()->getSoloField($table, $dataArr, 'round') .
							$mod->getFormTool()->getTCEForm()->getSoloField($table, $dataArr, 'round_name');
			$row[] = $mod->getFormTool()->getTCEForm()->getSoloField($table, $dataArr, 'date');
			$row[] = $mod->getFormTool()->getTCEForm()->getSoloField($table, $dataArr, 'status') .
						$mod->getFormTool()->createHidden('data[tx_cfcleague_games][NEW_'.$i.'][pid]', $mod->getPid()).
						$mod->getFormTool()->createHidden('data[tx_cfcleague_games][NEW_'.$i.'][competition]', $competition->getUid());


			$row[] = $mod->getFormTool()->getTCEForm()->getSoloField($table, $dataArr, 'home');
			$row[] = $mod->getFormTool()->getTCEForm()->getSoloField($table, $dataArr, 'guest');

			//$row[] = $mod->getFormTool()->create('data[tx_cfcleague_teams][NEW'.$i.'][pid]', $mod->getPid());
			$arr[] = $row;
		}
		$content .= $mod->getDoc()->table($arr);
		$content .= $mod->getFormTool()->createSubmit('doCreateMatches', $LANG->getLL('btn_create'), $GLOBALS['LANG']->getLL('msg_create_teams'));
		return $content;
	}

	public function makeLink(tx_rnbase_mod_IModule $mod) {

	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/handler/class.tx_cfcleague_mod1_handler_MatchCreator.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/handler/class.tx_cfcleague_mod1_handler_MatchCreator.php']);
}
?>