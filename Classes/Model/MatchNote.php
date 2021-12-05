<?php

namespace System25\T3sports\Model;

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
 * Model for a match note/event.
 */
class MatchNote extends BaseModel
{
    public const TYPE_TICKER = 100;

    public const TYPE_GOAL = 10;

    public const TYPE_GOAL_HEADER = 11;

    public const TYPE_GOAL_PENALTY = 12;

    public const TYPE_GOAL_OWN = 30;

    public const TYPE_GOAL_ASSIST = 31;

    public const TYPE_PENALTY_FORGIVEN = 32;

    public const TYPE_CORNER = 33;

    public const TYPE_CARD_YELLOW = 70;

    public const TYPE_CARD_YELLOWRED = 71;

    public const TYPE_CARD_RED = 72;

    public const TYPE_CHANGEOUT = 80;

    public const TYPE_CHANGEIN = 81;

    public const TYPE_CAPTAIN = 200;

    public function getTableName()
    {
        return 'tx_cfcleague_match_notes';
    }

    /**
     * Liefert die UID des Profils, des an der Aktion beteiligten Spielers der Heimmannschaft.
     *
     * @return int
     */
    public function getPlayerHome()
    {
        return (int) $this->getProperty('player_home');
    }

    /**
     * Liefert bei Auswechslungen die UID des Profils, des eingewechselten Spielers der Heimmannschaft.
     *
     * @return int
     */
    protected function getPlayerHome2()
    {
        return (int) $this->getProperty('player_home_2');
    }

    /**
     * Liefert die UID des Profils, des an der Aktion beteiligten Spielers der Gastmannschaft.
     *
     * @return int
     */
    public function getPlayerGuest()
    {
        return (int) $this->getProperty('player_guest');
    }

    /**
     * Liefert bei Auswechslungen die UID des Profils, des eingewechselten Spielers der Gastmannschaft.
     *
     * @return int
     */
    protected function getPlayerGuest2()
    {
        return (int) $this->getProperty('player_guest_2');
    }

    /**
     * Liefert die UID des Spielers, dem diese Meldung zugeordnet ist.
     *
     * @return int
     */
    public function getPlayer()
    {
        if ($this->isHome()) {
            return $this->getPlayerHome();
        }
        if ($this->isGuest()) {
            return $this->getPlayerGuest();
        }

        return 0;
    }

    /**
     * Liefert die UID des Spielers, dem diese Meldung zugeordnet ist.
     *
     * @return int
     */
    public function getPlayer2()
    {
        if ($this->isHome()) {
            return $this->getPlayerHome2();
        }
        if ($this->isGuest()) {
            return $this->getPlayerGuest2();
        }

        return 0;
    }

    /**
     * Liefert den Typ der Meldung.
     *
     * @return int den Typ der Meldung
     */
    public function getType()
    {
        return (int) $this->getProperty('type');
    }

    /**
     * Liefert die Minute der Meldung.
     *
     * @return int
     */
    public function getMinute()
    {
        return $this->getProperty('minute');
    }

    /**
     * Liefert true wenn die Aktion dem Heimteam zugeordnet ist.
     *
     * @return bool
     */
    public function isHome()
    {
        return $this->getProperty('player_home') > 0 || -1 == $this->getProperty('player_home');
    }

    /**
     * Liefert true wenn die Aktion dem Gastteam zugeordnet ist.
     *
     * @return bool
     */
    public function isGuest()
    {
        return $this->getProperty('player_guest') > 0 || -1 == $this->getProperty('player_guest');
    }

    /**
     * Liefert true wenn die Meldung ein Tor ist.
     *
     * @return bool
     */
    public function isGoal()
    {
        $type = $this->getType();

        return ($type >= 10 && $type < 20) || 30 == $type; // 30 ist das Eigentor
    }

    /**
     * Liefert true wenn es ein Eigentor ist.
     *
     * @return bool
     */
    public function isGoalOwn()
    {
        return 30 == $this->getType();
    }

    /**
     * Liefert true wenn ein Tor für das Heimteam gefallen ist.
     * Auch Eigentore werden
     * berücksichtigt.
     *
     * @return bool
     */
    public function isGoalHome()
    {
        if ($this->isGoal()) {
            return ($this->isHome() && !$this->isGoalOwn()) || ($this->isGuest() && $this->isGoalOwn());
        }

        return false;
    }

    /**
     * Liefert true wenn ein Tor für das Gastteam gefallen ist.
     * Auch Eigentore werden
     * berücksichtigt.
     *
     * @return bool
     */
    public function isGoalGuest()
    {
        if ($this->isGoal()) {
            return ($this->isGuest() && !(30 == $this->getType())) || ($this->isHome() && (30 == $this->getType()));
        }

        return false;
    }

    /**
     * Liefert true, wenn die Aktion eine Ein- oder Auswechslung ist.
     *
     * @return bool
     */
    public function isChange()
    {
        return '80' == $this->getType() || '81' == $this->getType();
    }

    /**
     * Liefert true wenn die Meldung eine Strafe ist (Karten).
     */
    public function isPenalty()
    {
        $type = (int) $this->getProperty('type');

        return $type >= self::TYPE_CARD_YELLOW && $type < self::TYPE_CHANGEOUT;
    }

    /**
     * Liefert true, wenn die Aktion eine Ein- oder Auswechslung ist.
     *
     * @return bool
     */
    public function isType(int $type)
    {
        return $type === $this->getType();
    }

    /**
     * Liefert bei einem Wechsel die UID des eingewechselten Spielers.
     */
    public function getPlayerUidChangeIn(): ?int
    {
        return $this->getPlayerChange(0);
    }

    /**
     * Liefert bei einem Wechsel die UIDs des ausgewechselten Spielers.
     *
     * @return int|null
     */
    public function getPlayerUidChangeOut(): ?int
    {
        return $this->getPlayerChange(1);
    }

    /**
     * Liefert den ausgewechselten Spieler, wenn der Tickertyp ein Wechsel ist.
     *
     * @param int $type 0 liefert den eingewechselten Spieler, 1 den ausgewechselten
     *
     * @return int die UID des Spielers
     */
    protected function getPlayerChange($type): ?int
    {
        // Ist es ein Wechsel?
        if ($this->isChange() && ($this->getProperty('player_home') || $this->getProperty('player_guest'))) {
            // Heim oder Gast?
            if ($this->getProperty('player_home')) {
//                $players = $this->match->getPlayersHome(1);
                $playerField = '80' == $this->getProperty('type') ? ($type ? 'player_home' : 'player_home_2') : ($type ? 'player_home_2' : 'player_home');
            } else {
//                $players = $this->match->getPlayersGuest(1);
                $playerField = '80' == $this->getProperty('type') ? ($type ? 'player_guest' : 'player_guest_2') : ($type ? 'player_guest_2' : 'player_guest');
            }
            if ($this->getProperty($playerField) < 0) {
                return $this->getUnknownPlayer();
            }

            return $this->getProperty($playerField);
//            return $players[$this->getProperty($playerField)];
        }

        return null;
    }

    public function __toString()
    {
        return get_class($this).'( uid['.$this->getUid().
        '] type['.$this->getProperty('type').
        '] minute['.$this->getProperty('minute').
        '] player_home['.$this->getProperty('player_home').
        '] player_guest['.$this->getProperty('player_guest').
        '])';
    }
}
