<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2010 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_cfcleague_search_Builder');
tx_rnbase::load('Tx_Rnbase_Utility_Strings');

/**
 * This is a facade to build search queries for matches from database.
 */
class tx_cfcleague_util_MatchTableBuilder
{
    public $_saisonIds;

    public $_groupIds;

    public $_teamgroupIds;

    public $_compIds;

    public $_roundIds;

    public $_clubIds;

    public $_homeClubIds;

    public $_guestClubIds;

    public $_refereeIds;

    public $_teamIds;

    public $_daysPast;

    public $_daysAhead;

    public $_dateStart; // bestimmter Starttermin

    public $_dateEnd; // bestimmter Endtermin

    public $_limit; // Anzahl Spiele limitieren

    public $_orderbyDate = false;

    public $_orderbyDateDesc = false;

    public $_status;

    public $_ticker;

    public $_report;

    public $_ignoreDummy;

    public $_compTypes; // Wettbewerbstypen

    public $_compObligation; // Pflichtwettbewerbe

    public $_pidList;

    public function __construct()
    {
    }

    /**
     * This is the final call to get all search fields and options.
     *
     * @param array $fields
     * @param array $options
     */
    public function getFields(&$fields, &$options)
    {
        tx_cfcleague_search_Builder::setField($fields, 'COMPETITION.SAISON', OP_IN_INT, $this->_saisonIds);
        tx_cfcleague_search_Builder::setField($fields, 'COMPETITION.AGEGROUP', OP_INSET_INT, $this->_groupIds);
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.COMPETITION', OP_IN_INT, $this->_compIds);
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.ROUND', OP_IN_INT, $this->_roundIds);
        tx_cfcleague_search_Builder::setField($fields, 'TEAM1.CLUB', OP_IN_INT, $this->_homeClubIds);
        tx_cfcleague_search_Builder::setField($fields, 'TEAM2.CLUB', OP_IN_INT, $this->_guestClubIds);
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.REFEREE', OP_IN_INT, $this->_refereeIds);
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.ROUND', OP_LTEQ_INT, $this->_maxRound);
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.ROUND', OP_GTEQ_INT, $this->_minRound);

        $this->handleClubInternals($fields);

        tx_cfcleague_search_Builder::buildMatchByClub($fields, $this->_clubIds);
        tx_cfcleague_search_Builder::buildMatchByTeam($fields, $this->_teamIds);
        tx_cfcleague_search_Builder::buildMatchByTeamAgeGroup($fields, $this->_teamgroupIds);
        // Wird der Zeitraum eingegrenzt?
        if (intval($this->_daysPast) || intval($this->_daysAhead)) {
            // Wenn in eine Richtung eingegrenzt wird und in der anderen Richtung kein
            // Wert gesetzt wurde, dann wird dafür das aktuelle Datum verwendet.
            // Auf jeden Fall wird immer in beide Richtungen eingegrenzt
            $cal = tx_rnbase::makeInstance('tx_rnbase_util_Calendar');
            $cal->clear(CALENDAR_SECOND);
            $cal->clear(CALENDAR_HOUR);
            $cal->clear(CALENDAR_MINUTE);
            $cal->add(CALENDAR_DAY_OF_MONTH, $this->_daysPast * -1);
            $fields['MATCH.DATE'][OP_GTEQ_INT] = $cal->getTime();
            //			$where .= ' tx_cfcleague_games.date >= ' . $cal->getTime();

            $cal = tx_rnbase::makeInstance('tx_rnbase_util_Calendar');
            $cal->clear(CALENDAR_SECOND);
            $cal->clear(CALENDAR_HOUR);
            $cal->clear(CALENDAR_MINUTE);
            $cal->add(CALENDAR_DAY_OF_MONTH, $this->_daysAhead);
            $fields['MATCH.DATE'][OP_LT_INT] = $cal->getTime();
        }
        // bestimmtes Startdatum
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.DATE', OP_GTEQ_INT, $this->_dateStart);
        // bestimmtes Enddatum
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.DATE', OP_LT_INT, $this->_dateEnd);
        if ($this->_ignoreDummy) {
            tx_cfcleague_search_Builder::setField($fields, 'TEAM1.DUMMY', OP_EQ_INT, 0);
            tx_cfcleague_search_Builder::setField($fields, 'TEAM2.DUMMY', OP_EQ_INT, 0);
        }
        // Spielstatus
        tx_cfcleague_search_Builder::setField($fields, 'MATCH.STATUS', OP_IN_INT, $this->_status);
        if ($this->_ticker) {
            tx_cfcleague_search_Builder::setField($fields, 'MATCH.LINK_TICKER', OP_EQ_INT, 1);
        }
        if ($this->_report) {
            tx_cfcleague_search_Builder::setField($fields, 'MATCH.LINK_REPORT', OP_EQ_INT, 1);
        }

        tx_cfcleague_search_Builder::setField($fields, 'COMPETITION.TYPE', OP_IN_INT, $this->_compTypes);
        if (intval($this->_compObligation)) {
            if (1 == intval($this->_compObligation)) {
                $fields['COMPETITION.OBLIGATION'][OP_EQ_INT] = 1;
            } else {
                $fields['COMPETITION.OBLIGATION'][OP_NOTEQ_INT] = 1;
            }
        }

        // Match limit
        if (intval($this->_limit)) {
            $options['limit'] = intval($this->_limit);
        }
        if ($this->_orderbyDate) {
            $options['orderby']['MATCH']['DATE'] = $this->_orderbyDateDesc ? 'DESC' : 'ASC';
        }
        if ($this->_pidList) {
            $options['pidlist'] = $this->_pidList;
        }
    }

