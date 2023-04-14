<?php

namespace System25\T3sports\Hooks;

use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Dates;
use Sys25\RnBase\Utility\Strings;
use System25\T3sports\Model\Stadium;
use System25\T3sports\Utility\TcaLookup;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2023 Rene Nitzsche <rene@system25.de>
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

class TceHook
{
    /**
     * Dieser Hook wird vor der Darstellung eines TCE-Formulars aufgerufen.
     * Werte aus der Datenbank können vor deren Darstellung manipuliert werden.
     * Nur bis 6.2 verwendet.
     */
    public function getMainFields_preProcess($table, &$row, $tceform)
    {
        if ('tx_cfcleague_profiles' == $table && !strstr($row['uid'], 'NEW')) {
            // '2|Trainer'
            $options = [];
            $options['where'] = 'uid_foreign='.$row['uid'];
            $options['orderby'] = 'sorting_foreign asc';
            $options['enablefieldsoff'] = 1;
            $types = [];
            $rows = Connection::getInstance()->doSelect('uid_local', 'tx_cfcleague_profiletypes_mm', $options);
            foreach ($rows as $type) {
                $types[] = $type['uid_local'];
            }
            $row['types'] = TcaLookup::getProfileTypeItems($types);
        }
        if ('tx_cfcleague_club' == $table) {
            // Umwandlung eine MySQL Date in einen timestamp
            // Scheint in 8.7 nicht mehr notwendig zu sein
            $row['established'] = $row['established'] ? Dates::datetime_mysql2tstamp($row['established']) : time();
        }
    }

    /**
     * Wir müssen dafür sorgen, daß die neuen IDs der Teams im Wettbewerb und Spielen
     * verwendet werden.
     *
     * @param array $incomingFieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tcemain
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $tcemain)
    {
        if ('tx_cfcleague_teams' == $table) {
            $this->checkProfiles($incomingFieldArray, 'players', $tcemain);
            $this->checkProfiles($incomingFieldArray, 'coaches', $tcemain);
            $this->checkProfiles($incomingFieldArray, 'supporters', $tcemain);
        }
        if ('tx_cfcleague_competition' == $table) {
            $this->checkProfiles($incomingFieldArray, 'teams', $tcemain);
            // Neue Teams im Wettbewerb?
            if (strstr($incomingFieldArray['teams'], 'NEW')) {
                $newItemIds = Strings::trimExplode(',', $incomingFieldArray['teams']);
                $itemUids = [];
                for ($i = 0; $i < count($newItemIds); ++$i) {
                    if (strstr($newItemIds[$i], 'NEW')) {
                        $itemUid = $tcemain->substNEWwithIDs[$newItemIds[$i]];
                    } else {
                        $itemUid = $newItemIds[$i];
                    }
                    // Wir übernehmen nur UIDs, die gefunden werden
                    if ($itemUid) {
                        $itemUids[] = $itemUid;
                    }
                }
                $itemUids = array_unique($itemUids);
                $incomingFieldArray['teams'] = implode(',', $itemUids);
            }
        }
        if ('tx_cfcleague_games' == $table) {
            if ($incomingFieldArray['arena'] > 0 && !$incomingFieldArray['stadium']) {
                $stadium = \tx_rnbase::makeInstance(Stadium::class, $incomingFieldArray['arena']);
                $incomingFieldArray['stadium'] = $stadium->getName();
            }
            if (strstr($incomingFieldArray['home'], 'NEW')) {
                $incomingFieldArray['home'] = $tcemain->substNEWwithIDs[$incomingFieldArray['home']];
            }
            if (strstr($incomingFieldArray['guest'], 'NEW')) {
                $incomingFieldArray['guest'] = $tcemain->substNEWwithIDs[$incomingFieldArray['guest']];
            }
        }
        if ('tx_cfcleague_stadiums' == $table || 'tx_cfcleague_club' == $table) {
            if ($incomingFieldArray['country'] > 0 && !$incomingFieldArray['countrycode']) {
                $country = BackendUtility::getRecord('static_countries', intval($incomingFieldArray['country']));
                $incomingFieldArray['countrycode'] = $country['cn_iso_2'];
            }
        }
    }

    /**
     * Nachbearbeitungen, unmittelbar BEVOR die Daten gespeichert werden.
     * Das POST bezieht sich
     * auf die Arbeit der TCE und nicht auf die Speicherung in der DB.
     *
     * @param string $status
     *            new oder update
     * @param string $table
     *            Name der Tabelle
     * @param int $id
     *            UID des Datensatzes
     * @param array $fieldArray
     *            Felder des Datensatzes, die sich ändern
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tcemain
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$tcemain)
    {
        if ('tx_cfcleague_club' == $table) {
            if (array_key_exists('established', $fieldArray)) {
                $estDate = $fieldArray['established'] ? Dates::date_tstamp2mysql($fieldArray['established']) : null;
                $fieldArray['established'] = $estDate;
            }
        }
    }

    /**
     * Prüft, ob im für den angegebenen Personentyp neue Personen angelegt wurden
     * und setzt die neuen UIDs.
     *
     * @param array $incomingFieldArray
     * @param string $profileType
     *            Spaltenname im Teamdatensatz (players, coaches, supporters)
     */
    protected function checkProfiles(&$incomingFieldArray, $profileType, $tcemain)
    {
        if (isset($incomingFieldArray[$profileType]) && strstr($incomingFieldArray[$profileType], 'NEW')) {
            $newProfileIds = Strings::trimExplode(',', $incomingFieldArray[$profileType]);
            $profileUids = [];
            for ($i = 0; $i < count($newProfileIds); ++$i) {
                if (strstr($newProfileIds[$i], 'NEW')) {
                    $profileUid = $tcemain->substNEWwithIDs[$newProfileIds[$i]];
                } else {
                    $profileUid = $newProfileIds[$i];
                }
                // Wir übernehmen nur UIDs, die gefunden werden
                if ($profileUid) {
                    $profileUids[] = $profileUid;
                }
            }
            $incomingFieldArray[$profileType] = implode(',', $profileUids);
        }
    }
}
