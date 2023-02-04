<?php

namespace System25\T3sports\Utility;

use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Backend\Utility\Icons;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Typo3Classes;
use tx_rnbase;

/***************************************************************
*  Copyright notice
*
*  (c) 2009-2023 Rene Nitzsche (rene@system25.de)
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
 * Kleine Methoden.
 */
class Misc
{
    /**
     * Zwei Arrays zusammenführen. Sollte eines der Array leer sein, dann wird es ignoriert.
     * Somit werden unnötige 0-Werte vermieden.
     */
    public static function mergeArrays($arr1, $arr2)
    {
        $ret = $arr1[0] ? $arr1 : 0;
        if ($ret && $arr2) {
            $ret = array_merge($ret, $arr2);
        } elseif ($arr2) {
            $ret = $arr2;
        }

        return $ret;
    }

    /**
     * Register a new matchnote.
     *
     * @param string $label
     * @param mixed $typeId
     */
    public static function registerMatchNote($label, $typeId)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'][] = [$label, $typeId];
    }

    /**
     * De-Register a matchnote.
     *
     * @param mixed $typeId
     */
    public static function removeMatchNote($typeId)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'])) {
            return;
        }
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'] as $idx => $note) {
            list(, $type) = $note;
            if ($type === $typeId) {
                unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['matchnotetypes'][$idx]);

                break;
            }
        }
    }

    /**
     * Register a new table strategy.
     *
     * @param string $label
     * @param string $comparator
     */
    public static function registerTableStrategy(string $id, string $label, string $comparator)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['tablestrategy'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['tablestrategy'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['tablestrategy'][$id] = [
            'label' => $label,
            'comparator' => $comparator,
        ];
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public static function lookupTableStrategy(string $id): array
    {
        $ret = [];
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['tablestrategy'][$id])) {
            $ret = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['tablestrategy'][$id];
        }

        return $ret;
    }

    /**
     * Register a new match formation.
     *
     * @param string $label
     * @param mixed $formationString
     */
    public static function registerFormation($label, $formationString)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cfc_league']['formations'][] = [$label, $formationString];
    }

    /**
     * Prints out the error.
     *
     * @param 	string 	$error
     */
    public static function tceError($error, $addinfo = '')
    {
        $error_doc = tx_rnbase::makeInstance(Typo3Classes::getDocumentTemplateClass());
        $error_doc->backPath = '';

        $content .= $error_doc->startPage('T3sports error Output');
        $content .= '
            <br/><br/>
            <table border="0" cellpadding="1" cellspacing="1" width="300" align="center">';

        $content .= '
            <tr class="bgColor5">
                <td colspan="2" align="center"><strong>Fehler</strong></td>
            </tr>';

        $content .= '
            <tr class="bgColor4">
                <td valign="top"><img'.Icons::skinImg('', 'gfx/icon_fatalerror.gif', 'width="18" height="16"').' alt="" /></td>
                <td>'.$GLOBALS['LANG']->sL($error, 0).'</td>
            </tr>';
        if ($addinfo) {
            $content .= '
            <tr class="bgColor4">
                <td valign="top"></td>
                <td>'.$addinfo.'</td>
            </tr>';
        }

        $content .= '
            <tr>
                <td colspan="2" align="center"><br />'.
                    '<form action="'.htmlspecialchars($_SERVER['HTTP_REFERER']).'"><input type="submit" value="Weiter" onclick="document.location='.htmlspecialchars($_SERVER['HTTP_REFERER']).'return false;" /></form>'.
                '</td>
            </tr>';

        $content .= '</table>';

        $content .= $error_doc->endPage();
        echo $content;
        exit;
    }

    /**
     * Backend method to determine if a page is below another page.
     *
     * @param int $uid
     * @param string $clause
     *
     * @return array[int]
     */
    public static function getPagePath($uid, $clause = '')
    {
        $loopCheck = 100;
        $output = []; // We return an array of uids
        $output[] = $uid;
        while (0 != $uid && $loopCheck > 0) {
            --$loopCheck;

            //'uid,pid,title,t3ver_oid,t3ver_wsid,t3ver_swapmode',
            $rows = Connection::getInstance()->doSelect('*', 'pages', [
                'where' => 'uid='.intval($uid).(strlen(trim($clause)) ? ' AND '.$clause : ''),
            ]);
            if (!empty($rows)) {
                $row = reset($rows);
                BackendUtility::workspaceOL('pages', $row);
                BackendUtility::fixVersioningPid('pages', $row);

                $uid = $row['pid'];
                $output[] = $uid;
            } else {
                break;
            }
        }

        return $output;
    }
}
