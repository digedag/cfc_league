<?php

namespace System25\T3sports\Service;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\RecordInterface;
use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\Strings;
use System25\T3sports\Model\Profile;
use System25\T3sports\Model\Repository\ProfileRepository;
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
 * Service for accessing profiles.
 *
 * @author Rene Nitzsche
 */
class ProfileService extends AbstractService
{
    private $profiles = [];

    /**
     * @var ProfileRepository
     */
    private $repo;
    /**
     * @var ProfileTypeService
     */
    private $profileTypeService;

    public function __construct(ProfileRepository $repo = null, ProfileTypeService $profileTypeSrv = null)
    {
        $this->repo = $repo ?: new ProfileRepository();
        $this->profileTypeService = $profileTypeSrv ?: new ProfileTypeService();
    }

    /**
     * Return all instances of all requested profiles.
     *
     * @param string $uids commaseparated uids
     *
     * @return Profile[]
     */
    public function loadProfiles($uids)
    {
        $uids = is_array($uids) ? $uids : Strings::intExplode(',', $uids);
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
     * @param Profile $profile
     *
     * @return array An array with all references by table
     */
    public function checkReferences(Profile $profile)
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
        $result = ServiceRegistry::getTeamService()->searchTeams($fields, $options);
        if (!$result->isEmpty()) {
            $ret['tx_cfcleague_teams'] = $result->toArray();
        }

        $fields = [];
        $fields['TEAMNOTE.PLAYER'][OP_EQ_INT] = $profile->getUid();
        $result = ServiceRegistry::getTeamService()->searchTeamNotes($fields, $options);
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
        $result = ServiceRegistry::getMatchService()->search($fields, $options);
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
        $result = ServiceRegistry::getMatchService()->searchMatchNotes($fields, $options);
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

        return Connection::getInstance()->doSelect('*', 'tx_cfcleague_team_notes', $options);
    }

    /**
     * Returns all available profile types for a TCA select item.
     *
     * @return array
     */
    public function getProfileTypes4TCA()
    {
        $items = [];
        $types = $this->profileTypeService->getProfileTypes();
        foreach ($types as $typedef) {
            $items[] = [
                Misc::translateLLL($typedef[0]),
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

        $this->profileTypeService->setProfileTypeItems($uidArr);

        $items = [];
        foreach ($uidArr as $uid => $label) {
            $items[] = $uid.'|'.Misc::translateLLL($label);
        }

        return implode(',', $items);
    }

    /**
     * Search database for profiles.
     *
     * @param array $fields
     * @param array $options
     *
     * @return Profile[]
     */
    public function search($fields, $options)
    {
        return $this->repo->search($fields, $options);
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
