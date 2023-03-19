<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$_EXTKEY = 'cfc_league';

\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_group=1
');
\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_saison=1
');
\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_competition=1
');
\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_club=1
');
\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_teams=1
');
\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_profiles=1
');
\Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_team_notes=1
');

// Die TCE-Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'System25\T3sports\Hooks\AfterDBHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'System25\T3sports\Hooks\TceHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'System25\T3sports\Hooks\CommandMapHook';

if (\Sys25\RnBase\Utility\Environment::isBackend()) {
    // Einbindung einer PageTSConfig
    \Sys25\RnBase\Utility\Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:cfc_league/Configuration/TypoScript/TSconfig/pageTSconfig.txt">');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['t3sports_logoSelect'] = [
        'nodeName' => 't3sLogoSelect',
        'priority' => '70',
        'class' => 'System25\T3sports\Form\Element\LogoSelect',
    ];
}

\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.ticker', '100');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal', '10');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.header', '11');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.penalty', '12');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.own', '30');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.assist', '31');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.penalty.forgiven', '32');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.corner', '33');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.yellow', '70');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.yellowred', '71');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.red', '72');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.changeout', '80');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.changein', '81');
\System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.captain', '200');

\System25\T3sports\Utility\Misc::registerFormation('', '0');
\System25\T3sports\Utility\Misc::registerFormation('3-5-2', '1-3-5-2');
\System25\T3sports\Utility\Misc::registerFormation('3-4-3', '1-3-4-3');
\System25\T3sports\Utility\Misc::registerFormation('4-2-3-1', '1-4-2-3-1');
\System25\T3sports\Utility\Misc::registerFormation('4-3-3', '1-4-3-3');
\System25\T3sports\Utility\Misc::registerFormation('4-4-2', '1-4-4-2');
\System25\T3sports\Utility\Misc::registerFormation('4-5-1', '1-4-5-1');
\System25\T3sports\Utility\Misc::registerFormation('5-3-2', '1-5-3-2');
\System25\T3sports\Utility\Misc::registerFormation('5-4-1', '1-5-4-1');

\System25\T3sports\Utility\Misc::registerTableStrategy('default', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition_tablestrategy_default', '');

// Include services
\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Stadiums' /* sv key */ ,
    [
        'title' => 'T3sports stadium service', 'description' => 'Operations for stadiums', 'subtype' => 'stadiums',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Stadiums',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Saison' /* sv key */ ,
    [
        'title' => 'T3sports saison service', 'description' => 'Operations for saisons', 'subtype' => 'saison',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Saison',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Competition' /* sv key */ ,
    [
        'title' => 'T3sports stadium service', 'description' => 'Operations for competitions', 'subtype' => 'competition',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Competition',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Match' /* sv key */ ,
    [
        'title' => 'T3sports match service', 'description' => 'Operations for matches', 'subtype' => 'match',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Match',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Group' /* sv key */ ,
    [
        'title' => 'T3sports group service', 'description' => 'Operations for groups', 'subtype' => 'group',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Group',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Teams' /* sv key */ ,
    [
        'title' => 'T3sports team service', 'description' => 'Operations for teams', 'subtype' => 'teams',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Teams',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_srv' /* sv type */ ,
    'tx_cfcleague_services_Profiles' /* sv key */ ,
    [
        'title' => 'T3sports profile service', 'description' => 'Operations for profiles', 'subtype' => 'profiles',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_Profiles',
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_profiletype' /* sv type */ ,
    'tx_cfcleague_services_ProfileTypes' /* sv key */ ,
    [
        'title' => 'Base profile types', 'description' => 'Defines the base types for profiles like players, coaches...', 'subtype' => 'basetypes',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => 'tx_cfcleague_services_ProfileTypes',
    ]
);

// ---------------
// Services for kind of sports
// ---------------

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_sports' /* sv type */ ,
    'tx_cfcleague_sports_Football' /* sv key */ ,
    [
        'title' => 'T3sports Football', 'description' => 'Special configurations for football.',
        'subtype' => 'football',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => System25\T3sports\Sports\Football::class,
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_sports' /* sv type */ ,
    'tx_cfcleague_sports_Handball' /* sv key */ ,
    [
        'title' => 'T3sports Handball', 'description' => 'Special configurations for handball.',
        'subtype' => 'handball',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => System25\T3sports\Sports\Handball::class,
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_sports' /* sv type */ ,
    'tx_cfcleague_sports_IceHockey' /* sv key */ ,
    [
        'title' => 'T3sports IceHockey', 'description' => 'Special configurations for IceHockey.',
        'subtype' => 'icehockey',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => System25\T3sports\Sports\IceHockey::class,
    ]
);

\Sys25\RnBase\Utility\Extensions::addService(
    $_EXTKEY,
    't3sports_sports' /* sv type */ ,
    'tx_cfcleague_sports_Volleyball' /* sv key */ ,
    [
        'title' => 'T3sports Volleyball', 'description' => 'Special configurations for Volleyball.',
        'subtype' => 'volleyball',
        'available' => true, 'priority' => 50, 'quality' => 50,
        'os' => '', 'exec' => '',
        'className' => System25\T3sports\Sports\Volleyball::class,
    ]
);
