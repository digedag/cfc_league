<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2019 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_mod_BaseModFunc');

tx_rnbase::load('tx_rnbase_util_Templates');
tx_rnbase::load('tx_rnbase_util_BaseMarker');
tx_rnbase::load('tx_rnbase_util_TYPO3');

/**
 * Die Klasse ist die Einstiegsklasse für das Modul "Wettbewerbe verwalten".
 */
class Tx_Cfcleague_Controller_Competition extends tx_rnbase_mod_BaseModFunc
{
    public $doc;

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

    /**
     * Verwaltet die Erstellung von Spielplänen von Ligen.
     *
     * @param string $template
     * @param tx_rnbase_configurations $configurations
     * @param tx_rnbase_util_FormatUtil $formatter
     * @param tx_rnbase_util_FormTool $formTool
     *
     * @return string
     */
    protected function getContent($template, &$configurations, &$formatter, $formTool)
    {
        global $LANG;
        // Zuerst mal müssen wir die passende Liga auswählen lassen:
        // Entweder global über die Datenbank oder die Ligen der aktuellen Seite
        // Selector-Instanz bereitstellen
        $this->selector = tx_rnbase::makeInstance('tx_cfcleague_selector');
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

        $tabs .= $menu['menu'];
        $tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

        $this->pObj->tabs = $tabs;

        switch ($menu['value']) {
            case 0:
                $funcContent = $this->showEditMatches($current_league, $this->getModule());

                break;
            case 1:
                $mod = tx_rnbase::makeInstance('Tx_Cfcleague_Controller_Competition_Teams');
                $funcContent = $mod->main($this->getModule(), $current_league);

                break;
            case 2:
                $mod = tx_rnbase::makeInstance('Tx_Cfcleague_Controller_Competition_MatchTable');
                $funcContent = $mod->main($this->getModule(), $current_league);

                break;
            case 3:
                $funcTemplate = tx_rnbase_util_Templates::getSubpart($template, '###FUNC_DFBSYNC###');
                $mod = tx_rnbase::makeInstance('Tx_Cfcleague_Controller_Competition_DfbSync');
                $funcContent = $mod->main($this->getModule(), $current_league, $funcTemplate);

                break;
        }
        $content .= $formTool->form->printNeededJSFunctions_top();
        $content .= $funcContent;
        // Den JS-Code für Validierung einbinden
        $content .= $formTool->form->printNeededJSFunctions();

        $modContent = tx_rnbase_util_Templates::getSubpart($template, '###MAIN###');
        $modContent = tx_rnbase_util_Templates::substituteMarkerArrayCached($modContent, [
            '###CONTENT###' => $content,
        ]);

        // $content .= $this->formTool->form->JSbottom('editform');
        return $modContent;
    }

    private function showEditMatches($current_league, $module)
    {
        $subMod = tx_rnbase::makeInstance('Tx_Cfcleague_Controller_Competition_MatchEdit');
        $content = $subMod->main($module, $current_league);

        return $content;
    }
}
