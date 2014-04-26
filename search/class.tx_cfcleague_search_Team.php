<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006-2009 Rene Nitzsche
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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_rnbase_util_SearchBase');
tx_rnbase::load('tx_rnbase_util_Misc');


/**
 * Class to search teams from database
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_Team extends tx_rnbase_util_SearchBase {

	protected function getTableMappings() {
		$tableMapping['TEAM'] = 'tx_cfcleague_teams';
		$tableMapping['COMPETITION'] = 'tx_cfcleague_competition';

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league', 'search_Team_getTableMapping_hook',
			array('tableMapping' => &$tableMapping), $this);
		return $tableMapping;
	}

	protected function getBaseTable() {
		return 'tx_cfcleague_teams';
	}
	function getWrapperClass() {
		return 'tx_cfcleague_models_Team';
	}

	protected function getJoins($tableAliases) {
		$join = '';
		if(isset($tableAliases['COMPETITION'])) {
			$join .= ' JOIN tx_cfcleague_competition AS COMPETITION ON FIND_IN_SET( TEAM.uid, COMPETITION.teams )';
		}

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league', 'search_Team_getJoins_hook',
			array('join' => &$join, 'tableAliases' => $tableAliases), $this);
		return $join;
	}
	protected function useAlias() {
		return true;
	}
	protected function getBaseTableAlias() {
		return 'TEAM';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_Team.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_Team.php']);
}

?>