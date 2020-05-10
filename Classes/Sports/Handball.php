<?php

namespace System25\T3sports\Sports;

use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017-2020 Rene Nitzsche (rene@system25.de)
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

class Handball extends AbstractService implements ISports
{
    /**
     * Get match provider.
     *
     * @return \tx_cfcleaguefe_table_ITableType
     */
    public function getLeagueTable()
    {
        if (\tx_rnbase_util_Extensions::isLoaded('cfc_league_fe')) {
            return \tx_rnbase::makeInstance('tx_cfcleaguefe_table_handball_Table');
        }

        return null;
    }

    /**
     * @return array
     */
    public function getTCAPointSystems()
    {
        return [
            [
                \tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2'),
                0,
            ],
        ];
    }

    public function getTCALabel()
    {
        return 'Handball';
    }

    public function isSetBased()
    {
        return false;
    }

    private $matchInfo = null;

    /*
     * (non-PHPdoc)
     * @see ISports::getMatchInfo()
     */
    public function getMatchInfo()
    {
        if (null == $this->matchInfo) {
            \tx_rnbase::load('tx_cfcleague_sports_MatchInfo');
            $this->matchInfo = \tx_rnbase::makeInstance('tx_cfcleague_sports_MatchInfo', [
                \tx_cfcleague_sports_MatchInfo::MATCH_TIME => 60,
                \tx_cfcleague_sports_MatchInfo::MATCH_PARTS => 2,
                \tx_cfcleague_sports_MatchInfo::MATCH_EXTRA_TIME => 10,
            ]);
        }

        return $this->matchInfo;
    }
}
