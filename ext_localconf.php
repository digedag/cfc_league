<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_group=1
');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_saison=1
');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_competition=1
');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_club=1
');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_teams=1
');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_profiles=1
');
tx_rnbase_util_Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_team_notes=1
');

// Die TCE-Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'System25\T3sports\Hooks\AfterDBHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'System25\T3sports\Hooks\TceHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'System25\T3sports\Hooks\CommandMapHook';


// if(tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
//     tx_rnbase_util_Extensions::registerAjaxHandler('T3sports::saveTickerMessage', 'EXT:' . $_EXTKEY . '/mod1/class.tx_cfcleague_mod1_AjaxTicker.php:tx_cfcleague_mod1_AjaxTicker->ajaxSaveTickerMessage');
// }

if (TYPO3_MODE == 'BE') {
    // Einbindung einer PageTSConfig
    tx_rnbase_util_Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:cfc_league/mod1/pageTSconfig.txt">');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['t3sports_logoSelect'] = [
        'nodeName' => 't3sLogoSelect',
        'priority' => '70',
        'class' => 'System25\T3sports\Form\Element\LogoSelect',
    ];
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
tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');


tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Stadiums' /* sv key */,
	array(
		'title' => 'T3sports stadium service', 'description' => 'Operations for stadiums', 'subtype' => 'stadiums',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Stadiums.php',
		'className' => 'tx_cfcleague_services_Stadiums',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Saison' /* sv key */,
	array(
		'title' => 'T3sports saison service', 'description' => 'Operations for saisons', 'subtype' => 'saison',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Saison.php',
		'className' => 'tx_cfcleague_services_Saison',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Competition' /* sv key */,
	array(
		'title' => 'T3sports stadium service', 'description' => 'Operations for competitions', 'subtype' => 'competition',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Competition.php',
		'className' => 'tx_cfcleague_services_Competition',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Match' /* sv key */,
	array(
		'title' => 'T3sports match service', 'description' => 'Operations for matches', 'subtype' => 'match',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Match.php',
		'className' => 'tx_cfcleague_services_Match',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Group' /* sv key */,
	array(
		'title' => 'T3sports group service', 'description' => 'Operations for groups', 'subtype' => 'group',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Group.php',
		'className' => 'tx_cfcleague_services_Group',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Teams' /* sv key */,
	array(
		'title' => 'T3sports team service', 'description' => 'Operations for teams', 'subtype' => 'teams',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Teams.php',
		'className' => 'tx_cfcleague_services_Teams',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Profiles' /* sv key */,
	array(
		'title' => 'T3sports profile service', 'description' => 'Operations for profiles', 'subtype' => 'profiles',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Profiles.php',
		'className' => 'tx_cfcleague_services_Profiles',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_profiletype' /* sv type */,  'tx_cfcleague_services_ProfileTypes' /* sv key */,
	array(
		'title' => 'Base profile types', 'description' => 'Defines the base types for profiles like players, coaches...', 'subtype' => 'basetypes',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_ProfileTypes.php',
		'className' => 'tx_cfcleague_services_ProfileTypes',
	)
);

// ---------------
// Services for kind of sports
// ---------------

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

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_sports' /* sv type */,  'tx_cfcleague_sports_Handball' /* sv key */,
    array(
        'title' => 'T3sports Handball', 'description' => 'Special configurations for handball.',
        'subtype' => 'handball',
        'available' => TRUE, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'sports/class.tx_cfcleague_sports_Handball.php',
        'className' => 'tx_cfcleague_sports_Handball',
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

