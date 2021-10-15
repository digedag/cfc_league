<?php

namespace System25\T3sports\Hooks;

use System25\T3sports\Model\Competition;
use System25\T3sports\Model\Profile;
use System25\T3sports\Utility\Misc;
use System25\T3sports\Utility\ServiceRegistry;
use tx_rnbase;

/**
 * *************************************************************
 * Copyright notice.
 *
 * (c) 2009-2021 Rene Nitzsche <rene@system25.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * *************************************************************
 */
class CommandMapHook
{
    /**
     * This hook is processed Bevor a commandmap is processed (delete, etc.).
     *
     * @param string $command
     *            the TCE command string: localize, copy, delete etc
     * @param string $table
     *            the table the data will be stored in
     * @param int $id
     *            the uid of the dataset we're working on
     * @param string $value
     * @param object $pObj
     *            the instance of the BE Form
     */
    public function processCmdmap_preProcess(&$command, $table, $id, $value, &$pObj)
    {
        if ('delete' == $command && 'tx_cfcleague_profiles' == $table) {
            // TODO: Check references
            $profile = tx_rnbase::makeInstance(Profile::class, $id);
            $refArr = ServiceRegistry::getProfileService()->checkReferences($profile);
            if (count($refArr) > 0) {
                // Abbruch
                $addInfo = '<p>';
                foreach ($refArr as $table => $data) {
                    $addInfo .= '<b>'.$table.':</b> '.count($data).'<br />';
                }
                $addInfo .= '</p>';
                Misc::tceError('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:label_msg_refError', $addInfo);
            }
        } elseif ('delete' == $command && 'tx_cfcleague_competition' == $table) {
            $competition = tx_rnbase::makeInstance(Competition::class, $id);
            $refArr = ServiceRegistry::getCompetitionService()->checkReferences($competition);
            if (count($refArr) > 0) {
                // Abbruch
                $addInfo = '<p>';
                foreach ($refArr as $table => $data) {
                    $addInfo .= '<b>'.$table.':</b> '.$data.'<br />';
                }
                $addInfo .= '</p>';
                Misc::tceError('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:label_msg_refError', $addInfo);
            }
        }
    }
}
