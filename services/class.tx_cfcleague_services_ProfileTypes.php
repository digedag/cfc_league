<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2017 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('Tx_Rnbase_Service_Base');

/**
 * Service for accessing profile types
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_ProfileTypes extends Tx_Rnbase_Service_Base
{

    static $types = array(
        1 => array(
            'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles_type_player',
            1
        ),
        2 => array(
            'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles_type_coach',
            2
        ),
        3 => array(
            'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles_type_referee',
            3
        ),
        4 => array(
            'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles_type_supporter',
            4
        )
    );

    public function getProfileTypes()
    {
        return array_values(self::$types);
    }

    public function setProfileTypeItems(&$uids)
    {
        $keys = array_keys($uids);
        foreach ($keys as $uid) {
            if (is_array(self::$types[$uid])) {
                $uids[$uid] = self::$types[$uid][0];
            }
        }
    }
}
