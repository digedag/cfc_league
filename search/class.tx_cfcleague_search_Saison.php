<?php
use Sys25\RnBase\Search\SearchBase;

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
 * Class to search saison from database.
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_search_Saison extends SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping = [];
        $tableMapping['SAISON'] = 'tx_cfcleague_stadiums';

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_Saison_getTableMapping_hook', [
            'tableMapping' => &$tableMapping,
        ], $this);

        return $tableMapping;
    }

    protected function getJoins($tableAliases)
    {
        $join = [];

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook('cfc_league', 'search_Saison_getJoins_hook', [
            'join' => &$join,
            'tableAliases' => $tableAliases,
        ], $this);

        return $join;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_util_SearchBase::useAlias()
     */
    protected function useAlias()
    {
        return true;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_util_SearchBase::getBaseTable()
     */
    protected function getBaseTable()
    {
        return 'tx_cfcleague_saison';
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_util_SearchBase::getBaseTableAlias()
     */
    protected function getBaseTableAlias()
    {
        return 'SAISON';
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_util_SearchBase::getWrapperClass()
     */
    public function getWrapperClass()
    {
        return 'tx_cfcleague_models_Saison';
    }
}
