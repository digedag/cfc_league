<?php

namespace System25\T3sports\Controller\Competition;

use DateTime;
use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModFunc;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\T3General;
use Sys25\RnBase\Utility\TYPO3;
use System25\T3sports\Handler\MatchCreator;
use System25\T3sports\MatchGeneration\Generator;
use System25\T3sports\Model\Competition;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2025 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse verwaltet die Erstellung von Spielplänen für Wettbewerbe.
 */
class MatchTable
{
    /** @var \Sys25\RnBase\Backend\Template\Override\DocumentTemplate */
    private $doc;

    /** @var IModule */
    private $module;

    /** @var ToolBox */
    private $formTool;

    /**
     * Verwaltet die Erstellung von Spielplänen von Ligen.
     *
     * @param IModule $module
     * @param Competition $competition
     */
    public function main($module, $competition)
    {
        $this->module = $module;
        $this->doc = $module->getDoc();

        $this->formTool = $module->getFormTool();
        // $start = microtime(true);

        // Die Neuanlage der manuellen Spiele erledigt der MatchCreator
        $content = MatchCreator::getInstance()->handleRequest($this->getModule());

        // Die Neuanlage der "automatischen" Spiele übernimmt diese Klasse
        $content .= $this->handleCreateMatchTable($competition);
        if (!$content) {
            // Ohne Submit zeigen wir das Formular
            $content .= $this->showMatchTable($competition);
        }

        return $content;
    }

    /**
     * @param Competition $comp
     *
     * @return string
     */
    private function handleCreateMatchTable(Competition $comp)
    {
        // Haben wir Daten im Request?
        $content = '';
        $data = T3General::_GP('data');
        if (isset($data['rounds']) && T3General::_GP('update')) {
            $result = $this->createMatches($data['rounds'], $comp);

            $content .= $this->doc->section($this->formTool->getLanguageService()->getLL('message').':', $result, 0, 1, IModFunc::ICON_INFO);
        }

        return $content;
    }

    /**
     * Zeigt den Spielplan an.
     *
     * @param Competition $comp
     *
     * @return string
     */
    private function showMatchTable(Competition $comp)
    {
        $lang = $this->module->getLanguageService();

        $content = '';
        $matchCnt = $comp->getNumberOfMatches(false);
        if ($matchCnt > 0) {
            $content .= $this->doc->section($lang->getLL('warning').':', $lang->getLL('msg_league_generation_hasmatches'), 0, 1, IModFunc::ICON_WARN);
            $content .= '<br/><br/>';
        }

        // Hier zwischen Automatisch und Manuell unterscheiden
        $menu = $this->getFormTool()->showMenu($this->getModule()
            ->getPid(), 't3s_mcmode', $this->getModule()
            ->getName(), [
                0 => 'Auto',
                '1' => 'Manual',
            ]);
        $content .= $menu['menu'];
        $mode = $menu['value'];
        $content .= '<br>';

        if (0 == $mode) { // Automatischer Spielplan
            $content .= $this->showMatchTableAuto($comp);
        } else { // Manuell Spiele anlegen
            $content .= MatchCreator::getInstance()->showScreen($comp, $this->getModule());
        }

        return $content;
    }

    /**
     * Automatische Erzeugung eines Spielplans.
     *
     * @param Competition $comp
     *
     * @return string
     */
    private function showMatchTableAuto(Competition $comp)
    {
        $lang = $this->module->getLanguageService();
        $content = '';
        // Wir holen die Mannschaften und den GameString aus der Liga
        // Beides jagen wir durch den Generator
        $options = [];
        $options['halfseries'] = (int) T3General::_GP('option_halfseries');
        $options['nomatch'] = $comp->getDummyTeamIds();
        $options['firstmatchday'] = $comp->getNumberOfRounds();
        $options['firstmatchnumber'] = $comp->getLastMatchNumber();
        // Zunächst mal Anzeige der Daten
        /** @var Generator $gen */
        $gen = tx_rnbase::makeInstance(Generator::class);
        $table = $gen->main($comp->getTeamIds(), $comp->getGenerationKey(), $options);

        if (count($gen->errors)) {
            // Da gibt es wohl ein Problem bei der Erzeugung der Spiele...
            $content .= $this->doc->section($lang->getLL('error').':', '<ul><li>'.implode('<li>', $gen->errors).'</ul>', 0, 1, IModFunc::ICON_FATAL);
        }
        if (count($gen->warnings)) {
            // Da gibt es wohl ein Problem bei der Erzeugung der Spiele...
            $content .= $this->doc->section($lang->getLL('warning').':', '<ul><li>'.implode('<li>', $gen->warnings).'</ul>', 0, 1, IModFunc::ICON_WARN);
        }
        if (count($table)) {
            // Wir zeigen alle Spieltage und fragen nach dem Termin
            $content .= $this->prepareMatchTable($table, $comp, $options['halfseries']);
            // Den Update-Button einfügen
            $content .= $this->formTool->createSubmit('update', $lang->getLL('btn_create'), $lang->getLL('msg_CreateGameTable'));
        }

        return $content;
    }

