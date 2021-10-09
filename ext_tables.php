<?php

use Sys25\RnBase\Utility\Extensions;

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}
$_EXTKEY = 'cfc_league';

Extensions::addLLrefForTCAdescr('tx_cfcleague_competition', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition.xml');
Extensions::addLLrefForTCAdescr('tx_cfcleague_competition_penalty', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition_penalty.xml');
Extensions::addLLrefForTCAdescr('tx_cfcleague_profiles', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_profiles.xml');
Extensions::addLLrefForTCAdescr('tx_cfcleague_club', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_club.xml');
Extensions::addLLrefForTCAdescr('tx_cfcleague_games', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_games.xml');
Extensions::addLLrefForTCAdescr('tx_cfcleague_teams', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_teams.xml');

if (Extensions::isLoaded('rgmediaimages')) {
    Extensions::addStaticFile($_EXTKEY, 'Configurations/TypoScript/video', 'Ext: cfcleague video support');
}

if (TYPO3_MODE == 'BE') {
    $modName = 'web_CfcLeagueM1';
    Extensions::registerModule('cfc_league', 'web', 'M1', 'bottom', [], [
        'access' => 'user,group',
        'routeTarget' => 'tx_cfcleague_mod1_Module',
        'icon' => 'EXT:cfc_league/Resources/Public/Icons/module-t3sports.svg',
        'labels' => 'LLL:EXT:cfc_league/mod1/locallang_mod.xml',
    ]);
    Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Competition::class, '', 'LLL:EXT:cfc_league/mod1/locallang.xml:mod_competition');
    Extensions::insertModuleFunction($modName, System25\T3sports\Controller\MatchTicker::class, '', 'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker');
    Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Team::class, '', 'LLL:EXT:cfc_league/mod1/locallang.xml:mod_team');
    Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Club::class, '', 'LLL:EXT:cfc_league/mod1/locallang.xml:mod_club');
    Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Profile::class, '', 'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles');

    $iconRegistry = tx_rnbase::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $icons = [
        'ext-cfcleague-clubs-default' => 'icon_tx_cfcleague_clubs.gif',
        'ext-cfcleague-competition-default' => 'icon_tx_cfcleague_competition.gif',
        'ext-cfcleague-competition-penalty-default' => 'icon_tx_cfcleague_competition_penalty.gif',
        'ext-cfcleague-games-default' => 'icon_table.gif',
        'ext-cfcleague-group-default' => 'icon_tx_cfcleague_group.gif',
        'ext-cfcleague-matchnotes-default' => 'icon_table.gif',
        'ext-cfcleague-notetypes-default' => 'icon_table.gif',
        'ext-cfcleague-profiles-default' => 'icon_tx_cfcleague_profiles.gif',
        'ext-cfcleague-saison-default' => 'icon_tx_cfcleague_saison.gif',
        'ext-cfcleague-statiums-default' => 'icon_table.gif',
        'ext-cfcleague-teamnotes-default' => 'icon_tx_cfcleague_teams.gif',
        'ext-cfcleague-teams-default' => 'icon_tx_cfcleague_teams.gif',
        'ext-cfcleague-ext-default' => 'module-t3sports.svg',
        'ext-cfcleague-folder-default' => 't3sports-folder-contains.svg',
    ];
    foreach ($icons as $identifier => $path) {
        $iconRegistry->registerIcon($identifier, \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class, [
            'source' => 'EXT:cfc_league/Resources/Public/Icons/'.$path,
        ]);
    }

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-cfcleague'] = 'ext-cfcleague-folder-default';

    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_folder',
        'cfcleague',
        'ext-cfcleague-ext-default',
    ];
}
