<?php

namespace System25\T3sports\Search;

use Sys25\RnBase\Database\Query\Join;
use Sys25\RnBase\Search\SearchBase;
use Sys25\RnBase\Utility\Misc;
use System25\T3sports\Model\Profile;

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
class ProfileSearch extends SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping = [
            'PROFILE' => 'tx_cfcleague_profiles',
            'TEAM' => 'tx_cfcleague_teams',
            'TEAMNOTE' => 'tx_cfcleague_team_notes',
        ];
        // Hook to append other tables
        Misc::callHook('cfc_league', 'search_Profile_getTableMapping_hook', [
            'tableMapping' => &$tableMapping,
        ], $this);

        return $tableMapping;
    }

    protected function getBaseTable()
    {
        return 'tx_cfcleague_profiles';
    }

    protected function getBaseTableAlias()
    {
        return 'PROFILE';
    }

    public function getWrapperClass()
    {
        return Profile::class;
    }

    protected function getJoins($tableAliases)
    {
        $join = [];
        if (isset($tableAliases['TEAM'])) {
            $join[] = new Join('PROFILE', 'tx_cfcleague_teams', 'FIND_IN_SET(PROFILE.uid, TEAM.players)', 'TEAM');
        }
        if (isset($tableAliases['TEAMNOTE'])) {
            $join[] = new Join('PROFILE', 'tx_cfcleague_team_notes', 'PROFILE.uid = TEAMNOTE.player', 'TEAMNOTE');
        }

        // Hook to append other tables
        Misc::callHook('cfc_league', 'search_Profile_getJoins_hook', [
            'join' => &$join,
            'tableAliases' => $tableAliases,
        ], $this);

        return $join;
    }
}
