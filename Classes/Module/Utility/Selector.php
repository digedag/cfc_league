<?php

namespace System25\T3sports\Module\Utility;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Configuration\Processor;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\T3General;
use Sys25\RnBase\Utility\TYPO3;
use System25\T3sports\Model\Club;
use System25\T3sports\Model\Competition;
use System25\T3sports\Model\Fixture;
use System25\T3sports\Model\Saison;
use System25\T3sports\Model\Team;
use System25\T3sports\Module\Linker\NewClubLinker;
use System25\T3sports\Utility\ServiceRegistry;
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
 * Die Klasse stellt Auswahlmenus zur Verfügung.
 */
class Selector
{
    public $doc;

    public $MCONF;

    private $modName;

    private $module;

    /** @var \TYPO3\CMS\Core\Imaging\IconFactory */
    protected $iconFactory;

    /**
     * Initialisiert das Objekt mit dem Template und der Modul-Config.
     */
    public function init($doc, IModule $module)
    {
        $this->doc = $doc;
        $this->MCONF['name'] = $module->getName(); // deprecated
        $this->modName = $module->getName();
        $this->module = $module;
        $this->iconFactory = tx_rnbase::makeInstance(\TYPO3\CMS\Core\Imaging\IconFactory::class);
    }

    /**
     * Returns the form tool.
     *
     * @return ToolBox
     */
    protected function getFormTool()
    {
        if (!$this->formTool) {
            // TODO: use formtool from module
            $this->formTool = tx_rnbase::makeInstance(ToolBox::class);
            $this->formTool->init($this->doc, $this->module);
        }

        return $this->formTool;
    }

    /**
     * Darstellung der Select-Box mit allen Ligen der übergebenen Seite.
     * Es wird auf die aktuelle Liga eingestellt.
     *
     * @return Competition aktuellen Wettbewerb als Objekt oder 0
     */
    public function showLeagueSelector(&$content, $pid, $leagues = 0)
    {
        // Wenn vorhanden, nehmen wir die übergebenen Wettbewerbe, sonst schauen wir auf der aktuellen Seite nach
        $leagues = $leagues ? $leagues : $this->findLeagues($pid);

        $objLeagues = $entries = [];
        foreach ($leagues as $league) {
            if (is_object($league)) {
                $objLeagues[$league->getUid()] = $league; // Objekt merken
                $entries[$league->getUid()] = $league->getProperty($league->getProperty('internal_name') ? 'internal_name' : 'name');
            } else {
                $entries[$league['uid']] = $league['internal_name'] ? $league['internal_name'] : $league['name'];
            }
        }
        // Ohne Liga-Array ist eine weitere Verarbeitung sinnlos
        if (!count($entries)) {
            return 0;
        }

        $menuData = $this->getFormTool()->showMenu($pid, 'league', $this->modName, $entries);

        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        if ($menuData['menu']) {
            $links = [];
            $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_competition', $menuData['value'], '');
            // Jetzt noch den Cache-Link
            $cacheIcon = $this->iconFactory->getIcon('actions-system-cache-clear',
                \TYPO3\CMS\Core\Imaging\Icon::SIZE_SMALL)->render();
            $links[] = $this->getFormTool()->createModuleLink(['clearCache' => 1], $pid, $cacheIcon, [
                'params' => [
                    'clearCache' => 1,
                ],
            ]);

            $links[] = $this->getFormTool()->createNewLink('tx_cfcleague_competition', $pid, '');
            $content .= $this->renderSelector($menuData['menu'], $links);
        }

        if (T3General::_GP('clearCache') && $menuData['value']) {
            // Hook aufrufen
            Misc::callHook('cfc_league', 'clearStatistics_hook', [
                'compUid' => $menuData['value'],
            ], $this);
        }

        // Aktuellen Wert als Liga-Objekt zurückgeben
        if (count($objLeagues)) {
            return $menuData['value'] ? $objLeagues[$menuData['value']] : 0;
        }

        return $menuData['value'] ? new Competition($menuData['value']) : 0;
    }

