<?php

namespace System25\T3sports\Search;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2021 Rene Nitzsche
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
 * Mit dem Builder werden haufig auftretende Suchanfragen zusammengebaut.
 *
 * @author Rene Nitzsche
 */
class SearchBuilder
{
    /**
     * Search for competition by scope data.
     *
     * @param array $fields
     * @param string $teamUids
     *            comma separated list of team UIDs
     *
     * @return bool true if condition is set
     */
    public static function buildCompetitionByScope(&$fields, $parameters, $configurations, $saisonUids, $groupUids, $compUids)
    {
        $result = false;
        if (strlen(trim($compUids))) {
            $fields['COMPETITION.UID'][OP_IN_INT] = $compUids;
            $result = true;
        }
        if (strlen(trim($groupUids))) {
            $fields['COMPETITION.AGEGROUP'][OP_EQ_INT] = $groupUids;
            $result = true;
        }
        if (strlen(trim($saisonUids))) {
            $fields['COMPETITION.SAISON'][OP_EQ_INT] = $saisonUids;
            $result = true;
        }
        // Wettbewerbstypen
        $types = $configurations->get('scope.competition.type');
        if (strlen(trim($types))) {
            $fields['COMPETITION.TYPE'][OP_IN_INT] = $types;
            $result = true;
        }
        // Pflichtwettbewerbe
        $obligate = intval($configurations->get('scope.competition.obligation'));
        if ($obligate) {
            if (1 == $obligate) {
                $fields['COMPETITION.OBLIGATION'][OP_EQ_INT] = 1;
            } else {
                $fields['COMPETITION.OBLIGATION'][OP_NOTEQ_INT] = 1;
            }
            $result = true;
        }

        return $result;
    }

    /**
     * Search for competition by teams.
     *
     * @param array $fields
     * @param string $teamUids
     *            comma separated list of team UIDs
     *
     * @return bool true if condition is set
     */
    public static function buildCompetitionByTeam(&$fields, $teamUids, $obligateOnly = 'false')
    {
        $result = false;
        if (strlen(trim($teamUids))) {
            $fields['TEAM.UID'][OP_EQ_INT] = $teamUids;
            if ($obligateOnly) {
                $fields['COMPETITION.OBLIGATION'][OP_EQ_INT] = '1';
            }
            $result = true;
        }

        return $result;
    }

    /**
     * Search for matches by scope.
     *
     * @param array $fields
     * @param string $clubUids
     *            club uids
     */
    public static function buildMatchByClub(&$fields, $clubUids)
    {
        if (strlen(trim($clubUids))) {
            $joined = [];
            $joined['value'] = trim($clubUids);
            $joined['cols'] = [
                'TEAM1.CLUB',
                'TEAM2.CLUB',
            ];
            $joined['operator'] = OP_IN_INT;
            $fields[SEARCH_FIELD_JOINED][] = $joined;
        }
    }

    /**
     * Search for matches by teamUids.
     *
     * @param array $fields
     * @param string $teamUids
     *            comma separated uid string
     */
    public static function buildMatchByTeam(&$fields, $teamUids)
    {
        if (strlen(trim($teamUids))) {
            $joined = [];
            $joined['value'] = trim($teamUids);
            $joined['cols'] = [
                'MATCH.HOME',
                'MATCH.GUEST',
            ];
            $joined['operator'] = OP_IN_INT;
            $fields[SEARCH_FIELD_JOINED][] = $joined;
        }
    }

    /**
     * Search for matches by agegroup of teams.
     *
     * @param array $fields
     * @param string $groupUids
     *            comma separated uid string
     */
    public static function buildMatchByTeamAgeGroup(&$fields, $groupUids)
    {
        if (strlen(trim($groupUids))) {
            $joined = [];
            $joined['value'] = trim($groupUids);
            $joined['cols'] = [
                'TEAM1.AGEGROUP',
                'TEAM2.AGEGROUP',
            ];
            $joined['operator'] = OP_IN_INT;
            $fields[SEARCH_FIELD_JOINED][] = $joined;
        }
    }

    public static function setField(&$fields, $field, $operator, $value)
    {
        $result = false;
        if (strlen(trim($value))) {
            $fields[$field][$operator] = $value;
            $result = true;
        }

        return $result;
    }

    /**
     * Search for teams by scope.
     *
     * @param array $fields
     * @param string $scope
     *            Scope Array
     *
     * @return true
     */
    public static function buildTeamByScope(&$fields, $scope)
    {
        $result = false;
        $result = self::setField($fields, 'COMPETITION.SAISON', OP_IN_INT, $scope['SAISON_UIDS']) || $result;
        $result = self::setField($fields, 'COMPETITION.AGEGROUP', OP_IN_INT, $scope['GROUP_UIDS']) || $result;
        $result = self::setField($fields, 'COMPETITION.UID', OP_IN_INT, $scope['COMP_UIDS']) || $result;
        $result = self::setField($fields, 'TEAM.CLUB', OP_IN_INT, $scope['CLUB_UIDS']) || $result;
        $fields['TEAM.DUMMY'][OP_EQ_INT] = 0; // Ignore dummies

        return true;
    }

    /**
     * Search for stadiums by scope.
     *
     * @param array $fields
     * @param string $scope
     *            Scope Array
     *
     * @return true
     */
    public static function buildStadiumByScope(&$fields, $scope)
    {
        $result = false;
        $result = self::setField($fields, 'COMPETITION.SAISON', OP_IN_INT, $scope['SAISON_UIDS']) || $result;
        $result = self::setField($fields, 'COMPETITION.AGEGROUP', OP_IN_INT, $scope['GROUP_UIDS']) || $result;
        $result = self::setField($fields, 'MATCH.COMPETITION', OP_IN_INT, $scope['COMP_UIDS']) || $result;
        $result = self::setField($fields, 'TEAM.CLUB', OP_IN_INT, $scope['CLUB_UIDS']) || $result;

        return true;
    }
}
