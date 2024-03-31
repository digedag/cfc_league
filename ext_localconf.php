<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$_EXTKEY = 'cfc_league';

Sys25\RnBase\Utility\Extensions::addUserTSConfig('
    options.saveDocNew.tx_cfcleague_group=1
    options.saveDocNew.tx_cfcleague_saison=1
    options.saveDocNew.tx_cfcleague_competition=1
    options.saveDocNew.tx_cfcleague_club=1
    options.saveDocNew.tx_cfcleague_teams=1
    options.saveDocNew.tx_cfcleague_profiles=1
    options.saveDocNew.tx_cfcleague_team_notes=1
');

// Die TCE-Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'System25\T3sports\Hooks\AfterDBHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'System25\T3sports\Hooks\TceHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'System25\T3sports\Hooks\CommandMapHook';

if (Sys25\RnBase\Utility\Environment::isBackend()) {
    // Einbindung einer PageTSConfig
    if (!Sys25\RnBase\Utility\TYPO3::isTYPO121OrHigher()) {
        Sys25\RnBase\Utility\Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:cfc_league/Configuration/page.tsconfig">');
    }

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['t3sports_logoSelect'] = [
        'nodeName' => 't3sLogoSelect',
        'priority' => '70',
        'class' => 'System25\T3sports\Form\Element\LogoSelect',
    ];
}

System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.ticker', '100');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal', '10');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.header', '11');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.penalty', '12');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.own', '30');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.goal.assist', '31');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.penalty.forgiven', '32');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.corner', '33');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.yellow', '70');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.yellowred', '71');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.red', '72');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.changeout', '80');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.changein', '81');
System25\T3sports\Utility\Misc::registerMatchNote('LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type.captain', '200');

System25\T3sports\Utility\Misc::registerFormation('', '0');
System25\T3sports\Utility\Misc::registerFormation('3-5-2', '1-3-5-2');
System25\T3sports\Utility\Misc::registerFormation('3-4-3', '1-3-4-3');
System25\T3sports\Utility\Misc::registerFormation('4-2-3-1', '1-4-2-3-1');
System25\T3sports\Utility\Misc::registerFormation('4-3-3', '1-4-3-3');
System25\T3sports\Utility\Misc::registerFormation('4-4-2', '1-4-4-2');
System25\T3sports\Utility\Misc::registerFormation('4-5-1', '1-4-5-1');
System25\T3sports\Utility\Misc::registerFormation('5-3-2', '1-5-3-2');
System25\T3sports\Utility\Misc::registerFormation('5-4-1', '1-5-4-1');

System25\T3sports\Utility\Misc::registerTableStrategy('default', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition_tablestrategy_default', '');

// ---------------
// Services for kind of sports
// ---------------
if (!Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    $provider = System25\T3sports\Sports\ServiceLocator::getInstance();
    $provider->addSports(new System25\T3sports\Sports\Football());
    $provider->addSports(new System25\T3sports\Sports\Handball());
    $provider->addSports(new System25\T3sports\Sports\IceHockey());
    $provider->addSports(new System25\T3sports\Sports\Volleyball());
    $provider->addSports(new System25\T3sports\Sports\Judo());
}
