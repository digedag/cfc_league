<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2014 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('Tx_Rnbase_Utility_Strings');

/**
 * Model for a match set.
 */
class tx_cfcleague_models_Set extends tx_rnbase_model_base {
	protected $p1, $p2, $set;
	public function __construct($set, $p1=0, $p2=0) {
		$this->setResult($set, $p1, $p2);
	}
	public function setResult($set, $p1, $p2) {
		$this->set = $set;
		$this->p1 = $p1;
		$this->p2 = $p2;
		$this->record = array(
			'set' => $this->set,
			'pointshome' => $this->p1,
			'pointsguest' => $this->p2,
		);
	}
	public function getSet() {
		return $this->set;
	}
	public function getPointsHome() {
		return $this->p1;
	}
	public function getPointsGuest() {
		return $this->p2;
	}
	public static function buildFromString($sets) {
		if(!$sets) return false;
		$sets = preg_split("/[\s]*[;,|][\s]*/", $sets);
//		$sets = Tx_Rnbase_Utility_Strings::trimExplode(';', $sets);
		$ret = array();
		foreach($sets As $idx => $setStr) {
			list($p1, $p2) = Tx_Rnbase_Utility_Strings::intExplode(':', $setStr);
			$ret[] = tx_rnbase::makeInstance('tx_cfcleague_models_Set', $idx+1, $p1, $p2);
		}
		return $ret;
	}
	public function getColumnNames() {
		return array_keys($this->record);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Set.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/models/class.tx_cfcleague_models_Set.php']);
}

