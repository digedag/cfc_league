<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2010 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model for a match.
 */
class tx_cfcleague_models_Match extends tx_rnbase_model_base
{

    const MATCH_STATUS_OPEN = 0;
    const MATCH_STATUS_RUNNING = 1;
    const MATCH_STATUS_FINISHED = 2;

    private $sets;
    private $resultInited = false;

    function __construct($rowOrUid)
    {
        parent::__construct($rowOrUid);
    }

    function getTableName()
    {
        return 'tx_cfcleague_games';
    }

    public function getGoalsHome($matchPart = '')
    {
        $this->initResult();
        $ret = $this->getProperty('goals_home');
        if (strlen($matchPart))
            $ret = $this->getProperty('goals_home_' . (($matchPart == 'last') ? $this->getProperty('matchparts') : $matchPart));
        return $ret;
    }

    public function getGoalsGuest($matchPart = '')
    {
        $this->initResult();
        $ret = $this->getProperty('goals_guest');
        if (strlen($matchPart))
            $ret = $this->getProperty('goals_guest_' . (($matchPart == 'last') ? $this->getProperty('matchparts') : $matchPart));
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

        if ($goalsDiff == 0)
            return 0;
        return ($goalsDiff < 0) ? 2 : 1;
    }

    /**
     * Notwendige Initialisierung für das Ergebnis des Spieldatensatzes
     */
    public function initResult()
    {
        if ($this->resultInited)
            return;

        // Um das Endergebnis zu ermitteln, muss bekannt sein, wieviele Spielabschnitte
        // es gibt. Dies steht im Wettbewerb
        $comp = $this->getCompetition();
        $this->setProperty('matchparts', $comp->getMatchParts());
        if ($comp->isAddPartResults())
            $this->initResultAdded($comp, $comp->getMatchParts());
        else
            $this->initResultSimple($comp, $comp->getMatchParts());
        $this->resultInited = true;
    }

