<?php

use Sys25\RnBase\Configuration\Processor;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2020 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse verwaltet die Erstellung von Spielern für Teams.
 */
class Tx_Cfcleague_Controller_Team_ProfileCreate
{
    private $doc;

    private $modName;

    /**
     * Verwaltet die Erstellung von Spielplänen von Ligen.
     *
     * @param tx_rnbase_mod_IModule $module
     * @param tx_cfcleague_models_Team $team
     */
    public function handleRequest($module, $team, $teamInfo)
    {
        // Zuerst mal müssen wir die passende Liga auswählen lassen:
        // Entweder global über die Datenbank oder die Ligen der aktuellen Seite
        // $pid, &$doc, &$formTool

        $pid = $module->getPid();
        $doc = $module->getDoc();
        $formTool = $module->getFormTool();
        $this->pid = $pid;
        $this->doc = $doc;
        $this->formTool = $formTool;
        $data = Tx_Rnbase_Utility_T3General::_GP('data');

        $content = $this->showCreateProfiles($data, $team, $teamInfo);

        return $content;
    }

    /**
     * Whether or not the given pid is inside the profile archive.
     *
     * @param int $pid
     *
     * @return bool
     */
    public static function isProfilePage($pid)
    {
        $rootPage = Processor::getExtensionCfgValue('cfc_league', 'profileRootPageId');
        $t3util = tx_rnbase::makeInstance('Tx_Cfcleague_Utility_TYPO3');
        $goodPages = $t3util->getPagePath($pid);

        return in_array($rootPage, $goodPages);
    }

    /**
     * @param array $data
     * @param tx_cfcleague_models_Team $team
     * @param tx_cfcleague_util_TeamInfo $teamInfo
     *
     * @return string
     */
    private function showCreateProfiles(&$data, $team, $teamInfo)
    {
        global $LANG;

        if (!self::isProfilePage($this->pid)) {
            $content = $this->doc->section('Message:', $LANG->getLL('msg_pageNotAllowed'), 0, 1, \tx_rnbase_mod_IModFunc::ICON_WARN);

            return $content;
        }

        if (is_array($data['tx_cfcleague_profiles'])) {
            $content .= $this->createProfiles($data, $team, $teamInfo);
            $team->reset();
        }

        if ($teamInfo->isTeamFull()) {
            // Kann nix mehr angelegt werden
            $content .= $this->doc->section('Message:', $LANG->getLL('msg_maxPlayers'), 0, 1, \tx_rnbase_mod_IModFunc::ICON_WARN);
        } else {
            $content .= $this->doc->section('Info:', $LANG->getLL('msg_checkPage').': <b>'.Tx_Rnbase_Backend_Utility::getRecordPath($this->pid, '', 0).'</b>', 0, 1, \tx_rnbase_mod_IModFunc::ICON_INFO);
            $content .= $teamInfo->getInfoTable($this->doc);
            // Wir zeigen 15 Zeilen mit Eingabefeldern
            $content .= $this->prepareInputTable($team, $teamInfo);
        }

        return $content;
    }

    /**
     * Erstellt eine Tabelle mit den schon vorhandenen Personen und den noch möglichen neuen
     * Personen.
     * Wenn keine Personen da sind, gibt es 15 Eingabefelder, sonst nur 5.
     *
     * @param \tx_cfcleague_models_Team $team
     * @param \tx_cfcleague_util_TeamInfo $teamInfo
     */
    protected function prepareInputTable($team, $teamInfo)
    {
        // Es werden zwei Tabellen erstellt
        $tableProfiles = $teamInfo->getTeamTable($this->doc);

        $arr = [
            [
                '&nbsp;',
                $GLOBALS['LANG']->getLL('label_firstname'),
                $GLOBALS['LANG']->getLL('label_lastname'),
                '&nbsp;',
            ],
        ];

        $maxFields = count($team->getPlayers()) > 5 ? 5 : 15;
        for ($i = 0; $i < $maxFields; ++$i) {
            $row = [];
            $row[] = $i + 1;
            $row[] = $this->formTool->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][first_name]', '', 10);
            $row[] = $this->formTool->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][last_name]', '', 10);
            $row[] = $this->formTool->createSelectByArray('data[tx_cfcleague_profiles][NEW'.$i.'][type]', '', self::getProfileTypeArray()).$this->formTool->createHidden('data[tx_cfcleague_profiles][NEW'.$i.'][pid]', $this->pid);
            $arr[] = $row;
        }
        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
        $tableForm = $tables->buildTable($arr);

        // Den Update-Button einfügen
        $button = $this->getFormTool()->createSubmit(
            'update',
            $GLOBALS['LANG']->getLL('btn_create'),
            $GLOBALS['LANG']->getLL('msg_CreateProfiles'),
            ['class' => 'btn btn-primary']
        );

        $content = '<div class="row">
