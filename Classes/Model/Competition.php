<?php

namespace System25\T3sports\Model;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Utility\Math;
use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\Strings;
use System25\T3sports\Sports\ISports;
use System25\T3sports\Utility\ServiceRegistry;

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
 * Model für einen Wettbewerb.
 */
class Competition extends BaseModel
{
    private static $instances = [];

    /**
     * array of teams.
     */
    private $teams;

    /**
     * array of matches
     * Containes retrieved matches by state.
     */
    private $matchesByState = [];

    /**
     * array of penalties.
     */
    private $penalties;

    private $cache = [];

    public function getTableName()
    {
        return 'tx_cfcleague_competition';
    }

    public function refresh()
    {
        parent::reset();
        $this->cache = [];
    }

    public function getSaisonUid()
    {
        return $this->getProperty('saison');
    }

    /**
     * Liefert alle Spiele des Wettbewerbs mit einem bestimmten Status.
     * Der Status kann sein:
     * <ul>
     * <li> 0 - angesetzt
     * <li> 1 - läuft
     * <li> 2 - beendet
     * </ul>.
     *
     * @param int $status - 0,1,2 für alle, Hin-, Rückrunde
     * @param int $scope - 0,1,2 für alle, Hin-, Rückrunde
     *
     * @return Fixture[]
     */
    public function getMatches($status, $scope = 0)
    {
        // Sicherstellen, dass wir eine Zahl bekommen
        if (isset($status) && Math::isInteger($status)) {
            $status = (int) $status;
            // Wir laden die Spieldaten zunächst ohne die Teams
            // Um die Datenmenge in Grenzen zu halten
            $round = 0;
            $scope = (int) $scope;
            if ($scope) {
                // Feststellen wann die Hinrunde endet: Anz Teams - 1
                $round = count(Strings::intExplode(',', $this->getProperty('teams')));
                $round = ($round) ? $round - 1 : $round;
            }
            // Check if data is already cached
            if (!is_array($this->matchesByState[$status.'_'.$scope])) {
                $what = '*';
                // Die UID der Liga setzen
                $where = 'competition="'.$this->getUid().'" ';
                switch ($status) {
                    case 1:
                        $where .= ' AND status>="'.$status.'"';

                        break;
                    default:
                        $where .= ' AND status="'.$status.'"';
                }
                if ($scope && $round) {
                    switch ($scope) {
                        case 1:
                            $where .= ' AND round<="'.$round.'"';

                            break;
                        case 2:
                            $where .= ' AND round>"'.$round.'"';

                            break;
                    }
                }
                $options = [
                    'where' => $where,
                    'wrapperclass' => Fixture::class,
                ];
                // Issue 1880237: Return matches sorted by round
                $options['orderby'] = 'round, date';
                $this->matchesByState[$status.'_'.$scope] = Connection::getInstance()->doSelect($what, 'tx_cfcleague_games', $options, 0);
            }

            return $this->matchesByState[$status.'_'.$scope];
        }

        return [];
    }

    public function getName()
    {
        return $this->getProperty('name');
    }

    public function getInternalName()
    {
        $ret = $this->getProperty('internal_name');
        $ret = strlen($ret) ? $ret : $this->getProperty('short_name');
        $ret = strlen($ret) ? $ret : $this->getProperty('name');

        return $ret;
    }

    /**
     * Set matches for a state and scope.
     *
     * @param array $matchesArr
     * @param int $status
     * @param int $scope
     */
    public function setMatches($matchesArr, $status, $scope = 0)
    {
        $this->matchesByState[intval($status).'_'.intval($scope)] = is_array($matchesArr) ? $matchesArr : null;
    }

    /**
     * Whether or not this competition is type league.
     *
     * @return bool
     */
    public function isTypeLeague()
    {
        return 1 == $this->getProperty('type');
    }

    /**
     * Whether or not this competition is type league.
     *
     * @return bool
     */
    public function isTypeCup()
    {
        return 2 == $this->getProperty('type');
    }

