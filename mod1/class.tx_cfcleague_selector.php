<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2018 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_cfcleague_models_Competition');
tx_rnbase::load('Tx_Rnbase_Backend_Utility');
tx_rnbase::load('tx_rnbase_mod_IModule');
tx_rnbase::load('Tx_Rnbase_Backend_Utility_Icons');
tx_rnbase::load('tx_rnbase_parameters');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');
tx_rnbase::load('Tx_Rnbase_Database_Connection');
tx_rnbase::load('Tx_Rnbase_Configuration_Processor');


/**
 * Die Klasse stellt Auswahlmenus zur Verfügung
 */
class tx_cfcleague_selector
{

    var $doc, $MCONF;

    private $modName;

    private $module;

    /** @var \TYPO3\CMS\Core\Imaging\IconFactory */
    protected $iconFactory;

    /**
     * Initialisiert das Objekt mit dem Template und der Modul-Config.
     */
    public function init($doc, tx_rnbase_mod_IModule $module)
    {
        $this->doc = $doc;
        $this->MCONF['name'] = $module->getName(); // deprecated
        $this->modName = $module->getName();
        $this->module = $module;
        if (tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
            $this->iconFactory = tx_rnbase::makeInstance(TYPO3\CMS\Core\Imaging\IconFactory::class);
        }
    }

    /**
     * Returns the form tool
     *
     * @return Tx_Rnbase_Backend_Form_ToolBox
     */
    protected function getFormTool()
    {
        if (! $this->formTool) {
            // TODO: use formtool from module
            $this->formTool = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Form_ToolBox');
            $this->formTool->init($this->doc, $this->module);
        }
        return $this->formTool;
    }

    /**
     * Darstellung der Select-Box mit allen Ligen der übergebenen Seite.
     * Es wird auf die aktuelle Liga eingestellt.
     *
     * @return tx_cfcleague_models_Competition aktuellen Wettbewerb als Objekt oder 0
     */
    public function showLeagueSelector(&$content, $pid, $leagues = 0)
    {
        // Wenn vorhanden, nehmen wir die übergebenen Wettbewerbe, sonst schauen wir auf der aktuellen Seite nach
        $leagues = $leagues ? $leagues : $this->findLeagues($pid);

        $objLeagues = $entries = array();
        foreach ($leagues as $league) {
            if (is_object($league)) {
                $objLeagues[$league->getUid()] = $league; // Objekt merken
                $entries[$league->getUid()] = $league->getProperty($league->getProperty('internal_name') ? 'internal_name' : 'name');
            } else {
                $entries[$league['uid']] = $league['internal_name'] ? $league['internal_name'] : $league['name'];
            }
        }
        // Ohne Liga-Array ist eine weitere Verarbeitung sinnlos
        if (! count($entries)) {
            return 0;
        }

        $menuData = $this->getFormTool()->showMenu($pid, 'league', $this->modName, $entries);

        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        if ($menuData['menu']) {
            $links = [];
            $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_competition', $menuData['value'], '');
            // Jetzt noch den Cache-Link
            $cacheIcon = tx_rnbase_util_TYPO3::isTYPO70OrHigher() ?
                    $this->iconFactory->getIcon('actions-system-cache-clear', TYPO3\CMS\Core\Imaging\Icon::SIZE_SMALL)->render()
                    :
                    '<img' . Tx_Rnbase_Backend_Utility_Icons::skinImg($GLOBALS['BACK_PATH'], 'gfx/clear_all_cache.gif', 'width="11" height="12"') . ' title="###LABEL_CLEAR_STATS_CACHE###" border="0" alt="Clear Cache" />';
            $links[] = $this->getFormTool()->createModuleLink(['clearCache'=>1], $pid, $cacheIcon, [
                'params' => [
                    'clearCache' => 1
                ]
            ]);

            $links[] = $this->getFormTool()->createNewLink('tx_cfcleague_competition', $pid, '');
            $content .= $this->renderSelector($menuData['menu'], $links);
        }

        if (tx_rnbase_parameters::getPostOrGetParameter('clearCache') && $menuData['value']) {
            // Hook aufrufen
            tx_rnbase_util_Misc::callHook('cfc_league', 'clearStatistics_hook', array(
                'compUid' => $menuData['value']
            ), $this);
        }

        // Aktuellen Wert als Liga-Objekt zurückgeben
        if (count($objLeagues)) {
            return $menuData['value'] ? $objLeagues[$menuData['value']] : 0;
        }
        return $menuData['value'] ? new tx_cfcleague_models_Competition($menuData['value']) : 0;
    }

