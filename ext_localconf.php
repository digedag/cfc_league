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

// Der TCE-Hook für das Anlegen neuer Spieler
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/mod1/class.tx_cfcleague_mod1_tcehook.php:tx_cfcleague_mod1_tcehook';


?>
