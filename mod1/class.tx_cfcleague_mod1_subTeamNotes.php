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

tx_rnbase::load('tx_rnbase_util_Misc');
tx_rnbase::load('tx_cfcleague_mod1_decorator');
tx_rnbase::load('tx_rnbase_util_Strings');


/**
 * Submodul: Bearbeiten von TeamNotes
 */
class tx_cfcleague_mod1_subTeamNotes {
	var $mod;

	/**
	 * Ausführung des Requests. Das Team muss bekannt sein
	 *
   * @param tx_rnbase_mod_IModule $module
	 * @param tx_cfcleague_team $currTeam
	 * @return string
	 */
	public function handleRequest($module, $currTeam, $teamInfo) {
		$this->mod = $module;
		$this->pid = $module->getPid();
		$this->modName = $module->getName();

		// Tasks:
		// 1. Alle Team-Notizen des Teams anzeigen
		// SELECT * FROM notizen where team=123
		// Notizen nach Typ anzeigen
		$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
		$types = $srv->getNoteTypes();
		if(!count($types)) {
			$content.=$this->mod->doc->section($GLOBALS['LANG']->getLL('message').':', $GLOBALS['LANG']->getLL('msg_create_notetypes'), 0, 1, ICON_INFO);
			return $content;
		}
		// Für jeden Typ einen Block anzeigen
		foreach($types As $type) {
			$content .= $this->showTeamNotes($currTeam, $type);
		}
		// 2. Neue Notiz für einen Spiele anlegen lassen
		// ggf. Daten im Request verarbeiten
//		$entries = $currTeam->getPlayerNames(0,1);
//		$menu = tx_rnbase_util_FormTool::showMenu($this->pid, 'player', $this->modName, $entries);
//		$content .= $menu['menu'];
//		$player = $menu['value'];
		return $content;
	}
	/**
	 * Liefert das FormTool
	 *
	 * @return tx_rnbase_util_FormTool
	 */
	private function getFormTool() {
		return $this->mod->getFormTool();
	}

	/**
	 * Darstellung der gefundenen Personen
	 *
	 * @param tx_cfcleague_team $currTeam
	 * @param tx_cfcleague_models_TeamNoteType $type
	 * @return string
	 */
	function showTeamNotes($currTeam, $type) {
		$out = '<h2>'.$type->getLabel().'</h2>';
		if($type->getDescription())
			$out .= '<p>'.$type->getDescription().'</p>';

		// Alle Notes dieses Teams laden
		$srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
		$notes = $srv->getTeamNotes($currTeam, $type);

		$decor = tx_rnbase::makeInstance('tx_cfcleague_util_TeamNoteDecorator', $this->getFormTool());
		$columns = array(
			'uid' => array('decorator' => $decor),
			'profile' => array('decorator' => $decor, 'title' => 'label_name'),
			'value' => array('decorator' => $decor, 'title' => 'label_value'),
			'mediatype' => array('decorator' => $decor, 'title' => 'tx_cfcleague_team_notes.mediatype'),
		);
		$rows = tx_cfcleague_mod1_decorator::prepareTable($notes, $columns, $this->getFormTool(), $options);
		$out .= $this->mod->getDoc()->table($rows[0]);

		// We use the mediatype from first entry
		if(count($notes))
			$options['params'] = '&mediatype='.$notes[0]->getMediaType();
		$options['params'] .= '&type='.$type->getUid();
		$options['params'] .= '&team='.$currTeam->getUid();
		$options['title'] = $GLOBALS['LANG']->getLL('label_create_new') .': ' . $type->getLabel();
		// Zielseite muss immer die Seite des Teams sein
		$out .= $this->getFormTool()->createNewButton('tx_cfcleague_team_notes', $currTeam->record['pid'], $options);
		return $out.'<br /><br />';
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
					$playerUids = implode(',', tx_cfcleague_profile_create::mergeArrays(tx_rnbase_util_Strings::intExplode(',', $currTeam->record['players']), $entryUids));
					$data['tx_cfcleague_teams'][$currTeam->record['uid']]['players'] = $playerUids;

					reset($data);
					$tce =& tx_cfcleague_db::getTCEmain($data);
					$tce->process_datamap();
					$out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
					$currTeam->record['players'] = $playerUids;
				}
			}
		}
		return (strlen($out)) ? $this->mod->getDoc()->section($GLOBALS['LANG']->getLL('message').':', $out, 0, 1, ICON_INFO) : '';
	}

	/**
	 * Get a profile searcher
	 *
	 * @param array $options
	 * @return tx_cfcleague_mod1_profilesearcher
	 */
	private function getProfileSearcher(&$options) {
		$searcher = tx_rnbase::makeInstance('tx_cfcleague_mod1_profilesearcher', $this->mod, $options);
		return $searcher;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_subTeamNotes.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/class.tx_cfcleague_mod1_subTeamNotes.php']);
}


?>
