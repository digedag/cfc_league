<?php

namespace System25\T3sports\Model\Repository;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use Sys25\RnBase\Utility\Strings;
use System25\T3sports\Model\Club;
use System25\T3sports\Search\ClubSearch;

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
class ClubRepository extends PersistenceRepository
{
    public function getSearchClass()
    {
        return ClubSearch::class;
    }

    /**
     * @param array $clubUids
     * @param string $saisonUids
     * @param string $groupUids
     * @param string $compUids
     *
     * @return Club[]
     */
    public function findAllByScope($clubUids, $saisonUids = '', $groupUids = '', $compUids = '')
    {
        // FIXME: Die Felder des Clubs aus der TCA laden.
        $what = 'DISTINCT tx_cfcleague_club.uid, tx_cfcleague_club.name, tx_cfcleague_club.short_name ';
        $from = [
            '
      tx_cfcleague_club
      INNER JOIN tx_cfcleague_teams ON tx_cfcleague_club.uid = tx_cfcleague_teams.club
      INNER JOIN tx_cfcleague_competition ON FIND_IN_SET(tx_cfcleague_teams.uid, tx_cfcleague_competition.teams)',
            'tx_cfcleague_club',
        ];

        $options['wrapperclass'] = Club::class;
        $options['orderby'] = 'name';

        $saison = (strlen($saisonUids)) ? implode(Strings::intExplode(',', $saisonUids), ',') : '';
        if (strlen($saison) > 0) {
            $where .= ' tx_cfcleague_competition.saison IN ('.$saison.')';
        }

        $groups = (strlen($groupUids)) ? implode(Strings::intExplode(',', $groupUids), ',') : '';
        if (strlen($groups) > 0) {
            if (strlen($where) > 0) {
                $where .= ' AND ';
            }
            $where .= ' tx_cfcleague_competition.agegroup IN ('.$groups.')';
        }

        $comps = (strlen($compUids)) ? implode(Strings::intExplode(',', $compUids), ',') : '';
        if (strlen($comps) > 0) {
            if (strlen($where) > 0) {
                $where .= ' AND ';
            }
            $where .= ' tx_cfcleague_competition.uid IN ('.$comps.')';
        }

        $clubs = (strlen($clubUids)) ? implode(Strings::intExplode(',', $clubUids), ',') : '';
        if (strlen($clubs) > 0) {
            if (strlen($where) > 0) {
                $where .= ' AND ';
            }
            $where .= ' tx_cfcleague_club.uid IN ('.$clubs.')';
        }

        $options['where'] = (strlen($where) > 0) ? $where : '1';

        /*
         * select distinct tx_cfcleague_club.uid, tx_cfcleague_club.name
         * from tx_cfcleague_club
         * INNER JOIN tx_cfcleague_teams ON tx_cfcleague_club.uid = tx_cfcleague_teams.club
         * INNER JOIN tx_cfcleague_competition ON FIND_IN_SET(tx_cfcleague_teams.uid, tx_cfcleague_competition.teams)
         *
         * WHERE tx_cfcleague_competition.saison = 1
         * AND tx_cfcleague_competition.agegroup = 1
         */
        return Connection::getInstance()->doSelect($what, $from, $options, 0);
    }
}
