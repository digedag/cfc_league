<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2018 Rene Nitzsche
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

define('COMPSRV_FIELD_COMP_NAME', 'COMP.NAME');
define('COMPSRV_FIELD_TEAM_NAME', 'TEAM.NAME');

/**
 * Class to search comptitions from database
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_Competition extends tx_rnbase_util_SearchBase
{

    protected function getTableMappings()
    {
        $tableMapping = [];
        $tableMapping['TEAM'] = 'tx_cfcleague_teams';
        $tableMapping['COMPETITION'] = 'tx_cfcleague_competition';
        $tableMapping['MATCH'] = 'tx_cfcleague_games';
        return $tableMapping;
    }

    protected function getBaseTable()
    {
        return 'tx_cfcleague_competition';
    }

    function getWrapperClass()
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
        if (isset($tableAliases['TEAM'])) {
            $join .= ' JOIN tx_cfcleague_teams AS TEAM ON FIND_IN_SET( TEAM.uid, COMPETITION.teams )';
        }
        if (isset($tableAliases['MATCH'])) {
            $join .= ' JOIN tx_cfcleague_games AS `MATCH` ON MATCH.competition = COMPETITION.uid ';
        }
        return $join;
    }
}
