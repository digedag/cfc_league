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


require_once('class.tx_cfcleague.php');


/**
 * Datenobjekt für eine Liga in der Datenbank
 */
class tx_cfcleague_saison{
  var $uid;
  var $record;
  var $competitionNames;
  var $competitions;
  /**
   * Konstruktor erwartet eine UID der Liga
   */
  function tx_cfcleague_saison($uid){
    $this->uid = $uid;
    $this->record = t3lib_BEfunc::getRecord('tx_cfcleague_saison', $uid);
  }

  /**
   * Liefert die Namen der zugeordneten Wettbewerbe als Array. Key ist die ID des Wettbewerbs
   */
  function getCompetitionNames() {

    if(!is_array($this->competitionNames)){
      $rows = tx_cfcleague_db::queryDB('uid, name', '1', 'tx_cfcleague_competition', '', 'sorting', 0);
      $this->competitionNames= array();
      foreach($rows As $row) {
        $this->competitionNames[$row['uid']] = $row['name'];
      }
    }
    return $this->competitionNames;

  }

  /**
   * Liefert die zugeordneten Wettbewerbe als Array.
   */
  function getCompetitions() {

    if(!is_array($this->competitions)){
      $this->competitions = tx_cfcleague_db::queryDB('*', 'saison=' . $this->uid , 'tx_cfcleague_competition', '', 'sorting', 0);
    }
    return $this->competitions;

  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_saison.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_saison.php']);
}
?>
