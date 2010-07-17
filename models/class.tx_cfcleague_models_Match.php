<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Rene Nitzsche (rene@system25.de)
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
 * Model for a match.
 */
class tx_cfcleague_models_Match extends tx_rnbase_model_base {

	private $sets;

	function getTableName(){return 'tx_cfcleague_games';}

	/**
	 * Return sets if available
	 * @return array[tx_cfcleague_models_Set]
	 */
	public function getSets() {
		if(!is_array($this->sets)) {
			tx_rnbase::load('tx_cfcleague_models_Set');
			$this->sets = tx_cfcleague_models_Set::buildFromString($this->record['sets']);
			$this->sets = $this->sets ? $this->sets : array();
		}
		return $this->sets;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Match.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Match.php']);
}

?>