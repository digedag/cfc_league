<?php

namespace System25\T3sports\Controller\Competition;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\BaseModule;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\T3General;
use Sys25\RnBase\Utility\TYPO3;
use System25\T3sports\Sports\ServiceLocator;
use tx_cfcleague_models_Competition as Competition;
use tx_cfcleague_util_ServiceRegistry as ServiceRegistry;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2021 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse verwaltet die Bearbeitung der Spieltage.
 */
class MatchEdit
{
    private $sportsServiceLocator;
    protected $formTool;

    public function __construct()
    {
        $this->sportsServiceLocator = new ServiceLocator();
    }

    /**
     * Bearbeitung von Spielen.
     * Es werden die Paaren je Spieltag angezeigt.
     *
     * @param IModule $module
     */
    public function main(IModule $module, $current_league)
    {
        global $LANG;

        $this->setModule($module);
        $pid = $module->getPid();
        $this->id = $module->getPid();
        $this->doc = $module->getDoc();

        $formTool = $module->getFormTool();
        $this->formTool = $formTool;
        $LANG->includeLLFile('EXT:cfc_league/Resources/Private/Language/locallang_db.xml');

        // Zuerst mal müssen wir die passende Liga auswählen lassen:
        $content = '';

        if (!count($current_league->getRounds())) {
            $content .= $LANG->getLL('no_round_in_league');
            $content .= '<br /><br />';
            $content .= $this->getFooter($current_league, 0, $pid, $formTool);

            return $content;
        }
        $currentTeam = $this->makeTeamSelector($content, $pid, $current_league);
        // Jetzt den Spieltag wählen lassen
        $current_round = 0;
        if (null == $currentTeam) {
            $current_round = $this->getSelector()->showRoundSelector($content, $pid, $current_league);
        }
        // Add button to set all games to "Finished"
        $content .= '<script type="text/javascript">
                        function setStatusFinished() {
                          var x = document.querySelectorAll("select[name*=\'status\']");
                          var i;
                          for (i = 0; i < x.length; i++) {
                            x[i].value = "2";
                          }
                        }
                    </script>';
        $content .= ' <input type="button" class="btn btn-default btn-sm" name="setStatus" value="'.$LANG->getLL('btn_statusToFinished').'" onclick="setStatusFinished()"><br><br>';

        $content .= '<div class="cleardiv"/>';
        $data = T3General::_GP('data');
        // Haben wir Daten im Request?
        if (is_array($data['tx_cfcleague_games'])) {
            $this->updateMatches($data);
        }

        $matches = $this->findMatches($currentTeam, $current_round, $current_league);
        $arr = $this->createTableArray($matches, $current_league);

        $tables = tx_rnbase::makeInstance(Tables::class);
        $content .= $tables->buildTable($arr[0]);

        // Den Update-Button einfügen
        $content .= $formTool->createSubmit('update', $LANG->getLL('btn_update'), $GLOBALS['LANG']->getLL('btn_update_msgEditGames'));
        // $content .= '<input type="submit" name="update" value="'.$LANG->getLL('btn_update').'" onclick="return confirm('.$GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('btn_update_msgEditGames')).')">';
        if ($arr[1]) { // Hat ein Team spielfrei?
            $content .= '<h3 style="margin-top:10px">'.$LANG->getLL('msg_free_of_play').'</h3><ul>';
            foreach ($arr[1] as $freeOfPlay) {
                $content .= '<li>'.$freeOfPlay['team'].$freeOfPlay['match_edit'].'</li>';
            }
            $content .= '</ul>';
        }
        $content .= '<br /><br />';
        $content .= $this->getFooter($current_league, $current_round, $pid, $formTool);

        return $content;
    }

