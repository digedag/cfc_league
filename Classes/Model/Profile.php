<?php

namespace System25\T3sports\Model;

use Exception;
use Sys25\RnBase\Domain\Model\BaseModel;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2021 Rene Nitzsche (rene@system25.de)
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
 * Model für eine Person.
 */
class Profile extends BaseModel
{
    private static $instances = [];

    public function getTableName()
    {
        return 'tx_cfcleague_profiles';
    }

    public function getFirstName()
    {
        return $this->getProperty('first_name');
    }

    public function getLastName()
    {
        return $this->getProperty('last_name');
    }

    /**
     * Returns the profile name.
     *
     * @param bool $reverse
     *
     * @return string
     */
    public function getName($reverse = false)
    {
        return $reverse ? $this->getLastName().', '.$this->getFirstName() : $this->getFirstName().' '.$this->getLastName();
    }

    public function getHomeTown()
    {
        return $this->getProperty('home_town');
    }

    /**
     * Liefert die Instance mit der übergebenen UID.
     * Die Daten werden gecached, so daß
     * bei zwei Anfragen für die selbe UID nur ein DB Zugriff erfolgt.
     *
     * @param int $uid
     *
     * @return Profile
     */
    public static function getProfileInstance($uid)
    {
        $uid = intval($uid);
        if (!$uid) {
            throw new Exception('No uid for '.self::class.' given!');
        }
        if (!is_object(self::$instances[$uid])) {
            self::$instances[$uid] = new self($uid);
        }

        return self::$instances[$uid];
    }

    public function addTeamNotes(&$team)
    {
        // TODO: Umstellen!
    }

    /**
     * Liefert true, wenn für den Spieler eine Einzelansicht verlinkt werden soll.
     *
     * @return true
     */
    public function hasReport()
    {
        return ((int) $this->getProperty('link_report')) > 0;
    }
}
