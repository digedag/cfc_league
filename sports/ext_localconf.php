<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');



tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_sports' /* sv type */,  'tx_cfcleague_sports_Football' /* sv key */,
	array(
		'title' => 'T3sports Football', 'description' => 'Special configurations for football.',
		'subtype' => 'football',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'sports/class.tx_cfcleague_sports_Football.php',
		'className' => 'tx_cfcleague_sports_Football',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_sports' /* sv type */,  'tx_cfcleague_sports_IceHockey' /* sv key */,
	array(
		'title' => 'T3sports IceHockey', 'description' => 'Special configurations for IceHockey.',
		'subtype' => 'icehockey',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'sports/class.tx_cfcleague_sports_IceHockey.php',
		'className' => 'tx_cfcleague_sports_IceHockey',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_sports' /* sv type */,  'tx_cfcleague_sports_Volleyball' /* sv key */,
	array(
		'title' => 'T3sports Volleyball', 'description' => 'Special configurations for Volleyball.',
		'subtype' => 'volleyball',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'sports/class.tx_cfcleague_sports_Volleyball.php',
		'className' => 'tx_cfcleague_sports_Volleyball',
	)
);

