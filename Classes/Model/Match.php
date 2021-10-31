<?php

namespace System25\T3sports\Model;

use Exception;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Domain\Model\BaseModel;
use System25\T3sports\Utility\ServiceRegistry;

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
 * Model for a match.
 */
class Match extends BaseModel
{
    public const MATCH_STATUS_INVALID = -1;
    public const MATCH_STATUS_RESCHEDULED = -10;

    public const MATCH_STATUS_OPEN = 0;

    public const MATCH_STATUS_RUNNING = 1;

    public const MATCH_STATUS_FINISHED = 2;

    private $sets;

    private $resultInited = false;
    private $competition;

    public function __construct($rowOrUid = null)
    {
        parent::__construct($rowOrUid);
        if ($rowOrUid) {
            $this->initResult();
        }
    }

    public function getTableName()
    {
        return 'tx_cfcleague_games';
    }

    public function getGoalsHome($matchPart = '')
    {
        $this->initResult();
        $ret = $this->getProperty('goals_home');
        if (strlen($matchPart)) {
            $ret = $this->getProperty('goals_home_'.(('last' == $matchPart) ? $this->getProperty('matchparts') : $matchPart));
        }

        return $ret;
    }

    public function getGoalsGuest($matchPart = '')
    {
        $this->initResult();
        $ret = $this->getProperty('goals_guest');
        if (strlen($matchPart)) {
            $ret = $this->getProperty('goals_guest_'.(('last' == $matchPart) ? $this->getProperty('matchparts') : $matchPart));
        }

        return $ret;
    }

    /**
     * Liefert den TOTO-Wert des Spiels.
     * Als 0 für ein Unentschieden, 1 für einen Heim-
     * und 2 für einen Auswärstsieg.
     *
     * @param string $matchPart
     *            The matchpart is 1,2,3...,et,ap,last
     */
    public function getToto($matchPart = '')
    {
        $goalsHome = $this->getGoalsHome($matchPart);
        $goalsGuest = $this->getGoalsGuest($matchPart);

        $goalsDiff = $goalsHome - $goalsGuest;

        if (0 == $goalsDiff) {
            return 0;
        }

        return ($goalsDiff < 0) ? 2 : 1;
    }

    /**
     * Notwendige Initialisierung für das Ergebnis des Spieldatensatzes.
     */
    public function initResult()
    {
        if ($this->resultInited) {
            return;
        }

        // Um das Endergebnis zu ermitteln, muss bekannt sein, wieviele Spielabschnitte
        // es gibt. Dies steht im Wettbewerb
        $comp = $this->getCompetition();
        $this->setProperty('matchparts', $comp->getMatchParts());
        if ($comp->isAddPartResults()) {
            $this->initResultAdded($comp, $comp->getMatchParts());
        } else {
            $this->initResultSimple($comp, $comp->getMatchParts());
        }
        $this->resultInited = true;
    }

    /**
     * Init result and expect the endresult in last match part.
     *
     * @param Competition $comp
     * @param int $matchParts
     */
    private function initResultSimple($comp, $matchParts)
    {
        $goalsHome = $this->getProperty('goals_home_'.$matchParts);
        $goalsGuest = $this->getProperty('goals_guest_'.$matchParts);
        // Gab es Verländerung oder Elfmeterschiessen
        if ($this->isPenalty()) {
            $goalsHome = $this->getProperty('goals_home_ap');
            $goalsGuest = $this->getProperty('goals_guest_ap');
        } elseif ($this->isExtraTime()) {
            $goalsHome = $this->getProperty('goals_home_et');
            $goalsGuest = $this->getProperty('goals_guest_et');
        }
        $this->setProperty('goals_home', $goalsHome);
        $this->setProperty('goals_guest', $goalsGuest);
    }

    /**
     * Init result and add all matchpart results.
     *
     * @param Competition $comp
     * @param int $matchParts
     */
    private function initResultAdded($comp, $matchParts)
    {
        $goalsHome = 0;
        $goalsGuest = 0;

        // Teilergebnisse holen
        $matchParts = $matchParts > 0 ? $matchParts : 1;
        for ($i = 1; $i <= $matchParts; ++$i) {
            $goalsHome += $this->getProperty('goals_home_'.$i);
            $goalsGuest += $this->getProperty('goals_guest_'.$i);
        }
        // Gab es Verländerung oder Elfmeterschiessen
        if ($this->isPenalty()) {
            $goalsHome += $this->getProperty('goals_home_ap');
            $goalsGuest += $this->getProperty('goals_guest_ap');
        } elseif ($this->isExtraTime()) {
            $goalsHome += $this->getProperty('goals_home_et');
            $goalsGuest += $this->getProperty('goals_guest_et');
        }
        $this->setProperty('goals_home', $goalsHome);
        $this->setProperty('goals_guest', $goalsGuest);
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->getProperty('status') > 0 ? $this->getGoalsHome().' : '.$this->getGoalsGuest() : '- : -';
    }

