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
use System25\T3sports\Module\Searcher\ProfileSearcher;
use System25\T3sports\Module\Utility\TeamInfo;
use System25\T3sports\Utility\Misc;
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
 * Submodul: Hinzufügen von vorhandenen Spielern zu einem Team.
 */
class ProfileAdd
{
    public $mod;

    /**
     * Ausführung des Requests.
     * Das Team muss bekannt sein.
     *
     * @param IModule $module
     * @param Team $currTeam
     * @param TeamInfo $teamInfo
     *
     * @return string
     */
    public function handleRequest($module, team $currTeam, TeamInfo $teamInfo)
    {
        $this->mod = $module;

        if ($teamInfo->isTeamFull()) {
            // Kann nix mehr angelegt werden
            return $this->mod->doc->section('Message:', $GLOBALS['LANG']->getLL('msg_maxPlayers'), 0, 1, IModFunc::ICON_WARN);
        }

        // ggf. Daten im Request verarbeiten
        $out .= $this->handleAddProfiles($currTeam, $teamInfo);
        $out .= $this->handleNewProfiles($currTeam, $teamInfo);
        $currTeam->reset();
        $teamInfo->refresh();
        $out .= $teamInfo->getInfoTable($this->mod->doc);
        $out .= $this->showAddProfiles($currTeam, $teamInfo);

        return $out;
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
     * @param TeamInfo $teamInfo
     *
     * @return string
     */
    protected function showAddProfiles(Team $currTeam, TeamInfo $teamInfo)
    {
        $options = [
            'checkbox' => 1,
        ];

        // Todo: wir müssen wissen, welche Teil des Teams selectiert ist
        $profiles = $currTeam->getPlayers();
        foreach ($profiles as $profile) {
            $options['dontcheck'][$profile->getUid()] = $GLOBALS['LANG']->getLL('msg_profile_already_joined');
        }

        $searcher = $this->getProfileSearcher($options);
        $tableForm = '<div style="margin-top:10px">'.$searcher->getSearchForm().'</div>';
        $tableForm .= $searcher->getResultList();
        if ($searcher->getSize()) {
            $tableForm .= $this->getFormTool()->createSelectByArray('profileType', '', ProfileCreate::getProfileTypeArray());
            // Button für Zuordnung
            $tableForm .= $this->getFormTool()->createSubmit(
                'profile2team',
                $GLOBALS['LANG']->getLL('label_join_profiles'),
                '',
                ['class' => 'btn btn-primary btn-sm']
            );
        }
        // Ein Formular für die Neuanlage
        $tableForm .= $this->getCreateForm();
        // Jetzt noch die Team-Liste
        $teamTable = $teamInfo->getTeamTable($this->mod->getDoc());

        $content = '<div class="row">
<div class="col-sm-6">'.$tableForm.'</div>
<div class="col-sm-6">'.$teamTable.'</div>
</div>
';

        return $content;
    }

    /**
     * Blendet ein kleines Formular für die Neuanlage einer Person ein.
     */
    protected function getCreateForm()
    {
        global $LANG;

        if (!ProfileCreate::isProfilePage($this->mod->getPid())) {
            $content = $this->mod->getDoc()->section('Message:', $LANG->getLL('msg_pageNotAllowed'), 0, 1, IModFunc::ICON_WARN);

            return $content;
        }
        $arr = [
            [
                $LANG->getLL('label_firstname'),
                $LANG->getLL('label_lastname'),
                '&nbsp;',
                '&nbsp;',
            ],
        ];
        $row = [];
        $i = 1;
        $row[] = $this->getFormTool()->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][first_name]', '', 10);
        $row[] = $this->getFormTool()->createTxtInput('data[tx_cfcleague_profiles][NEW'.$i.'][last_name]', '', 10);
        $row[] = $this->getFormTool()->createSelectByArray('data[tx_cfcleague_profiles][NEW'.$i.'][type]', '', ProfileCreate::getProfileTypeArray());
        $row[] = $this->getFormTool()->createSubmit('newprofile2team', $GLOBALS['LANG']->getLL('btn_create'), $GLOBALS['LANG']->getLL('msg_CreateProfiles')).$this->getFormTool()->createHidden('data[tx_cfcleague_profiles][NEW'.$i.'][pid]', $this->mod->getPid());
        $arr[] = $row;
        $tables = tx_rnbase::makeInstance(Tables::class);
        $formTable = $tables->buildTable($arr);

        $out = '<hr /><div class="form-group">
      <label>'.$LANG->getLL('label_create_profile4team').'</label>
      '.$formTable.'</div>';

        return $out;
    }

