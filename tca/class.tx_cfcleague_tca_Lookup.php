<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (rene@system25.de)
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

/**
 */
class tx_cfcleague_tca_Lookup {
	/**
	 * Returns all available profile types for a TCA select item
	 *
	 * @param array $config
	 */
	function getProfileTypes($config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		$config['items'] = $srv->getProfileTypes4TCA();
	}
	function getProfileTypeItems($uids) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		return $srv->getProfileTypeItems4TCA($uids);
	}
	/**
	 * Liefert die vorhandenen MatchNote-Typen
	 * @param $config
	 * @return array
	 */
	public function getMatchNoteTypes($config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
		$config['items'] = $srv->getMatchNoteTypes4TCA();
	}
	/**
	 * Liefert die vorhandenen Liga Tabellen-Typen
	 * @param $config
	 * @return array
	 */
	public function getSportsTypes($config) {
		tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
		$config['items'] = $srv->getSports4TCA();

	}
	public function getPointSystems($config) {
		$sports = $config['row']['sports'];
		$srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
		// TODO: Fehler wegfangen
		$config['items'] = $srv->getPointSystems($sports);

//		$config['items'] = array(
//					Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2'),1),
//					Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_3'),0)
//		);
	}
	/**
	 * Set possible stadiums for a match. The stadiums are selected from home club.
	 *
	 * @param array $PA
	 * @param t3lib_TCEforms $fobj
	 */
  function getStadium4Match($PA, $fobj){
 		$current = intval($PA['row']['arena']);
 		$currentAvailable = false;
    if($PA['row']['home']) {
    	$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
    	$stadiums = $srv->getStadiums($PA['row']['home']);
    	foreach ($stadiums As $stadium) {
    		$currentAvailable = $currentAvailable ? $currentAvailable : ($current == $stadium->getUid() || $current == 0);
    		$PA['items'][] = array($stadium->getName(), $stadium->getUid());
    	}
    }
    if(!$currentAvailable) {
    	// Das aktuelle Stadium ist nicht mehr im Verein gefunden worden. Es wird daher nachgeladen
    	$stadium = tx_rnbase::makeInstance('tx_cfcleague_models_Stadium', $current);
    	if($stadium->isValid())
    		$PA['items'][] = array($stadium->getName(), $stadium->getUid());
    }
  }
	/**
	 * Set possible logos for a team. The logos are selected from club.
	 *
	 * @param array $PA
	 * @param t3lib_TCEforms $fobj
	 */
  function getLogo4Team($PA, $fobj){
    if($PA['row']['club']) {
    	$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
    	// FIXME: Wenn Teams nicht global verwaltet werden, dann kommt der Verein nicht als UID
    	// tx_cfcleague_club_1|M%C3%BCnchen%2C%20FC%20Bayern%20M%C3%BCnchen
    	$items = $srv->getLogos($PA['row']['club']);
//    	t3lib_div::debug($PA['row']['club'], 'class.tx_cfcleague_tca_Lookup.php '); // TODO: remove me
    	if(count($items)) $PA['items'] = array(); // Es muss immer eine Farbe zugeordnet werden
    	foreach ($items As $item) {
    		$currentAvailable = $currentAvailable ? $currentAvailable : ($current == $item->getUid() || $current == 0);
    		$PA['items'][] = array($item->record['title'], $item->getUid());
    	}
    }
  }
  
  static function getTeamLogoField() {
		require_once(t3lib_extMgm::extPath('dam').'tca_media_field.php');
		$ret = txdam_getMediaTCA('image_field','logo');
		$ret['label'] = 'Team Logo';
		$ret['config']['userFunc'] = 'EXT:cfc_league/tca/class.tx_cfcleague_tca_Lookup.php:&tx_cfcleague_tca_Lookup->getSingleField_teamLogo';
		$ret['config']['type'] = 'select';
		$ret['config']['itemsProcFunc'] = 'tx_cfcleague_tca_Lookup->getLogo4Team';
		$ret['config']['maxitems'] = '1';
		$ret['config']['size'] = '1';
		$ret['config']['items'] = Array(Array('','0'));
		unset($ret['config']['MM']);
		unset($ret['config']['MM_foreign_select']);
		unset($ret['config']['MM_match_fields']);
		unset($ret['config']['MM_opposite_field']);

		return $ret;
  }
	function getSingleField_teamLogo($PA, &$fObj)	{
		global $TYPO3_CONF_VARS;

		$tceforms = &$PA['pObj'];
		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];
		if(!$row['club']) 
			return $tceforms->sL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_tca_noclubselected');
		$config = $PA['fieldConf']['config'];

		$item = $tceforms->getSingleField_typeSelect($table,$field,$row,$PA);
		if($row['logo']) {
			// Logo anzeigen
			$currPic = t3lib_BEfunc::getRecord('tx_dam',$row['logo']);
			require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_tcefunc.php');
			$tcefunc = t3lib_div::makeInstance('tx_dam_tcefunc');
			if(!method_exists($tcefunc, 'renderFileList')) return $item;
			$tcefunc->tceforms = &$tceforms;
			$item .= $tcefunc->renderFileList(array('rows' => array($currPic)));
		}
		return $item;
	}

	public static function getCountryField() {
		return Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_country',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array(' ', '0'),
				),
		    'foreign_table' => 'static_countries',
				'foreign_table_where' => ' ORDER BY static_countries.cn_short_en ',
				'size' => 1,
				'default' => 54,
				'minitems' => 0,
				'maxitems' => 1,
			)
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/tca/class.tx_cfcleague_tca_Lookup.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/tca/class.tx_cfcleague_tca_Lookup.php']);
}

?>