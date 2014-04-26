<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2014 Rene Nitzsche (rene@system25.de)
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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model for age group
 */
class tx_cfcleague_models_Group extends tx_rnbase_model_base {
	
  function getTableName(){return 'tx_cfcleague_group';}

  /**
   * Liefert den Namen
   *
   * @return string
   */
  function getName() {
  	return $this->record['name'];
  }

  /**
   * Returns an instance
   * @param int $uid
   * @return tx_cfcleague_models_Group
   */
  static public function getInstance($uid) {
  	return tx_cfcleague_util_ServiceRegistry::getGroupService()->getGroupByUid($uid);
  }
  
	/**
	 * statische Methode, die ein Array mit Instanzen dieser Klasse liefert. Ist der übergebene
	 * Parameter leer, dann werden alle Datensätze aus der Datenbank geliefert. Ansonsten 
	 * wird ein String mit der uids der gesuchten Saisons erwartet ('2,4,10,...').
	 */
	static function findAll($uids) {
		// SELECT * FROM tx_cfcleague_group WHERE uid IN ($uid)
		$options['where'] = (is_string($uids) && strlen($uids) > 0) ? 'uid IN (' . $uids .')' : '1';
		$options['orderby'] = 'sorting asc';
		$options['wrapperclass'] = 'tx_cfcleaguefe_models_group';
		return  tx_rnbase_util_DB::doSelect('*', 'tx_cfcleague_group', $options);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Group.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Group.php']);
}

?>