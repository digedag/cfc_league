<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche <rene@system25.de>
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

require_once(t3lib_extMgm::extPath('cfc_league') . 'class.tx_cfcleague_db.php');

class tx_cfcleague_mod1_tcehook {

  /**
   * Wir müssen dafür sorgen, daß die neuen IDs der Teams im Wettbewerb und Spielen
   * verwendet werden.
   */
  function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$tcemain)  {

    if($table == 'tx_cfcleague_teams') {
      $this->checkProfiles($incomingFieldArray,'players', $tcemain);
      $this->checkProfiles($incomingFieldArray,'coaches', $tcemain);
      $this->checkProfiles($incomingFieldArray,'supporters', $tcemain);
    }
  }

  /**
   * Prüft, ob im für den angegebenen Personentyp neue Personen angelegt wurden
   * und setzt die neuen UIDs.
   *
   * @param array $incomingFieldArray
   * @param string $profileType Spaltenname im Teamdatensatz (players,coaches,supporters)
   */
  function checkProfiles(&$incomingFieldArray, $profileType, &$tcemain) {
    if(strstr($incomingFieldArray[$profileType], 'NEW')) {
      $newProfileIds = t3lib_div::trimExplode(',', $incomingFieldArray[$profileType]);
      $profileUids = array();
      for($i=0; $i < count($newProfileIds); $i++) {
        if(strstr($newProfileIds[$i], 'NEW')) 
          $profileUid = $tcemain->substNEWwithIDs[$newProfileIds[$i]];
        else
          $profileUid = $newProfileIds[$i];
        // Wir übernehmen nur UIDs, die gefunden werden
        if($profileUid) $profileUids[] = $profileUid;
      }
      $incomingFieldArray[$profileType] = implode($profileUids, ',');
    }
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfcleague/mod1/class.tx_cfcleague_mod1_tcehook.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfcleague/mod1/class.tx_cfcleague_mod1_tcehook.php']);
}

?>