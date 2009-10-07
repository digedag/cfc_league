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

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

require_once(t3lib_extMgm::extPath('rn_base') . 'util/class.tx_rnbase_util_Misc.php');

/**
 */
class tx_cfcleague_tca_Lookup {
	/**
	 * Returns all available profile types for a TCA select item
	 *
	 * @param array $config
	 */
	function getProfileTypes($config) {
		tx_div::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		$config['items'] = $srv->getProfileTypes4TCA();
	}
	function getProfileTypeItems($uids) {
		tx_div::load('tx_cfcleague_util_ServiceRegistry');
		$srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
		return $srv->getProfileTypeItems4TCA($uids);
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
    	$clazz = tx_div::makeInstanceClassname('tx_cfcleague_models_Stadium');
    	$stadium = new $clazz($current);
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
    	$items = $srv->getLogos($PA['row']['club']);
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
		unset($ret['config']['form_type']);
		$ret['config']['type'] = 'select';
		$ret['config']['itemsProcFunc'] = 'tx_cfcleague_tca_Lookup->getLogo4Team';
		$ret['config']['maxitems'] = '1';
		$ret['config']['size'] = '1';
		$ret['config']['items'] = Array(Array('','0'));
		
//		t3lib_div::debug($ret['config']['form_type'], 'tx_cfcleague_tca_Lookup :: getTeamLogoField'); // TODO: remove me
  	return $ret;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/tca/class.tx_cfcleague_tca_Lookup.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/tca/class.tx_cfcleague_tca_Lookup.php']);
}

?>