    /**
     * Return sets if available.
     *
     * @return Set[]
     */
    public function getSets()
    {
        if (!is_array($this->sets)) {
            $this->sets = Set::buildFromString($this->getProperty('sets'));
            $this->sets = $this->sets ? $this->sets : [];
        }

        return $this->sets;
    }

    /**
     * Liefert die UIDs der Spieler des Heimteams der Startelf.
     * Wird derzeit nur in T3sportstats verwendet.
     * if you need instances of profiles use this call:
     * <pre>
     * $profiles = $profileSrv->loadProfiles($match->getPlayersHome());
     * </pre>.
     *
     * @param bool $all wenn true werden auch die Ersatzspieler mit geliefert
     *
     * @return string comma separated uids
     */
    public function getPlayersHome($all = false)
    {
        $ids = $this->getProperty('players_home');
        if ($all && strlen($this->getProperty('substitutes_home')) > 0) {
            // Auch Ersatzspieler anhängen
            if (strlen($ids) > 0) {
                $ids = $ids.','.$this->getProperty('substitutes_home');
            }
        }

        return $ids;
    }

    /**
     * Liefert die Spieler des Gastteams der Startelf.
     *
     * @param bool $all wenn true werden auch die Ersatzspieler mit geliefert
     *
     * @return string comma separated uids
     */
    public function getPlayersGuest($all = false)
    {
        $ids = $this->getProperty('players_guest');
        if ($all && strlen($this->getProperty('substitutes_guest')) > 0) {
            // Auch Ersatzspieler anhängen
            if (strlen($ids) > 0) {
                $ids = $ids.','.$this->getProperty('substitutes_guest');
            }
        }

        return $ids;
    }

    /**
     * Substitutes of home team.
     *
     * @return string comma separated uids
     */
    public function getSubstitutesHome()
    {
        return $this->getProperty('substitutes_home');
    }

    /**
     * Substitutes of guest team.
     *
     * @return string comma separated uids
     */
    public function getSubstitutesGuest()
    {
        return $this->getProperty('substitutes_guest');
    }

    /**
     * Returns the competition.
     *
     * @return Competition
     */
    public function getCompetition()
    {
        if (!$this->competition) {
            $this->competition = Competition::getCompetitionInstance($this->getProperty('competition'));
        }

        return $this->competition;
    }

    public function setCompetition($competition)
    {
        $this->competition = $competition;
    }

    /**
     * Liefert das Heim-Team als Objekt.
     *
     * @return Team
     */
    public function getHome()
    {
        if (!$this->_teamHome) {
            $this->_teamHome = $this->getTeam($this->getProperty('home'));
        }

        return $this->_teamHome;
    }

    /**
     * Setzt das Heim-Team.
     */
    public function setHome($team)
    {
        $this->_teamHome = $team;
    }

    /**
     * Liefert das Gast-Team als Objekt.
     *
     * @return Team
     */
    public function getGuest()
    {
        if (!$this->_teamGuest) {
            $this->_teamGuest = $this->getTeam($this->getProperty('guest'));
        }

        return $this->_teamGuest;
    }

    /**
     * Setzt das Gast-Team.
     */
    public function setGuest($team)
    {
        $this->_teamGuest = $team;
    }

    /**
     * Liefert das Team als Objekt.
     *
     * @return Team
     */
    private function getTeam($uid)
    {
        if (!$uid) {
            throw new Exception('Invalid match with uid '.$this->getUid().': At least one team is not set.');
        }
        $team = ServiceRegistry::getTeamService()->getTeam($uid);

        return $team;
    }

    public function getHomeNameShort()
    {
        return $this->getHome()->getNameShort();
    }

    public function getGuestNameShort()
    {
        return $this->getGuest()->getNameShort();
    }

    /**
     * Returns true if match is finished.
     *
     * @return bool
     */
    public function isFinished()
    {
        return 2 == intval($this->getProperty('status'));
    }

