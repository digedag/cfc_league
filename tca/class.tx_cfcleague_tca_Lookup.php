<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2019 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('Tx_Rnbase_Utility_Strings');

class tx_cfcleague_tca_Lookup
{
    /**
     * Returns all available profile types for a TCA select item.
     *
     * @param array $config
     */
    public function getProfileTypes(&$config)
    {
        tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
        $srv = tx_cfcleague_util_ServiceRegistry::getProfileService();
        $config['items'] = $srv->getProfileTypes4TCA();
    }

    public function getProfileTypeItems($uids)
    {
        tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
        $srv = tx_cfcleague_util_ServiceRegistry::getProfileService();

        return $srv->getProfileTypeItems4TCA($uids);
    }

    /**
     * Liefert die vorhandenen MatchNote-Typen.
     *
     * @param
     *            $config
     *
     * @return array
     */
    public function getMatchNoteTypes(&$config)
    {
        tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
        $srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
        $config['items'] = $srv->getMatchNoteTypes4TCA();
    }

    /**
     * Liefert die vorhandenen Liga Tabellen-Typen.
     *
     * @param
     *            $config
     *
     * @return array
     */
    public function getSportsTypes(&$config)
    {
        tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
        $srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
        $config['items'] = $srv->getSports4TCA();
    }

    /**
     * Liefert die möglichen Spielsysteme.
     * Das könnte man noch abhängig von der Sportart machen,
     * aber hier reicht es erstmal, wenn wir das über die
     * TCA erweitern können!
     *
     * @param
     *            $config
     *
     * @return array
     */
    public function getFormations(&$config)
    {
        tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');
        $items = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'];
        $config['items'] = $items;
    }

    public function getPointSystems(&$config)
    {
        $sports = $config['row']['sports'];
        // In der 7.6 ist immer ein Array im Wert
        $sports = is_array($sports) ? (count($sports) ? reset($sports) : false) : $sports;
        if ($sports) {
            $srv = tx_cfcleague_util_ServiceRegistry::getCompetitionService();
            $config['items'] = $srv->getPointSystems($sports);
        }

        // $config['items'] = array(
        // Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2'), 1),
        // Array(tx_rnbase_util_Misc::translateLLL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_3'), 0)
        // );
    }

    /**
     * Set possible stadiums for a match.
     * The stadiums are selected from home club.
     *
     * @param array $PA
     * @param t3lib_TCEforms $fobj
     */
    public function getStadium4Match($PA, $fobj)
    {
        $current = intval($PA['row']['arena']);
        $currentAvailable = false;
        $teamId = is_array($PA['row']['home']) ? reset($PA['row']['home']) : $PA['row']['home'];
        if ($teamId) {
            $srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
            $stadiums = $srv->getStadiums($teamId);
            foreach ($stadiums as $stadium) {
                $currentAvailable = $currentAvailable ? $currentAvailable : ($current == $stadium->getUid() || 0 == $current);
                $PA['items'][] = [
                    $stadium->getName(),
                    $stadium->getUid(),
                ];
            }
        }
        if (!$currentAvailable) {
            // Das aktuelle Stadium ist nicht mehr im Verein gefunden worden. Es wird daher nachgeladen
            $stadium = tx_rnbase::makeInstance('tx_cfcleague_models_Stadium', $current);
            if ($stadium->isValid()) {
                $PA['items'][] = [
                    $stadium->getName(),
                    $stadium->getUid(),
                ];
            }
        }
    }

    /**
     * Set possible logos for a team.
     * The logos are selected from club.
     *
     * @param array $PA
     * @param t3lib_TCEforms $fobj
     */
    public function getLogo4Team($PA, $fobj)
    {
        $clubId = is_array($PA['row']['club']) ? reset($PA['row']['club']) : $PA['row']['club'];
        if ($clubId) {
            $srv = tx_cfcleague_util_ServiceRegistry::getTeamService();
            // FIXME: Wenn Teams nicht global verwaltet werden, dann kommt der Verein nicht als UID
            // tx_cfcleague_club_1|M%C3%BCnchen%2C%20FC%20Bayern%20M%C3%BCnchen
            // Hier werden bei FAL Referenzen geliefert.
            // In der 7.6 wird bei Relationen nun wohl immer ein Array geliefert.
            $items = $srv->getLogos($clubId);
            // Bei FAL wird die UID der Referenz gespeichert. Damit können die zusätzlichen
            // Daten der Referenz verwendet werden.
            if (count($items)) {
                $PA['items'] = [];
            }
            foreach ($items as $item) {
                // $currentAvailable = $currentAvailable ? $currentAvailable : ($current == $item->getUid() || $current == 0);
                // Je nach Pflege der Daten sind unterschiedliche Felder gefüllt.
                $label = (
                    $item->getProperty('title') ? $item->getProperty('title') : (
                    $item->getProperty('name') ? $item->getProperty('name') : $item->getProperty('file')
                )
                );
                $PA['items'][] = array(
                    $label,
                    $item->getUid(),
                );
            }
        }
    }

