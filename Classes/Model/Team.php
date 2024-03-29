<?php

namespace System25\T3sports\Model;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Domain\Model\MediaModel;
use Sys25\RnBase\Utility\Strings;
use tx_rnbase;

/***************************************************************
*  Copyright notice
*
*  (c) 2007-2021 Rene Nitzsche (rene@system25.de)
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
 * Model für ein Team.
 */
class Team extends BaseModel
{
    private static $instances = [];

    public function getTableName()
    {
        return 'tx_cfcleague_teams';
    }

    public function getNameShort()
    {
        return $this->getProperty('short_name');
    }

    /**
     * Liefert true, wenn für das Team eine Einzelansicht verlinkt werden kann.
     */
    public function hasReport()
    {
        return intval($this->getProperty('link_report'));
    }

    /**
     * Returns the url of the first team logo.
     * (not yet implemented).
     *
     * @return string
     */
    public function getLogoPath()
    {
        if ($this->getProperty('logo')) {
            // LogoFeld
            $media = tx_rnbase::makeInstance(MediaModel::class, $this->getProperty('logo'));

            return $media->getProperty('file');
        } elseif ($this->getProperty('club')) {
            $club = tx_rnbase::makeInstance(Club::class, $this->getProperty('club'));

            return $club->getFirstLogo();
        }

        return '';
    }

    public function getGroupUid()
    {
        return $this->getProperty('agegroup');
    }

    /**
     * Liefert den Verein des Teams als Objekt.
     *
     * @return Club Verein als Objekt oder null
     */
    public function getClub()
    {
        if (!$this->getProperty('club')) {
            return null;
        }

        return tx_rnbase::makeInstance(Club::class, $this->getProperty('club'));
    }

    public function getClubUid()
    {
        return $this->getProperty('club');
    }

    /**
     * Check if team is a dummy for free_of_match.
     *
     * @return bool
     */
    public function isDummy()
    {
        return 1 === (int) $this->getProperty('dummy');
    }

    /**
     * Check if team is marked as out of competition.
     *
     * @return bool
     */
    public function isOutOfCompetition(): bool
    {
        return 2 === (int) $this->getProperty('dummy');
    }

    /**
     * Liefert die Spieler des Teams in der vorgegebenen Reihenfolge als Profile. Der
     * Key ist die laufende Nummer und nicht die UID!
     *
     * @return Profile[]
     */
    public function getPlayers()
    {
        return $this->getTeamMember('players');
    }

    /**
     * Liefert die Trainer des Teams in der vorgegebenen Reihenfolge als Profile. Der
     * Key ist die laufende Nummer und nicht die UID!
     *
     * @return Profile[]
     */
    public function getCoaches()
    {
        return $this->getTeamMember('coaches');
    }

    /**
     * Liefert die Betreuer des Teams in der vorgegebenen Reihenfolge als Profile. Der
     * Key ist die laufende Nummer und nicht die UID!
     *
     * @return Profile[]
     */
    public function getSupporters()
    {
        return $this->getTeamMember('supporters');
    }

    /**
     * Liefert Mitglieder des Teams als Array. Teammitglieder sind Spieler, Trainer und Betreuer.
     * Die gefundenen Profile werden sortiert in der Reihenfolge im Team geliefert.
     *
     * @column Name der DB-Spalte mit den gesuchten Team-Mitgliedern
     *
     * @return Profile[]
     */
    private function getTeamMember($column)
    {
        if (strlen(trim($this->getProperty($column))) > 0) {
            $what = '*';
            $from = 'tx_cfcleague_profiles';
            $options['where'] = 'uid IN ('.$this->getProperty($column).')';
            $options['wrapperclass'] = Profile::class;

            $rows = Connection::getInstance()->doSelect($what, $from, $options, 0);

            return $this->sortProfiles($rows, $column);
        }

        return [];
    }

    /**
     * Sortiert die Personen (Spieler/Trainer) entsprechend der Reihenfolge im Team.
     *
     * @param Profile[] $profiles
     */
    private function sortProfiles($profiles, $recordKey = 'players')
    {
        $ret = [];

        if (strlen(trim($this->getProperty($recordKey))) > 0) {
            if (count($profiles)) {
                // Jetzt die Spieler in die richtige Reihenfolge bringen
                $uids = Strings::intExplode(',', $this->getProperty($recordKey));
                $uids = array_flip($uids);
                foreach ($profiles as $player) {
                    $ret[(int) $uids[$player->getUid()]] = $player;
                }
                ksort($ret);
            }
        } else {
            // Wenn keine Spieler im Team geladen sind, dann wird das Array unverändert zurückgegeben
            return $profiles;
        }

        return $ret;
    }
}
