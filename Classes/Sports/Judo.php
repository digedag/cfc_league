<?php

namespace System25\T3sports\Sports;

use Sys25\RnBase\Utility\Misc;
use tx_rnbase;

/***************************************************************
*  Copyright notice
*
*  (c) 2008-2024 Rene Nitzsche (rene@system25.de)
*  (c) 2018- Hubert Küsters (info@hubertkuesters.de)
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

class Judo implements ISports
{
    private $matchInfo;

    /**
     * Get match provider.
     *
     * @return tx_cfcleaguefe_table_ITableType
     */
    public function getLeagueTable()
    {
        if (tx_rnbase_util_Extensions::isLoaded('cfc_league_fe')) {
            return tx_rnbase::makeInstance('tx_cfcleaguefe_table_judo_Table');
        }

        return null;
    }

    /**
     * @return array
     */
    public function getTCAPointSystems()
    {
        return [
            [Misc::translateLLL('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.point_system_2_judo'), 1],
        ];
    }

    public function getTCALabel()
    {
        return 'Judo';
    }

    public function isSetBased(): bool
    {
        return false;
    }

    public function hasPoints(): bool
    {
        return true;
    }

    /*
     * (non-PHPdoc)
     * @see tx_cfcleague_sports_ISports::getMatchInfo()
     */
    public function getMatchInfo()
    {
        if (null === $this->matchInfo) {
            $this->matchInfo = tx_rnbase::makeInstance('MatchInfo', [
                MatchInfo::MATCH_TIME => 60,
                MatchInfo::MATCH_PARTS => 2,
                MatchInfo::MATCH_EXTRA_TIME => 10,
            ]);
        }

        return $this->matchInfo;
    }

    public function getIdentifier(): string
    {
        return 'judo';
    }
}
