<?php
global $_EXTKEY;

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/cfc_league/mod1/');
//$BACK_PATH='../../../';
$BACK_PATH='../../../../typo3/';
$MCONF['name']='web_txcfcleagueM1';

	
$MCONF['access']='user,group';
$MCONF['script']='index.php';

//$MLANG['default']['tabs_images']['tab'] = '../moduleicon.gif';
$MLANG['default']['tabs_images']['tab'] = '../icon_table.gif';
$MLANG['default']['ll_ref']='LLL:EXT:cfc_league/mod1/locallang_mod.xml';

// Mögliche Icons im BE für die Funktion doc->icons()
define('ICON_OK', -1);
define('ICON_INFO', 1);
define('ICON_WARN', 2);
define('ICON_FATAL', 3);


?>
