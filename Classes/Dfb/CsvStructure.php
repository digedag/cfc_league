<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2018 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_parameters');

/**
 */
class Tx_Cfcleague_Dfb_CsvStructure
{
    const COL_DATE               = 'Datum';
    const COL_TIME               = 'Uhrzeit';
    const COL_SAISON             = 'Saison';
    const COL_UNION              = 'Verband';
    const COL_AGE_GROUP_ID       = 'MannschaftsartID';
    const COL_AGE_GROUP          = 'Mannschaftsart';
    const COL_LEAGUE_ID          = 'SpielklasseID';
    const COL_LEAGUE_TYPE        = 'Spielklasse';
    const COL_AREA_ID            = 'SpielgebietID';
    const COL_AREA               = 'Spielgebiet';
    const COL_MATCHTABLE         = 'Rahmenspielplan';
    const COL_LEAGUE_NUMBER      = 'Staffelnummer';
    const COL_LEAGUE             = 'Staffel';
    const COL_LEAGUE_IDENT       = 'Staffelkennung';
    const COL_LEAGUE_CHIEF       = 'Staffelleiter';
    const COL_MATCH_DATE         = 'Spieldatum';
    const COL_MATCH_TIME         = 'Anstosszeit'; // Changed from "Uhrzeit"
    const COL_MATCH_WEEKDAY      = 'Wochentag';
    const COL_MATCH_ROUND        = 'Spieltag';
    const COL_MATCH_KEYDAY       = 'Schlüsseltag';
    const COL_MATCH_HOME         = 'Heimmannschaft';
    const COL_MATCH_GUEST        = 'Gastmannschaft';
    const COL_MATCH_ID           = 'Spielkennung';
    const COL_MATCH_VALID        = 'freigegeben';
    const COL_STADIUM            = 'Spielstätte';
    const COL_REFEREE            = 'Spielleitung';
    const COL_ASSIST_1           = 'Assistent 1';
    const COL_ASSIST_2           = 'Assistent 2';
    const COL_POSTPONE_WEEKDAY   = 'verlegtWochentag';
    const COL_POSTPONE_DATE      = 'verlegtSpieldatum';
    const COL_POSTPONE_TIME      = 'verlegtUhrzeit';

    /** Spalte in CSV-Datei */
    const DATA_COL = 'data_col';
    /** Pflichtfeld */
    const DATA_REQUIRED = 'data_required';

    protected $structure = [];
    public function __construct(array $headers)
    {
        $this->init($headers);
    }
    protected function init(array $headers)
    {
        $this->structure = [
            self::COL_MATCH_DATE    => $this->createColData(),
            self::COL_MATCH_TIME    => $this->createColData(),
            self::COL_MATCH_HOME    => $this->createColData(),
            self::COL_MATCH_GUEST   => $this->createColData(),
            self::COL_MATCH_ROUND   => $this->createColData(),
            self::COL_MATCH_ID      => $this->createColData(),
            self::COL_STADIUM       => $this->createColData(),
            self::COL_LEAGUE_IDENT  => $this->createColData(),
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
        if( $day = $this->getData($line, $this->structure[self::COL_POSTPONE_DATE][self::DATA_COL])) {
            $time = $this->getData($line, $this->structure[self::COL_POSTPONE_TIME][self::DATA_COL]);
        }
        else {
            $day = $this->getData($line, $this->structure[self::COL_MATCH_DATE][self::DATA_COL]);
            $time = $this->getData($line, $this->structure[self::COL_MATCH_TIME][self::DATA_COL]);
        }
        $date = tx_rnbase_util_Dates::getDateTime($day.' '.$time);
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

