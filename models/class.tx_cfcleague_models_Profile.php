<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2009 Rene Nitzsche (rene@system25.de)
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

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

tx_div::load('tx_rnbase_model_base');

/**
 * Model für eine Person.
 */
class tx_cfcleague_models_Profile extends tx_rnbase_model_base {

	function getTableName(){return 'tx_cfcleague_profiles';}

	function getFirstName() {
		return $this->record['first_name'];
	}
	function getLastName() {
		return $this->record['last_name'];
	}
	/**
	 * Returns the profile name
	 *
	 * @param boolean $reverse
	 * @return string
	 */
	function getName($reverse=false) {
		return $reverse ? $this->getLastName() . ', ' . $this->getFirstName() : 
											$this->getFirstName() .' ' . $this->getLastName();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Profile.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Profile.php']);
}

?>