    /**
     * Whether or not this competition is type league.
     *
     * @return bool
     */
    public function isTypeOther()
    {
        return 0 == $this->getProperty('type');
    }

    /**
     * Returns the number of match parts.
     * Default is two.
     *
     * @return int
     */
    public function getMatchParts()
    {
        $parts = intval($this->getProperty('match_parts'));

        return $parts > 0 ? $parts : 2;
    }

    /**
     * Whether or not the match result should be calculated from part results.
     *
     * @return bool
     */
    public function isAddPartResults()
    {
        return intval($this->getProperty('addparts')) > 0;
    }

    /**
     * Liefert die Anzahl der Spielrunden.
     *
     * @return int
     */
    public function getNumberOfRounds()
    {
        return count($this->getRounds());
    }

    /**
     * Liefert ein Array mit allen Spielrunden der Liga.
     *
     * @return CompetitionRound[]
     */
    public function getRounds()
    {
        if (!array_key_exists('rounds', $this->cache)) {
            $srv = ServiceRegistry::getMatchService();
            // build SQL for select
            $options = [];
            // TODO: Die vielen Spaltennamen haben historische Gründe. Da müsste bei den Clients aufgeräumt werden...
            $options['what'] = 'distinct round as uid,round AS number,round,round_name,round_name As name, max(status) As finished, max(status) As max_status';
            $options['groupby'] = 'round,round_name';
            $options['orderby']['COMPROUND.ROUND'] = 'asc';
            $options['forcewrapper'] = true;
            $fields = [];
            $fields['COMPROUND.COMPETITION'][OP_EQ_INT] = $this->getUid();
            $this->cache['rounds'] = $srv->searchMatchRound($fields, $options);
        }

        return $this->cache['rounds'];
    }

    /**
     * @deprecated MatchService::getMatches4Competition und getMatchesByRound verwenden!
     */
    public function getGames($round = '')
    {
        if ($round) {
            return $this->getMatchesByRound(round);
        }
        $srv = ServiceRegistry::getMatchService();

        return $srv->getMatches4Competition($this);
    }

    /**
     * Liefert die Spiele einer bestimmten Spielrunde.
     *
     * @param int $roundId
     *
     * @return Fixture[]
     */
    public function getMatchesByRound($roundId)
    {
        $fields = [];
        $options = [];
        $fields['MATCH.ROUND'][OP_EQ_INT] = $roundId;
        $fields['MATCH.COMPETITION'][OP_EQ_INT] = $this->getUid();
        $service = ServiceRegistry::getMatchService();
        $matches = $service->search($fields, $options);

        return $matches;
    }

    /**
     * Returns the last match number.
     *
     * @return int
     */
    public function getLastMatchNumber()
    {
        $fields = [];
        $fields['MATCH.COMPETITION'][OP_EQ_INT] = $this->getUid();
        $options = [];
        // $options['debug'] =1;
        $options['what'] = 'max(convert(match_no,signed)) AS max_no';
        $srv = ServiceRegistry::getMatchService();
        $arr = $srv->search($fields, $options);

        return count($arr) ? $arr[0]['max_no'] : 0;
    }

    /**
     * Wenn vorhanden, wird die ID des Spielfrei-Teams geliefert.
     * TODO: sollte nur boolean liefern.
     *
     * @return int ID des Spielfrei-Teams oder 0
     */
    public function hasDummyTeam()
    {
        $teams = $this->getTeamNames(1);
        foreach ($teams as $team) {
            if (1 == $team['dummy']) {
                return $team['uid'];
            }
        }

        return 0;
    }

    /**
     * Liefert ein Array mit UIDs der Dummy-Teams.
     *
     * @return array
     */
    public function getDummyTeamIds()
    {
        if (!array_key_exists('dummyteamids', $this->cache)) {
            $srv = ServiceRegistry::getCompetitionService();
            $this->cache['dummyteamids'] = $srv->getDummyTeamIds($this);
        }

        return $this->cache['dummyteamids'];
    }

