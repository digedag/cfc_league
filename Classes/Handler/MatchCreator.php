<?php

namespace System25\T3sports\Handler;

use Sys25\RnBase\Backend\Form\FormBuilder;
use Sys25\RnBase\Backend\Module\IModFunc;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\T3General;
use System25\T3sports\Model\Competition;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2023 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse ermöglicht die manuelle Erstellung von Spielplänen.
 */
class MatchCreator
{
    /**
     * Returns an instance.
     *
     * @return MatchCreator
     */
    public static function getInstance()
    {
        return tx_rnbase::makeInstance(get_class());
    }

    /**
     * Neuanlage von Spielen über die TCE.
     *
     * @param IModule $mod
     *
     * @return string
     */
    public function handleRequest(IModule $mod)
    {
        $submitted = T3General::_GP('doCreateMatches');
        if (!$submitted) {
            return '';
        }
        $tcaData = T3General::_GP('data');
        $tce = Connection::getInstance()->getTCEmain($tcaData);
        $tce->process_datamap();
        $content = $mod->getDoc()->section('Message:', $GLOBALS['LANG']->getLL('msg_matches_created'), 0, 1, IModFunc::ICON_INFO);

        return $content;
    }

    /**
     * @param Competition $competition
     * @param IModule $mod
     */
    public function showScreen(Competition $competition, IModule $mod)
    {
        global $LANG;
        $LANG->includeLLFile('EXT:cfc_league/Resources/Private/Language/locallang_db.xlf');

        $items = [];
        for ($i = 1; $i < 33; ++$i) {
            $items[$i] = $i.(1 == $i ? ' ###LABEL_MATCH###' : ' ###LABEL_MATCHES###');
        }
        $menu = $mod->getFormTool()->showMenu($mod->getPid(), 'matchs3create', $mod->getName(), $items);
        $content = $menu['menu'];
        $maxMatches = $menu['value'];

        $table = 'tx_cfcleague_games';
        // Jetzt 6 Boxen mit Name und Kurzname
        $arr = [
            0 => [
                $LANG->getLL('tx_cfcleague_games.round'),
                $LANG->getLL('tx_cfcleague_games.date'),
                $LANG->getLL('tx_cfcleague_games.status'),
                $LANG->getLL('tx_cfcleague_games.home'),
                $LANG->getLL('tx_cfcleague_games.guest'),
            ],
        ];

        $dataArr = [
            'pid' => $mod->getPid(),
            'competition' => $competition->getUid(),
            'date' => time(),
            'round' => $competition->getNumberOfRounds(),
            'round_name' => $competition->getNumberOfRounds().$LANG->getLL('createGameTable_round'),
        ];

        /* @var $formBuilder FormBuilder */
        $formBuilder = $mod->getFormTool()->getTCEForm();
        for ($i = 0; $i < $maxMatches; ++$i) {
            $row = [];
            $dataArr['uid'] = 'NEW'.$i;
            $dataArr['date'] = strtotime('+'.$i.' weeks');
            $row[] = $formBuilder->getSoloField($table, $dataArr, 'round').$formBuilder->getSoloField($table, $dataArr, 'round_name');
            $row[] = $formBuilder->getSoloField($table, $dataArr, 'date');
            $row[] = $formBuilder->getSoloField($table, $dataArr, 'status').$mod->getFormTool()->createHidden('data[tx_cfcleague_games][NEW'.$i.'][pid]', $mod->getPid()).$mod->getFormTool()->createHidden('data[tx_cfcleague_games][NEW'.$i.'][competition]', $competition->getUid());

            // die Team können derzeit nicht per SoloField geholt werden, weil der
            // gesetzte Wettbewerb verloren geht.
            $row[] = $formBuilder->getSoloField($table, $dataArr, 'home');
            $row[] = $formBuilder->getSoloField($table, $dataArr, 'guest');

            // $row[] = $mod->getFormTool()->create('data[tx_cfcleague_teams][NEW'.$i.'][pid]', $mod->getPid());
            $arr[] = $row;
        }
        $tables = tx_rnbase::makeInstance(Tables::class);
        $content .= $tables->buildTable($arr);
        $content .= $mod->getFormTool()->createSubmit('doCreateMatches', $LANG->getLL('btn_create'), $GLOBALS['LANG']->getLL('msg_CreateGameTable'));

        return $content;
    }

    public function makeLink(IModule $mod)
    {
    }
}
