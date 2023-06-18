<?php

namespace System25\T3sports\Model;

use Exception;
use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Maps\Coord;
use Sys25\RnBase\Maps\ICoord;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2023 Rene Nitzsche (rene@system25.de)
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
 * Model for a stadium.
 */
class Stadium extends BaseModel
{
    private static $instances = [];

    public function getTableName()
    {
        return 'tx_cfcleague_stadiums';
    }

    /**
     * Returns the stadium name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getProperty('name');
    }

    public function getCountryCode()
    {
        return $this->getProperty('countrycode');
    }

    /**
     * Returns the city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getProperty('city');
    }

    /**
     * Returns the zip.
     *
     * @return string
     */
    public function getZip()
    {
        return $this->getProperty('zip');
    }

    /**
     * Returns the street.
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->getProperty('street');
    }

    public function getLongitute()
    {
        return floatval($this->getProperty('lng'));
    }

    public function getLatitute()
    {
        return floatval($this->getProperty('lat'));
    }

    /**
     * Returns coords.
     *
     * @return ICoord or false
     */
    public function getCoords()
    {
        $coords = false;
        if ($this->getLongitute() || $this->getLatitute()) {
            $coords = tx_rnbase::makeInstance(Coord::class);
            $coords->setLatitude($this->getLatitute());
            $coords->setLongitude($this->getLongitute());
        }

        return $coords;
    }

    /**
     * Returns address dataset or null.
     *
     * @return Address or null
     */
    public function getAddress()
    {
        if (!$this->getProperty('address')) {
            return null;
        }
        $address = tx_rnbase::makeInstance(Address::class, $this->getProperty('address'));

        return $address->isValid() ? $address : null;
    }

    /**
     * Liefert die Instance mit der übergebenen UID.
     * Die Daten werden gecached, so daß
     * bei zwei Anfragen für die selbe UID nur ein DB Zugriff erfolgt.
     *
     * @param int $uid
     *
     * @return Stadium
     */
    public static function getStadiumInstance($uid = null)
    {
        $uid = (int) $uid;
        if (!$uid) {
            throw new Exception('No uid for '.self::getTableName().' given!');
        }
        if (!isset(self::$instances[$uid])) {
            self::$instances[$uid] = new self($uid);
        }

        return self::$instances[$uid];
    }

    /**
     * Returns the url of the first stadium logo.
     *
     * @return string
     *
     * @deprecated use Typoscript
     */
    public function getLogoPath()
    {
        return '';
    }
}
