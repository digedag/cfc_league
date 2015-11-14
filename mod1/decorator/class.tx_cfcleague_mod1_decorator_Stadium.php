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


/**
 * Diese Klasse ist für die Darstellung von Stadien im Backend verantwortlich
 */
class tx_cfcleague_mod1_decorator_Stadium {
	public function __construct(tx_rnbase_mod_IModule $mod) {
		$this->mod = $mod;
	}

	/**
	 * Returns the module
	 * @return tx_rnbase_mod_IModule
	 */
	private function getModule() {
		return $this->mod;
	}
	/**
	 *
	 * @param string $value
	 * @param string $colName
	 * @param array $record
	 * @param tx_cfcleague_models_Stadium $item
	 */
	public function format($value, $colName, $record, $item) {
		$ret = $value;
		$formTool = $this->getModule()->getFormTool();
		if($colName == 'uid') {
			$ret = $item->getUid(). $formTool->createEditLink('tx_cfcleague_stadiums', $item->getUid(), 'Edit');
		}
		elseif($colName == 'address') {
			$ret = self::getAddress($item);
		}
		elseif($colName == 'longlat') {
			if($item->getCoords())
				$ret = $item->getLongitute() .'/' . $item->getLatitute();
		}
		return $ret;
	}
	private static function getAddress(tx_cfcleague_models_Stadium $item) {
		$ret = '';
		if($item->getStreet())
			$ret .= $item->getStreet().'<br />';
		if($item->getZip())
			$ret .= $item->getZip().' ';
		if($item->getCity())
			$ret .= $item->getCity().'<br/>';
		if($item->getCountryCode())
			$ret .= $item->getCountryCode();
		return $ret;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/decorator/class.tx_cfcleague_mod1_decorator_Stadium.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/decorator/class.tx_cfcleague_mod1_decorator_Stadium.php']);
}
?>