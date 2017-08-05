<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2017 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_cfcleague_mod1_profilesearcher');
tx_rnbase::load('Tx_Cfcleague_Controller_Team_ProfileCreate');
tx_rnbase::load('Tx_Rnbase_Utility_Strings');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');

/**
 * Submodul: Hinzufügen von vorhandenen Spielern zu einem Team
 */
class Tx_Cfcleague_Controller_Team_ProfileAdd
{

    var $mod;

    /**
     * Ausführung des Requests.
     * Das Team muss bekannt sein
     *
     * @param tx_rnbase_mod_IModule $module
     * @param tx_cfcleague_models_Team $currTeam
     * @param tx_cfcleague_util_TeamInfo $teamInfo
     * @return string
     */
    public function handleRequest($module, $currTeam, $teamInfo)
    {
        $this->mod = $module;

        if ($teamInfo->isTeamFull()) {
            // Kann nix mehr angelegt werden
            return $this->mod->doc->section('Message:', $GLOBALS['LANG']->getLL('msg_maxPlayers'), 0, 1, ICON_WARN);
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
     * Liefert das FormTool
     *
     * @return tx_rnbase_util_FormTool
     */
    protected function getFormTool()
    {
        return $this->mod->getFormTool();
    }

    /**
     * Darstellung der gefundenen Personen
     *
     * @param tx_cfcleague_models_Team $currTeam
     * @param tx_cfcleague_util_TeamInfo $teamInfo
     * @return string
     */
    protected function showAddProfiles($currTeam, $teamInfo)
    {
        $options = [
            'checkbox' => 1
        ];

        // Todo: wir müssen wissen, welche Teil des Teams selectiert ist
        $profiles = $currTeam->getPlayers();
        foreach ($profiles as $profile) {
            $options['dontcheck'][$profile->getUid()] = $GLOBALS['LANG']->getLL('msg_profile_already_joined');
        }

        $searcher = $this->getProfileSearcher($options);
        $tableForm = '<div style="margin-top:10px">' . $searcher->getSearchForm() . '</div>';
        $tableForm .= $searcher->getResultList();
        if ($searcher->getSize()) {
            $tableForm .= $this->getFormTool()->createSelectByArray('profileType', '', Tx_Cfcleague_Controller_Team_ProfileCreate::getProfileTypeArray());
            // Button für Zuordnung
            $tableForm .= $this->getFormTool()->createSubmit('profile2team', $GLOBALS['LANG']->getLL('label_join_profiles'));
        }
        // Ein Formular für die Neuanlage
        $tableForm .= $this->getCreateForm();
        // Jetzt noch die Team-Liste
        $teamTable = $teamInfo->getTeamTable($this->mod->getDoc());

        $tableLayout = Array(
            'table' => Array(
                '<table class="typo3-dblist table" width="100%" cellspacing="0" cellpadding="0" border="0">',
                '</table><br/>'
            ),
            'defRow' => Array( // Formate für alle Zeilen
                'defCol' => Array(
                    '<td valign="top" style="padding:0 5px;">',
                    '</td>'
                ) // Format für jede Spalte in jeder Zeile
            )
        );

        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
        $content = $tables->buildTable([
            [
                $tableForm,
                $teamTable
            ]
        ], $tableLayout);

        return $content;
    }

    /**
     * Blendet ein kleines Formular für die Neuanlage einer Person ein
     */
    protected function getCreateForm()
    {
        global $LANG;

        if (! Tx_Cfcleague_Controller_Team_ProfileCreate::isProfilePage($this->mod->getPid())) {
            $content = $this->mod->getDoc()->section('Message:', $LANG->getLL('msg_pageNotAllowed'), 0, 1, ICON_WARN);
            return $content;
        }
        $arr = Array(
            Array(
                $LANG->getLL('label_firstname'),
                $LANG->getLL('label_lastname'),
                '&nbsp;',
                '&nbsp;'
            )
        );
        $row = array();
        $i = 1;
        $row[] = $this->getFormTool()->createTxtInput('data[tx_cfcleague_profiles][NEW' . $i . '][first_name]', '', 10);
        $row[] = $this->getFormTool()->createTxtInput('data[tx_cfcleague_profiles][NEW' . $i . '][last_name]', '', 10);
        $row[] = $this->getFormTool()->createSelectByArray('data[tx_cfcleague_profiles][NEW' . $i . '][type]', '', Tx_Cfcleague_Controller_Team_ProfileCreate::getProfileTypeArray());
        $row[] = $this->getFormTool()->createSubmit('newprofile2team', $GLOBALS['LANG']->getLL('btn_create'), $GLOBALS['LANG']->getLL('msg_CreateProfiles')) . $this->getFormTool()->createHidden('data[tx_cfcleague_profiles][NEW' . $i . '][pid]', $this->mod->getPid());
        $arr[] = $row;
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
        $formTable = $tables->buildTable($arr);

        $out .= $this->mod->getDoc()->section($LANG->getLL('label_create_profile4team'), $formTable, 0, 1);
        return $out;
    }

    /**
     * Add profiles to a team
     *
     * @param tx_cfcleague_models_Team $currTeam
     * @param tx_cfcleague_util_TeamInfo $teamInfo
     * @return string
     */
    protected function handleNewProfiles($currTeam, $teamInfo)
    {
        $profile2team = strlen(Tx_Rnbase_Utility_T3General::_GP('newprofile2team')) > 0; // Wurde der Submit-Button gedrückt?
        $out = '';
        if (! $profile2team) {
            return $out;
        }
        $request = Tx_Rnbase_Utility_T3General::_GP('data');

        $profiles = [
            'tx_cfcleague_profiles' => $request['tx_cfcleague_profiles'],
        ];

        $out = Tx_Cfcleague_Controller_Team_ProfileCreate::createProfiles($profiles, $currTeam, $teamInfo);
        return $out;
    }

    /**
     * Name is required
     *
     * @param
     *            $profileArr
     * @return boolean
     */
    public function isValidProfile($profileArr)
    {
        return strlen($profileArr['last_name']) > 0;
    }

    /**
     * Add profiles to a team
     *
     * @param tx_cfcleague_models_Team $currTeam
     * @param tx_cfcleague_util_TeamInfo $baseInfo
     * @return string
     */
    protected function handleAddProfiles(&$currTeam, $baseInfo)
    {
        $out = '';
        $profile2team = strlen(Tx_Rnbase_Utility_T3General::_GP('profile2team')) > 0; // Wurde der Submit-Button gedrückt?
        if ($profile2team) {
            $entryUids = Tx_Rnbase_Utility_T3General::_GP('checkEntry');
            if (! is_array($entryUids) || ! count($entryUids)) {
                $out = $GLOBALS['LANG']->getLL('msg_no_profile_selected') . '<br/><br/>';
            } else {
                $type = (int) Tx_Rnbase_Utility_T3General::_GP('profileType');
                if ($type == 1) {
                    if ($baseInfo->get('freePlayers') < count($entryUids)) {
                        // Team ist schon voll
                        $out = $GLOBALS['LANG']->getLL('msg_maxPlayers') . '<br/><br/>';
                    } else {
                        // Die Spieler hinzufügen
                        $this->addProfiles2Team($currTeam, 'players', $entryUids);
                        $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined') . '<br/><br/>';
                    }
                } elseif ($type == 2) {
                    // Die Trainer hinzufügen
                    $this->addProfiles2Team($currTeam, 'coaches', $entryUids);
                    $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined') . '<br/><br/>';
                } else {
                    // Die Trainer hinzufügen
                    $this->addProfiles2Team($currTeam, 'supporters', $entryUids);
                    $out .= $GLOBALS['LANG']->getLL('msg_profiles_joined') . '<br/><br/>';
                }
            }
        }
        return (strlen($out)) ? $this->mod->getDoc()->section($GLOBALS['LANG']->getLL('message') . ':', $out, 0, 1, ICON_INFO) : '';
    }

    /**
     * Fügt Personen einem Team hinzu
     *
     * @param tx_cfcleague_models_Team $currTeam
     * @param string $profileCol
     * @param array $entryUids
     */
    protected function addProfiles2Team(&$currTeam, $profileCol, $entryUids)
    {
        tx_rnbase::load('tx_cfcleague_util_Misc');
        $playerUids = implode(',', tx_cfcleague_util_Misc::mergeArrays(Tx_Rnbase_Utility_Strings::intExplode(',', $currTeam->getProperty($profileCol)), $entryUids));
        $data['tx_cfcleague_teams'][$currTeam->getUid()][$profileCol] = $playerUids;

        reset($data);
        $tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
        $currTeam->setProperty($profileCol, $playerUids);
    }

    /**
     * Get a match searcher
     *
     * @param array $options
     * @return tx_cfcleague_mod1_profilesearcher
     */
    protected function getProfileSearcher(&$options)
    {
        $searcher = tx_rnbase::makeInstance('tx_cfcleague_mod1_profilesearcher', $this->mod, $options);
        return $searcher;
    }
}

