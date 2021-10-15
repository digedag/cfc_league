<?php

namespace System25\T3sports\Controller\Profile;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\Strings;
use Sys25\RnBase\Utility\T3General;
use System25\T3sports\Utility\ServiceRegistry;

/***************************************************************
*  Copyright notice
*
*  (c) 2007-2021 Rene Nitzsche <rene@system25.de>
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

class ProfileMerger
{
    /**
     * Start merging profile. The leading profile will overtake all references
     * of the obsolete profile. So at the end the should be no references to second
     * profile anymore.
     *
     * @param int $leadingProfileUID UID of leading profile
     * @param int $obsoleteProfileUID UID of obsolute profile
     */
    public function merge($leadingProfileUID, $obsoleteProfileUID)
    {
        // Alle Referenzen sollen auf das erste Profil übergehen
        // Tabellen:
        // tx_cfcleague_teams
        // tx_cfcleague_games
        // tx_cfcleague_match_notes
        // TODO: tx_cfcleague_teamnotes

        // Wir machen alles über die TCA, also das Array aufbauen
        $data = [];
        $this->mergeTeams($data, $leadingProfileUID, $obsoleteProfileUID);
        $this->mergeMatches($data, $leadingProfileUID, $obsoleteProfileUID);
        $this->mergeMatchNotes($data, $leadingProfileUID, $obsoleteProfileUID);
        $this->mergeTeamNotes($data, $leadingProfileUID, $obsoleteProfileUID);

        Misc::callHook(
            'cfc_league',
            'mergeProfiles_hook',
            ['data' => &$data, 'leadingUid' => $leadingProfileUID, 'obsoleteUid' => $obsoleteProfileUID],
            $this
        );

        $tce = Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
    }

    private function mergeTeamNotes(&$data, $leading, $obsolete)
    {
        $srv = ServiceRegistry::getProfileService();
        $rows = $srv->getTeamsNotes4Profile($obsolete);
        foreach ($rows as $row) {
            $data['tx_cfcleague_team_notes'][$row['uid']]['player'] = $leading;
        }
    }

    private function mergeMatchNotes(&$data, $leading, $obsolete)
    {
        $rows = ServiceRegistry::getMatchService()->searchMatchNotesByProfile($obsolete);
        foreach ($rows as $matchNote) {
            $this->mergeField('player_home', 'tx_cfcleague_match_notes', $data, $matchNote->getRecord(), $leading, $obsolete);
            $this->mergeField('player_guest', 'tx_cfcleague_match_notes', $data, $matchNote->getRecord(), $leading, $obsolete);
        }
    }

    private function mergeMatches(&$data, $leading, $obsolete)
    {
        $rows = ServiceRegistry::getMatchService()->searchMatchesByProfile($obsolete);
        foreach ($rows as $match) {
            $this->mergeField('players_home', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('players_guest', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('substitutes_home', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('substitutes_guest', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('coach_home', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('coach_guest', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('referee', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
            $this->mergeField('assists', 'tx_cfcleague_games', $data, $match->getRecord(), $leading, $obsolete);
        }
    }

    private function mergeTeams(&$data, $leading, $obsolete)
    {
        // Teams suchen, in denen obsolete spielt
        $teamRows = ServiceRegistry::getTeamService()->searchTeamsByProfile($obsolete);
        foreach ($teamRows as $team) {
            // Drei Felder können das Profile enthalten:
            // players
            $this->mergeField('players', 'tx_cfcleague_teams', $data, $team->getRecord(), $leading, $obsolete);
            $this->mergeField('coaches', 'tx_cfcleague_teams', $data, $team->getRecord(), $leading, $obsolete);
            $this->mergeField('supporters', 'tx_cfcleague_teams', $data, $team->getRecord(), $leading, $obsolete);
        }
    }

    private function mergeField($fieldName, $tableName, &$data, $row, $leading, $obsolete)
    {
        $val = $this->replaceUid($row[$fieldName], $leading, $obsolete);
        if (strlen($val)) {
            $data[$tableName][$row['uid']][$fieldName] = $val;
        }
    }

    private function replaceUid($fieldValue, $leading, $obsolete)
    {
        $ret = '';
        if (T3General::inList($fieldValue, $obsolete)) {
            $values = Strings::intExplode(',', $fieldValue);
            $idx = array_search($obsolete, $values);
            if (false !== $idx) {
                $values[$idx] = $leading;
            }
            $ret = implode(',', $values);
        }

        return $ret;
    }
}
