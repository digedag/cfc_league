<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Rene Nitzsche (rene@system25.de)
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
class tx_cfcleague_mod1_linker_NewClub
{
    /**
     * @param tx_mkhoga_models_Company $item
     * @param tx_rnbase_util_FormTool $formTool
     * @param int $currentPid
     * @param array $options
     *
     * @return string
     */
    public function makeLink($item, $formTool, $currentPid, $options)
    {
        $ret = '';
        // Gibt es auf der Seite schon Vereine?
        $fields['CLUB.PID'][OP_EQ_INT] = $currentPid;
        $cnt = tx_cfcleague_util_ServiceRegistry::getTeamService()->searchClubs($fields, array('count' => 1));
        $options = array();
        $options['confirm'] = 0 == $cnt ? $GLOBALS['LANG']->getLL('label_msg_confirmNewClubPage') : $GLOBALS['LANG']->getLL('label_msg_confirmNewClub');
        $options['title'] = $GLOBALS['LANG']->getLL('label_addclub');
        $ret .= $formTool->createNewLink('tx_cfcleague_club', $currentPid, '', $options);

        return $ret;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/linker/class.tx_cfcleague_mod1_linker_NewClub.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/mod1/linker/class.tx_cfcleague_mod1_linker_NewClub.php'];
}
