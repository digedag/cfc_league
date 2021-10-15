<?php

namespace System25\T3sports\Hooks;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Strings;

/**
 * *************************************************************
 * Copyright notice.
 *
 * (c) 2009-2020 Rene Nitzsche <rene@system25.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * *************************************************************
 */
class AfterDBHook
{
    /**
     * Nachbearbeitungen, unmittelbar NACHDEM die Daten gespeichert wurden.
     *
     * @param string $status new oder update
     * @param string $table Name der Tabelle
     * @param int $id UID des Datensatzes
     * @param array $fieldArray Felder des Datensatzes, die sich ändern
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $tcemain
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$tcemain)
    {
        if ('tx_cfcleague_profiles' == $table) {
            if (array_key_exists('types', $fieldArray)) {
                $db = Connection::getInstance();
                // Die Types werden zusätzlich in einer MM-Tabelle gespeichert
                $id = ('new' == $status) ? $tcemain->substNEWwithIDs[$id] : $id;
                if ('new' != $status) {
                    $db->doDelete('tx_cfcleague_profiletypes_mm', 'uid_foreign='.$id, 0);
                }
                $types = Strings::intExplode(',', $fieldArray['types']);
                $i = 0;
                foreach ($types as $type) {
                    if (!intval($type)) {
                        continue;
                    }
                    $values = [];
                    $values['uid_local'] = $type;
                    $values['uid_foreign'] = $id;
                    $values['tablenames'] = 'tx_cfcleague_profiles';
                    $values['sorting_foreign'] = $i++;
                    $db->doInsert('tx_cfcleague_profiletypes_mm', $values, 0);
                }
            }
        }
    }
}
