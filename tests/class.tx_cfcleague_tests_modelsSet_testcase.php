<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2013 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_cfcleague_models_Set');
tx_rnbase::load('tx_rnbase_tests_BaseTestCase');

class tx_cfcleague_tests_modelsSets_testcase extends tx_rnbase_tests_BaseTestCase {

	public function testBuildFromStringEmpty() {
		$sets = tx_cfcleague_models_Set::buildFromString('');
		$this->assertFalse($sets);
	}
	/**
	 * @dataProvider getStrings
	 */
	public function testBuildFromString($setString, $setCnt, $setResult) {
		$sets = tx_cfcleague_models_Set::buildFromString($setString);

		$this->assertEquals($setCnt, count($sets));
		$i = 0;
		foreach($sets As $set) {
			$expResult = $setResult[$i];
			$this->assertEquals($i+1, $set->getSet());
			$this->assertEquals($expResult[0], $set->getPointsHome());
			$this->assertEquals($expResult[1], $set->getPointsGuest());
			$this->assertEquals($expResult[0], $set->record['pointshome']);
			$this->assertEquals($expResult[1], $set->record['pointsguest']);
			$i++;
		}
	}
	public function getStrings() {
		return array(
			array('20:11;10:8', 2, array(array(20, 11), array(10, 8))),
			array('20:11', 1, array(array(20, 11))),
			array('b:11', 1, array(array(0, 11))),
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league_fe/tests/class.tx_cfcleague_tests_modelsSets_testcase.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league_fe/tests/class.tx_cfcleague_tests_modelsSets_testcase.php']);
}
?>