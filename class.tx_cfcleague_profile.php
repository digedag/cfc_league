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


define('TABLE_PROFILES', 'tx_cfcleague_profiles');

/**
 * Datenobjekt für eine Person in der Datenbank
 */
class tx_cfcleague_profile {
  var $uid;
  var $record;
  
  /**
   * Konstruktor erwartet eine UID der Liga
   */
  function tx_cfcleague_profile($uid){
    $this->uid = $uid;
//    $this->record = t3lib_BEfunc::getRecord(TABLE_TEAMS,$uid);
    $this->refresh();
  }

  /**
   * Lädt die Daten des Teams neu aus der Datenbank
   *
   */
  function refresh() {
    $this->record = t3lib_BEfunc::getRecord(TABLE_PROFILES, $this->uid);
  }

  /**
   * Liefert eine Kurzbeschreibung des Spielers
   *
   */
  function getInfoString() {
  	
  }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_profile.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_profile.php']);
}

?>
