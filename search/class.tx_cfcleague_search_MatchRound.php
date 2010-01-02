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

define('MATCHSRV_FIELD_MATCH_COMPETITION', 'MATCH.COMPETITION');
define('MATCHSRV_FIELD_MATCH_ROUND', 'MATCH.ROUND');
define('MATCHSRV_FIELD_MATCH_DATE', 'MATCH.DATE');


/**
 * Class to search matches from database
 * 
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_MatchRound extends tx_rnbase_util_SearchBase {

	protected function getTableMappings() {
		$tableMapping['MATCHROUND'] = 'tx_cfcleague_games';
		$tableMapping['COMPETITION'] = 'tx_cfcleague_competition';

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league','search_MatchRound_getTableMapping_hook',
			array('tableMapping' => &$tableMapping), $this);
		return $tableMapping;
	}

	protected function getBaseTable() {
		return 'tx_cfcleague_games';
	}
	function getWrapperClass() {
		return 'tx_cfcleague_models_MatchRound';
	}

	protected function getJoins($tableAliases) {
		$join = '';
		if(isset($tableAliases['COMPETITION'])) {
			$join .= ' JOIN tx_cfcleague_competition ON tx_cfcleague_games.competition = tx_cfcleague_competition.uid ';
		}
		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league','search_MatchRound_getJoins_hook',
			array('join' => &$join, 'tableAliases' => $tableAliases), $this);
		return $join;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_MatchRound.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_MatchRound.php']);
}
?>