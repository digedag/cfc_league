<?php

namespace System25\T3sports\Search;

use Sys25\RnBase\Database\Query\Join;
use Sys25\RnBase\Search\SearchBase;
use tx_rnbase_util_Misc;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006-2021 Rene Nitzsche
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

/**
 * Class to search team notes from database.
 *
 * @author Rene Nitzsche
 */
class TeamNoteSearch extends SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping = [];
        $tableMapping['TEAMNOTE'] = 'tx_cfcleague_team_notes';
        $tableMapping['NOTETYPE'] = 'tx_cfcleague_note_types';
        $tableMapping['TEAM'] = 'tx_cfcleague_teams';
        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_TeamNote_getTableMapping_hook', [
            'tableMapping' => &$tableMapping,
        ], $this);

        return $tableMapping;
    }

    protected function getBaseTable()
    {
        return 'tx_cfcleague_team_notes';
    }

    protected function getBaseTableAlias()
    {
        return 'TEAMNOTE';
    }

    protected function useAlias()
    {
        return true;
    }

    public function getWrapperClass()
    {
        return 'tx_cfcleague_models_TeamNote';
    }

    protected function getJoins($tableAliases)
    {
        $join = '';
        if (isset($tableAliases['TEAM'])) {
            $join[] = new Join('TEAMNOTE', 'tx_cfcleague_teams', 'TEAM.uid = TEAMNOTE.team', 'TEAM');
        }
        if (isset($tableAliases['NOTETYPE'])) {
            $join[] = new Join('TEAMNOTE', 'tx_cfcleague_note_types', 'NOTETYPE.uid = TEAMNOTE.type', 'NOTETYPE');
        }

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_TeamNote_getJoins_hook', [
            'join' => &$join,
            'tableAliases' => $tableAliases,
        ], $this);

        return $join;
    }
}
