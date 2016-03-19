<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_util_DB');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');


class tx_cfcleague_mod1_AjaxTicker {
  /**
   * Save Tickermessage...
   *
   * @param array $params
   * @param TYPO3AJAX $ajaxObj
   */
  public function ajaxSaveTickerMessage($params, &$ajaxObj) {
		$tickerMessage = trim(strip_tags(Tx_Rnbase_Utility_T3General::_POST('value')));
		$t3Time = intval(Tx_Rnbase_Utility_T3General::_POST('t3time'));
		$t3match = intval(Tx_Rnbase_Utility_T3General::_POST('t3match'));

		if(!is_object($GLOBALS['BE_USER'])) {
  		$ajaxObj->addContent('message', 'No BE user found!');
			return;
		}

		if(!$tickerMessage || !$t3match) {
  		$ajaxObj->addContent('message', 'Invalid request!');
			return;
		}
		$matchRecord = t3lib_BEfunc::getRecord('tx_cfcleague_games', $t3match);

		$record = array(
			'comment' => $tickerMessage,
			'game' => $t3match,
			'type' => 100,
			'minute' => $t3Time,
			'pid' => $matchRecord['pid'],
		);
		$data = array('tx_cfcleague_match_notes' => array('NEW1' => $record));
		$tce =& tx_rnbase_util_DB::getTCEmain($data);
		$tce->process_datamap();

		$GLOBALS['LANG']->includeLLFile('EXT:cfc_league/mod1/locallang.xml');
		$ajaxObj->addContent('message', $GLOBALS['LANG']->getLL('msg_sendInstant'));
  }
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_AjaxTicker.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_AjaxTicker.php']);
}


?>