    /**
     * Add profiles to a team.
     *
     * @param Team $currTeam
     * @param TeamInfo $teamInfo
     *
     * @return string
     */
    protected function handleNewProfiles(Team $currTeam, TeamInfo $teamInfo)
    {
        $profile2team = strlen(T3General::_GP('newprofile2team')) > 0; // Wurde der Submit-Button gedrückt?
        $out = '';
        if (!$profile2team) {
            return $out;
        }
        $request = T3General::_GP('data');

        $profiles = [
            'tx_cfcleague_profiles' => $request['tx_cfcleague_profiles'],
        ];

        $out = ProfileCreate::createProfiles($profiles, $currTeam, $teamInfo);

        return $out;
    }

    /**
     * Name is required.
     *
     * @param $profileArr
     *
     * @return bool
     */
    public function isValidProfile($profileArr)
    {
        return strlen($profileArr['last_name']) > 0;
    }

    /**
     * Add profiles to a team.
     *
     * @param Team $currTeam
     * @param TeamInfo $teamInfo
     *
     * @return string
     */
    protected function handleAddProfiles(Team $currTeam, TeamInfo $teamInfo)
    {
        $out = '';
        $profile2team = strlen(T3General::_GP('profile2team')) > 0; // Wurde der Submit-Button gedrückt?
        if ($profile2team) {
            $entryUids = T3General::_GP('checkEntry');
            if (!is_array($entryUids) || !count($entryUids)) {
                $out = $GLOBALS['LANG']->getLL('msg_no_profile_selected').'<br/><br/>';
            } else {
                $type = (int) T3General::_GP('profileType');
                if (1 == $type) {
                    if ($teamInfo->get('freePlayers') < count($entryUids)) {
                        // Team ist schon voll
                        $out = $GLOBALS['LANG']->getLL('msg_maxPlayers').'<br/><br/>';
                    } else {
                        // Die Spieler hinzufügen
                        $this->addProfiles2Team($currTeam, 'players', $entryUids);
                        $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
                    }
                } elseif (2 == $type) {
                    // Die Trainer hinzufügen
                    $this->addProfiles2Team($currTeam, 'coaches', $entryUids);
                    $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
                } else {
                    // Die Trainer hinzufügen
                    $this->addProfiles2Team($currTeam, 'supporters', $entryUids);
                    $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined').'<br/><br/>';
                }
            }
        }

        return (strlen($out)) ? $this->mod->getDoc()->section($GLOBALS['LANG']->getLL('message').':', $out, 0, 1, IModFunc::ICON_INFO) : '';
    }

    /**
     * Fügt Personen einem Team hinzu.
     *
     * @param Team $currTeam
     * @param string $profileCol
     * @param array $entryUids
     */
    protected function addProfiles2Team(Team $currTeam, $profileCol, $entryUids)
    {
        $playerUids = implode(',', Misc::mergeArrays(Strings::intExplode(',', $currTeam->getProperty($profileCol)), $entryUids));
        $data = [];
        $data['tx_cfcleague_teams'][$currTeam->getUid()][$profileCol] = $playerUids;

        reset($data);
        $tce = Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
        $currTeam->setProperty($profileCol, $playerUids);
    }

    /**
     * Get a profile searcher.
     *
     * @param array $options
     *
     * @return ProfileSearcher
     */
    protected function getProfileSearcher($options)
    {
        $searcher = tx_rnbase::makeInstance(ProfileSearcher::class, $this->mod, $options);

        return $searcher;
    }
}
