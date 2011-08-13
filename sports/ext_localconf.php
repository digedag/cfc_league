<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');



t3lib_extMgm::addService($_EXTKEY,  't3sports_sports' /* sv type */,  'tx_cfcleague_sports_Football' /* sv key */,
	array(
		'title' => 'T3sports Football', 'description' => 'Special configurations for football.', 
		'subtype' => 'football',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sports/class.tx_cfcleague_sports_Football.php',
		'className' => 'tx_cfcleague_sports_Football',
	)
);

t3lib_extMgm::addService($_EXTKEY,  't3sports_sports' /* sv type */,  'tx_cfcleague_sports_IceHockey' /* sv key */,
	array(
		'title' => 'T3sports IceHockey', 'description' => 'Special configurations for IceHockey.', 
		'subtype' => 'icehockey',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sports/class.tx_cfcleague_sports_IceHockey.php',
		'className' => 'tx_cfcleague_sports_IceHockey',
	)
);


?>