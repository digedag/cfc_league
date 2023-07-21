<?php

namespace System25\T3sports\Controller\Profile;

use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Utility\T3General;
use Sys25\RnBase\Utility\TYPO3;

/*
 *  Copyright notice
 *
 *  (c) 2007-2023 Rene Nitzsche (rene@system25.de)
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

class ShowItem
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    public function getInfoScreen($table, $uid)
    {
        $urlParams = ['uid' => $uid, 'table' => $table];
        $routeIdent = 'show_item';
        // typo3/record/info
        $uriStr = BackendUtility::getModuleUrl($routeIdent, $urlParams);
        if (0 === strpos($uriStr, '/typo3')) {
            $uriStr = substr($uriStr, 7);
        }
        $uriStr = T3General::getIndpEnv('TYPO3_REQUEST_DIR').$uriStr;

        return sprintf('<iframe src="%s" width="100%%" height="800px"></iframe>', $uriStr);
    }
}