    /**
     * @param \tx_cfcleague_models_Team $currentTeam
     * @param int $current_round
     * @param Competition $current_league
     */
    private function findMatches($currentTeam, $current_round, $current_league)
    {
        // Mit Matchtable nach Spielen suchen
        $service = ServiceRegistry::getMatchService();
        $matchTable = $service->getMatchTableBuilder();
        $matchTable->setCompetitions($current_league->getUid());

        $matches = [];
        if (null == $currentTeam) {
            // Nun zeigen wir die Spiele des Spieltags
            $matchTable->setRounds($current_round);
        } else {
            $matchTable->setTeams($currentTeam->getUid());
        }

        $fields = $options = [];
        $options['orderby']['MATCH.DATE'] = 'ASC';
        $matchTable->getFields($fields, $options);
        $matches = $service->search($fields, $options);

        return $matches;
    }

    private function makeTeamSelector(&$content, $pid, $current_league)
    {
        global $LANG;
        $teamOptions = [];
        $teamOptions['selectorId'] = 'teamMatchEdit';
        $teamOptions['noLinks'] = true;
        $teamOptions['firstItem']['id'] = -1;
        $teamOptions['firstItem']['label'] = $LANG->getLL('label_roundmode');

        return $this->getSelector()->showTeamSelector($content, $pid, $current_league, $teamOptions);
    }

    /**
     * @return \tx_cfcleague_selector
     */
    private function getSelector()
    {
        if (!is_object($this->selector)) {
            $this->selector = tx_rnbase::makeInstance('tx_cfcleague_selector');
            $this->selector->init($this->getModule()
                ->getDoc(), $this->getModule());
        }

        return $this->selector;
    }

    /**
     * @return BaseModule
     */
    private function getModule()
    {
        return $this->module;
    }

    private function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @param Competition $currentLeague
     * @param int $current_round
     * @param mixed $pid
     * @param ToolBox $formTool
     *
     * @return string
     */
    protected function getFooter(Competition $currentCompetition, $current_round, $pid, ToolBox $formTool)
    {
        $rounds = $currentCompetition->getRounds();
        $roundName = '';
        foreach ($rounds as $round) {
            if ($round['round'] == $current_round) {
                $roundName = $round['round_name'];
            }
        }
        $params = [];
        $params[ToolBox::OPTION_DEFVALS] = [
            'tx_cfcleague_games' => [
                'competition' => $currentCompetition->getUid(),
                'round' => $current_round,
                'round_name' => $roundName,
            ],
        ];
        $params[ToolBox::OPTION_TITLE] = $GLOBALS['LANG']->getLL('label_create_match');
        $content = $formTool->createNewLink('tx_cfcleague_games', $pid, $GLOBALS['LANG']->getLL('label_create_match'), $params);

        return $content;
    }

    /**
     * Liefert die passenden Überschrift für die Tabelle.
     *
     * @param int $parts
     * @param Competition $competition
     *
     * @return array
     */
    private function getHeadline($parts, $competition)
    {
        global $LANG;
        $arr = [
            '',
            $LANG->getLL('tx_cfcleague_games.date'),
            $LANG->getLL('tx_cfcleague_games.status'),
            $LANG->getLL('tx_cfcleague_games.home'),
            $LANG->getLL('tx_cfcleague_games.guest'),
        ];

        if ($competition->isAddPartResults() || 1 == $parts) {
            $arr[] = $LANG->getLL('tx_cfcleague_games.endresult');
        }
        // Hier je Spielart die Überschrift setzen
        if ($parts > 1) {
            for ($i = $parts; $i > 0; --$i) {
                $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_'.$i);
                if (!$label) {
                    // Prüfen ob ein default gesetzt ist
                    $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_default');
                    if ($label) {
                        $label = $i.'. '.$label;
                    }
                }
                $arr[] = $label ? $label : $i.'. part';
            }
        }
        $sports = $this->sportsServiceLocator->getSportsService($competition->getSports());
        if ($sports->isSetBased()) {
            $arr[] = $LANG->getLL('tx_cfcleague_games_sets');
        }

        $arr[] = $LANG->getLL('tx_cfcleague_games.visitors');

        return $arr;
    }

