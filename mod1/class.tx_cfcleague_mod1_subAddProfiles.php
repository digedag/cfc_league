<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Rene Nitzsche (rene@system25.de)
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
tx_div::load('tx_rnbase_util_Misc');
tx_div::load('tx_cfcleague_mod1_profilesearcher');


/**
 * Submodul: Hinzufügen von vorhandenen Spielern zu einem Team
 */
class tx_cfcleague_mod1_subAddProfiles {
	var $mod;
	public function tx_cfcleague_mod1_subAddProfiles(&$mod) {
		$this->mod = $mod;
	}
	/**
	 * Ausführung des Requests. Das Team muss bekannt sein
	 *
	 * @param tx_cfcleague_models_betset $currTeam
	 * @return string
	 */
	public function handleRequest(&$currTeam, $baseInfo) {
		// ggf. Daten im Request verarbeiten
		$out .= $this->handleAddProfiles($currTeam, $baseInfo);
		$out .= $this->showAddProfiles($currTeam);
		return $out;
	}
	/**
	 * Liefert das FormTool
	 *
	 * @return tx_rnbase_util_FormTool
	 */
	private function getFormTool() {
		return $this->mod->formTool;
	}

	/**
	 * Darstellung der gefundenen Personen
	 *
	 * @param tx_cfcleague_team $currTeam
	 * @return string
	 */
	function showAddProfiles($currTeam) {
		
		$options['checkbox'] = 1;

		// Todo: wir müssen wissen, welche Teil des Teams selectiert ist
		$profiles = $currTeam->getPlayerNames();
		foreach($profiles As $profile) {
			$options['dontcheck'][$profile['uid']] = $GLOBALS['LANG']->getLL('msg_profile_already_joined');
		}

		$searcher = $this->getProfileSearcher($options);
		$out .= $searcher->getSearchForm();
		$out .= $this->mod->doc->spacer(15);
		$out.= $searcher->getResultList();
		if($searcher->getSize()) {
			// Button für Zuordnung
			$out .= $this->mod->formTool->createSubmit('profile2team', $GLOBALS['LANG']->getLL('label_join_players'), $GLOBALS['LANG']->getLL('msg_join_players'));
		}
		return $out;
	}
	/**
	 * Add matches to a betset
	 *
	 * @param tx_t3sportsbet_models_betset $currBetSet
	 * @return string
	 */
	private function handleAddProfiles(&$currTeam, $baseInfo) {
		$out = '';
		$profile2team = strlen(t3lib_div::_GP('profile2team')) > 0; // Wurde der Submit-Button gedrückt?
		if($profile2team) {
			$entryUids = t3lib_div::_GP('checkEntry');
			if(!is_array($entryUids) || ! count($entryUids)) {
				$out = $GLOBALS['LANG']->getLL('msg_no_profile_selected').'<br/><br/>';
			}
			else {
				if($baseInfo['freePlayers'] < count($entryUids)) {
					// Team ist schon voll
					$out = $GLOBALS['LANG']->getLL('msg_maxPlayers').'<br/><br/>';
				}
				else {
					// Die Spieler hinzufügen
					$playerUids = implode(',',tx_cfcleague_profile_create::mergeArrays(t3lib_div::intExplode(',',$currTeam->record['players']), $entryUids));
					$data['tx_cfcleague_teams'][$currTeam->record['uid']]['players'] = $playerUids;
							
					reset($data);
					$tce =& tx_cfcleague_db::getTCEmain($data);
					$tce->process_datamap();
					$out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
					$currTeam->record['players'] = $playerUids;
				}
			}
		}
		return (strlen($out)) ? $this->mod->doc->section($GLOBALS['LANG']->getLL('message').':',$out, 0, 1,ICON_INFO) : '';
	}
	
	/**
	 * Get a match searcher
	 *
	 * @param array $options
	 * @return tx_cfcleague_mod1_profilesearcher
	 */
	private function getProfileSearcher(&$options) {
		$clazz = tx_div::makeInstanceClassname('tx_cfcleague_mod1_profilesearcher');
		$searcher = new $clazz($this->mod, $options);
		return $searcher;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_subAddProfiles.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_subAddProfiles.php']);
}


?>
