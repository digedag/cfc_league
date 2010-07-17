<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Rene Nitzsche (rene@system25.de)
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


/**
 * Model für einen Satz.
 */
class tx_cfcleague_models_Set {
	private $p1, $p2;
	public $record = array();
	public function __construct($p1=0, $p2=0) {
		$this->setResult($p1, $p2);
	}
	public function setResult($p1, $p2) {
		$this->p1 = $p1;
		$this->p2 = $p2;
		$this->record['home'] = $p1;
		$this->record['guest'] = $p2;
	}
	public function getPointsHome() {
		return $this->p1;
	}
	public function getPointsGuest() {
		return $this->p2;
	}
	public static function buildFromString($sets) {
		if(!$sets) return false;
		$sets = t3lib_div::trimExplode(';', $sets);
		$ret = array();
		foreach($sets As $setStr) {
			list($p1, $p2) = t3lib_div::intExplode(':', $setStr);
			$ret[] = tx_rnbase::makeInstance('tx_cfcleague_models_Set', $p1, $p2);
		}
		return $ret;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Set.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Set.php']);
}

?>