    /**
     * Build the TCA entry for logo select-field in team record.
     * All logos from connected club are selectable.
     *
     * @return array
     */
    public static function getTeamLogoField()
    {
        $ret = tx_rnbase_util_TSFAL::getMediaTCA('logo', [
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.logo',
            'config' => [
                'size' => 1,
                'maxitems' => 1,
            ],
        ]);
        unset($ret['config']['filter']);
        foreach ($ret['config'] as $key => $field) {
            if (0 === strpos($key, 'foreign_')) {
                unset($ret['config'][$key]);
            }
        }
        // Die Auswahlbox rendern
        $ret['config']['type'] = 'select';
        $ret['config']['renderType'] = 't3sLogoSelect';
        // Die passenden Logos suchen
        $ret['config']['itemsProcFunc'] = 'tx_cfcleague_tca_Lookup->getLogo4Team';
        $ret['config']['maxitems'] = '1';
        $ret['config']['size'] = '1';
        $ret['config']['items'] = [
            ['', '0'],
        ];

        return $ret;
    }

    /**
     * Build a select box and an image preview of selected logo.
     *
     * @param array $PA
     * @param TYPO3\CMS\Backend\Form\Element\UserElement $fObj
     */
    public function getSingleField_teamLogo($PA, $fObj)
    {
        global $TYPO3_CONF_VARS;

        // In der 7.6 geht das nicht mehr...
        $tceforms = &$PA['pObj'];
        $table = $PA['table'];
        $field = $PA['field'];
        $row = $PA['row'];

        if (!$row['club']) {
            return $tceforms->sL('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_tca_noclubselected');
        }
        $config = $PA['fieldConf']['config'];

        $item = $tceforms->getSingleField_typeSelect($table, $field, $row, $PA);
        if ($row['logo']) {
            if (tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
                $item = '<table cellspacing="0" cellpadding="0" border="0">
								<tr><td style="padding-bottom:1em" colspan="2">'.$item.'</td></tr>';

                try {
                    // Im Logo wird die UID der Referenz zwischen Verein und dem Logo gespeichert
                    // Damit können die zusätzlichen Metadaten der Referenz genutzt werden
                    $fileObject = tx_rnbase_util_TSFAL::getFileReferenceById($row['logo']);
                    tx_rnbase::load('tx_rnbase_util_TSFAL');
                    $thumbs = tx_rnbase_util_TSFAL::createThumbnails(array(
                        $fileObject,
                    ));
                    $item .= '<tr><td>'.$thumbs[0].'</td>
										<td style="padding-left:1em"><table cellspacing="0" cellpadding="0" border="0">
										<tr><td style="padding-right:1em">Filename: </td><td>'.$fileObject->getProperty('identifier').'</td></tr>
										<tr><td style="padding-right:1em">Size: </td><td>'.\TYPO3\CMS\Core\Utility\GeneralUtility::formatSize($fileObject->getProperty('size')).'</td></tr>
										<tr><td style="padding-right:1em">Dimension: </td><td>'.$fileObject->getProperty('width').'x'.$fileObject->getProperty('height').' px</td></tr>
									</table></td></tr>';
                } catch (Exception $e) {
                    $item .= sprintf('<tr><td>Error rendering file with uid "%d"</td></tr>', $row['logo']);
                }
                $item .= '</table>';
            } else {
                // Logo anzeigen
                $currPic = t3lib_BEfunc::getRecord('tx_dam', $row['logo']);
                require_once tx_rnbase_util_Extensions::extPath('dam').'lib/class.tx_dam_tcefunc.php';
                $tcefunc = tx_rnbase::makeInstance('tx_dam_tcefunc');
                if (!method_exists($tcefunc, 'renderFileList')) {
                    return $item;
                }
                $tcefunc->tceforms = &$tceforms;
                $item .= $tcefunc->renderFileList(array(
                    'rows' => array(
                        $currPic,
                    ),
                ));
            }
        }

        return $item;
    }

