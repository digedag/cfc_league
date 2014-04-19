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
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_tceAfterDB.php:tx_cfcleague_hooks_tceAfterDB';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_tcehook.php:tx_cfcleague_hooks_tcehook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_tcehook.php:tx_cfcleague_hooks_tcehook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_cfcleague_hooks_cmhooks.php:tx_cfcleague_hooks_cmhooks';

$GLOBALS ['TYPO3_CONF_VARS']['BE']['AJAX']['T3sports::saveTickerMessage'] = 'EXT:' . $_EXTKEY . '/mod1/class.tx_cfcleague_mod1_AjaxTicker.php:tx_cfcleague_mod1_AjaxTicker->ajaxSaveTickerMessage';

require_once(t3lib_extMgm::extPath('rn_base').'class.tx_rnbase.php');

if (TYPO3_MODE == 'BE') {
	// Einbindung einer PageTSConfig
	t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:cfc_league/mod1/pageTSconfig.txt">');
}

tx_rnbase::load('tx_cfcleague_util_Misc');

tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.ticker', '100');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal', '10');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.header', '11');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.penalty', '12');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.own', '30');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.assist', '31');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.penalty.forgiven', '32');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.corner', '33');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.yellow', '70');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.yellowred', '71');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.red', '72');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.changeout', '80');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.changein', '81');
tx_cfcleague_util_Misc::registerMatchNote('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.captain', '200');

tx_cfcleague_util_Misc::registerFormation('', '0');
tx_cfcleague_util_Misc::registerFormation('3-5-2', '1-3-5-2');
tx_cfcleague_util_Misc::registerFormation('3-4-3', '1-3-4-3');
tx_cfcleague_util_Misc::registerFormation('4-2-3-1', '1-4-2-3-1');
tx_cfcleague_util_Misc::registerFormation('4-3-3', '1-4-3-3');
tx_cfcleague_util_Misc::registerFormation('4-4-2', '1-4-4-2');
tx_cfcleague_util_Misc::registerFormation('4-5-1', '1-4-5-1');
tx_cfcleague_util_Misc::registerFormation('5-3-2', '1-5-3-2');
tx_cfcleague_util_Misc::registerFormation('5-4-1', '1-5-4-1');


// Include services
require_once(t3lib_extMgm::extPath('cfc_league').'services/ext_localconf.php');
require_once(t3lib_extMgm::extPath('cfc_league').'sports/ext_localconf.php');

