<?php

namespace System25\T3sports\Controller\Team;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModFunc;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Utility\T3General;
use Sys25\RnBase\Utility\Strings;
use Sys25\RnBase\Database\Connection;
use System25\T3sports\Utility\Misc;
use tx_cfcleague_mod1_decorator;
use tx_cfcleague_models_Team as Team;
use tx_cfcleague_models_TeamNoteType as TeamNoteType;
use tx_cfcleague_util_ServiceRegistry as ServiceRegistry;
use tx_rnbase;


/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2021 Rene Nitzsche (rene@system25.de)
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
 * Submodul: Bearbeiten von TeamNotes.
 */
class TeamNotes
{
    protected $mod;

    /**
     * Ausführung des Requests.
     * Das Team muss bekannt sein.
     *
     * @param IModule $module
     * @param \tx_cfcleague_models_team $currTeam
     *
     * @return string
     */
    public function handleRequest($module, $currTeam, $teamInfo)
    {
        $this->mod = $module;
        $this->pid = $module->getPid();
        $this->modName = $module->getName();

        // Tasks:
        // 1. Alle Team-Notizen des Teams anzeigen
        // SELECT * FROM notizen where team=123
        // Notizen nach Typ anzeigen
        $srv = ServiceRegistry::getTeamService();
        $types = $srv->getNoteTypes();
        if (!count($types)) {
            $content .= $this->mod->doc->section($GLOBALS['LANG']->getLL('message').':',
                $GLOBALS['LANG']->getLL('msg_create_notetypes'), 0, 1, IModFunc::ICON_INFO);

            return $content;
        }
        // Für jeden Typ einen Block anzeigen
        foreach ($types as $type) {
            $content .= $this->showTeamNotes($currTeam, $type);
        }
        // 2. Neue Notiz für einen Spiele anlegen lassen
        // ggf. Daten im Request verarbeiten
        // $entries = $currTeam->getPlayerNames(0,1);
        // $menu = tx_rnbase_util_FormTool::showMenu($this->pid, 'player', $this->modName, $entries);
        // $content .= $menu['menu'];
        // $player = $menu['value'];
        return $content;
    }

    /**
     * Liefert das FormTool.
     *
     * @return ToolBox
     */
    protected function getFormTool()
    {
        return $this->mod->getFormTool();
    }

    /**
     * Darstellung der gefundenen Personen.
     *
     * @param Team $currTeam
     * @param TeamNoteType $type
     *
     * @return string
     */
    protected function showTeamNotes(Team $currTeam, TeamNoteType $type)
    {
        $out = '<h2>'.$type->getLabel().'</h2>';
        if ($type->getDescription()) {
            $out .= '<p>'.$type->getDescription().'</p>';
        }

        // Alle Notes dieses Teams laden
        $srv = ServiceRegistry::getTeamService();
        $notes = $srv->getTeamNotes($currTeam, $type);

        $decor = tx_rnbase::makeInstance('tx_cfcleague_util_TeamNoteDecorator', $this->getFormTool());
        $columns = [
            'uid' => [
                'decorator' => $decor,
            ],
            'profile' => [
                'decorator' => $decor,
                'title' => 'label_name',
            ],
            'value' => [
                'decorator' => $decor,
                'title' => 'label_value',
            ],
            'mediatype' => [
                'decorator' => $decor,
                'title' => 'tx_cfcleague_team_notes.mediatype',
            ],
        ];
        $rows = tx_cfcleague_mod1_decorator::prepareTable($notes, $columns, $this->getFormTool(), $options);

        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
        $out .= $tables->buildTable($rows[0]);

        $options[ToolBox::OPTION_DEFVALS] = [
            'tx_cfcleague_team_notes' => [
                'team' => $currTeam->getUid(),
                'type' => $type->getUid(),
            ],
        ];
        // We use the mediatype from first entry
        if (count($notes)) {
            $options[ToolBox::OPTION_DEFVALS]['tx_cfcleague_team_notes']['mediatype'] = $notes[0]->getMediaType();
        }

        $options['title'] = $GLOBALS['LANG']->getLL('label_create_new').': '.$type->getLabel();
        // Zielseite muss immer die Seite des Teams sein
        $out .= $this->getFormTool()->createNewButton('tx_cfcleague_team_notes', $currTeam->getProperty('pid'), $options);

        return $out.'<br /><br />';
    }

    /**
     * @param Team $currTeam
     *
     * @return string
     */
    protected function handleAddProfiles($currTeam, $baseInfo)
    {
        $out = '';
        $profile2team = strlen(T3General::_GP('profile2team')) > 0; // Wurde der Submit-Button gedrückt?
        if ($profile2team) {
            $entryUids = T3General::_GP('checkEntry');
            if (!is_array($entryUids) || !count($entryUids)) {
                $out = $GLOBALS['LANG']->getLL('msg_no_profile_selected').'<br/><br/>';
            } else {
                if ($baseInfo['freePlayers'] < count($entryUids)) {
                    // Team ist schon voll
                    $out = $GLOBALS['LANG']->getLL('msg_maxPlayers').'<br/><br/>';
                } else {
                    // Die Spieler hinzufügen
                    $playerUids = implode(',', Misc::mergeArrays(Strings::intExplode(',', $currTeam->getProperty('players')), $entryUids));
                    $data['tx_cfcleague_teams'][$currTeam->getUid()]['players'] = $playerUids;

                    reset($data);

                    $tce = Connection::getInstance()->getTCEmain($data);
                    $tce->process_datamap();
                    $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
                    $currTeam->getProperty('players', $playerUids);
                }
            }
        }

        return (strlen($out)) ? $this->mod->getDoc()->section($GLOBALS['LANG']->getLL('message').':', $out, 0, 1, \tx_rnbase_mod_IModFunc::ICON_INFO) : '';
    }

    /**
     * Get a profile searcher.
     *
     * @param array $options
     *
     * @return \tx_cfcleague_mod1_profilesearcher
     */
    protected function getProfileSearcher(&$options)
    {
        $searcher = tx_rnbase::makeInstance('tx_cfcleague_mod1_profilesearcher', $this->mod, $options);

        return $searcher;
    }
}
