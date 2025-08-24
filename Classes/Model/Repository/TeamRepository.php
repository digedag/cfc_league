<?php

namespace System25\T3sports\Model\Repository;

use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use System25\T3sports\Search\TeamSearch;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017-2025 Rene Nitzsche (rene@system25.de)
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
        $fields = [];
        $fields['TEAM.CLUB'][OP_EQ_INT] = $clubUid;
        $fields['COMPETITION.SAISON'][OP_IN_INT] = $saisonIds;
        $fields['COMPETITION.AGEGROUP'][OP_IN_INT] = $agegroups;
        $options = [];
        $options['distinct'] = 1;

        return $this->search($fields, $options);

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
