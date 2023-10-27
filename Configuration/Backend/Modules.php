<?php

return [
    'web_CfcLeagueM1' => [
        'parent' => 'web',
        'position' => ['bottom'],
        'access' => 'user',
        'workspaces' => '*',
        'iconIdentifier' => 'ext-cfcleague-ext-default',
        'path' => '/module/web/t3sports',
        'labels' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'CfcLeague',
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Module\T3sportsModule::class,
            ],
        ],
    ],
    'web_CfcLeagueM1_competition' => [
        'parent' => 'web_CfcLeagueM1',
        'access' => 'user',
        'workspaces' => '*',
        'iconIdentifier' => 'ext-cfcleague-ext-default',
        'path' => '/module/web/t3sports/competition',
        'labels' => [
            'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_competition',
        ],
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Controller\Competition::class.'::main',
            ],
        ],
        'moduleData' => [
            'langFiles' => [],
            'pages' => '0',
            'depth' => 0,
        ],
    ],
    'web_CfcLeagueM1_ticker' => [
        'parent' => 'web_CfcLeagueM1',
        'access' => 'user',
        'workspaces' => '*',
        'iconIdentifier' => 'ext-cfcleague-ext-default',
        'path' => '/module/web/t3sports/ticker',
        'labels' => [
            'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:match_ticker',
        ],
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Controller\MatchTicker::class.'::main',
            ],
        ],
        'moduleData' => [
            'langFiles' => [],
            'pages' => '0',
            'depth' => 0,
        ],
    ],
    'web_CfcLeagueM1_team' => [
        'parent' => 'web_CfcLeagueM1',
        'access' => 'user',
        'workspaces' => '*',
        'iconIdentifier' => 'ext-cfcleague-ext-default',
        'path' => '/module/web/t3sports/team',
        'labels' => [
            'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_team',
        ],
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Controller\Team::class.'::main',
            ],
        ],
        'moduleData' => [
            'langFiles' => [],
            'pages' => '0',
            'depth' => 0,
        ],
    ],
    'web_CfcLeagueM1_club' => [
        'parent' => 'web_CfcLeagueM1',
        'access' => 'user',
        'workspaces' => '*',
        'iconIdentifier' => 'ext-cfcleague-ext-default',
        'path' => '/module/web/t3sports/club',
        'labels' => [
            'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_club',
        ],
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Controller\Club::class.'::main',
            ],
        ],
        'moduleData' => [
            'langFiles' => [],
            'pages' => '0',
            'depth' => 0,
        ],
    ],
    'web_CfcLeagueM1_profiles' => [
        'parent' => 'web_CfcLeagueM1',
        'access' => 'user',
        'workspaces' => '*',
        'iconIdentifier' => 'ext-cfcleague-ext-default',
        'path' => '/module/web/t3sports/profile',
        'labels' => [
            'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:mod_profiles',
        ],
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Controller\Profile::class.'::main',
            ],
        ],
        'moduleData' => [
            'langFiles' => [],
            'pages' => '0',
            'depth' => 0,
        ],
    ],
];