    /**
     * Darstellung der Select-Box mit allen Teams des übergebenen Wettbewerbs.
     * Es wird auf das aktuelle Team eingestellt.
     *
     * @return tx_cfcleague_models_Team aktuelle Team als Objekt
     */
    public function showTeamSelector(&$content, $pid, $league, $options = array())
    {
        if (! $league) {
            return 0;
        }

        $selectorId = $options['selectorId'] ? $options['selectorId'] : 'team';
        $entries = array();
        if ($options['firstItem']) {
            $entries[$options['firstItem']['id']] = $options['firstItem']['label'];
        }

        foreach ($league->getTeamNames() as $id => $team_name) {
            $entries[$id] = $team_name;
        }

        $menuData = $this->getFormTool()->showMenu($pid, $selectorId, $this->modName, $entries);

        $teamObj = null;
        if ($menuData['value'] > 0) {
            $teamObj = tx_rnbase::makeInstance('tx_cfcleague_models_Team', $menuData['value']);
        }
        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        $menu = $menuData['menu'];
        $links = [];
        $noLinks = $options['noLinks'] ? true : false;
        if (! $noLinks && $menu) {
            $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_teams', $menuData['value']);
            if ($teamObj->getProperty('club')) {
                $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_club', intval($teamObj->getProperty('club')), $GLOBALS['LANG']->getLL('label_club'));
            }
        }
        $content .= $this->renderSelector($menuData['menu'], $links);

        return $teamObj;
    }

