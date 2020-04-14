<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2013 Rene Nitzsche (rene@system25.de)
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
 * Util methods for match sets.
 */
class tx_cfcleague_util_MatchSets
{
    /**
     * returns sum of all set points of home team.
     *
     * @param tx_cfcleague_models_Match $match
     */
    public static function countSetPointsHome($match)
    {
        $result = 0;
        $sets = $match->getSets();
        /* @var $set tx_cfcleague_models_Set */
        foreach ($sets as $set) {
            $result += $set->getPointsHome();
        }

        return $result;
    }

    /**
     * returns sum of all set points of guest team.
     *
     * @param tx_cfcleague_models_Match $match
     */
    public static function countSetPointsGuest($match)
    {
        $result = 0;
        $sets = $match->getSets();
        /* @var $set tx_cfcleague_models_Set */
        foreach ($sets as $set) {
            $result += $set->getPointsGuest();
        }

        return $result;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_MatchSets.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_MatchSets.php'];
}