    public static function getCountryField()
    {
        return array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_country',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array(
                        ' ',
                        '0',
                    ),
                ),
                'foreign_table' => 'static_countries',
                'foreign_table_where' => ' ORDER BY static_countries.cn_short_en ',
                'size' => 1,
                'default' => 54,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        );
    }

    /**
     * Die Spieler des Heimteams ermitteln
     * Used: Edit-Maske eines Spiels für Teamaufstellung und Match-Note.
     *
     * @param array $PA
     * @param TYPO3\CMS\Backend\Form\Element\UserElement $fobj
     */
    public function getPlayersHome4Match($PA, $fobj)
    {
        global $LANG;
        $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

        // tx_rnbase_util_Debug::debug(count($PA[items]), 'items cfcleague');
        $teamId = (int) $this->getPAValue($PA['row']['home']);
        $matchValue = $this->getPAValue($PA['row']['game']);
        if ($teamId) {
            // Abfrage aus Spieldatensatz
            // Es werden alle Spieler des Teams benötigt
            $players = $this->findProfiles($teamId, 'getPlayers');
            $PA['items'] = $players;
        } elseif ($matchValue) {
            // Abfrage aus MatchNote-Datensatz
            // Wenn wir die Match ID haben, können wir die Spieler auch so ermitteln
            // Es werden alle aufgestellten Spieler des Matches benötigt
            /* @var $match tx_cfcleague_models_Match */
            $match = tx_rnbase::makeInstance('tx_cfcleague_models_Match', $this->getRowId($matchValue));

            $players = tx_cfcleague_util_ServiceRegistry::getProfileService()->loadProfiles($match->getPlayersHome(true));
            // $players = $match->getPlayerNamesHome();
            $playerArr = [
                0 => '',
            ]; // empty item
            foreach ($players as $player) {
                $playerArr[] = [
                    $player->getName(true),
                    $player->getUid(),
                ];
            }
            sort($playerArr);
            $PA['items'] = $playerArr;
            // Abschließend noch den Spieler "Unbekannt" hinzufügen! Dieser ist nur in Matchnotes verfügbar
            $PA['items'][] = [
                $LANG->getLL('tx_cfcleague.unknown'),
                '-1',
            ];
        } else {
            $PA['items'] = [];
        }
    }

    /**
     * Die Spieler des Gastteams ermitteln
     * Used: Edit-Maske eines Spiels für Teamaufstellung und MatchNote.
     *
     * @param array $PA
     * @param TYPO3\CMS\Backend\Form\Element\UserElement $fobj
     */
    public function getPlayersGuest4Match($PA, $fobj)
    {
        global $LANG;
        $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');

        $teamId = (int) $this->getPAValue($PA['row']['guest']);
        $matchValue = $this->getPAValue($PA['row']['game']);

        if ($teamId) {
            $players = $this->findProfiles($teamId, 'getPlayers');
            $PA['items'] = $players;
        } elseif ($matchValue) {
            // Wenn wir die Match ID haben könne wir die Spieler auch so ermitteln
            /* @var $match tx_cfcleague_models_Match */
            $match = tx_rnbase::makeInstance('tx_cfcleague_models_Match', $this->getRowId($matchValue));
            // $players = $match->getPlayerNamesGuest();
            $players = tx_cfcleague_util_ServiceRegistry::getProfileService()->loadProfiles($match->getPlayersGuest(true));
            $playerArr = [
                0 => '',
            ]; // empty item
            foreach ($players as $player) {
                $playerArr[] = [
                    $player->getName(true),
                    $player->getUid(),
                ];
            }
            sort($playerArr);
            $PA['items'] = $playerArr;
            // Abschließend noch den Spieler "Unbekannt" hinzufügen!
            $PA['items'][] = [
                $LANG->getLL('tx_cfcleague.unknown'),
                '-1',
            ];
        } else { // Ohne Daten müssen wir alle Spieler löschen
            $PA['items'] = [];
        }
    }

    /**
     * Liefert die verschachtelte UID eines Strings der Form
     * tx_table_name_uid|valuestring.
     */
    private function getRowId($value)
    {
        if (is_array($value)) {
            return (int) $value['uid'];
        }
        $ret = Tx_Rnbase_Utility_Strings::trimExplode('|', $value);
        $ret = Tx_Rnbase_Utility_Strings::trimExplode('_', $ret[0]);

        return intval($ret[count($ret) - 1]);
    }

    /**
     * Find player of team
     * Used: Edit mask for team notes.
     *
     * @param array $PA
     * @param TYPO3\CMS\Backend\Form\Element\UserElement $fobj
     */
    public function getPlayers4Team(&$PA, $fobj)
    {
        global $LANG;
        $LANG->includeLLFile('EXT:cfc_league/locallang_db.xml');
        $column = 'team';
        if ($PA['row'][$column]) {
            $tablename = 'tx_cfcleague_team_notes';
            $tcaFieldConf = $GLOBALS['TCA'][$tablename]['columns'][$column]['config'];
            $fieldValue = $PA['row'][$column];
            $team = is_array($fieldValue) ? $fieldValue : Tx_Rnbase_Utility_Strings::trimExplode('|', $fieldValue);
            $team = $team[0];
            if ('db' == $tcaFieldConf['type']) {
                // FIXME: funktioniert nicht in 7.6!
                if (tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
                    throw new Exception("not implemented in 7.6\n".tx_rnbase_util_Debug::getDebugTrail());
                }
                $dbAnalysis = tx_rnbase::makeInstance('t3lib_loadDBGroup');
                $dbAnalysis->registerNonTableValues = 0;
                $dbAnalysis->start($team, $tcaFieldConf['allowed'], '', 0, $tablename, $tcaFieldConf);
                $valueArray = $dbAnalysis->getValueArray(false);
                // Abfrage aus Spieldatensatz
                // Es werden alle Spieler des Teams benötigt
                $team = $valueArray[0];
            }
            $players = $this->findProfiles($team, 'getPlayers');
            $players = array_merge($players, $this->findProfiles($team, 'getCoaches'));
            $players = array_merge($players, $this->findProfiles($team, 'getSupporters'));
            $PA['items'] = $players;
        } else {
            $PA['items'] = [];
        }
    }

    /**
     * Liefert die Spieler (uid und name) einer Mannschaft.
     * Die Spieler sind alphabetisch sortiert.
     *
     * @return []
     */
    private function findProfiles($teamId, $getter)
    {
        $rows = [];
        if (0 == intval($teamId)) {
            return $rows;
        }

        $team = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $teamId);
        /* @var $profile tx_cfcleague_models_Profile */
        $profiles = $team->$getter();
        foreach ($profiles as $profile) {
            $rows[] = array(
                $profile->getName(true),
                $profile->getUid(),
            );
        }
        sort($rows);

        return $rows;
    }

    /**
     * Die Trainer des Heimteams ermitteln.
     */
    public function getCoachesHome4Match($PA, $fobj)
    {
        $teamId = $this->getPAValue($PA['row']['home']);

        if ($teamId) {
            $coaches = $this->findCoaches($teamId);
            $PA['items'] = $coaches;
        }
    }

    /**
     * Die Trainer des Gastteams ermitteln.
     */
    public function getCoachesGuest4Match($PA, $fobj)
    {
        $teamId = $this->getPAValue($PA['row']['guest']);

        if ($teamId) {
            $coaches = $this->findCoaches($teamId);
            $PA['items'] = $coaches;
        }
    }

    /**
     * Liefert die Trainer (uid und name) einer Mannschaft.
     */
    private function findCoaches($teamId)
    {
        $rows = array();
        if (0 == intval($teamId)) {
            return $rows;
        }

        /* @var $team tx_cfcleague_models_Team */
        $team = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $teamId);
        /* @var $profile tx_cfcleague_models_Profile */
        $profiles = $team->getCoaches();
        $rows[] = [
            '',
            0,
        ]; // Leeres erstes Element
        foreach ($profiles as $profile) {
            $rows[] = array(
                $profile->getName(),
                $profile->getUid(),
            );
        }

        return $rows;
    }

    /**
     * Liefert die Teams eines Wettbewerbs.
     * Wird im Spiel-TCE-Dialog zur
     * Auswahl der Teams verwendet.
     *
     * @param array $PA
     * @param \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems $fobj
     */
    public function getTeams4Competition($PA, $fobj)
    {
        // Aktuellen Wettbewerb ermitteln, wenn 0 bleiben die Felder leer
        $compId = (int) $this->getPAValue($PA['row']['competition']);
        if ($compId) {
            $PA['items'] = $this->findTeams($compId);
        } else {
            $PA['items'] = [];
        }
    }

    /**
     * Sucht die Teams eines Wettbewerbs.
     *
     * @param int $competitionId
     * @param int complete_row wenn false wird nur Name und UID für SELECT-Box geliefert
     */
    private function findTeams($competitionId, $complete_row = '0')
    {
        /* @var $competition tx_cfcleague_models_Competition */
        $competition = tx_rnbase::makeInstance('tx_cfcleague_models_Competition', $competitionId);
        $teamNames = $competition->getTeamNames();
        $rows = [];
        foreach ($teamNames as $uid => $name) {
            $rows[] = [$name, $uid];
        }

        return $rows;
    }

    /**
     * Read value from PA.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function getPAValue($value)
    {
        return is_array($value) ? reset($value) : $value;
    }
}
