<?php

namespace System25\T3sports\Search;

use Sys25\RnBase\Database\Query\Join;
use Sys25\RnBase\Search\SearchBase;
use Sys25\RnBase\Utility\Misc;
use System25\T3sports\Model\Match;

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
 * Class to search matches from database.
 *
 * @author Rene Nitzsche
 */
class MatchSearch extends SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping = [];
        $tableMapping['MATCH'] = 'tx_cfcleague_games';
        $tableMapping['COMPETITION'] = 'tx_cfcleague_competition';
        $tableMapping['TEAM1'] = 'TEAM1';
        $tableMapping['TEAM2'] = 'TEAM2';
        // Hook to append other tables
        Misc::callHook('cfc_league', 'search_Match_getTableMapping_hook', [
            'tableMapping' => &$tableMapping,
        ], $this);

        return $tableMapping;
    }

    protected function getBaseTable()
    {
        return 'tx_cfcleague_games';
    }

    protected function getBaseTableAlias()
    {
        return 'MATCH';
    }

    public function getWrapperClass()
    {
        return Match::class;
    }

    protected function getJoins($tableAliases)
    {
        $join = [];
        if (isset($tableAliases['COMPETITION'])) {
            $join[] = new Join('MATCH', 'tx_cfcleague_competition', 'MATCH.competition = COMPETITION.uid', 'COMPETITION');
        }
        if (isset($tableAliases['TEAM1'])) {
            $join[] = new Join('MATCH', 'tx_cfcleague_teams', 'MATCH.home = TEAM1.uid', 'TEAM1');
        }
        if (isset($tableAliases['TEAM2'])) {
            $join[] = new Join('MATCH', 'tx_cfcleague_teams', 'MATCH.guest = TEAM2.uid', 'TEAM2');
        }
        // Hook to append other tables
        Misc::callHook('cfc_league', 'search_Match_getJoins_hook', [
            'join' => &$join,
            'tableAliases' => $tableAliases,
        ], $this);

        return $join;
    }
}
