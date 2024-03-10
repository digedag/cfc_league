<?php

namespace System25\T3sports\Sports;

use Sys25\RnBase\Utility\Misc;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2024 Rene Nitzsche (rene@system25.de)
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

class Volleyball implements ISports
{
    /**
     * @return array
     */
    public function getTCAPointSystems()
    {
        return [
            [Misc::translateLLL('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.point_system_2'), 0],
            [Misc::translateLLL('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.point_system_3'), 1],
        ];
    }

    public function getTCALabel()
    {
        return 'Volleyball';
    }

    public function isSetBased(): bool
    {
        return true;
    }

    public function hasPoints(): bool
    {
        return false;
    }

    private $matchInfo;

    /* (non-PHPdoc)
     * @see ISports::getMatchInfo()
     */
    public function getMatchInfo()
    {
        if (null == $this->matchInfo) {
            // Bei Volleyball gibt es keine festen Zeiten
            $this->matchInfo = tx_rnbase::makeInstance(MatchInfo::class, []);
        }

        return $this->matchInfo;
    }

    public function getIdentifier(): string
    {
        return 'volleyball';
    }
}
