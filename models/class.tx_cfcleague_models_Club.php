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
 * Model für einen Verein.
 */
class tx_cfcleague_models_Club extends tx_rnbase_model_base
{

    public function getTableName()
    {
        return 'tx_cfcleague_club';
    }

    public function getName()
    {
        return $this->getProperty('name');
    }

    public function getNameShort()
    {
        return $this->getProperty('short_name');
    }

    public function getCountryCode()
    {
        return $this->getProperty('countrycode');
    }

    /**
     * Whether or not this is a favorite club
     *
     * @return boolean
     */
    public function isFavorite()
    {
        return intval($this->getProperty('favorite')) > 0;
    }

    /**
     * Returns the city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getProperty('city');
    }

    /**
     * Returns address dataset or null
     *
     * @return tx_cfcleague_models_Address or null
     */
    public function getAddress()
    {
        if (! $this->getProperty('address')) {
            return null;
        }
        $address = tx_rnbase::makeInstance('tx_cfcleague_models_Address', $this->getProperty('address'));
        return $address->isValid() ? $address : null;
    }

    /**
     * Returns the zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->getProperty('zip');
    }

    /**
     * Returns the street
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
     * Returns coords
     *
     * @return tx_rnbase_maps_ICoord or false
     */
    public function getCoords()
    {
        $coords = false;
        if ($this->getLongitute() || $this->getLatitute()) {
            $coords = tx_rnbase::makeInstance('tx_rnbase_maps_Coord');
            $coords->setLatitude($this->getLatitute());
            $coords->setLongitude($this->getLongitute());
        }
        return $coords;
    }

    /**
     * Returns the url of the first club logo.
     *
     * @return string
     */
    public function getFirstLogo()
    {
        if (tx_rnbase_util_Extensions::isLoaded('dam')) {
            if ($this->getProperty('dam_logo')) {
                $damPics = tx_dam_db::getReferencedFiles('tx_cfcleague_club', $this->getUid(), 'dam_images');
                if (list ($uid, $filePath) = each($damPics['files'])) {
                    return $filePath;
                }
            }
        }
        // TODO: Return logo for simple image field
        return '';
    }
}
