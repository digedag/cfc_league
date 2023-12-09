<?php

namespace System25\T3sports\Module\Linker;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Domain\Model\RecordInterface;
use System25\T3sports\Utility\ServiceRegistry;

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
 * Neuen Verein anlegen.
 */
class NewClubLinker
{
    /**
     * @param RecordInterface $item
     * @param ToolBox $formTool
     * @param int $currentPid
     * @param array $options
     *
     * @return string
     */
    public function makeLink($item, ToolBox $formTool, $currentPid, $options)
    {
        $ret = '';
        $fields = [];
        // Gibt es auf der Seite schon Vereine?
        $fields['CLUB.PID'][OP_EQ_INT] = $currentPid;
        $cnt = ServiceRegistry::getTeamService()->searchClubs($fields, ['count' => 1]);
        $options = [];
        $options[ToolBox::OPTION_CONFIRM] = 0 == $cnt ? '###LABEL_MSG_CONFIRMNEWCLUBPAGE###' : '###LABEL_MSG_CONFIRMNEWCLUB###';
        $options[ToolBox::OPTION_HOVER_TEXT] = '###LABEL_ADDCLUB###';
        $ret .= $formTool->createNewLink('tx_cfcleague_club', $currentPid, '', $options);

        return $ret;
    }
}
