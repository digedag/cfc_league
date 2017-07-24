<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2014 Rene Nitzsche <rene@system25.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

// unset($MCONF);


// require_once('conf.php');
// require_once($BACK_PATH.'init.php');

// tx_rnbase::load('tx_rnbase_util_TYPO3');
// if(!tx_rnbase_util_TYPO3::isTYPO62OrHigher())
// 	require_once(PATH_typo3.'template.php');

// $GLOBALS['LANG']->includeLLFile('EXT:cfc_league/mod1/locallang.xml');
// $GLOBALS['LANG']->includeLLFile('EXT:cfc_league/locallang_db.xml');
// $GLOBALS['BE_USER']->modAccess($GLOBALS['MCONF'], 1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]


// Make instance:
$SOBE = tx_rnbase::makeInstance('tx_cfcleague_mod1_Module');
$SOBE->init();

$SOBE->main();
$SOBE->printContent();

