<?php
use Sys25\RnBase\Database\Query\Join;
use Sys25\RnBase\Search\SearchBase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2020 Rene Nitzsche
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
 * Class to search comptitions from database.
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_Competition extends SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping = [];
        $tableMapping['TEAM'] = 'tx_cfcleague_teams';
        $tableMapping['COMPETITION'] = 'tx_cfcleague_competition';
        $tableMapping['MATCH'] = 'tx_cfcleague_games';

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_Competition_getTableMapping_hook', [
            'tableMapping' => &$tableMapping,
        ], $this);

        return $tableMapping;
    }

    protected function getBaseTable()
    {
        return 'tx_cfcleague_competition';
    }

    public function getWrapperClass()
    {
        return 'tx_cfcleague_models_Competition';
    }

    protected function getBaseTableAlias()
    {
        return 'COMPETITION';
    }

    protected function useAlias()
    {
        return true;
    }

    protected function getJoins($tableAliases)
    {
        $join = '';
        $join = [];
        if (isset($tableAliases['TEAM'])) {
            $join[] = new Join('COMPETITION','tx_cfcleague_teams', 'FIND_IN_SET( TEAM.uid, COMPETITION.teams )', 'TEAM');
        }
        if (isset($tableAliases['MATCH'])) {
            $join[] = new Join('COMPETITION','tx_cfcleague_games', 'MATCH.competition = COMPETITION.uid', 'MATCH');
        }

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_Competition_getJoins_hook', [
            'join' => &$join,
            'tableAliases' => $tableAliases,
        ], $this);

        return $join;
    }
}
