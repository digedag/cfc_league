<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2014 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_cache_Manager');
/**
 * Service for accessing age groups
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Group extends t3lib_svbase {
	/**
	 * Returns a group instance by its uid.
	 * @param int $uid
	 * @return tx_cfcleague_models_Group
	 */
	public function getGroupByUid($uid) {
		$cache = tx_rnbase_cache_Manager::getCache('t3sports');
		$group = $cache->get('group_'.$uid);
		if(!$group) {
			$group = tx_rnbase::makeInstance('tx_cfcleague_models_Group', $uid);
			$cache->set('group_'.$uid, $group, 3600);
		}
  	return $group;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Group.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/services/class.tx_cfcleague_services_Group.php']);
}

?>