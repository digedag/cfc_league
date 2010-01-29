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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_rnbase_model_base');

/**
 * Model für einen Verein.
 */
class tx_cfcleague_models_Club extends tx_rnbase_model_base {

	function getTableName(){return 'tx_cfcleague_club';}

	function getName() {
		return $this->record['name'];
	}
	function getNameShort() {
		return $this->record['short_name'];
	}

	/**
	 * Returns the city
	 * @return string
	 */
	public function getCity() {
		return $this->record['city'];
	}

	/**
	 * Returns the zip
	 * @return string
	 */
	public function getZip() {
		return $this->record['zip'];
	}
	/**
	 * Returns the street
	 * @return string
	 */
	public function getStreet() {
		return $this->record['street'];
	}

	/**
	 * Returns the url of the first club logo.
	 *
	 * @return string
	 */
	public function getFirstLogo() {
		if($this->record['dam_logo']) {
			$damPics = tx_dam_db::getReferencedFiles('tx_cfcleague_club', $this->uid, 'dam_images');
			if(list($uid, $filePath) = each($damPics['files'])) {
				return $filePath;
			}
		}
		return '';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Club.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Club.php']);
}

?>