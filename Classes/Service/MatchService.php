<?php

namespace System25\T3sports\Service;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\RecordInterface;
use Sys25\RnBase\Search\SearchBase;
use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use System25\T3sports\Model\Competition;
use System25\T3sports\Model\CompetitionRound;
use System25\T3sports\Model\Match;
use System25\T3sports\Model\MatchNote;
use System25\T3sports\Model\Repository\MatchNoteRepository;
use System25\T3sports\Model\Repository\MatchRepository;
use System25\T3sports\Search\CompetitionRoundSearch;
use System25\T3sports\Utility\MatchTableBuilder;
use tx_rnbase;
use tx_rnbase_util_Misc as Misc;

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
 * Service for accessing match information.
 *
 * @author Rene Nitzsche
 */
class MatchService extends AbstractService
{
    private $repo;
    private $mnRepo;

    public function __construct(MatchRepository $repo = null, MatchNoteRepository $mnRepo = null)
    {
        $this->repo = $repo ?: new MatchRepository();
        $this->mnRepo = $mnRepo ?: new MatchNoteRepository();
    }

    /**
     * Returns all available profile types for a TCA select item.
     *
     * @return array
     */
    public function getMatchNoteTypes4TCA()
    {
        $types = [];
        // Zuerst in der Ext_Conf die BasisTypen laden
        $types = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'];

        // Jetzt schauen, ob noch weitere Typpen per Service geliefert werden
        $baseType = 't3sports_matchnotetype';
        $services = Misc::lookupServices($baseType);
        foreach ($services as $subtype => $info) {
            $srv = Misc::getService($baseType, $subtype);
            $types = array_merge($types, $srv->getMatchNoteTypes());
        }
        $items = [];
        foreach ($types as $typedef) {
            $items[] = [
                Misc::translateLLL($typedef[0]),
                $typedef[1],
            ];
        }

        return $items;
    }

    /**
     * Spiele des/der Teams in einem Wettbewerb.
     *
     * @param Competition $comp
     * @param string $teamIds
     * @param string $status
     *
     * @return Match[]
     */
    public function getMatches4Competition($comp, $teamIds = '', $status = '0,1,2')
    {
        $fields = $options = [];
        // $options['debug'] = 1;
        $builder = $this->getMatchTableBuilder();
        $builder->setCompetitions($comp->getUid());
        $builder->setStatus($status);
        $builder->setTeams($teamIds);
        $builder->getFields($fields, $options);

        $matches = $this->search($fields, $options);

        return $matches;
    }

    /**
     * @return MatchTableBuilder
     */
    public function getMatchTableBuilder()
    {
        return tx_rnbase::makeInstance(MatchTableBuilder::class);
    }

    /**
     * Search database for matches.
     *
     * @param array $fields
     * @param array $options
     *
     * @return array of tx_cfcleague_models_Match
     */
    public function search($fields, $options)
    {
        return $this->repo->search($fields, $options);
    }

    /**
     * Search database for matches.
     *
     * @param array $fields
     * @param array $options
     *
     * @return array of tx_cfcleague_models_Match
     */
    public function searchMatchNotes($fields, $options)
    {
        return $this->mnRepo->search($fields, $options);
    }

    /**
     * Query database for all match notes of a profile.
     *
     * @param int $profileUid
     *
     * @return MatchNote[]
     */
    public function searchMatchNotesByProfile($profileUid)
    {
        $fields = $options = [];
        // FIXME: Umstellen https://github.com/digedag/rn_base/issues/47
        $fields[SEARCH_FIELD_CUSTOM] = '( FIND_IN_SET('.$profileUid.', player_home)
				 OR FIND_IN_SET('.$profileUid.', player_guest) )';

        return $this->searchMatchNotes($fields, $options);
    }

    public function searchMatchesByProfile($profileUid)
    {
        $where = 'FIND_IN_SET('.$profileUid.', referee) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', assists) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', coach_home) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', coach_guest) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', players_home) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', players_guest) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', substitutes_home) ';
        $where .= ' OR FIND_IN_SET('.$profileUid.', substitutes_guest) ';

        $fields = $options = [];
        // FIXME: Umstellen https://github.com/digedag/rn_base/issues/47
        $fields[SEARCH_FIELD_CUSTOM] = '( '.$where.' )';

        return $this->search($fields, $options);
    }

    /**
     * Search database for matches.
     *
     * @param array $fields
     * @param array $options
     *
     * @return CompetitionRound
     */
    public function searchMatchRound($fields, $options)
    {
        $searcher = SearchBase::getInstance(CompetitionRoundSearch::class);

        return $searcher->search($fields, $options);
    }

    /**
     * Diese Funktion ermittelt die Spiele eines Spieltags.
     * Die Namen der Teams werden aufgelöst.
     *
     * @param Competition $competition
     * @param int $round
     * @param bool $ignoreFreeOfPlay
     *
     * @return array plain
     */
    public function searchMatchesByRound($competition, $round, $ignoreFreeOfPlay = false)
    {
        $what = 'tx_cfcleague_games.uid,home,guest, t1.name AS name_home, t2.name AS name_guest, '.
            't1.short_name AS short_name_home, t1.dummy AS no_match_home, t2.short_name AS short_name_guest, t2.dummy AS no_match_guest, '.'goals_home_1,goals_guest_1,goals_home_2,goals_guest_2, '.'goals_home_3,goals_guest_3,goals_home_4,goals_guest_4, '.'goals_home_et,goals_guest_et,goals_home_ap,goals_guest_ap, visitors,date,status';
        $from = [
            'tx_cfcleague_games INNER JOIN tx_cfcleague_teams t1 ON (home= t1.uid) INNER JOIN tx_cfcleague_teams t2 ON (guest= t2.uid) ',
            'tx_cfcleague_games',
        ];

        $where = 'competition="'.$competition->getUid().'"';
        $where .= ' AND round='.intval($round);
        if ($ignoreFreeOfPlay) { // keine spielfreien Spiele laden
            $where .= ' AND t1.dummy <> 1 AND t2.dummy <> 1 ';
        }

        return Connection::getInstance()->doSelect($what, $from, [
            'where' => $where,
        ]);
    }

    /**
     * Ermittelt für das übergebene Spiel die MatchNotes.
     * Wenn $types = 1 dann
     * werden nur die Notes mit dem Typ != 100 geliefert.
     *
     * @param Match $match
     * @param bool $excludeTicker
     *
     * @return MatchNote[]
     */
    public function retrieveMatchNotes($match, $excludeTicker = true)
    {
        $options = [];
        $options['where'] = 'game = '.$match->getUid();
        if ($excludeTicker) {
            $options['where'] .= ' AND type != 100';
        }
        $options['orderby'] = 'minute asc';
        $options['wrapperclass'] = MatchNote::class;

        $matchNotes = Connection::getInstance()->doSelect('*', 'tx_cfcleague_match_notes', $options);

        return $matchNotes;
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