    /**
     * Liefert die Namen der zugeordneten Teams als Array.
     * Key ist die ID des Teams.
     *
     * @param int $asArray
     *            Wenn 1 wird pro Team ein Array mit Name, Kurzname und Flag spielfrei geliefert
     *
     * @return array
     */
    public function getTeamNames($asArray = 0)
    {
        $key = 'teamnames'.$asArray;
        if (!array_key_exists($key, $this->cache)) {
            $srv = ServiceRegistry::getTeamService();
            $this->cache[$key] = $srv->getTeamNames($this, $asArray);
        }

        return $this->cache[$key];
    }

    /**
     * Anzahl der Spiele des/der Teams in diesem Wettbewerb.
     */
    public function getNumberOfMatches($teamIds, $status = '0,1,2')
    {
        if (!array_key_exists('numofmatches', $this->cache)) {
            $srv = ServiceRegistry::getCompetitionService();
            $this->cache['numofmatches'] = $srv->getNumberOfMatches($this, $teamIds, $status);
        }

        return $this->cache['numofmatches'];
    }

    /**
     * Liefert die Anzahl der Spielabschnitte in diesem Wettbewerb.
     *
     * @return int
     */
    public function getNumberOfMatchParts()
    {
        return intval($this->getProperty('match_parts')) ? intval($this->getProperty('match_parts')) : 2;
    }

    /**
     * Returns the age croup of this competition.
     * Since version 0.6.0 there are multiple agegroups possible. For backward compatibility this
     * method returns the first competition per default.
     *
     * @return Group
     */
    public function getGroup($all = false)
    {
        $groupIds = Strings::intExplode(',', $this->getProperty('agegroup'));
        if (!count($groupIds)) {
            return null;
        }
        if (!$all) {
            return Group::getGroupInstance($groupIds[0]);
        }
        $ret = [];
        foreach ($groupIds as $groupId) {
            $ret[] = Group::getGroupInstance($groupId);
        }

        return $ret;
    }

    /**
     * Returns the uid of first agegroup of this competition.
     *
     * @return int
     */
    public function getFirstGroupUid()
    {
        $groupIds = Strings::intExplode(',', $this->getProperty('agegroup'));

        return count($groupIds) ? $groupIds[0] : 0;
    }

    /**
     * Returns the agegroups of this competition.
     *
     * @return Group[]
     */
    public function getGroups()
    {
        $groupIds = Strings::intExplode(',', $this->getProperty('agegroup'));
        $ret = [];
        foreach ($groupIds as $groupId) {
            $ret[] = Group::getGroupInstance($groupId);
        }

        return $ret;
    }

    /**
     * Returns all team participating this competition.
     *
     * @return Team[]
     */
    public function getTeams($ignoreDummies = true)
    {
        if (!is_array($this->teams)) {
            $uids = $this->getProperty('teams');
            if (!$uids) {
                return [];
            }
            $options = [
                'where' => 'uid IN ('.$uids.') ',
            ];
            if ($ignoreDummies) {
                $options['where'] .= ' AND dummy <> 1  ';
            }
            $options['wrapperclass'] = Team::class;
            $options['orderby'] = 'sorting';
            $this->teams = Connection::getInstance()->doSelect('*', 'tx_cfcleague_teams', $options, 0);
        }

        return $this->teams;
    }

    /**
     * Returns all team ids as array.
     *
     * @return array[int]
     */
    public function getTeamIds()
    {
        return Strings::intExplode(',', $this->getProperty('teams'));
    }

    /**
     * Liefert den Generation-String für die Liga.
     */
    public function getGenerationKey()
    {
        return $this->getProperty('match_keys');
    }

    /**
     * @return string
     */
    public function getExtId()
    {
        return $this->getProperty('extid');
    }

    /**
     * Set participating teams.
     * This is usually not necessary, since getTeams()
     * makes an automatic lookup in database.
     *
     * @param array $teamsArr
     *            if $teamsArr is no array the internal array is removed
     */
    public function setTeams($teamsArr)
    {
        $this->teams = is_array($teamsArr) ? $teamsArr : null;
    }