    /**
     *
     * @param int $pid
     * @return tx_cfcleague_models_Club[]
     */
    protected function lookupClubs($pid)
    {
        $globalClubs = intval(Tx_Rnbase_Configuration_Processor::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
        $clubOrdering = intval(Tx_Rnbase_Configuration_Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;
        $fields = array();
        if (! $globalClubs) {
            $fields['CLUB.PID'][OP_EQ_INT] = $pid;
        }
        $dbOptions = array();
        if ($clubOrdering) {
            $dbOptions['orderby']['CLUB.CITY'] = 'asc';
        }
        $dbOptions['orderby']['CLUB.NAME'] = 'asc';
        return tx_cfcleague_util_ServiceRegistry::getTeamService()->searchClubs($fields, $dbOptions);
    }

    /**
     * Darstellung der Select-Box mit allen Vereinen.
     * Es wird auf den aktuellen Verein eingestellt.
     *
     * @return tx_cfcleague_models_Club
     */
    public function showClubSelector(&$content, $pid, $options = array())
    {
        $clubs = $this->lookupClubs($pid);

        $objClubs = $entries = array();
        if ($options['firstItem']) {
            $entries[$options['firstItem']['id']] = $options['firstItem']['label'];
        }

        $clubOrdering = intval(Tx_Rnbase_Configuration_Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;
        foreach ($clubs as $club) {
            $label = ($clubOrdering ? $club->getCity() . ' - ' : '') . $club->getName();
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
        if (! $noLinks && $menu) {
            $links[] = $this->getFormTool()->createEditLink('tx_cfcleague_club', $menuData['value']);
            $links[] = $this->createNewClubLink($pid);
        }
        $content .= $this->renderSelector($menuData['menu'], $links);

        return $currItem;
    }

    private function createNewClubLink($pid)
    {
        $linker = tx_rnbase::makeInstance('tx_cfcleague_mod1_linker_NewClub');
        return $linker->makeLink(null, $this->getFormTool(), $pid, array());
    }

    /**
     * Darstellung der Select-Box mit allen Spielrunden des übergebenen Wettbewerbs.
     * Es
     * wird auf die aktuelle Runde eingestellt.
     *
     * @param string $content
     * @param int $pid
     * @param tx_cfcleague_models_Competition $league
     * @return int current value
     */
    public function showRoundSelector(&$content, $pid, $league)
    {
        $entries = [];
        $objRounds = [];
        foreach ($league->getRounds() as $round) {
            if (is_object($round)) {
                $objRounds[$round->getUid()] = $round;
                $entries[$round->getUid()] = $round->getProperty('name') . (intval($round->getProperty('finished')) ? ' *' : '');
            } else {
                $entries[$round['round']] = $round['round_name'] . (intval($round['max_status']) ? ' *' : '');
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
            if (tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
                $menu = '<div class="cfcselector"><div class="selector col-md-2">' . $data['menu'] . '</div></div>';
                $links = $prev . $next;
                $menu = $menu . '<span class="links col-md-2">' . $links . '</span>';
            } else {
                $menu = '<div class="cfcselector"><div class="selector">' . $prev . '' . $data['menu'] . '' . $next . '</div></div>';
            }
        }
        $content .= $menu;

        return (int) count($objRounds) ? $objRounds[$data['value']] : $data['value'];
    }

    /**
     * Darstellung der Select-Box mit allen übergebenen Spielen.
     * Es wird auf das aktuelle Spiel eingestellt.
     *
     * @return tx_cfcleague_models_Match current match
     */
    public function showMatchSelector(&$content, $pid, $matches)
    {
        $entries = array();
        foreach ($matches as $match) {
            $entries[$match['uid']] = $match['short_name_home'] . ' - ' . $match['short_name_guest'];
        }

        $data = $this->getFormTool()->showMenu($pid, 'match', $this->MCONF['name'], $entries, $this->getScriptURI());
        // In den Content einbauen
        // Zusätzlich noch einen Edit-Link setzen
        $links = [$this->getFormTool()->createEditLink('tx_cfcleague_games', $data['value'])];
        if ($data['menu']) {
            $content .= $this->renderSelector($data['menu'], $links);
        }

        // Aktuellen Wert als Match-Objekt zurückgeben
        tx_rnbase::load('tx_cfcleague_models_Match');
        return tx_rnbase::makeInstance('tx_cfcleague_models_Match', $data['value']);
    }

    /**
     * Darstellung der Select-Box mit allen Saisons in der Datenbank.
     *
     * @return tx_cfcleague_models_Saison
     */
    public function showSaisonSelector(&$content, $pid)
    {
        // Zuerst die Saisons ermitteln
        $saisons = Tx_Rnbase_Database_Connection::getInstance()->doSelect('uid,name', 'tx_cfcleague_saison', [
            'orderby' => 'sorting asc',
            'wrapperclass' => 'tx_cfcleague_models_Saison'
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
        } elseif (count($entries) == 1) {
            $comp = reset($entries);
            $menu = $this->renderSelector($comp);
        }
        $content .= $menu;

        // Aktuellen Wert als Saison-Objekt zurückgeben
        return $data['value'] ? tx_rnbase::makeInstance('tx_cfcleague_models_Saison', $data['value']) : NULL;
    }

    /**
     * Zeigt ein TabMenu
     *
     * @param int $pid
     * @param string $name
     * @param array $entries
     * @return array with keys 'menu' and 'value'
     */
    public function showTabMenu($pid, $name, $entries)
    {
        return $this->getFormTool()->showTabMenu($pid, $name, $this->MCONF['name'], $entries);
    }

    /**
     * Zeigt eine Art Tab-Menu
     *
     * @deprecated
     */
    public function showMenu($pid, $name, $entries)
    {
        $MENU = [$name => $entries];
        $SETTINGS = Tx_Rnbase_Backend_Utility::getModuleData($MENU, Tx_Rnbase_Utility_T3General::_GP('SET'), $this->MCONF['name']) // Das ist der Name des Moduls
        ;
        $ret = array();
        $ret['menu'] = Tx_Rnbase_Backend_Utility::getFuncMenu($pid, 'SET[' . $name . ']', $SETTINGS[$name], $MENU[$name], $this->getScriptURI());
        $ret['value'] = $SETTINGS[$name];
        return $ret;
    }

    /**
     *
     * @return string
     */
    protected function getScriptURI()
    {
        return '';
    }

    private function renderSelector($menu, array $links = [])
    {
        return '<div class="cfcselector" style="float: left; width: 100%"><span class="selector col-md-2">' . $menu .
            '</span>'. (empty($links) ? '' : '<span class="links">' . implode(' ', $links) . '</span>').
            '</div>';
    }

    /**
     * Liefert die Ligen der aktuellen Seite.
     *
     * @return array mit Rows
     */
    private function findLeagues($pid)
    {
        tx_rnbase::load('Tx_Rnbase_Database_Connection');
        return Tx_Rnbase_Database_Connection::getInstance()->doSelect('*', 'tx_cfcleague_competition', [
            'where' => 'pid="' . $pid . '"',
            'orderby' => 'sorting asc'
        ]);
    }
}

