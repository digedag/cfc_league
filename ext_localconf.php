<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_group=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_saison=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_competition=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_club=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_teams=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_profiles=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_cfcleague_team_notes=1
');

// Die TCE-Hooks
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_tceAfterDB.php:tx_cfcleague_hooks_tceAfterDB';
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_tcehook.php:tx_cfcleague_hooks_tcehook';
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_tcehook.php:tx_cfcleague_hooks_tcehook';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_cmhooks.php:tx_cfcleague_hooks_cmhooks';
require_once(t3lib_extMgm::extPath('rn_base').'class.tx_rnbase.php');

// Include services
require_once(t3lib_extMgm::extPath('cfc_league').'services/ext_localconf.php');

?>
