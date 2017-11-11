<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2017 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model für eine Person.
 */
class tx_cfcleague_models_Profile extends tx_rnbase_model_base
{

    private static $instances = array();

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
     * Returns the profile name
     *
     * @param boolean $reverse
     * @return string
     */
    public function getName($reverse = false)
    {
        return $reverse ? $this->getLastName() . ', ' . $this->getFirstName() : $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Liefert die Instance mit der übergebenen UID.
     * Die Daten werden gecached, so daß
     * bei zwei Anfragen für die selbe UID nur ein DB Zugriff erfolgt.
     *
     * @param int $uid
     * @return tx_netfewo_models_Objekt
     */
    public static function getProfileInstance($uid)
    {
        $uid = intval($uid);
        if (! $uid) {
            throw new Exception('No uid for ' . self::getTableName() . ' given!');
        }
        if (! is_object(self::$instances[$uid])) {
            self::$instances[$uid] = new tx_cfcleague_models_Profile($uid);
        }
        return self::$instances[$uid];
    }

    public function addTeamNotes(&$team)
    {
        // TODO: Umstellen!
    }

    /**
     * Liefert true, wenn für den Spieler eine Einzelansicht verlinkt werden soll.
     * @return true
     */
    public function hasReport()
    {
        return ((int) $this->getProperty('link_report')) > 0;
    }

    /**
     * Liefert das Sternzeichen der Person.
     */
    public function getSign()
    {
        return 'TODO';
        // $signs = Signs::getInstance();
        // return intval($this->record['birthday']) != 0 ? $signs->getSign($this->record['birthday']) : '';
    }
}
