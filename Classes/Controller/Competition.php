<?php

namespace System25\T3sports\Controller;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\BaseModFunc;
use Sys25\RnBase\Configuration\ConfigurationInterface;
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
 *  (c) 2008-2023 Rene Nitzsche (rene@system25.de)
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

    public $MCONF;

    /** @var Selector */
    private $selector;

    /**
     * Method getFuncId.
     *
     * @return string
     */
    public function getFuncId()
    {
        return 'funccompetitions';
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
        global $LANG;
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
        $this->getModule()->selector = $selector;

        if (!$current_league) {
            $content .= $this->getModule()
                ->getDoc()
                ->section('Info:', $LANG->getLL('no_league_in_page'), 0, 1, self::ICON_WARN);
            $content .= '<p style="margin-top:5px; font-weight:bold;">'.$formTool->createNewLink('tx_cfcleague_competition', $this->getModule()
                ->getPid(), $LANG->getLL('msg_create_new_competition')).'</p>';

            return $content;
        }

        $menu = $this->selector->showTabMenu($this->getModule()
            ->getPid(), 'comptools', [
            '0' => $LANG->getLL('edit_games'),
            '1' => $LANG->getLL('mod_compteams'),
            '2' => $LANG->getLL('create_games'),
            '3' => $LANG->getLL('mod_compdfbsync'),
        ]);

        $tabs = $menu['menu'];
        $tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

        $this->pObj->tabs = $tabs;

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
                $mod = tx_rnbase::makeInstance(DfbSync::class);
                $funcContent = $mod->main($this->getModule(), $current_league, $funcTemplate);

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
        $subMod = tx_rnbase::makeInstance(MatchEdit::class);
        $content = $subMod->main($module, $current_league);

        return $content;
    }
}
