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
	public function handleRequest(&$currTeam, $teamInfo) {
		if($teamInfo->isTeamFull()) {
			// Kann nix mehr angelegt werden
			return $this->mod->doc->section('Message:',$GLOBALS['LANG']->getLL('msg_maxPlayers'),0,1,ICON_WARN);
		}

		$out = $this->mod->doc->section('Message:',$teamInfo->getInfoTable($this->mod->doc),0,1, ICON_INFO);
		// ggf. Daten im Request verarbeiten
		$out .= $this->handleAddProfiles($currTeam, $teamInfo);
		$out .= $this->showAddProfiles($currTeam, $teamInfo);
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
	 * @param tx_cfcleague_models_Team $currTeam
	 * @param tx_cfcleague_util_TeamInfo $teamInfo
	 * @return string
	 */
	function showAddProfiles($currTeam, $teamInfo) {
		
		$options['checkbox'] = 1;

		// Todo: wir müssen wissen, welche Teil des Teams selectiert ist
		$profiles = $currTeam->getPlayerNames();
		foreach($profiles As $profile) {
			$options['dontcheck'][$profile['uid']] = $GLOBALS['LANG']->getLL('msg_profile_already_joined');
		}

		$searcher = $this->getProfileSearcher($options);
		$tableForm = '<div style="margin-top:10px">'.$searcher->getSearchForm().'</div>';
		$tableForm .= $this->mod->doc->spacer(15);
		$tableForm.= $searcher->getResultList();
		if($searcher->getSize()) {
			tx_rnbase::load('tx_cfcleague_mod1_modTeamsProfileCreate');
			$tableForm .= $this->mod->formTool->createSelectSingleByArray('profileType', '',tx_cfcleague_mod1_modTeamsProfileCreate::getProfileTypeArray());
			// Button für Zuordnung
			$tableForm .= $this->mod->formTool->createSubmit('profile2team', $GLOBALS['LANG']->getLL('label_join_profiles'));
		}
		// Ein Formular für die Neuanlage
		$tableForm .= $this->getCreateForm();
		// Jetzt noch die Team-Liste
		$teamTable = $teamInfo->getTeamTable($this->mod->doc);

		$tableLayout = Array (
			'table' => Array('<table class="typo3-dblist" width="100%" cellspacing="0" cellpadding="0" border="0">', '</table><br/>'),
			'defRow' => Array ( // Formate für alle Zeilen
				'defCol' => Array('<td valign="top" style="padding:0 5px;">','</td>') // Format für jede Spalte in jeder Zeile
			),
		);

		$content = $this->mod->doc->table(Array(Array($tableForm,$teamTable)), $tableLayout);

		return $content;
	}
	/**
	 * Blendet ein kleines Formular für die Neuanlage einer Person ein
	 *
	 */
	private function getCreateForm() {
		$out = '<div>Neu</div>';
		return $out;
	}
	/**
	 * Add profiles to a betset
	 *
	 * @param tx_cfcleague_models_Team $currTeam
	 * @param tx_cfcleague_util_TeamInfo $baseInfo
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
				$type = intval(t3lib_div::_GP('profileType'));
				if($type == 1) {
					if($baseInfo->get('freePlayers') < count($entryUids)) {
						// Team ist schon voll
						$out = $GLOBALS['LANG']->getLL('msg_maxPlayers').'<br/><br/>';
					}
					else {
						// Die Spieler hinzufügen
						$this->addProfiles2Team($currTeam, 'players', $entryUids);
						$out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
					}
				}
				elseif($type == 2) {
					// Die Trainer hinzufügen
					$this->addProfiles2Team($currTeam, 'coaches', $entryUids);
					$out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
				}
				else {
					// Die Trainer hinzufügen
					$this->addProfiles2Team($currTeam, 'supporters', $entryUids);
					$out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
				}
			}
		}
		return (strlen($out)) ? $this->mod->doc->section($GLOBALS['LANG']->getLL('message').':',$out, 0, 1,ICON_INFO) : '';
	}
	/**
	 * Fügt Personen einem Team hinzu
	 *
	 * @param tx_cfcleague_models_Team $currTeam
	 * @param string $profileCol
	 * @param array $entryUids
	 */
	private function addProfiles2Team(&$currTeam, $profileCol, $entryUids) {
		tx_rnbase::load('tx_cfcleague_util_Misc');
		$playerUids = implode(',',tx_cfcleague_util_Misc::mergeArrays(t3lib_div::intExplode(',',$currTeam->record[$profileCol]), $entryUids));
		$data['tx_cfcleague_teams'][$currTeam->record['uid']][$profileCol] = $playerUids;
				
		reset($data);
		$tce =& tx_cfcleague_db::getTCEmain($data);
		$tce->process_datamap();
		$currTeam->record[$profileCol] = $playerUids;
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