    /**
     * Darstellung der Select-Box mit allen Teams des übergebenen Wettbewerbs.
     * Es wird auf das aktuelle Team eingestellt.
     *
     * @return Team aktuelle Team als Objekt
     */
    public function showTeamSelector(&$content, $pid, Competition $league, $options = [])
    {
        if (!$league) {
            return 0;
        }

        $selectorId = $options['selectorId'] ? $options['selectorId'] : 'team';
        $entries = [];
        if ($options['firstItem']) {
            $entries[$options['firstItem']['id']] = $options['firstItem']['label'];
        }

        foreach ($league->getTeamNames() as $id => $team_name) {
            $entries[$id] = $team_name;
        }

        $menuData = $this->getFormTool()->showMenu($pid, $selectorId, $this->modName, $entries);

        $teamObj = null;
        if ($menuData['value'] > 0) {
            $teamObj = tx_rnbase::makeInstance(Team::class, $menuData['value']);
        }
        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        $menu = $menuData['menu'];
        $links = [];
        $noLinks = $options['noLinks'] ? true : false;
        if (!$noLinks && $menu) {
            $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_teams', $menuData['value']);
            if ($teamObj->getProperty('club')) {
                $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_club', intval($teamObj->getProperty('club')), $GLOBALS['LANG']->getLL('label_club'));
            }
        }
        $content .= $this->renderSelector($menuData['menu'], $links);

        return $teamObj;
    }

