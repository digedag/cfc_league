<?php

namespace System25\T3sports\Model;

use Exception;
use Sys25\RnBase\Domain\Model\BaseModel;

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
 * Model für eine Person.
 */
class Profile extends BaseModel
{
    private static $instances = [];
    /* @var MatchNote */
    private $matchNotes = [];

    public function getTableName()
    {
        return 'tx_cfcleague_profiles';
    }

    public function getFirstName()
    {
        return $this->getProperty('first_name');
    }

    public function getLastName()
    {
        return $this->getProperty('last_name');
    }

    /**
     * Returns the profile name.
     *
     * @param bool $reverse
     *
     * @return string
     */
    public function getName($reverse = false)
    {
        return $reverse ? $this->getLastName().', '.$this->getFirstName() : $this->getFirstName().' '.$this->getLastName();
    }

    public function getHomeTown()
    {
        return $this->getProperty('home_town');
    }

    /**
     * Liefert die Instance mit der übergebenen UID.
     * Die Daten werden gecached, so daß
     * bei zwei Anfragen für die selbe UID nur ein DB Zugriff erfolgt.
     *
     * @param int $uid
     *
     * @return Profile
     */
    public static function getProfileInstance($uid)
    {
        $uid = intval($uid);
        if (!$uid) {
            throw new Exception('No uid for '.self::class.' given!');
        }
        if (!is_object(self::$instances[$uid])) {
            self::$instances[$uid] = new self($uid);
        }

        return self::$instances[$uid];
    }

    public function addTeamNotes(&$team)
    {
        // TODO: Umstellen!
    }

    /**
     * Liefert true, wenn für den Spieler eine Einzelansicht verlinkt werden soll.
     *
     * @return true
     */
    public function hasReport()
    {
        return ((int) $this->getProperty('link_report')) > 0;
    }

    /**
     * Fügt diesem Profile eine neue Note hinzu.
     */
    public function addMatchNote(MatchNote $note)
    {
        if (!isset($this->matchNotes)) {
            $this->matchNotes = [];
        } // Neues TickerArray erstellen
        $this->matchNotes[] = $note;
        // Wir prüfen direkt auf Teamcaptain
        $this->check4Captain($note);
    }

    private function check4Captain(MatchNote $note)
    {
        if ($note->isType(200)) {
            // Wenn das im Record liegt, kann es auch per TS ausgewertet werden!
            $this->setProperty('teamCaptain', '1');
        }
    }

    /**
     * Returns 1 if player is team captain in a match.
     * Works if match_notes set.
     */
    public function isCaptain()
    {
        return (int) $this->getProperty('teamCaptain');
    }

    /**
     * Returns a match_note if player was changed out during a match.
     * Works if match_notes set.
     */
    public function isChangedOut()
    {
        if (is_array($this->matchNotes)) {
            for ($i = 0; $i < count($this->matchNotes); ++$i) {
                $note = $this->matchNotes[$i];
                if ($note->isType(80)) {
                    return $note;
                }
            }
        }
        return false;
    }

    /**
     * Returns a match_note if player received a penalty card during a match.
     * Works if match_notes set.
     *
     * @return
     */
    public function isPenalty()
    {
        // Die Matchnotes müssen absteigend durchsucht werden, da die letzte Strafe entscheidend ist
        if (is_array($this->matchNotes)) {
            $arr = array_reverse($this->matchNotes);
            for ($i = 0; $i < count($arr); ++$i) {
                $note = $arr[$i];
                if ($note->isPenalty()) {
                    return $note;
                }
            }
        }

        return false;
    }
}
