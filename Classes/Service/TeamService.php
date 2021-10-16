<?php

namespace System25\T3sports\Service;

use Sys25\RnBase\Cache\CacheManager;
use Sys25\RnBase\Domain\Model\MediaModel;
use Sys25\RnBase\Search\SearchBase;
use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use Sys25\RnBase\Utility\TSFAL;
use System25\T3sports\Model\Club;
use System25\T3sports\Model\Competition;
use System25\T3sports\Model\Group;
use System25\T3sports\Model\Repository\TeamRepository;
use System25\T3sports\Model\Stadium;
use System25\T3sports\Model\Team;
use System25\T3sports\Model\TeamNote;
use System25\T3sports\Model\TeamNoteType;
use System25\T3sports\Search\ClubSearch;
use System25\T3sports\Search\SearchBuilder;
use System25\T3sports\Search\TeamNoteSearch;
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
 * Service for accessing teams.
 *
 * @author Rene Nitzsche
 */
class TeamService extends AbstractService
{
    private $repo;

    public function __construct(TeamRepository $repo = null)
    {
        $this->repo = $repo ?: new TeamRepository();
    }

    /**
     * Returns all stadiums for a team.
     * This works only if a club is referenced by this team.
     *
     * @param int $teamUid
     *
     * @return Stadium[]
     */
    public function getStadiums($teamUid)
    {
        $fields = $options = [];
        $fields['TEAM.UID'][OP_EQ_INT] = $teamUid;
        $options['orderby']['STADIUM.NAME'] = 'asc';
        $srv = ServiceRegistry::getStadiumService();

        return $srv->search($fields, $options);
    }

    /**
     * Returns a team.
     *
     * @param int $teamUid
     *
     * @return Team
     */
    public function getTeam($teamUid)
    {
        if (!$teamUid) {
            return false;
        }

        return $this->repo->findByUid($teamUid);
    }

    /**
     * Find all logos for a club.
     *
     * @return MediaModel[]
     */
    public function getLogos($clubUid)
    {
        return TSFAL::fetchFiles('tx_cfcleague_club', $clubUid, 'logo');
    }

    /**
     * Returns all team note types.
     *
     * @return TeamNoteType[]
     */
    public function getNoteTypes()
    {
        return TeamNoteType::getAll();
    }

    /**
     * Returns all teamnotes for with team with specific type.
     *
     * @param Team $team
     * @param TeamNoteType $type
     */
    public function getTeamNotes(Team $team, $type = false)
    {
        $fields = $options = [];
        $fields['TEAMNOTE.TEAM'][OP_EQ_INT] = $team->getUid();
        if (is_object($type)) {
            $fields['TEAMNOTE.TYPE'][OP_EQ_INT] = $type->getUid();
        }
        $options = [];

        return $this->searchTeamNotes($fields, $options);
    }

    /**
     * Liefert die Namen der zugeordneten Teams als Array.
     * Key ist die ID des Teams.
     *
     * @param Competition $comp
     * @param bool $asArray Wenn 1 wird pro Team ein Array mit Name, Kurzname und Flag spielfrei geliefert
     *
     * @return array
     */
    public function getTeamNames(Competition $comp, $asArray = false)
    {
        $teamNames = [];
        // Ohne zugeordnete Team, muss nicht gefragt werden
        if (!$comp->getProperty('teams')) {
            return $teamNames;
        }

        $fields = [];
        $fields['TEAM.UID'][OP_IN_INT] = $comp->getProperty('teams');
        $options = [];
        $options['what'] = 'uid,name,short_name,dummy,club';
        $rows = $this->searchTeams($fields, $options);
        foreach ($rows as $row) {
            $teamNames[$row['uid']] = $asArray ? $row : $row['name'];
        }

        return $teamNames;
    }

    /**
     * Returns the teams age group.
     * This value is retrieved from the teams competitions. So
     * the first competition found, decides about the age group.
     *
     * @param Team $team
     *
     * @return Group|null
     */
    public function getAgeGroup(Team $team)
    {
        if (!is_object($team) || !$team->isValid()) {
            return null;
        }

        $cache = CacheManager::getCache('t3sports');
        $agegroup = $cache->get('team_'.$team->getUid());
        if (!$agegroup) {
            if (intval($team->getProperty('agegroup'))) {
                $agegroup = Group::getGroupInstance($team->getProperty('agegroup'));
            }
            if (!$agegroup) {
                $comps = $this->getCompetitions4Team($team, true);
                for ($i = 0, $cnt = count($comps); $i < $cnt; ++$i) {
                    if (is_object($comps[$i]->getGroup())) {
                        $agegroup = $comps[$i]->getGroup();

                        break;
                    }
                }
            }
            $cache->set('team_'.$team->getUid(), $agegroup, 600); // 10 minutes
        }

        return $agegroup;
    }

    /**
     * Returns the competitons of this team.
     *
     * @param
     *            tx_cfcleague_models_Team
     * @param bool $obligateOnly
     *            if true, only obligate competitions are returned
     *
     * @return Competition[]
     */
    public function getCompetitions4Team($team, $obligateOnly = false)
    {
        $fields = $options = [];
        SearchBuilder::buildCompetitionByTeam($fields, $team->getUid(), $obligateOnly);
        $srv = ServiceRegistry::getCompetitionService();

        return $srv->search($fields, $options);
    }

    /**
     * Search database for team notes.
     *
     * @param array $fields
     * @param array $options
     *
     * @return TeamNote[]
     */
    public function searchTeamNotes($fields, $options)
    {
        $searcher = SearchBase::getInstance(TeamNoteSearch::class);

        return $searcher->search($fields, $options);
    }

    /**
     * Search database for teams.
     *
     * @param array $fields
     * @param array $options
     *
     * @return Team[]
     */
    public function searchTeams($fields, $options)
    {
        return $this->repo->search($fields, $options);
    }

    /**
     * Search database for clubs.
     *
     * @param array $fields
     * @param array $options
     *
     * @return Club[]
     */
    public function searchClubs($fields, $options)
    {
        $searcher = SearchBase::getInstance(ClubSearch::class);

        return $searcher->search($fields, $options);
    }

    /**
     * Query database for all teams with this profile as player, coach or supporter.
     *
     * @param int $profileUid
     *
     * @return Team[]
     */
    public function searchTeamsByProfile($profileUid)
    {
        $fields = $options = [];
        // FIXME: Umstellen https://github.com/digedag/rn_base/issues/47
        $fields[SEARCH_FIELD_CUSTOM] = '( FIND_IN_SET('.$profileUid.', players)
				 OR FIND_IN_SET('.$profileUid.', coaches)
				 OR FIND_IN_SET('.$profileUid.', supporters) )';

        return $this->searchTeams($fields, $options);
    }
}
