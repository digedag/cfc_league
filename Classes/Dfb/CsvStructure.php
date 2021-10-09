<?php

namespace System25\T3sports\Dfb;

use Sys25\RnBase\Utility\Dates;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2020 Rene Nitzsche (rene@system25.de)
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

class CsvStructure
{
    public const COL_DATE = 'Datum';

    public const COL_TIME = 'Uhrzeit';

    public const COL_SAISON = 'Saison';

    public const COL_UNION = 'Verband';

    public const COL_AGE_GROUP_ID = 'MannschaftsartID';

    public const COL_AGE_GROUP = 'Mannschaftsart';

    public const COL_LEAGUE_ID = 'SpielklasseID';

    public const COL_LEAGUE_TYPE = 'Spielklasse';

    public const COL_AREA_ID = 'SpielgebietID';

    public const COL_AREA = 'Spielgebiet';

    public const COL_MATCHTABLE = 'Rahmenspielplan';

    public const COL_LEAGUE_NUMBER = 'Staffelnummer';

    public const COL_LEAGUE = 'Staffel';

    public const COL_LEAGUE_IDENT = 'Staffelkennung';

    public const COL_LEAGUE_CHIEF = 'Staffelleiter';

    public const COL_MATCH_DATE = 'Spieldatum';

    public const COL_MATCH_TIME = 'Anstosszeit'; // Changed from "Uhrzeit"

    public const COL_MATCH_WEEKDAY = 'Wochentag';

    public const COL_MATCH_ROUND = 'Spieltag';

    public const COL_MATCH_KEYDAY = 'Schlüsseltag';

    public const COL_MATCH_HOME = 'Heimmannschaft';

    public const COL_MATCH_GUEST = 'Gastmannschaft';

    public const COL_MATCH_ID = 'Spielkennung';

    public const COL_MATCH_VALID = 'freigegeben';

    public const COL_STADIUM = 'Spielstätte';

    public const COL_REFEREE = 'Spielleitung';

    public const COL_ASSIST_1 = 'Assistent 1';

    public const COL_ASSIST_2 = 'Assistent 2';

    public const COL_POSTPONE_WEEKDAY = 'verlegtWochentag';

    public const COL_POSTPONE_DATE = 'verlegtSpieldatum';

    public const COL_POSTPONE_TIME = 'verlegtUhrzeit';

    /** Spalte in CSV-Datei */
    public const DATA_COL = 'data_col';

    /** Pflichtfeld */
    public const DATA_REQUIRED = 'data_required';

    protected $structure = [];

    public function __construct(array $headers)
    {
        $this->init($headers);
    }

    protected function init(array $headers)
    {
        $this->structure = [
            self::COL_MATCH_DATE => $this->createColData(),
            self::COL_MATCH_TIME => $this->createColData(),
            self::COL_MATCH_HOME => $this->createColData(),
            self::COL_MATCH_GUEST => $this->createColData(),
            self::COL_MATCH_ROUND => $this->createColData(),
            self::COL_MATCH_ID => $this->createColData(),
            self::COL_STADIUM => $this->createColData(),
            self::COL_LEAGUE_IDENT => $this->createColData(),
        ];
        foreach ($this->structure as $field => $data) {
            if ($idx = array_search($field, $headers)) {
                $this->structure[$field][self::DATA_COL] = $idx;
            }
        }
    }

    public function getMatchId(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_ID][self::DATA_COL]);
    }

    public function getCompetitionId($line)
    {
        return $this->getData($line, $this->structure[self::COL_LEAGUE_IDENT][self::DATA_COL]);
    }

    public function getStadium(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_STADIUM][self::DATA_COL]);
    }

    public function getRound(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_ROUND][self::DATA_COL]);
    }

    public function getHome(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_HOME][self::DATA_COL]);
    }

    public function getGuest(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_GUEST][self::DATA_COL]);
    }

    public function getKickoffDate(array $line)
    {
        if ($day = $this->getData($line, $this->structure[self::COL_POSTPONE_DATE][self::DATA_COL])) {
            $time = $this->getData($line, $this->structure[self::COL_POSTPONE_TIME][self::DATA_COL]);
        } else {
            $day = $this->getData($line, $this->structure[self::COL_MATCH_DATE][self::DATA_COL]);
            $time = $this->getData($line, $this->structure[self::COL_MATCH_TIME][self::DATA_COL]);
        }
        $date = Dates::getDateTime($day.' '.$time);

        return $date->getTimestamp();
    }

    protected function getData($line, $col)
    {
        return $line[$col];
    }

    protected function createColData($required = true)
    {
        return [
            self::DATA_REQUIRED => $required,
            self::DATA_COL => null,
        ];
    }
}