    /**
     * Init result and expect the endresult in last match part.
     *
     * @param tx_cfcleague_models_Competition $comp
     * @param int $matchParts
     */
    private function initResultSimple($comp, $matchParts)
    {
        $goalsHome = $this->getProperty('goals_home_' . $matchParts);
        $goalsGuest = $this->getProperty('goals_guest_' . $matchParts);
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
     * @param tx_cfcleague_models_Competition $comp
     * @param int $matchParts
     */
    private function initResultAdded($comp, $matchParts)
    {
        $goalsHome = 0;
        $goalsGuest = 0;

        // Teilergebnisse holen
        $matchParts = $matchParts > 0 ? $matchParts : 1;
        for ($i = 1; $i <= $matchParts; $i ++) {
            $goalsHome += $this->getProperty('goals_home_' . $i);
            $goalsGuest += $this->getProperty('goals_guest_' . $i);
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
     *
     * @return string
     */
    public function getResult()
    {
        return $this->getProperty('status') > 0 ? $this->getGoalsHome() . ' : ' . $this->getGoalsGuest() : '- : -';
    }

    /**
     * Return sets if available
     *
     * @return array[tx_cfcleague_models_Set]
     */
    public function getSets()
    {
        if (! is_array($this->sets)) {
            tx_rnbase::load('tx_cfcleague_models_Set');
            $this->sets = tx_cfcleague_models_Set::buildFromString($this->getProperty('sets'));
            $this->sets = $this->sets ? $this->sets : array();
        }
        return $this->sets;
    }

    /**
     * Liefert die UIDs der Spieler des Heimteams der Startelf.
     * Wird derzeit nur in T3sportstats verwendet.
     * if you need instances of profiles use this call:
     * <pre>
     * $profiles = $profileSrv->loadProfiles($match->getPlayersHome());
     * </pre>
     *
     * @param $all wenn
     *            true werden auch die Ersatzspieler mit geliefert
     * @return string comma separated uids
     */
    public function getPlayersHome($all = false)
    {
        $ids = $this->getProperty('players_home');
        if ($all && strlen($this->getProperty('substitutes_home')) > 0) {
            // Auch Ersatzspieler anhängen
            if (strlen($ids) > 0)
                $ids = $ids . ',' . $this->getProperty('substitutes_home');
        }
        return $ids;
    }

    /**
     * Liefert die Spieler des Gastteams der Startelf
     *
     * @param $all wenn
     *            true werden auch die Ersatzspieler mit geliefert
     * @return string comma separated uids
     */
    public function getPlayersGuest($all = false)
    {
        $ids = $this->getProperty('players_guest');
        if ($all && strlen($this->getProperty('substitutes_guest')) > 0) {
            // Auch Ersatzspieler anhängen
            if (strlen($ids) > 0)
                $ids = $ids . ',' . $this->getProperty('substitutes_guest');
        }
        return $ids;
    }

    /**
     * Returns the competition
     *
     * @return tx_cfcleague_models_Competition
     */
    public function getCompetition()
    {
        if (! $this->competition) {
            tx_rnbase::load('tx_cfcleague_models_Competition');
            $this->competition = tx_cfcleague_models_Competition::getCompetitionInstance($this->getProperty('competition'));
        }
        return $this->competition;
    }

    public function setCompetition($competition)
    {
        $this->competition = $competition;
    }

    /**
     * Liefert das Heim-Team als Objekt
     *
     * @return tx_cfcleague_models_Team
     */
    public function getHome()
    {
        if (! $this->_teamHome) {
            $this->_teamHome = $this->getTeam($this->getProperty('home'));
        }
        return $this->_teamHome;
    }

    /**
     * Setzt das Heim-Team
     */
    public function setHome($team)
    {
        $this->_teamHome = $team;
    }

    /**
     * Liefert das Gast-Team als Objekt
     *
     * @return tx_cfcleague_models_Team
     */
    public function getGuest()
    {
        if (! $this->_teamGuest) {
            $this->_teamGuest = $this->getTeam($this->getProperty('guest'));
        }
        return $this->_teamGuest;
    }

    /**
     * Setzt das Gast-Team
     */
    public function setGuest($team)
    {
        $this->_teamGuest = $team;
    }

    /**
     * Liefert das Team als Objekt
     *
     * @return tx_cfcleague_models_Team
     */
    private function getTeam($uid)
    {
        if (! $uid)
            throw new Exception('Invalid match with uid ' . $this->getUid() . ': At least one team is not set.');
        $team = tx_cfcleague_util_ServiceRegistry::getTeamService()->getTeam($uid);
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
     * Returns true if match is finished
     *
     * @return boolean
     */
    public function isFinished()
    {
        return intval($this->getProperty('status')) == 2;
    }

    /**
     * Returns true if match is running
     *
     * @return boolean
     */
    public function isRunning()
    {
        return intval($this->getProperty('status')) == 1;
    }

    /**
     * Returns true if match has extra time
     *
     * @return boolean
     */
    public function isExtraTime()
    {
        return intval($this->getProperty('is_extratime')) == 1;
    }

    /**
     * Returns true if match has extra time
     *
     * @return boolean
     */
    public function isPenalty()
    {
        return intval($this->getProperty('is_penalty')) == 1;
    }

    /**
     * Returns true of match is a dummy (free of play).
     *
     * @return boolean
     */
    public function isDummy()
    {
        return $this->getHome()->isDummy() || $this->getGuest()->isDummy();
    }

    /**
     *
     * @return boolean true if live ticker is turn on
     */
    public function isTicker()
    {
        return ((int)$this->getProperty('link_ticker')) > 0;
    }

    /**
     * Liefert true, wenn für das Spiel ein Spielbericht vorliegt.
     * @return boolean
     */
    public function hasReport()
    {
        return (((int) $this->getProperty('has_report')) + ((int) $this->getProperty('link_report'))) > 0;
    }


    /**
     * Liefert alle MatchNotes des Spiels als Referenz auf ein Array.
     * Die Ticker werden in chronologischer Reihenfolge geliefert.
     * Alle MatchNotes haben eine Referenz auf das zugehörige Spiel
     *
     * @param string $orderBy
     *            'asc' or 'desc'
     * @param int $limit
     *            maximum number of note to retrieve
     * @return array[tx_cfcleague_models_MatchNote]
     */
    public function getMatchNotes($orderBy = 'asc', $limit = FALSE)
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
        if (! isset($this->matchNotes)) {
            $what = '*';
            $from = 'tx_cfcleague_match_notes';
            $options = array();
            $options['where'] = 'game = ' . $this->getUid();
            $options['wrapperclass'] = 'tx_cfcleague_models_MatchNote';
            // HINT: Die Sortierung nach dem Typ ist für die Auswechslungen wichtig.
            $options['orderby'] = 'minute asc, extra_time asc, uid asc';
            $this->matchNotes = Tx_Rnbase_Database_Connection::getInstance()->doSelect($what, $from, $options, 0);
            // Das Match setzen (foreach geht hier nicht weil es nicht mit Referenzen arbeitet...)
            $anz = count($this->matchNotes);
            for ($i = 0; $i < $anz; $i ++) {
                $this->matchNotes[$i]->setMatch($this);
                // Zusätzlich die Notes nach ihrem Typ sortieren
                $this->matchNoteTypes[intval($this->matchNotes[$i]->getProperty('type'))][] = $this->matchNotes[$i];
            }
        }
        return $orderBy == 'asc' ? $this->matchNotes : array_reverse($this->matchNotes);
    }
}