    /**
     * Set orderby date match.
     *
     * @param bool $asc true for ascending, false for descending
     */
    public function setOrderByDate($asc = true)
    {
        $this->_orderbyDate = true;
        $this->_orderbyDateDesc = $asc;
    }

    /**
     * Wenn Spiele unterschiedlicher Vereine gesucht werden, dann müssen vereinsinterne
     * Duelle ausgeschlossen werden.
     *
     * @param $fields
     */
    private function handleClubInternals(&$fields)
    {
        $homeClubs = Tx_Rnbase_Utility_Strings::intExplode(',', $this->_homeClubIds);
        $clubs = array_merge($homeClubs, Tx_Rnbase_Utility_Strings::intExplode(',', $this->_guestClubIds));
        $clubs = array_unique($clubs);
        if (count($clubs) > 1) {
            // Interne Spiele der Vereine ausschließen
            $fields[SEARCH_FIELD_CUSTOM] = 't1.club != t2.club';
        }
    }

    /**
     * Find matches by given scope array.
     *
     * @param array $scope
     */
    public function setScope($scope)
    {
        $this->setSaisons($scope['SAISON_UIDS']);
        $this->setAgeGroups($scope['GROUP_UIDS']);
        $this->setCompetitions($scope['COMP_UIDS']);
        $this->setRounds($scope['ROUND_UIDS']);
        $this->setClubs($scope['CLUB_UIDS']);
        $this->setCompetitionObligation($scope['COMP_OBLIGATION']);
        $this->setCompetitionTypes($scope['COMP_TYPES']);
        $this->setTeamAgeGroups($scope['TEAMGROUP_UIDS']);

        // Maybe we need it later...
        $this->_scopeArr = $scope;
    }

    /**
     * Search for matches of competitions with a specific age groups.
     *
     * @param string $uids
     */
    public function setAgeGroups($uids)
    {
        $this->_groupIds = $uids;
    }

    /**
     * Search for matches of teams with a specific age groups.
     *
     * @param string $uids
     */
    public function setTeamAgeGroups($uids)
    {
        $this->_teamgroupIds = $uids;
    }

    /**
     * Search for matches of specific competitions.
     *
     * @param string $uids
     */
    public function setCompetitions($uids)
    {
        $this->_compIds = $uids;
    }

