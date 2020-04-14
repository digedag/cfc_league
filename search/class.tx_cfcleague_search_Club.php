<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2018 Rene Nitzsche
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
 * Class to search clubs from database.
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_Club extends tx_rnbase_util_SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping = [];
        $tableMapping['CLUB'] = 'tx_cfcleague_club';
        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_Club_getTableMapping_hook', array(
            'tableMapping' => &$tableMapping,
        ), $this);

        return $tableMapping;
    }

    protected function getBaseTable()
    {
        return 'tx_cfcleague_club';
    }

    public function getWrapperClass()
    {
        return 'tx_cfcleague_models_Club';
    }

    protected function getJoins($tableAliases)
    {
        $join = '';

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_Club_getJoins_hook', array(
            'join' => &$join,
            'tableAliases' => $tableAliases,
        ), $this);

        return $join;
    }
}
