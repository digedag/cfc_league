<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Rene Nitzsche (rene@system25.de)
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
 * Model for a note type.
 */
class tx_cfcleague_models_TeamNoteType extends tx_rnbase_model_base
{

    private static $instances = array();

    public function getTableName()
    {
        return 'tx_cfcleague_note_types';
    }

    /**
     * Liefert den Namen des Markers
     *
     * @return string
     */
    public function getMarker()
    {
        return $this->getProperty('marker');
    }

    /**
     * Liefert den Namen des Types
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getProperty('label');
    }

    /**
     * Liefert die Beschreibung des Types
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getProperty('description');
    }

    /**
     * Liefert die Instance eines Landes
     *
     * @param int $uid
     * @return tx_cfcleague_models_TeamNoteType
     */
    public static function getInstance($uid = NULL)
    {
        self::_init();
        return self::$instances[$uid];
    }

    /**
     * Returns an array with all types
     *
     * @return array
     */
    public static function getAll()
    {
        self::_init();
        return array_values(self::$instances);
    }

    /**
     * Lädt alle Instanzen aus der DB und legt sie in das Array self::$instances.
     * Key ist die UID des Records.
     */
    private static function _init()
    {
        if (count(self::$instances)) {
            return;
        }

        $options = [
            'wrapperclass' => 'tx_cfcleague_models_TeamNoteType'
        ];
        $result = Tx_Rnbase_Database_Connection::getInstance()->doSelect('*', 'tx_cfcleague_note_types', $options, 0);

        foreach ($result as $type) {
            self::$instances[$type->getUid()] = $type;
        }
    }
}
