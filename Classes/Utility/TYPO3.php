<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2016 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_parameters');

class Tx_Cfcleague_Utility_TYPO3
{
    /**
     * Backend method to determine if a page is below another page.
     *
     * @param int $uid
     * @param string $clause
     *
     * @return array[int]
     */
    public function getPagePath($uid, $clause = '')
    {
        $loopCheck = 100;
        $output = []; // We return an array of uids
        $output[] = $uid;
        while (0 != $uid && $loopCheck > 0) {
            --$loopCheck;

            //'uid,pid,title,t3ver_oid,t3ver_wsid,t3ver_swapmode',
            $rows = Tx_Rnbase_Database_Connection::getInstance()->doSelect('*', 'pages', [
                    'where' => 'uid='.intval($uid).(strlen(trim($clause)) ? ' AND '.$clause : ''),
            ]);
            if (!empty($rows)) {
                $row = reset($rows);
                Tx_Rnbase_Backend_Utility::workspaceOL('pages', $row);
                Tx_Rnbase_Backend_Utility::fixVersioningPid('pages', $row);

                $uid = $row['pid'];
                $output[] = $uid;
            } else {
                break;
            }
        }

        return $output;
    }
}
