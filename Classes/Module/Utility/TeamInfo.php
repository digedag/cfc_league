<?php

namespace System25\T3sports\Module\Utility;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModFunc;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Frontend\Request\Parameters;
use Sys25\RnBase\Utility\Strings;
use Sys25\RnBase\Utility\T3General;
use System25\T3sports\Model\Team;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2021 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse wird im BE verwendet und liefert Informationen über ein Team.
 */
class TeamInfo
{
    private $baseInfo = [];

    private $team;

    /** @var ToolBox */
    private $formTool;

    /**
     * @param Team $team
     * @param ToolBox $formTool
     */
    public function __construct(Team $team, ToolBox $formTool)
    {
        $this->formTool = $formTool;
        $this->init($team);
    }

    private function init($team)
    {
        global $TCA;
        $this->team = $team;

        $this->baseInfo['maxCoaches'] = intval($TCA['tx_cfcleague_teams']['columns']['coaches']['config']['maxitems']);
        $this->baseInfo['maxPlayers'] = intval($TCA['tx_cfcleague_teams']['columns']['players']['config']['maxitems']);
        $this->baseInfo['maxSupporters'] = intval($TCA['tx_cfcleague_teams']['columns']['supporters']['config']['maxitems']);

        $this->baseInfo['freePlayers'] = $this->baseInfo['maxPlayers'] - $this->getPlayerSize();
        $this->baseInfo['freeCoaches'] = $this->baseInfo['maxCoaches'] - $this->getPlayerSize();
        $this->baseInfo['freeSupporters'] = $this->baseInfo['maxSupporters'] - $this->getPlayerSize();
    }

    public function refresh()
    {
        $this->init($this->team);
    }

    public function get($item)
    {
        return $this->baseInfo[$item];
    }

    /**
     * @return ToolBox
     */
    public function getFormTool()
    {
        return $this->formTool;
    }

    /**
     * Liefert true, wenn keine Personen zugeordnet werden können.
     *
     * @return bool
     */
    public function isTeamFull()
    {
        return $this->baseInfo['freePlayers'] < 1 && $this->baseInfo['freeCoaches'] < 1 && $this->baseInfo['freeSupporters'] < 1;
    }

    /**
     * Liefert die Informationen, über den Zustand des Teams.
     *
     * @return string
     */
    public function getInfoTable(IModule $module)
    {
        $lang = $module->getLanguageService();
        $tableLayout = [
            'table' => ['<table class="table table-striped table-hover table-condensed">', '</table>'],
            'defRow' => [ // Format für 1. Zeile
                'tr' => [
                    '<tr>', '</tr>',
                ],
                'defCol' => [
                    '<td>', '</td>',
                ],
            ],
        ];
        $arr = [];
        $arr[] = [
            $lang->getLL('msg_number_of_players'),
            $this->baseInfo['freePlayers'],
        ];
        $arr[] = [
            $lang->getLL('msg_number_of_coaches'),
            $this->baseInfo['freeCoaches'],
        ];
        $arr[] = [
            $lang->getLL('msg_number_of_supporters'),
            $this->baseInfo['freeSupporters'],
        ];

        /** @var Tables $tables */
        $tables = tx_rnbase::makeInstance(Tables::class);

        return $tables->buildTable($arr, $tableLayout);
    }

    /**
     * Liefert eine Tabelle mit den aktuellen Spielern, Trainern und Betreuern des Teams.
     *
     * @param object $doc
     *
     * @return string
     */
    public function getTeamTable(IModule $module)
    {
        $arr = [
            [
                '&nbsp;',
                '###LABEL_FIRSTNAME###',
                '###LABEL_LASTNAME###',
                '&nbsp;',
                '&nbsp;',
            ],
        ];

        $this->addProfiles($arr, $this->getCoachNames($this->getTeam()), '###LABEL_PROFILE_COACH###', 'coach');
        $this->addProfiles($arr, $this->getPlayerNames($this->getTeam()), '###LABEL_PROFILE_PLAYER###', 'player');
        $this->addProfiles($arr, $this->getSupporterNames($this->getTeam()), '###LABEL_PROFILE_SUPPORTER###', 'supporter');

        $tables = tx_rnbase::makeInstance(Tables::class);
        $tableProfiles = count($arr) > 1 ? $tables->buildTable($arr) : '';

        return $tableProfiles;
    }

