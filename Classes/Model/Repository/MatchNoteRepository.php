<?php

namespace System25\T3sports\Model\Repository;

use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use System25\T3sports\Model\Match;
use System25\T3sports\Model\MatchNote;
use System25\T3sports\Search\MatchNoteSearch;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017-2021 Rene Nitzsche (rene@system25.de)
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
 * @author Rene Nitzsche
 */
class MatchNoteRepository extends PersistenceRepository
{
    public function getSearchClass()
    {
        return MatchNoteSearch::class;
    }

    /**
     * Ermittelt für die übergebenen Spiele die MatchNotes.
     * Wenn $types = 1 dann
     * werden nur die Notes mit dem Typ < 100 geliefert. Die MatchNotes werden direkt
     * in den übergebenen Matches gesetzt.
     * Die ermittelten MatchNotes haben keine Referenz auf das zugehörige Match!
     *
     * @param Match[] $matches
     * @param int $types
     *
     * @return
     */
    public function retrieveMatchNotes(array $matches, $types = 1)
    {
        if (!count($matches)) {
            return $matches;
        }
        // Die Spiele in einen Hash legen, damit wir sofort Zugriff auf ein Spiel haben
        $matchesHash = [];
        $matchIds = [];
        $anz = count($matches);
        for ($i = 0; $i < $anz; ++$i) {
            $matchesHash[$matches[$i]->getUid()] = $matches[$i];
            $matchIds[] = $matches[$i]->getUid();
        }

        $matchIds = implode(',', $matchIds); // ID-String erstellen

        $what = '*';
        $from = 'tx_cfcleague_match_notes';
        $options['where'] = 'game IN ('.$matchIds.')';
        if ($types) {
            $options['where'] .= ' AND type < 100';
        }
        $options['orderby'] = 'game asc, minute asc';
        $options['wrapperclass'] = 'tx_cfcleaguefe_models_match_note';

        $matchNotes = $this->getConnection()->doSelect($what, $from, $options);

        // Das Match setzen (foreach geht hier nicht weil es nicht mit Referenzen arbeitet...)
        $anz = count($matchNotes);
        for ($i = 0; $i < $anz; ++$i) {
            // Hier darf nur mit Referenzen gearbeitet werden
            $matchesHash[$matchNotes[$i]->getProperty('game')]->addMatchNote($matchNotes[$i]);
        }

        return $matches;
    }

    /**
     * Lädt die MatchNotes eines Spiels.
     *
     * @param Match $match
     * @param string $orderBy
     *
     * @return MatchNote[]
     */
    public function loadMatchNotesByMatch(Match $match, $orderBy = 'asc')
    {
        $what = '*';
        $from = 'tx_cfcleague_match_notes';
        $options = [];
        $options['where'] = 'game = '.$match->getUid();
        $options['wrapperclass'] = MatchNote::class;
        // HINT: Die Sortierung nach dem Typ ist für die Auswechslungen wichtig.
        $options['orderby'] = 'minute asc, extra_time asc, uid asc';
        $matchNotes = $this->getConnection()->doSelect($what, $from, $options, 0);
        // Das Match setzen (foreach geht hier nicht weil es nicht mit Referenzen arbeitet...)
        $anz = count($matchNotes);
        foreach ($matchNotes as $matchNote) {
            $matchNote->setMatch($match);
        }

        return 'asc' == $orderBy ? $matchNotes : array_reverse($matchNotes);
    }
}
