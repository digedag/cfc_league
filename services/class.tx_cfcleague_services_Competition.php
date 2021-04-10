<?php

use Sys25\RnBase\Search\SearchBase;
use System25\T3sports\Search\CompetitionSearch;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2021 Rene Nitzsche (rene@system25.de)
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
 * Service for accessing competitions.
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Competition extends tx_cfcleague_services_Base
{
    /**
     * Returns uids of dummy teams.
     *
     * @param tx_cfcleague_models_Competition $comp
     *
     * @return array[int]
     */
    public function getDummyTeamIds($comp)
    {
        $srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
        $fields = [];
        $fields['TEAM.DUMMY'][OP_EQ_INT] = 1;
        $fields['TEAM.UID'][OP_IN_INT] = $comp->getProperty('teams');
        $options = [];
        $options['what'] = 'uid';
        // $options['debug'] = 1;
        $rows = $srv->searchTeams($fields, $options);
        $ret = [];
        foreach ($rows as $row) {
            $ret[] = $row['uid'];
        }

        return $ret;
    }

    /**
     * Anzahl der Spiele des/der Teams in diesem Wettbewerb.
     *
     * @param tx_cfcleague_models_Competition $comp
     * @param string $teamIds
     * @param string $status
     *
     * @return number
     */
    public function getNumberOfMatches($comp, $teamIds = '', $status = '0,1,2')
    {
        $what = 'count(uid) As matches';
        $from = 'tx_cfcleague_games';
        $options = [];
        $options['where'] = 'status IN('.$status.') AND ';
        if ($teamIds) {
            $options['where'] .= '( home IN('.$teamIds.') OR ';
            $options['where'] .= 'guest IN('.$teamIds.')) AND ';
        }
        $options['where'] .= 'competition = '.$comp->getUid().' ';
        $rows = Tx_Rnbase_Database_Connection::getInstance()->doSelect($what, $from, $options, 0);
        $ret = 0;
        if (count($rows)) {
            $ret = (int) $rows[0]['matches'];
        }

        return $ret;
    }

    /**
     * Search database for competitions.
     *
     * @param array $fields
     * @param array $options
     *
     * @return array of tx_cfcleague_models_Competition
     */
    public function search($fields, $options)
    {
        $searcher = SearchBase::getInstance(CompetitionSearch::class);

        return $searcher->search($fields, $options);
    }

    public function getPointSystems($sports)
    {
        $srv = tx_rnbase_util_Misc::getService('t3sports_sports', $sports);

        return $srv->getTCAPointSystems();
    }

    /**
     * Returns all available table types for a TCA select item.
     *
     * @return array
     */
    public function getSports4TCA()
    {
        $items = $types = [];

        // Jetzt schauen, ob noch weitere Sportarten per Service geliefert werden
        $baseType = 't3sports_sports';
        $services = tx_rnbase_util_Misc::lookupServices($baseType);
        foreach ($services as $subtype => $info) {
            $srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
            $types[] = [
                $srv->getTCALabel(),
                $subtype,
            ];
        }

        foreach ($types as $typedef) {
            $items[] = [
                tx_rnbase_util_Misc::translateLLL($typedef[0]),
                $typedef[1],
            ];
        }

        return $items;
    }

    /**
     * @param tx_cfcleague_models_Competition $competition
     */
    public function checkReferences($competition)
    {
        $ret = [];
        $fields = [];
        $fields['MATCH.COMPETITION'][OP_EQ_INT] = $competition->getUid();
        $result = tx_cfcleague_util_ServiceRegistry::getMatchService()->search($fields, [
            'count' => 1,
        ]);
        if ($result > 0) {
            $ret['tx_cfcleague_games'] = $result;
        }

        return $ret;
    }

    /**
     * @param tx_cfcleague_models_Saison $saison
     *
     * @return array[tx_cfcleague_models_Competition]
     */
    public function getCompetitionsBySaison(tx_cfcleague_models_Saison $saison)
    {
        $fields = [];
        $fields['COMPETITION.SAISON'][OP_EQ_INT] = $saison->getUid();

        return $this->search($fields, [
            'orderby' => [
                'COMPETITION.NAME' => 'asc',
            ],
        ]);
    }

    /**
     * Returns all available table strategies for a TCA select item.
     *
     * @return array
     */
    public function getTableStrategies4TCA()
    {
        $types = [];
        // Zuerst in der Ext_Conf die BasisTypen laden
        $types = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['tablestrategy'];

        $items = [];
        foreach ($types as $key => $data) {
            $items[] = [
                tx_rnbase_util_Misc::translateLLL($data['label']),
                $key,
            ];
        }

        return $items;
    }
}
