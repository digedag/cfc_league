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

tx_rnbase::load('tx_rnbase_util_SearchBase');
tx_rnbase::load('tx_rnbase_util_Misc');


/**
 * Class to search match notes from database
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_MatchNote extends tx_rnbase_util_SearchBase {

	protected function getTableMappings() {
		$tableMapping['MATCHNOTE'] = 'tx_cfcleague_match_notes';
		$tableMapping['MATCH'] = 'tx_cfcleague_games';
		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league', 'search_MatchNote_getTableMapping_hook',
			array('tableMapping' => &$tableMapping), $this);
		return $tableMapping;
	}

	protected function getBaseTable() {
		return 'tx_cfcleague_match_notes';
	}
	function getWrapperClass() {
		return 'tx_cfcleague_models_MatchNote';
	}

	protected function getJoins($tableAliases) {
		$join = '';
		if(isset($tableAliases['MATCH'])) {
			$join .= ' JOIN tx_cfcleague_games ON tx_cfcleague_games.uid = tx_cfcleague_match_notes.game ';
		}

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('cfc_league', 'search_MatchNote_getJoins_hook',
			array('join' => &$join, 'tableAliases' => $tableAliases), $this);
		return $join;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_MatchNote.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/search/class.tx_cfcleague_search_MatchNote.php']);
}

?>