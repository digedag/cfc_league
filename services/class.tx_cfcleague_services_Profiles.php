<?php
use Sys25\RnBase\Search\SearchBase;
use System25\T3sports\Search\ProfileSearch;

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
 * Service for accessing profiles.
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Profiles extends tx_cfcleague_services_Base
{
    private $profiles = [];

    /**
     * Return all instances of all requested profiles.
     *
     * @param string $uids
     *            commaseparated uids
     *
     * @return array[tx_cfcleague_models_Profile]
     */
    public function loadProfiles($uids)
    {
        $uids = is_array($uids) ? $uids : Tx_Rnbase_Utility_Strings::intExplode(',', $uids);
        $ret = [];
        $toLoad = [];
        foreach ($uids as $key => $uid) {
            if (array_key_exists($uid, $this->profiles)) {
                $ret[$key] = $this->profiles[$uid];
            } else {
                $toLoad[$key] = $uid;
            }
        }

        if (!empty($toLoad)) {
            $fields = [];
            $fields['PROFILE.UID'][OP_IN_INT] = implode(',', $toLoad);
            $options = [];
            $rows = $this->search($fields, $options);
            $toLoadFlip = array_flip($toLoad);
            foreach ($rows as $profile) {
                $this->profiles[$profile->getUid()] = $profile;
                $ret[$toLoadFlip[$profile->getUid()]] = $profile;
            }
        }

        return $ret;
    }

    /**
     * Returns all team notes for a given profile.
     *
     * @param tx_cfcleague_models_Profile $profile
     *
     * @return array An array with all references by table
     */
    public function checkReferences($profile)
    {
        $ret = [];
        // Zuerst die Teams
        $options = [];
        $options['what'] = 'uid';

        $fields = [];
        $fields[SEARCH_FIELD_JOINED][0] = [
            'value' => $profile->getUid(),
            'cols' => [
                'TEAM.PLAYERS',
                'TEAM.SUPPORTERS',
                'TEAM.COACHES',
            ],
            'operator' => OP_INSET_INT,
        ];
        $result = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchTeams($fields, $options);
        if (count($result)) {
            $ret['tx_cfcleague_teams'] = $result;
        }

        $fields = [];
        $fields['TEAMNOTE.PLAYER'][OP_EQ_INT] = $profile->getUid();
        $result = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchTeamNotes($fields, $options);
        if (count($result)) {
            $ret['tx_cfcleague_team_notes'] = $result;
        }

        $fields = [];
        $fields[SEARCH_FIELD_JOINED][0] = [
            'value' => $profile->getUid(),
            'cols' => [
                'MATCH.REFEREE',
                'MATCH.ASSISTS',
                'MATCH.PLAYERS_HOME',
                'MATCH.PLAYERS_GUEST',
                'MATCH.SUBSTITUTES_HOME',
                'MATCH.SUBSTITUTES_GUEST',
                'MATCH.COACH_HOME',
                'MATCH.COACH_GUEST',
            ],
            'operator' => OP_INSET_INT,
        ];
        $result = tx_cfcleague_util_ServiceRegistry::getMatchService()->search($fields, $options);
        if (count($result)) {
            $ret['tx_cfcleague_games'] = $result;
        }

        $fields = [];
        $fields[SEARCH_FIELD_JOINED][0] = [
            'value' => $profile->getUid(),
            'cols' => [
                'MATCHNOTE.PLAYER_HOME',
                'MATCHNOTE.PLAYER_GUEST',
            ],
            'operator' => OP_EQ_INT,
        ];
        $result = tx_cfcleague_util_ServiceRegistry::getMatchService()->searchMatchNotes($fields, $options);
        if (count($result)) {
            $ret['tx_cfcleague_match_notes'] = $result;
        }

        return $ret;
    }

    /**
     * Returns all team notes for a given profile.
     *
     * @param int $profileUID
     */
    public function getTeamsNotes4Profile($profileUID)
    {
        $options = ['where' => 'player = '.$profileUID];

        return Tx_Rnbase_Database_Connection::getInstance()->doSelect('*', 'tx_cfcleague_team_notes', $options);
    }

    /**
     * Returns all available profile types for a TCA select item.
     *
     * @return array
     */
    public function getProfileTypes4TCA()
    {
        $items = [];
        $baseType = 't3sports_profiletype';
        $services = tx_rnbase_util_Misc::lookupServices($baseType);
        foreach ($services as $subtype => $info) {
            $srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
            $types = array_merge($items, $srv->getProfileTypes());
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
     * Find all profile types for a given array with uids.
     *
     * @param array $uids
     *
     * @return string imploded uid|label-String for TCA select fields
     */
    public function getProfileTypeItems4TCA($uids)
    {
        $uidArr = [];
        foreach ($uids as $uid) {
            $uidArr[$uid] = '';
        }

        $baseType = 't3sports_profiletype';
        $services = tx_rnbase_util_Misc::lookupServices($baseType);
        foreach ($services as $subtype => $info) {
            $srv = tx_rnbase_util_Misc::getService($baseType, $subtype);
            $srv->setProfileTypeItems($uidArr);
        }
        $items = [];
        foreach ($uidArr as $uid => $label) {
            $items[] = $uid.'|'.tx_rnbase_util_Misc::translateLLL($label);
        }

        return implode(',', $items);
    }

    /**
     * Search database for profiles.
     *
     * @param array $fields
     * @param array $options
     *
     * @return array[tx_cfcleague_models_Profile]
     */
    public function search($fields, $options)
    {
        $searcher = SearchBase::getInstance(ProfileSearch::class);

        return $searcher->search($fields, $options);
    }
}
