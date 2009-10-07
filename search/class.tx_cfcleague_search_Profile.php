<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006-2008 Rene Nitzsche
 *  Contact: rene@system25.de
 *  All rights reserved
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ***************************************************************/

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

tx_div::load('tx_rnbase_util_SearchBase');
tx_div::load('tx_rnbase_util_Misc');


/**
 * Class to search matches from database
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_Profile extends tx_rnbase_util_SearchBase {

	protected function getTableMappings() {
		$tableMapping['PROFILE'] = 'tx_cfcleague_profiles';
		$tableMapping['TEAM'] = 'tx_cfcleague_teams';
		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league','search_Profile_getTableMapping_hook',
			array('tableMapping' => &$tableMapping), $this);
		return $tableMapping;
	}

	protected function getBaseTable() {
		return 'tx_cfcleague_profiles';
	}
	function getWrapperClass() {
		return 'tx_cfcleague_models_Profile';
	}

	protected function getJoins($tableAliases) {
		$join = '';
		if(isset($tableAliases['TEAM'])) {
			$join .= ' JOIN tx_cfcleague_teams ON FIND_IN_SET(tx_cfcleague_profiles.uid, tx_cfcleague_teams.players) ';
		}

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league','search_Profile_getJoins_hook',
			array('join' => &$join, 'tableAliases' => $tableAliases), $this);
		return $join;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_Profile.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_Profile.php']);
}

?>