    /**
     * Returns true if match is running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return 1 == intval($this->getProperty('status'));
    }

    /**
     * Returns true if match has extra time.
     *
     * @return bool
     */
    public function isExtraTime()
    {
        return 1 == intval($this->getProperty('is_extratime'));
    }

    /**
     * Returns true if match has extra time.
     *
     * @return bool
     */
    public function isPenalty()
    {
        return 1 == intval($this->getProperty('is_penalty'));
    }

    /**
     * Returns true if match is a dummy (free of play).
     *
     * @return bool
     */
    public function isDummy(): bool
    {
        return $this->getHome()->isDummy() || $this->getGuest()->isDummy();
    }

    /**
     * Returns true if match is out of competition.
     *
     * @return bool
     */
    public function isOutOfCompetition(): bool
    {
        return $this->getHome()->isOutOfCompetition() || $this->getGuest()->isOutOfCompetition();
    }

    /**
     * @return bool true if live ticker is turn on
     */
    public function isTicker()
    {
        return ((int) $this->getProperty('link_ticker')) > 0;
    }

    /**
     * Liefert true, wenn für das Spiel ein Spielbericht vorliegt.
     *
     * @return bool
     */
    public function hasReport()
    {
        return (((int) $this->getProperty('has_report')) + ((int) $this->getProperty('link_report'))) > 0;
    }

    /**
     * Liefert das Stadion.
     *
     * @return Stadium|null
     */
    public function getArena()
    {
        if (!intval($this->getProperty('arena'))) {
            return null;
        }

        return Stadium::getStadiumInstance($this->getProperty('arena'));
    }

    /**
     * Returns the name of arena for this match. This can differ from arena name!
     *
     * @return string
     */
    public function getStadium()
    {
        return $this->getProperty('stadium');
    }

    /**
     * Liefert den Referee als Datenobjekt.
     *
     * @return Profile
     */
    public function getReferee()
    {
        if ($this->getProperty('referee')) {
            try {
                $profile = Profile::getProfileInstance($this->getProperty('referee'));

                return $profile->isValid() ? $profile : null;
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @return string comma separated uids
     */
    public function getAssists()
    {
        return $this->getProperty('assists');
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->getProperty('date');
    }

    public function getVisitors()
    {
        return $this->getProperty('visitors');
    }

    /**
     * Liefert alle MatchNotes des Spiels als Referenz auf ein Array.
     * Die Ticker werden in chronologischer Reihenfolge geliefert.
     * Alle MatchNotes haben eine Referenz auf das zugehörige Spiel.
     *
     * @param string $orderBy
     *            'asc' or 'desc'
     * @param int $limit
     *            maximum number of note to retrieve
     *
     * @return MatchNote[]
     */
    public function getMatchNotes($orderBy = 'asc', $limit = false)
    {
        $notes = $this->resolveMatchNotes($orderBy);
        if ($limit) {
            return array_slice($notes, 0, (int) $limit);
        }

        return $notes;
    }

    /**
     * Lädt die MatchNotes dieses Spiels.
     * Sollten sie schon geladen sein, dann
     * wird nix gemacht.
     *
     * @param string $orderBy
     */
    private function resolveMatchNotes($orderBy = 'asc')
    {
        if (!isset($this->matchNotes)) {
            $what = '*';
            $from = 'tx_cfcleague_match_notes';
            $options = [];
            $options['where'] = 'game = '.$this->getUid();
            $options['wrapperclass'] = 'tx_cfcleague_models_MatchNote';
            // HINT: Die Sortierung nach dem Typ ist für die Auswechslungen wichtig.
            $options['orderby'] = 'minute asc, extra_time asc, uid asc';
            $this->matchNotes = Connection::getInstance()->doSelect($what, $from, $options, 0);
            // Das Match setzen (foreach geht hier nicht weil es nicht mit Referenzen arbeitet...)
            $anz = count($this->matchNotes);
            for ($i = 0; $i < $anz; ++$i) {
                $this->matchNotes[$i]->setMatch($this);
                // Zusätzlich die Notes nach ihrem Typ sortieren
                $this->matchNoteTypes[intval($this->matchNotes[$i]->getProperty('type'))][] = $this->matchNotes[$i];
            }
        }

        return 'asc' == $orderBy ? $this->matchNotes : array_reverse($this->matchNotes);
    }
}
