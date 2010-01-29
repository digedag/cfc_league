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

tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model for a stadium.
 */
class tx_cfcleague_models_Stadium extends tx_rnbase_model_base {
	private static $instances = array();

	function getTableName(){return 'tx_cfcleague_stadiums';}

	/**
	 * Returns the stadium name
	 *
	 * @return string
	 */
	function getName() {
		return $this->record['name'];
	}
  /**
   * Returns address dataset or null
   * @return tx_cfcleague_models_Address or null
   */
  function getAddress() {
  	if(!$this->record['address'])
  		return null;
    $address = tx_rnbase::makeInstance('tx_cfcleague_models_Address', $this->record['address']);
		return $address->isValid() ? $address : null;
  }

	/**
	 * Liefert die Instance mit der übergebenen UID. Die Daten werden gecached, so daß
	 * bei zwei Anfragen für die selbe UID nur ein DB Zugriff erfolgt.
	 *
	 * @param int $uid
	 * @return tx_netfewo_models_Objekt
	 */
	static function getInstance($uid) {
		$uid = intval($uid);
		if(!$uid) throw new Exception('No uid for '.self::getTableName().' given!');
		if(!is_object(self::$instances[$uid])) {
			self::$instances[$uid] = new tx_cfcleague_models_Stadium($uid);
		}
		return self::$instances[$uid];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Stadium.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Stadium.php']);
}

?>