    /**
     * Bearbeitung von Anweisungen aus dem Request.
     */
    public function handleRequest()
    {
        $lang = $this->getFormTool()->getLanguageService();
        $data = Parameters::getPostOrGetParameter('remFromTeam');
        if (!is_array($data)) {
            return '';
        }

        $fields = [
            'player' => 'players',
            'coach' => 'coaches',
            'supporter' => 'supporters',
        ];
        $team = $this->getTeam();
        $tceData = [];
        foreach ($data as $type => $uid) {
            $profileUids = $team->getProperty($fields[$type]);
            if (!$profileUids) {
                continue;
            }

            if (T3General::inList($profileUids, $uid)) {
                $profileUids = T3General::rmFromList($uid, $profileUids);
                $tceData['tx_cfcleague_teams'][$team->getUid()][$fields[$type]] = $profileUids;
                $team->setProperty($fields[$type], $profileUids);
            }
        }

        $tce = Connection::getInstance()->getTCEmain($tceData);
        $tce->process_datamap();

        return $this->getFormTool()
            ->getDoc()
            ->section('Info:', $lang->getLL('msg_removedProfileFromTeam'), 0, 1, IModFunc::ICON_INFO);
    }

    /**
     * Add profiles to profile list.
     *
     * @param array $arr
     * @param array $profiles
     * @param string $label
     */
    private function addProfiles(&$arr, $profileNames, $label, $type)
    {
        $lang = $this->getFormTool()->getLanguageService();
        $i = 1;
        if ($profileNames) {
            foreach ($profileNames as $uid => $prof) {
                if (1 == $i) {
                    $arr[] = [
                        '',
                        '&nbsp;',
                        '',
                        '',
                        '',
                    ]; // Leere Zeile als Trenner;
                }
                $row = [];
                $row[] = 1 == $i++ ? $label : '';
                $row[] = $prof['first_name'];
                $row[] = $prof['last_name'];
                $row[] = $this->getFormTool()->createEditLink('tx_cfcleague_profiles', $uid);
                $row[] = $this->getFormTool()->createSubmit(
                    'remFromTeam['.$type.']',
                    $uid,
                    $lang->getLL('msg_remove_team_'.$type),
                    [
                        ToolBox::OPTION_ICON_NAME => 'actions-delete',
                        ToolBox::OPTION_HOVER_TEXT => 'Remove from Team',
                    ]
                );
                $arr[] = $row;
            }
        }
    }

    /**
     * @return Team
     */
    private function getTeam()
    {
        return $this->team;
    }

    /**
     * Liefert die Anzahl der zugeordneten Spieler.
     */
    public function getPlayerSize()
    {
        $value = $this->team->getProperty('players');

        return $value ? count(Strings::intExplode(',', $value)) : 0;
    }

    /**
     * Liefert die Anzahl der zugeordneten Trainer.
     */
    public function getCoachSize()
    {
        $value = $this->team->getProperty('coaches');

        return $value ? count(Strings::intExplode(',', $value)) : 0;
    }

    /**
     * Liefert die Anzahl der zugeordneten Betreuer.
     *
     * @return int
     */
    public function getSupporterSize()
    {
        $value = $this->team->getProperty('supporters');

        return $value ? count(Strings::intExplode(',', $value)) : 0;
    }

    /**
     * Liefert die Namen alle Spieler des Teams als Array.
     * Key ist die ID des Profils.
     *
     * @param Team $team
     *
     * @return array
     */
    protected function getPlayerNames($team)
    {
        $name = [];
        foreach ($team->getPlayers() as $profile) {
            $name[$profile->getUid()] = $profile->getProperty();
        }

        return $name;
    }

    /**
     * Liefert die Namen der Trainer des Teams als Array.
     * Key ist die ID des Profils.
     *
     * @param Team $team
     *
     * @return array
     */
    protected function getCoachNames($team)
    {
        $name = [];
        foreach ($team->getCoaches() as $profile) {
            $name[$profile->getUid()] = $profile->getProperty();
        }

        return $name;
    }

    /**
     * Liefert die Namen der Betreuer des Teams als Array.
     * Key ist die ID des Profils.
     *
     * @param Team $team
     *
     * @return array
     */
    protected function getSupporterNames($team)
    {
        $name = [];
        foreach ($team->getSupporters() as $profile) {
            $name[$profile->getUid()] = $profile->getProperty();
        }

        return $name;
    }
}