    /**
     * Erstellt das Vorabformular, daß für jeden Spieltag notwendige Daten abfragt.
     */
    private function prepareMatchTable($table, &$league, $option_halfseries)
    {
        $lang = $this->module->getLanguageService();

        $content = '';
        // Wir benötigen eine Select-Box mit der man die Rückrunden-Option einstellen kann
        // Bei Änderung soll die Seite neu geladen werden, damit nur die Halbserie angezeigt wird.
        $content .= $this->formTool->createSelectByArray('option_halfseries', $option_halfseries, [
            '0' => '###LABEL_CREATE_SAISON###',
            '1' => '###LABEL_CREATE_FIRSTHALF###',
            '2' => '###LABEL_CREATE_SECONDHALF###',
        ], [
            'reload' => 1,
        ]);
        $content .= '<br />';

        // Führende 0 für Spieltag im einstelligen Bereich
        $this->formTool->insertJsModule('@digedag/cfc_league/match-create.js');

        $content .= $this->formTool->createCheckbox('option_leadingZero', '1', false);
        $content .= '###LABEL_LEADING_ZERO###';
        $content .= '<br />';

        $arr = [
            [
                $lang->getLL('label_roundset'),
            ],
        ];
        // $arr = Array(Array($LANG->getLL('label_round'), $LANG->getLL('label_roundname').' / '.
        // $LANG->getLL('label_rounddate'), $LANG->getLL('label_roundset')));
        $tables = tx_rnbase::makeInstance(Tables::class);

        $matchDay = new DateTime('next saturday 15:00:00');

        foreach ($table as $round => $matchArr) {
            $row = [];
            $fields = [];

            // Die Formularfelder, die jetzt erstellt werden, wandern später direkt in die neuen Game-Records
            // Ein Hidden-Field für die Runde
            $fields[] = $this->formTool->createHidden('data[rounds][round_'.$round.'][round]', $round);
            $roundName = $round.$lang->getLL('createGameTable_round');
            $fields[] = $this->formTool->createTxtInput('data[rounds][round_'.$round.'][round_name]', $roundName, 10, [
                'class' => 'roundname',
            ]);
            $fields[] = $this->formTool->createDateInput('data[rounds][round_'.$round.'][date]', $matchDay->format('U'), [
                ToolBox::OPTION_HIDE_LABEL => true,
            ]);
            $row[] = '<div>'.implode('', $fields).'</div>'.
            // Anzeige der Paarungen
            $tables->buildTable($this->createMatchTableArray($matchArr, $league, 'data[rounds][round_'.$round.']'));

            $arr[] = $row;
            $matchDay->modify('+1 week');
        }
        $content .= $tables->buildTable($arr);

        return $content;
    }

    /**
     * Erstellt das Datenarray zur Erstellung der HTML-Tabelle mit den Spielen des Spieltages.
     *
     * @param Competition $league
     */
    private function createMatchTableArray($matches, &$league, $namePrefix)
    {
        $lang = $this->module->getLanguageService();
        $teamNames = $league->getTeamNames();
        $arr = [
            [
                $lang->getLL('label_match_no'),
                $lang->getLL('label_home'),
                $lang->getLL('label_guest'),
            ],
        ];
        foreach ($matches as $match) {
            $row = [];
            $row[] = $match->noMatch ? '' : str_pad($match->nr2, 3, '000', STR_PAD_LEFT);
            $row[] = $this->createSelectBox($teamNames, $match->home, $namePrefix.'[matches]['.$match->nr.'][home]');
            $row[] = $this->createSelectBox($teamNames, $match->guest, $namePrefix.'[matches]['.$match->nr.'][guest]').$this->formTool->createHidden($namePrefix.'[matches]['.$match->nr.'][nr2]', $match->nr2);
            // $row[] = $teamNames[$match->home];
            // $row[] = $teamNames[$match->guest];
            $arr[] = $row;
        }

        return $arr;
    }

    private function createSelectBox($teamNames, $currentTeam, $name)
    {
        $ret = '<select name="'.$name.'">';
        foreach ($teamNames as $key => $teamName) {
            $ret .= '<option value="'.$key.'"'.($key == $currentTeam ? ' selected="selected" ' : '').'>'.$teamName.'</option>';
        }
        $ret .= "</select>\n";

        return $ret;
    }

    /**
     * Returns the formtool.
     *
     * @return ToolBox
     */
    protected function getFormTool()
    {
        return $this->formTool;
    }

    /**
     * @return IModule
     */
    protected function getModule()
    {
        return $this->module;
    }

    /**
     * Erstellt die Spiele der Liga.
     * Diese werden aus den Daten gebildet, die im Request liegen.
     */
    protected function createMatches($rounds, $league)
    {
        $lang = $this->module->getLanguageService();

        // Aus den Spielen der $table die TCA-Datensätze erzeugen
        $data = [
            'tx_cfcleague_games' => [],
        ];

        // Wir erstellen die Spiel je Spieltag
        foreach ($rounds as $roundData) {
            // Die ID des Spieltags ermitteln
            $matches = $roundData['matches'];
            // Die Paarungen holen
            foreach ($matches as $matchId => $match) {
                // Die Basis des Spieldatensatzes ist $roundData
                $new_match = [];
                $new_match['round'] = (int) $roundData['round'];
                $new_match['round_name'] = $roundData['round_name'];
                $new_match['date'] = $roundData['date'];
                $new_match['home'] = $match['home'];
                $new_match['guest'] = $match['guest'];
                $new_match['match_no'] = $match['nr2'];
                $new_match['competition'] = $league->getUid();
                $new_match['pid'] = $league->getPid();
                $data['tx_cfcleague_games']['NEW'.$matchId] = $new_match;
            }
        }

        // Die neuen Notes werden jetzt gespeichert
        reset($data);

        $tce = Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();

        return $lang->getLL('msg_matches_created');
    }
}