<div class="col-sm-6">'.$tableForm.$button.'</div>
<div class="col-sm-6">'.$tableProfiles.'</div>
</div>
';

        return $content;
    }

    /**
     * Liefert ein Array der Personentypen.
     *
     * @return array
     */
    public static function getProfileTypeArray()
    {
        return [
            '1' => $GLOBALS['LANG']->getLL('label_profile_player'),
            '2' => $GLOBALS['LANG']->getLL('label_profile_coach'),
            '3' => $GLOBALS['LANG']->getLL('label_profile_supporter'),
        ];
    }

    /**
     * Erstellt die angeforderten Profile.
     *
     * @param array $profiles
     *            Array mit den Daten aus dem Request
     * @param tx_cfcleague_models_Team $team
     *            das aktuelle Team, dem die Personen zugeordnet werden
     * @param tx_cfcleague_util_TeamInfo $teamInfo
     */
    public static function createProfiles($profiles, $team, $teamInfo)
    {
        global $LANG;

        $maxCoaches = $teamInfo->get('maxCoaches');
        $maxPlayers = $teamInfo->get('maxPlayers');
        $profiles = $profiles['tx_cfcleague_profiles'];
        $content = '';

        $playerIds = []; // Sammelt die UIDs der neuen Spieler
        $coachIds = []; // Sammelt die UIDs der neuen Trainer
        $supportIds = []; // Sammelt die UIDs der neuen Betreuer
        $warnings = []; // Sammelt Profile die nicht angelegt werden konnten

        $data = [];
        foreach ($profiles as $uid => $profile) {
            // Zuerst Leerzeichen entfernen
            $profile['last_name'] = trim($profile['last_name']);
            $profile['first_name'] = trim($profile['first_name']);

            if (strlen($profile['last_name']) > 0) { // Nachname ist Pflichtfeld
                $type = $profile['type'];
                unset($profile['type']);

                // Darf dieses Profil noch angelegt werden?
                if ('1' == $type && (($teamInfo->getPlayerSize() + count($playerIds)) >= $maxPlayers)) { // Spieler
                    $warnings[] = $profile['last_name'].', '.$profile['first_name'];
                } elseif ('2' == $type && (($teamInfo->getCoachSize() + count($coachIds)) >= $maxCoaches)) { // Trainer
                    $warnings[] = $profile['last_name'].', '.$profile['first_name'];
                } else {
                    $profile['summary'] = '';
                    $profile['description'] = '';

                    // Jetzt das Array vorbereiten
                    $data['tx_cfcleague_profiles'][$uid] = $profile;
                    if ('1' == $type) {
                        $playerIds[] = $uid;
                    } elseif ('2' == $type) {
                        $coachIds[] = $uid;
                    } else {
                        $supportIds[] = $uid;
                    }
                }
            }
        }

        tx_rnbase::load('tx_cfcleague_util_Misc');
        // Die IDs der Trainer, Spieler und Betreuer mergen
        if (count($coachIds)) {
            $data['tx_cfcleague_teams'][$team->getUid()]['coaches'] =
                implode(',', tx_cfcleague_util_Misc::mergeArrays(Tx_Rnbase_Utility_Strings::intExplode(',', $team->getProperty('coaches')), $coachIds));
        }
        if (count($playerIds)) {
            $data['tx_cfcleague_teams'][$team->getUid()]['players'] =
                implode(',', tx_cfcleague_util_Misc::mergeArrays(Tx_Rnbase_Utility_Strings::intExplode(',', $team->getProperty('players')), $playerIds));
        }
        if (count($supportIds)) {
            $data['tx_cfcleague_teams'][$team->getUid()]['supporters'] =
                implode(',', tx_cfcleague_util_Misc::mergeArrays(Tx_Rnbase_Utility_Strings::intExplode(',', $team->getProperty('supporters')), $supportIds));
        }

        if (count($data)) {
            reset($data);
            $tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
            $tce->process_datamap();
            $content .= count($tce->errorLog) ? $LANG->getLL('msg_tce_errors') : $LANG->getLL('msg_profiles_created');
            $content .= '<br /><br />';
        } else {
            $content .= $LANG->getLL('msg_no_person_found').'<br /><br />';
        }

        if ($warnings) {
            $content .= '<b>'.$LANG->getLL('msg_profiles_warnings').'</b><br><ul><li>';
            $content .= implode('<li>', $warnings);
            $content .= '</ul>';
        }

        return $content;
    }

    /**
     * Returns the formtool.
     *
     * @return tx_rnbase_util_FormTool
     */
    private function getFormTool()
    {
        return $this->formTool;
    }
}