    /**
     * Returns an instance of tx_cfcleague_models_competition.
     *
     * @param int $uid
     *
     * @return Competition or null
     */
    public static function getCompetitionInstance($uid, $record = 0)
    {
        $uid = (int) $uid;
        if (!array_key_exists($uid, self::$instances)) {
            $comp = new self(is_array($record) ? $record : $uid);
            self::$instances[$uid] = $comp->isValid() ? $comp : null;
        }

        return self::$instances[$uid];
    }

    /**
     * statische Methode, die ein Array mit Instanzen dieser Klasse liefert.
     * Es werden entweder alle oder nur bestimmte Wettkämpfe einer Saison geliefert.
     *
     * @param string $saisonUid
     *            int einzelne UID einer Saison
     * @param string $groupUid
     *            int einzelne UID einer Altersklasse
     * @param string $uids
     *            String kommaseparierte Liste von Competition-UIDs
     * @param string $compTypes
     *            String kommaseparierte Liste von Wettkampftypen (1-Liga;2-Pokal;0-Sonstige)
     *
     * @return Competition[] die gefundenen Wettkämpfe
     */
    public static function findAll($saisonUid = '', $groupUid = '', $uids = '', $compTypes = '')
    {
        if (is_string($uids) && strlen($uids) > 0) {
            $where = 'uid IN ('.$uids.')';
        } else {
            $where = '1';
        }

        if (is_numeric($saisonUid)) {
            $where .= ' AND saison = '.$saisonUid.'';
        }

        if (is_numeric($groupUid)) {
            $where .= ' AND agegroup = '.$groupUid.'';
        }

        if (strlen($compTypes)) {
            $where .= ' AND type IN ('.implode(',', Strings::intExplode(',', $compTypes)).')';
        }

        /*
         * SELECT * FROM tx_cfcleague_competition WHERE uid IN ($uid)
         */

        return Connection::getInstance()->doSelect('*', 'tx_cfcleague_competition', [
            'where' => $where,
            'orderby' => 'sorting',
            'wrapperclass' => Competition::class,
        ]);
    }

    /**
     * Liefert ein Array mit den Tabellen-Markierungen
     * arr[$position] = array(markId, comment);.
     */
    public function getTableMarks()
    {
        $str = $this->getProperty('table_marks');
        if (!$str) {
            return 0;
        }

        $ret = [];
        $arr = Strings::trimExplode('|', $str);
        foreach ($arr as $item) {
            // Jedes Item splitten
            $mark = Strings::trimExplode(';', $item);
            $positions = Strings::intExplode(',', $mark[0]);
            $comments = Strings::trimExplode(',', $mark[1]);
            // Jetzt das Ergebnisarray aufbauen
            foreach ($positions as $position) {
                $ret[$position] = [
                    $comments[0],
                    $comments[1],
                ];
            }
        }

        return $ret;
    }

    /**
     * Liefert die verhängten Strafen für Teams des Wettbewerbs.
     */
    public function getPenalties()
    {
        if (!is_array($this->penalties)) {
            // Die UID der Liga setzen
            $options = [
                'where' => 'competition="'.$this->getUid().'" ',
                'wrapperclass' => CompetitionPenalty::class,
            ];

            $this->penalties = Connection::getInstance()->
                            doSelect('*', 'tx_cfcleague_competition_penalty', $options);
        }

        return $this->penalties;
    }

    /**
     * Set penalties.
     *
     * @param array $penalties
     */
    public function setPenalties($penalties)
    {
        $this->penalties = is_array($penalties) ? $penalties : null;
    }

    /**
     * Returns the kind of sports if set.
     * Default is football.
     *
     * @return string default is 'football'
     */
    public function getSports()
    {
        return $this->getProperty('sports') ? $this->getProperty('sports') : 'football';
    }

    /**
     * @return ISports
     *
     * @deprecated use System25\T3sports\Sports\ServiceLocator
     */
    public function getSportsService()
    {
        return Misc::getService('t3sports_sports', $this->getSports());
    }
}
