<?php

namespace System25\T3sports\Model\Repository;

use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use System25\T3sports\Search\TeamSearch;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017-2021 Rene Nitzsche (rene@system25.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @author Rene Nitzsche
 */
class TeamRepository extends PersistenceRepository
{
    public function getSearchClass()
    {
        return TeamSearch::class;
    }

    /**
     * Liefert die Teams dieses Vereins
     * TODO: In Service auslagern.
     *
     * @param int $clubUid
     * @param string $saisonIds commaseperated saison-uids
     * @param string $agegroups commaseperated agegroup-uids
     */
    public function findByClubAndSaison($clubUid, $saisonIds, $agegroups)
    {
        $what = 'distinct tx_cfcleague_teams.uid, tx_cfcleague_teams.comment, '.
                'tx_cfcleague_teams.name, tx_cfcleague_teams.short_name, '.
                'tx_cfcleague_teams.coaches, tx_cfcleague_teams.players, tx_cfcleague_teams.supporters, '.
                'tx_cfcleague_teams.coaches_comment, tx_cfcleague_teams.players_comment, tx_cfcleague_teams.supporters_comment, '.
                'tx_cfcleague_teams.t3images';
        $from = [
            'tx_cfcleague_teams INNER JOIN tx_cfcleague_competition c ON FIND_IN_SET(tx_cfcleague_teams.uid, c.teams) AND c.hidden=0 AND c.deleted=0 ',
            'tx_cfcleague_teams',
        ];
        $options = [];
        // FIXME: Umstellen auf SearchClass
        $options['where'] = 'tx_cfcleague_teams.club = '.$clubUid.' AND c.saison IN ('.$saisonIds.') AND c.agegroup IN ('.$agegroups.')';
        $options['wrapperclass'] = $this->getSearcher()->getWrapperClass();

        return $this->getConnection()->doSelect($what, $from, $options);

        /*
         * SELECT distinct t.uid, t.name FROM tx_cfcleague_teams
         * INNER JOIN tx_cfcleague_competition c
         * ON FIND_IN_SET(t.uid, c.teams)
         * WHERE t.club = 1
         * AND c.saison = 1
         * AND c.agegroup = 1
         */
    }
}
