<?php

namespace System25\T3sports\Controller\Team;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModFunc;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Strings;
use Sys25\RnBase\Utility\T3General;
use System25\T3sports\Model\Team;
use System25\T3sports\Model\TeamNoteType;
use System25\T3sports\Module\Decorator\TeamNoteDecorator;
use System25\T3sports\Module\Searcher\ProfileSearcher;
use System25\T3sports\Utility\Misc;
use System25\T3sports\Utility\ServiceRegistry;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2023 Rene Nitzsche (rene@system25.de)
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
    /**
     * @var IModule
     */
    private $mod;

    /**
     * Ausführung des Requests.
     * Das Team muss bekannt sein.
     *
     * @param IModule $module
     * @param Team $currTeam
     *
     * @return string
     */
    public function handleRequest(IModule $module, Team $currTeam, $teamInfo)
    {
        $this->mod = $module;
        $lang = $module->getLanguageService();

        // Tasks:
        // 1. Alle Team-Notizen des Teams anzeigen
        // SELECT * FROM notizen where team=123
        // Notizen nach Typ anzeigen
        $srv = ServiceRegistry::getTeamService();
        $types = $srv->getNoteTypes();
        if (!count($types)) {
            $content .= $this->mod->getDoc()->section($lang->getLL('message').':',
                $lang->getLL('msg_create_notetypes'), 0, 1, IModFunc::ICON_INFO);

            return $content;
        }
        $content = '';
        // Für jeden Typ einen Block anzeigen
        foreach ($types as $type) {
            $content .= $this->showTeamNotes($currTeam, $type);
        }
        // 2. Neue Notiz für einen Spiele anlegen lassen
        // ggf. Daten im Request verarbeiten
        // $entries = $currTeam->getPlayerNames(0,1);
        // $menu = ToolBox::showMenu($this->pid, 'player', $this->modName, $entries);
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

        $decor = tx_rnbase::makeInstance(TeamNoteDecorator::class, $this->getFormTool());
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

        /** @var Tables $tables */
        $tables = tx_rnbase::makeInstance(Tables::class);
        $rows = $tables->prepareTable($notes, $columns, $this->getFormTool(), []);
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
    protected function handleAddProfiles(Team $currTeam, $baseInfo)
    {
        $lang = $this->mod->getLanguageService();
        $out = '';
        $profile2team = strlen(T3General::_GP('profile2team')) > 0; // Wurde der Submit-Button gedrückt?
        if ($profile2team) {
            $entryUids = T3General::_GP('checkEntry');
            if (!is_array($entryUids) || !count($entryUids)) {
                $out = $lang->getLL('msg_no_profile_selected').'<br/><br/>';
            } else {
                if ($baseInfo['freePlayers'] < count($entryUids)) {
                    // Team ist schon voll
                    $out = $lang->getLL('msg_maxPlayers').'<br/><br/>';
                } else {
                    // Die Spieler hinzufügen
                    $playerUids = implode(',', Misc::mergeArrays(Strings::intExplode(',', $currTeam->getProperty('players')), $entryUids));
                    $data['tx_cfcleague_teams'][$currTeam->getUid()]['players'] = $playerUids;

                    reset($data);

                    $tce = Connection::getInstance()->getTCEmain($data);
                    $tce->process_datamap();
                    $out .= $lang->getLL('msg_profiles_joined').'<br/><br/>';
                    $currTeam->setProperty('players', $playerUids);
                }
            }
        }

        return (strlen($out)) ? $this->mod->getDoc()->section(
            $lang->getLL('message'
            ).':', $out, 0, 1, IModFunc::ICON_INFO) : '';
    }

    /**
     * Get a profile searcher.
     *
     * @param array $options
     *
     * @return ProfileSearcher
     */
    protected function getProfileSearcher(&$options)
    {
        $searcher = tx_rnbase::makeInstance(ProfileSearcher::class, $this->mod, $options);

        return $searcher;
    }
}
