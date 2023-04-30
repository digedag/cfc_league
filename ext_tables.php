<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$_EXTKEY = 'cfc_league';

\Sys25\RnBase\Utility\Extensions::addLLrefForTCAdescr('tx_cfcleague_competition', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition.xlf');
\Sys25\RnBase\Utility\Extensions::addLLrefForTCAdescr('tx_cfcleague_competition_penalty', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition_penalty.xlf');
\Sys25\RnBase\Utility\Extensions::addLLrefForTCAdescr('tx_cfcleague_profiles', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_profiles.xlf');
\Sys25\RnBase\Utility\Extensions::addLLrefForTCAdescr('tx_cfcleague_club', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_club.xlf');
\Sys25\RnBase\Utility\Extensions::addLLrefForTCAdescr('tx_cfcleague_games', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_games.xlf');
\Sys25\RnBase\Utility\Extensions::addLLrefForTCAdescr('tx_cfcleague_teams', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_teams.xlf');

if (\Sys25\RnBase\Utility\Extensions::isLoaded('rgmediaimages')) {
    \Sys25\RnBase\Utility\Extensions::addStaticFile($_EXTKEY, 'Configurations/TypoScript/video', 'Ext: cfcleague video support');
}

if (\Sys25\RnBase\Utility\Environment::isBackend()) {
    if (!\Sys25\RnBase\Utility\TYPO3::isTYPO121OrHigher()) {
        $modName = 'web_CfcLeagueM1';
        \Sys25\RnBase\Utility\Extensions::registerModule(
            'cfc_league',
            'web',
            'M1',
            'bottom', [], [
                'access' => 'user,group',
                'routeTarget' => System25\T3sports\Module\T3sportsModule::class,
                'icon' => 'EXT:cfc_league/Resources/Public/Icons/module-t3sports.svg',
                'labels' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_mod.xlf',
        ]);
        \Sys25\RnBase\Utility\Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Competition::class, '', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_competition');
        \Sys25\RnBase\Utility\Extensions::insertModuleFunction($modName, System25\T3sports\Controller\MatchTicker::class, '', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:match_ticker');
        \Sys25\RnBase\Utility\Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Team::class, '', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_team');
        \Sys25\RnBase\Utility\Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Club::class, '', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_club');
        \Sys25\RnBase\Utility\Extensions::insertModuleFunction($modName, System25\T3sports\Controller\Profile::class, '', 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_profiles');
    }

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
}