    /**
     * Build a TCA form field for an attribute.
     *
     * @param string $table
     * @param array $record
     * @param string $fieldName
     * @param int $uid
     *            uid of record to edit
     *
     * @return string|mixed
     */
    private function buildInputField($table, $record, $fieldName, $uid)
    {
        return $this->formTool->getTCEForm()->getSoloField($table, $record, $fieldName);
    }

    /**
     * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages.
     *
     * @param \tx_cfcleague_models_Match[] $matches
     * @param Competition $competition
     *
     * @return array mit zwei Elementen: Idx 0 enthält Array für Darstellung als Tabelle, Idx 1
     *         enthält, falls vorhanden den Namen des spielfreien Teams
     */
    private function createTableArray($matches, $competition)
    {
        $parts = $competition->getNumberOfMatchParts();
        $arr = [
            0 => [
                $this->getHeadline($parts, $competition),
            ],
        ];

        foreach ($matches as $match) {
            $row = [];
            $isNoMatch = $match->isDummy();
            $matchUid = $match->getUid();

            $table = 'tx_cfcleague_games';
            if (!$isNoMatch) {
                $row[] = $matchUid.$this->formTool->createEditLink('tx_cfcleague_games', $matchUid, '');
                $dataArr = TYPO3::isTYPO70OrHigher() ? $match->getProperty() : $this->formTool->getTCEFormArray($table, $matchUid);
                $row[] = $this->buildInputField($table, $dataArr, 'date', $matchUid);
                $row[] = $this->buildInputField($table, $dataArr, 'status', $matchUid);
                $row[] = $this->formTool->createEditLink('tx_cfcleague_teams', $match->getProperty('home'), $match->getHome()
                    ->getNameShort());
                $row[] = $this->formTool->createEditLink('tx_cfcleague_teams', $match->getProperty('guest'), $match->getGuest()
                    ->getNameShort());

                if ($competition->isAddPartResults() && 1 != $parts) {
                    $row[] = $match->getResult();
                }
                // Jetzt die Spielabschitte einbauen, wobei mit dem letzten begonnen wird
                for ($i = $parts; $i > 0; --$i) {
                    $row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$matchUid.'][goals_home_'.$i.']', $match->getProperty('goals_home_'.$i), 3).' : '.$this->formTool->createIntInput('data[tx_cfcleague_games]['.$matchUid.'][goals_guest_'.$i.']', $match->getProperty('goals_guest_'.$i), 3);
                }

                $sports = $this->sportsServiceLocator->getSportsService($competition->getSports());
                if ($sports->isSetBased()) {
                    $row[] = $this->formTool->createTxtInput('data[tx_cfcleague_games]['.$matchUid.'][sets]', $match->getProperty('sets'), 12);
                }

                $row[] = $this->formTool->createIntInput('data[tx_cfcleague_games]['.$matchUid.'][visitors]', $match->getProperty('visitors'), 6);
                $arr[0][] = $row;
            } else {
                $row = [];
                $isHomeDummy = $match->getHome()->isDummy();
                $row['team'] = $isHomeDummy ? $match->getGuest()->getName() : $match->getHome()->getName();
                $row['team_edit'] = $this->formTool->createEditLink('tx_cfcleague_teams', ($isHomeDummy ? $match->getProperty('guest') : $match->getProperty('home')), ($isHomeDummy ? $match->getGuest()
                    ->getNameShort() : $match->getHome()
                    ->getNameShort()));
                $row['match_edit'] = $this->formTool->createEditLink('tx_cfcleague_games', $matchUid);
                $arr[1][] = $row;
            }
        }

        return $arr;
    }

    /**
     * Aktualisiert die Spiele mit den Daten aus dem Request.
     */
    private function updateMatches($tcaData)
    {
        $tce = Connection::getInstance()->getTCEmain($tcaData);
        $tce->process_datamap();
    }
}
