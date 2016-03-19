<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (rene@system25.de)
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
 * Klasse für Datenbankabfragen
 */
class tx_cfcleague_db{
  /**
   * Stellt eine Anfrage an die DB und liefert die ermittelten Zeilen zurück
   * @param $what requested columns
   * @param $where
   * @param $from either the name of a table or an array with index 0 the from clause
   *              and index 1 the requested tablename
   */
  function queryDB($what, $where, $from=TABLE_GAMES, $groupBy = '', $orderBy = '', $debug=0, $limit = ''){
  	if($debug)
  		$time = microtime(true);
  	$tableName = $from;
    $fromClause = $from;
    if(is_array($from)){
      $tableName = $from[1];
      $fromClause = $from[0];
    }

    // Zur Where-Clause noch die gültigen Felder hinzufügen
    $where .= t3lib_BEfunc::deleteClause($tableName);

    if($debug) {
      $sql = $GLOBALS['TYPO3_DB']->SELECTquery($what, $fromClause, $where, $groupBy, $orderBy);
      tx_rnbase_util_Debug::debug($sql, 'SQL (tx_cfcleague_db)');
      tx_rnbase_util_Debug::debug(array($what, $from, $where), 'Params (tx_cfcleague_db)');
    }

    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
      $what,
      $fromClause,
      $where,
      $groupBy,
      $orderBy,
      $limit
    );

    $rows = array();
    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
      $rows[] = $row;
    }
    $GLOBALS['TYPO3_DB']->sql_free_result($res);

    if($debug)
      tx_rnbase_util_Debug::debug(count($rows), 'Rows retrieved (tx_cfcleague_db) Time: ' . (microtime(true) - $time) . 's');

    return $rows;
  }

  /**
   * Schickt einen Update an die Datenbank und schreibt die Aktion ins Log
   * Die Methode verwendet intern eine Instanz der TCEmain. Diese wird static
   * vorgehalten, so daß mehrmalige Aufrufe kein Problem sind
   * @param $tce eine Instanz der TCEmain, Sinnvoll wenn man viele Updates auf einmal ausführt
   */
  function updateDB($table, $uid, $dataArr){
    $tce =& tx_cfcleague_db::getTCEmain();
    $tce->updateDB($table, $uid, $dataArr);
  }



  /**
   * Liefert eine initialisierte TCEmain Instanz
   */
  function &getTCEmain($data = 0) {
    static $tce;

    if(!$tce || $data) {
      // Die TCEmain laden
      $tce = tx_rnbase::makeInstance('t3lib_tcemain');
      $tce->stripslashes_values = 0;
      // Wenn wir ein data-Array bekommen verwenden wir das
      $tce->start($data ? $data : Array(), Array());

      // set default TCA values specific for the user
      $TCAdefaultOverride = $GLOBALS['BE_USER']->getTSConfigProp('TCAdefaults');
      if (is_array($TCAdefaultOverride)) {
        $tce->setDefaultsFromUserTS($TCAdefaultOverride);
      }
    }
    return $tce;
  }

	/**
	 * Backend method to determine if a page is below a page
	 */
	public function getPagePath($uid, $clause='')   {
		$loopCheck = 100;
		$output = array(); // We return an array of uids
		$output[] = $uid;
		while ($uid!=0 && $loopCheck>0) {
			$loopCheck--;

			//'uid,pid,title,t3ver_oid,t3ver_wsid,t3ver_swapmode',
			$rows = tx_rnbase_util_DB::doSelect('*', 'pages', array(
				'where' => 'uid='.intval($uid).(strlen(trim($clause)) ? ' AND '.$clause : ''),
			));
			if(!empty($rows)) {
				$row = reset($rows);
				t3lib_BEfunc::workspaceOL('pages', $row);
				t3lib_BEfunc::fixVersioningPid('pages', $row);

				$uid = $row['pid'];
				$output[] = $uid;
			} else {
				break;
			}
		}
		return $output;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_db.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_db.php']);
}