    /**
     * @param int $pid
     *
     * @return Club[]
     */
    protected function lookupClubs($pid)
    {
        $globalClubs = intval(Processor::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
        $clubOrdering = intval(Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;
        $fields = [];
        if (!$globalClubs) {
            $fields['CLUB.PID'][OP_EQ_INT] = $pid;
        }
        $dbOptions = [];
        if ($clubOrdering) {
            $dbOptions['orderby']['CLUB.CITY'] = 'asc';
        }
        $dbOptions['orderby']['CLUB.NAME'] = 'asc';

        return ServiceRegistry::getTeamService()->searchClubs($fields, $dbOptions);
    }

    /**
     * Darstellung der Select-Box mit allen Vereinen.
     * Es wird auf den aktuellen Verein eingestellt.
     *
     * @return Club
     */
    public function showClubSelector(&$content, $pid, $options = [])
    {
        $clubs = $this->lookupClubs($pid);

        $objClubs = $entries = [];
        if ($options['firstItem']) {
            $entries[$options['firstItem']['id']] = $options['firstItem']['label'];
        }

        $clubOrdering = intval(Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;
        foreach ($clubs as $club) {
            $label = ($clubOrdering ? $club->getCity().' - ' : '').$club->getName();
            $objClubs[$club->getUid()] = $club;
            $entries[$club->getUid()] = $label;
        }

        $selectorId = $options['selectorId'] ? $options['selectorId'] : 'club';
        $menuData = $this->getFormTool()->showMenu($pid, $selectorId, $this->modName, $entries);

        $currItem = null;
        if ($menuData['value'] > 0) {
            $currItem = $objClubs[$menuData['value']];
        }
        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        $menu = $menuData['menu'];
        $links = [];
        $noLinks = $options['noLinks'] ? true : false;
        if (!$noLinks && $menu) {
            $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_club', $menuData['value']);
            $links[] = $this->createNewClubLink($pid);
        }
        $content .= $this->renderSelector($menuData['menu'], $links);

        return $currItem;
    }

    private function createNewClubLink($pid)
    {
        $linker = tx_rnbase::makeInstance(NewClubLinker::class);

        return $linker->makeLink(null, $this->getFormTool(), $pid, []);
    }

    /**
     * Darstellung der Select-Box mit allen Spielrunden des übergebenen Wettbewerbs.
     * Es
     * wird auf die aktuelle Runde eingestellt.
     *
     * @param string $content
     * @param int $pid
     * @param Competition $league
     *
     * @return int current value
     */
    public function showRoundSelector(&$content, $pid, $league)
    {
        $entries = [];
        $objRounds = [];
        foreach ($league->getRounds() as $round) {
            if (is_object($round)) {
                $objRounds[$round->getUid()] = $round;
                $entries[$round->getUid()] = $round->getProperty('name').(intval($round->getProperty('finished')) ? ' *' : '');
            } else {
                $entries[$round['round']] = $round['round_name'].(intval($round['max_status']) ? ' *' : '');
            }
        }

        $data = $this->getFormTool()->showMenu($pid, 'round', $this->MCONF['name'], $entries, $this->getScriptURI());
        // In den Content einbauen
        // Spielrunden sind keine Objekte, die bearbeitet werden können
        if ($data['menu']) {
            $keys = array_flip(array_keys($entries));
            $currIdx = $keys[$data['value']];
            $keys = array_flip($keys);
            $prevIdx = ($currIdx > 0) ? $currIdx - 1 : count($entries) - 1;
            $nextIdx = ($currIdx < (count($entries) - 1)) ? $currIdx + 1 : 0;

            $prev = $this->getFormTool()->createModuleLink(['SET[round]' => $keys[$prevIdx]], $pid, '&lt;');
            $next = $this->getFormTool()->createModuleLink(['SET[round]' => $keys[$nextIdx]], $pid, '&gt;');

            $links = [$prev, $next];
            $content .= $this->renderSelector($data['menu'], $links);
        }
//        $content .= $menu;

        return (int) count($objRounds) ? $objRounds[$data['value']] : $data['value'];
    }

    /**
     * Darstellung der Select-Box mit allen übergebenen Spielen.
     * Es wird auf das aktuelle Spiel eingestellt.
     *
     * @return Fixture current match
     */
    public function showMatchSelector(&$content, $pid, $matches)
    {
        $entries = [];
        foreach ($matches as $match) {
            $entries[$match['uid']] = $match['short_name_home'].' - '.$match['short_name_guest'];
        }

        $data = $this->getFormTool()->showMenu($pid, 'match', $this->MCONF['name'], $entries, $this->getScriptURI());
        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        $links = [$this->getFormTool()->createEditLink('tx_cfcleague_games', $data['value'])];
        if ($data['menu']) {
            $content .= $this->renderSelector($data['menu'], $links);
        }

        // Aktuellen Wert als Match-Objekt zurückgeben
        return tx_rnbase::makeInstance(Fixture::class, $data['value']);
    }

    /**
     * Darstellung der Select-Box mit allen Saisons in der Datenbank.
     *
     * @return Saison
     */
    public function showSaisonSelector(&$content, $pid)
    {
        // Zuerst die Saisons ermitteln
        $saisons = Connection::getInstance()->doSelect('uid,name', 'tx_cfcleague_saison', [
            'orderby' => 'sorting asc',
            'wrapperclass' => 'tx_cfcleague_models_Saison',
        ]);

        $entries = [];
        foreach ($saisons as $item) {
            $entries[$item->getUid()] = $item->getName();
        }
        $data = $this->getFormTool()->showMenu($pid, 'saison', $this->MCONF['name'], $entries, $this->getScriptURI());

        // In den Content einbauen
        // Wir verzichten hier auf den Link und halten nur den Abstand ein
        if ($data['menu']) {
            $menu = $this->renderSelector($data['menu']);
        } elseif (1 == count($entries)) {
            $comp = reset($entries);
            $menu = $this->renderSelector($comp);
        }
        $content .= $menu;

        // Aktuellen Wert als Saison-Objekt zurückgeben
        return $data['value'] ? tx_rnbase::makeInstance(Saison::class, $data['value']) : null;
    }

    /**
     * Zeigt ein TabMenu.
     *
     * @param int $pid
     * @param string $name
     * @param array $entries
     *
     * @return array with keys 'menu' and 'value'
     */
    public function showTabMenu($pid, $name, $entries)
    {
        return $this->getFormTool()->showTabMenu($pid, $name, $this->MCONF['name'], $entries);
    }

    /**
     * Zeigt eine Art Tab-Menu.
     *
     * @deprecated
     */
    public function showMenu($pid, $name, $entries)
    {
        $MENU = [$name => $entries];
        $SETTINGS = BackendUtility::getModuleData($MENU, T3General::_GP('SET'), $this->MCONF['name']) // Das ist der Name des Moduls
        ;
        $ret = [];
        $ret['menu'] = BackendUtility::getFuncMenu($pid, 'SET['.$name.']', $SETTINGS[$name], $MENU[$name], $this->getScriptURI());
        $ret['value'] = $SETTINGS[$name];

        return $ret;
    }

    /**
     * @return string
     */
    protected function getScriptURI()
    {
        return '';
    }

    private function renderSelector($menu, array $links = [])
    {
        $menu = '<div class="row"><div class="col-sm-4">'.$menu.'</div>';
        if (!empty($links)) {
            $menu = $menu.'<span class="col-sm-4">'.implode(' ', $links).'</span>';
        }
        $menu .= '</div>';

        return $menu;
    }

    /**
     * Liefert die Ligen der aktuellen Seite.
     *
     * @return array mit Rows
     */
    private function findLeagues($pid)
    {
        return Connection::getInstance()->doSelect('*', 'tx_cfcleague_competition', [
            'where' => 'pid="'.$pid.'"',
            'orderby' => 'sorting asc',
        ]);
    }
}
