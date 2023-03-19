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
            'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_mod.xlf:mod_competition',
        ],
        'routes' => [
            '_default' => [
                'target' => System25\T3sports\Controller\Competition::class.'::main',
            ],
        ],
        // 'moduleData' => [
        //     'pages' => '0',
        //     'depth' => 0,
        // ],
    ],
];
