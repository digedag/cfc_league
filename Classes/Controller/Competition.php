<?php

namespace System25\T3sports\Controller;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\BaseModFunc;
use Sys25\RnBase\Frontend\Marker\FormatUtil;
use Sys25\RnBase\Frontend\Marker\Templates;
use System25\T3sports\Controller\Competition\DfbSync;
use System25\T3sports\Controller\Competition\MatchEdit;
use System25\T3sports\Controller\Competition\MatchTable;
use System25\T3sports\Controller\Competition\Teams;
use System25\T3sports\Module\Utility\Selector;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2024 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse ist die Einstiegsklasse für das Modul "Wettbewerbe verwalten".
 */
class Competition extends BaseModFunc
{
    public $doc;

    /**
     * @var Selector
     */
    private $selector;

    /**
     * @var DfbSync
     */
    private $dfbSync;
    /**
     * @var MatchEdit
     */
    private $matchEdit;

    public $MCONF;

    /**
     * Method getFuncId.
     *
     * @return string
     */
    public function getFuncId()
    {
        return 'funccompetitions';
    }

    public function getModuleIdentifier()
    {
        return 'cfc_league';
    }

    public function __construct(?MatchEdit $matchEdit = null, ?DfbSync $dfbSync = null)
    {
        $this->dfbSync = $dfbSync ?: tx_rnbase::makeInstance(DfbSync::class);
        $this->matchEdit = $matchEdit ?: tx_rnbase::makeInstance(MatchEdit::class);
    }

    /**
     * Verwaltet die Erstellung von Spielplänen von Ligen.
     *
     * @param string $template
     * @param ConfigurationInterface$configurations
     * @param FormatUtil $formatter
     * @param ToolBox $formTool
     *
     * @return string
     */
    protected function getContent($template, &$configurations, &$formatter, $formTool)
    {
        $lang = $this->getModule()->getLanguageService();
        // Zuerst mal müssen wir die passende Liga auswählen lassen:
        // Entweder global über die Datenbank oder die Ligen der aktuellen Seite
        // Selector-Instanz bereitstellen
        $this->selector = tx_rnbase::makeInstance(Selector::class);
        $this->selector->init($this->getModule()
            ->getDoc(), $this->getModule());

        // Anzeige der vorhandenen Ligen
        $selector = '';
        $current_league = $this->selector->showLeagueSelector($selector, $this->getModule()
            ->getPid());
        $content = '';
        $this->getModule()->setSelector($selector);

        if (!$current_league) {
            $content .= $this->getModule()
                ->getDoc()
                ->section('Info:', $lang->getLL('no_league_in_page'), 0, 1, self::ICON_WARN);
            $newCompLink = $formTool->createNewLink(
                'tx_cfcleague_competition',
                $this->getModule()->getPid(),
                $lang->getLL('msg_create_new_competition')
            );
            $content .= '<p style="margin-top:5px; font-weight:bold;">'.$newCompLink.'</p>';

            return $content;
        }

        $menu = $this->selector->showTabMenu($this->getModule()
            ->getPid(), 'comptools', [
            '0' => $lang->getLL('edit_games'),
            '1' => $lang->getLL('mod_compteams'),
            '2' => $lang->getLL('create_games'),
            '3' => $lang->getLL('mod_compdfbsync'),
        ]);

        $tabs = $menu['menu'];
        $tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

        // FIXME: check in older versions
        $this->getModule()->setSubMenu($tabs);

        switch ($menu['value']) {
            case 0:
                $funcContent = $this->showEditMatches($current_league, $this->getModule());

                break;
            case 1:
                $mod = tx_rnbase::makeInstance(Teams::class);
                $funcContent = $mod->main($this->getModule(), $current_league);

                break;
            case 2:
                $mod = tx_rnbase::makeInstance(MatchTable::class);
                $funcContent = $mod->main($this->getModule(), $current_league);

                break;
            case 3:
                $funcTemplate = Templates::getSubpart($template, '###FUNC_DFBSYNC###');
                $funcContent = $this->dfbSync->main($this->getModule(), $current_league, $funcTemplate);

                break;
        }
        $content .= $formTool->form->printNeededJSFunctions_top();
        $content .= $funcContent;
        // Den JS-Code für Validierung einbinden
        $content .= $formTool->form->printNeededJSFunctions();

        $modContent = Templates::getSubpart($template, '###MAIN###');
        $modContent = Templates::substituteMarkerArrayCached($modContent, [
            '###CONTENT###' => $content,
        ]);

        // $content .= $this->formTool->form->JSbottom('editform');
        return $modContent;
    }

    private function showEditMatches($current_league, $module)
    {
        $content = $this->matchEdit->main($module, $current_league);

        return $content;
    }
}