    /**
     * Search for matches of specific competition rounds.
     *
     * @param string $uids
     */
    public function setRounds($uids)
    {
        $this->_roundIds = $uids;
    }

    /**
     * Search for matches up to a specific matchround (including).
     *
     * @param int $round
     */
    public function setMaxRound($round)
    {
        $this->_maxRound = $round;
    }

    /**
     * Search for matches from a specific matchround (including).
     *
     * @param int $round
     */
    public function setMinRound($round)
    {
        $this->_minRound = $round;
    }

    /**
     * Search for matches of specific clubs.
     *
     * @param string $uids
     */
    public function setClubs($uids)
    {
        $this->_clubIds = $uids;
    }

    /**
     * Returns clubs ids.
     *
     * @return string
     */
    public function getClubs()
    {
        return $this->_clubIds;
    }

    /**
     * Search for home matches of specific clubs.
     *
     * @param string $uids
     */
    public function setHomeClubs($uids)
    {
        $this->_homeClubIds = $uids;
    }

    /**
     * Search for guest matches of specific clubs.
     *
     * @param string $uids
     */
    public function setGuestClubs($uids)
    {
        $this->_guestClubIds = $uids;
    }

    /**
     * Wether or not to include dummy matches.
     *
     * @param bool $flag
     */
    public function setIgnoreDummy($flag = true)
    {
        $this->_ignoreDummy = $flag;
    }

    /**
     * Search for matches of specific saisons.
     *
     * @param string $uids
     */
    public function setSaisons($uids)
    {
        $this->_saisonIds = $uids;
    }

    /**
     * Search for matches of specific teams.
     *
     * @param string $teamUids
     */
    public function setTeams($teamUids)
    {
        $this->_teamIds = $teamUids;
    }

    /**
     * Search for matches of specific referees.
     *
     * @param string $refUids
     */
    public function setReferees($refUids)
    {
        $this->_refereeIds = $refUids;
    }

    /**
     * Grenzt den Zeitraum für den Spielplan auf genaue Termine ein.
     *
     * @param $start_date int Timestamp des Startdatums
     * @param $end_date int Timestamp des Enddatum
     */
    public function setDateRange($start_date, $end_date)
    {
        $this->_dateStart = $start_date;
        $this->_dateEnd = $end_date;
    }

    /**
     * Grenzt den Zeitraum für den Spielplan ein.
     *
     * @param $daysPast int Anzahl Tage in der Vergangenheit
     * @param $daysAhead int Anzahl Tage in der Zukunft
     */
    public function setTimeRange($daysPast = 0, $daysAhead = 0)
    {
        $this->_daysPast = $daysPast;
        $this->_daysAhead = $daysAhead;
    }

    /**
     * Limit the number of returned matches.
     *
     * @param $limit
     */
    public function setLimit($limit)
    {
        $this->_limit = $limit;
    }

    /**
     * Set the state of returned matches.
     *
     * @param $status
     */
    public function setStatus($status)
    {
        $this->_status = $status;
    }

    /**
     * Matches with live ticker only.
     *
     * @param $flag
     */
    public function setLiveTicker($flag = true)
    {
        $this->_ticker = $flag;
    }

    /**
     * Matches with report only.
     *
     * @param $flag
     */
    public function setReport($flag = true)
    {
        $this->_report = $flag;
    }

    /**
     * Whether or not matches belong to obligate competitions.
     *
     * @param $value 0 - all, 1 - obligate only, 2 - no obligates
     */
    public function setCompetitionObligation($value)
    {
        $this->_compObligation = $value;
    }

    /**
     * Whether or not matches belong to specific competitions types.
     *
     * @param $value comma separated competition types
     */
    public function setCompetitionTypes($value)
    {
        $this->_compTypes = $value;
    }

    /**
     * Findet nur Spiele von bestimmten Seiten.
     */
    public function setPidList($pidList)
    {
        $this->_pidList = $pidList;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_MatchTableBuilder.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_MatchTableBuilder.php'];
}
