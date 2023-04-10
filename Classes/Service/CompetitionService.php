<?php

namespace System25\T3sports\Service;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\RecordInterface;
use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use Sys25\RnBase\Utility\Misc;
use System25\T3sports\Model\Competition;
use System25\T3sports\Model\Repository\CompetitionRepository;
use System25\T3sports\Model\Saison;
use System25\T3sports\Utility\ServiceRegistry;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2023 Rene Nitzsche (rene@system25.de)
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
class CompetitionService extends AbstractService
{
    /**
     * @var CompetitionRepository
     */
    private $repo;

    public function __construct(CompetitionRepository $repo = null)
    {
        $this->repo = $repo ?: new CompetitionRepository();
    }

    /**
     * Returns uids of dummy teams.
     *
     * @param Competition $comp
     *
     * @return int[]
     */
    public function getDummyTeamIds($comp)
    {
        $srv = ServiceRegistry::getTeamService();
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
     * @param Competition $comp
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
        $rows = Connection::getInstance()->doSelect($what, $from, $options, 0);
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
     * @return \Sys25\RnBase\Domain\Collection\BaseCollection
     */
    public function search($fields, $options)
    {
        return $this->repo->search($fields, $options);
    }

    public function getPointSystems($sports)
    {
        $srv = \System25\T3sports\Utility\Misc::getSports($sports);

        return $srv->getTCAPointSystems();
    }

    /**
     * Returns all available table types for a TCA select item.
     *
     * @return array
     */
    public function getSports4TCA()
    {
        $items = [];

        // Jetzt schauen, ob noch weitere Sportarten per Service geliefert werden
        $sportsData = \System25\T3sports\Utility\Misc::lookupSports();
        foreach ($sportsData as $data) {
            $items[] = [
                Misc::translateLLL($data['sports']->getTCALabel()),
                $data['type'],
            ];
        }

        return $items;
    }

    /**
     * @param Competition $competition
     */
    public function checkReferences($competition)
    {
        $ret = [];
        $fields = [];
        $fields['MATCH.COMPETITION'][OP_EQ_INT] = $competition->getUid();
        $result = ServiceRegistry::getMatchService()->search($fields, [
            'count' => 1,
        ]);
        if ($result > 0) {
            $ret['tx_cfcleague_games'] = $result;
        }

        return $ret;
    }

    /**
     * @param Saison $saison
     *
     * @return Competition[]
     */
    public function getCompetitionsBySaison(Saison $saison)
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
                Misc::translateLLL($data['label']),
                $key,
            ];
        }

        return $items;
    }

    /**
     * Create or update model.
     *
     * @param RecordInterface $model
     */
    public function persist(RecordInterface $model)
    {
        return $this->repo->persist($model);
    }
}
