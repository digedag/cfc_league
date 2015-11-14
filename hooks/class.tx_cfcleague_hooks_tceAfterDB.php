<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche <rene@system25.de>
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

require_once(tx_rnbase_util_Extensions::extPath('cfc_league') . 'class.tx_cfcleague_db.php');
tx_rnbase::load('tx_rnbase_util_Strings');

class tx_cfcleague_hooks_tceAfterDB {

	/**
	 * Nachbearbeitungen, unmittelbar NACHDEM die Daten gespeichert wurden.
	 *
	 * @param string $status new oder update
	 * @param string $table Name der Tabelle
	 * @param int $id UID des Datensatzes
	 * @param array $fieldArray Felder des Datensatzes, die sich ändern
	 * @param tce_main $tcemain
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$tcemain) {
		if($table == 'tx_cfcleague_profiles') {
			if(array_key_exists('types', $fieldArray)){
				// Die Types werden zusätzlich in einer MM-Tabelle gespeichert
				$id = ($status == 'new') ? $tcemain->substNEWwithIDs[$id] : $id;
				if($status != 'new')
					tx_rnbase_util_DB::doDelete('tx_cfcleague_profiletypes_mm', 'uid_foreign='.$id, 0);
				$types = tx_rnbase_util_Strings::intExplode(',', $fieldArray['types']);
				$i = 0;
				foreach($types As $type) {
					if(!intval($type)) continue;
					$values['uid_local'] = $type;
					$values['uid_foreign'] = $id;
					$values['tablenames'] = 'tx_cfcleague_profiles';
					$values['sorting_foreign'] = $i++;
					tx_rnbase_util_DB::doInsert('tx_cfcleague_profiletypes_mm', $values, 0);
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/hooks/class.tx_cfcleague_hooks_tceAfterDB.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/hooks/class.tx_cfcleague_hooks_tceAfterDB.php']);
}

?>