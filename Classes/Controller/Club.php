<?php

namespace System25\T3sports\Controller;

use System25\T3sports\Controller\Club\ClubStadiumHandler;
use Sys25\RnBase\Backend\Module\BaseModFunc;
use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Utility\Misc;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2021 Rene Nitzsche (rene@system25.de)
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
 * BaseModule to manage clubs, stadiums etc.
 */
class Club extends BaseModFunc
{
    /**
     * Method getFuncId.
     *
     * @return string
     */
    public function getFuncId()
    {
        return 'funcclubs';
    }

    /**
     * @param string $template
     * @param \tx_rnbase_configurations $configurations
     * @param \tx_rnbase_util_FormatUtil $formatter
     * @param ToolBox $formTool
     */
    protected function getContent($template, &$configurations, &$formatter, $formTool)
    {
        $selector = tx_rnbase::makeInstance('tx_cfcleague_selector');
        $selector->init($this->doc, $this->getModule());

        // Zuerst holen wir alle Tabs, erstellen die MenuItems und werten den Request aus
        $tabItems = [];
        $tabItems[] = tx_rnbase::makeInstance(ClubStadiumHandler::class);
        Misc::callHook('cfc_league', 'modClub_tabItems', [
            'tabItems' => &$tabItems,
        ], $this);

        $menuItems = [];
        foreach ($tabItems as $idx => $tabItem) {
            $menuItems[$idx] = $tabItem->getTabLabel();
            $tabItem->handleRequest($this->getModule());
        }

        $selectorStr = '';
        $club = $selector->showClubSelector($selectorStr, $this->getModule()
            ->getPid());
        $this->getModule()->selector = $selectorStr;

        if (!$club) {
            $addInfo = '###LABEL_MSG_CREATENEWCLUBNOW###';
            $linker = tx_rnbase::makeInstance('tx_cfcleague_mod1_linker_NewClub');
            $addInfo .= $linker->makeLink(null, $formTool, $this->getModule()
                ->getPid(), []);

            $content .= $this->getModule()
                ->getDoc()
                ->section('###LABEL_MSG_NOCLUBONPAGE###', $addInfo, 0, 1, self::ICON_INFO);

            return $content;
        }

        // Wenn ein Team gefunden ist, dann können wir das Modul schreiben
        $menu = $formTool->showTabMenu($this->getModule()
            ->getPid(), 'clubtools', $this->getModule()
            ->getName(), $menuItems);

        $tabs .= $menu['menu'];
        $tabs .= '<div style="display: block; border: 1px solid #a2aab8;" ></div>';

        $this->pObj->tabs = $tabs;

        $handler = $tabItems[$menu['value']];
        if (is_object($handler)) {
            $modContent .= $handler->showScreen($club, $this->getModule());
        }

        $content .= $formTool->getTCEForm()->printNeededJSFunctions_top();
        $content .= $modContent;
        // Den JS-Code für Validierung einbinden
        $content .= $formTool->getTCEForm()->printNeededJSFunctions();

        return $content;
    }
}
