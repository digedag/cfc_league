<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}
$_EXTKEY = 'cfc_league';

tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_competition', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition.xml');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_competition_penalty', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition_penalty.xml');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_profiles', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_profiles.xml');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_club', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_club.xml');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_games', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_games.xml');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_teams', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_teams.xml');

if (tx_rnbase_util_Extensions::isLoaded('rgmediaimages')) {
    tx_rnbase_util_Extensions::addStaticFile($_EXTKEY, 'Configurations/TypoScript/video', 'Ext: cfcleague video support');
}

if (TYPO3_MODE == 'BE') {
    $modName = 'web_CfcLeagueM1';
    tx_rnbase_util_Extensions::registerModule('cfc_league', 'web', 'M1', 'bottom', [], [
        'access' => 'user,group',
        'routeTarget' => 'tx_cfcleague_mod1_Module',
        'icon' => 'EXT:cfc_league/Resources/Public/Icons/module-t3sports.svg',
        'labels' => 'LLL:EXT:cfc_league/mod1/locallang_mod.xml',
    ]);
    tx_rnbase_util_Extensions::insertModuleFunction($modName, 'Tx_Cfcleague_Controller_Competition', '', 'LLL:EXT:cfc_league/mod1/locallang.xml:mod_competition');
    tx_rnbase_util_Extensions::insertModuleFunction($modName, System25\T3sports\Controller\MatchTicker::class, '', 'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker');
    tx_rnbase_util_Extensions::insertModuleFunction($modName, 'Tx_Cfcleague_Controller_Team', '', 'LLL:EXT:cfc_league/mod1/locallang.xml:mod_team');
    tx_rnbase_util_Extensions::insertModuleFunction($modName, 'Tx_Cfcleague_Controller_Club', '', 'LLL:EXT:cfc_league/mod1/locallang.xml:mod_club');
    tx_rnbase_util_Extensions::insertModuleFunction($modName, 'Tx_Cfcleague_Controller_Profile', '', 'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles');

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
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
