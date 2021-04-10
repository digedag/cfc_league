<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

$rteConfig = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';

$tx_cfcleague_games = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games',
        'label' => 'round_name',
        'label_alt' => 'competition,home,guest',
        'label_alt_force' => 1,
        'searchFields' => 'uid,round_name,addinfo,stadium,game_report',
        // configure fields for NeighborRow initialization
        'useColumnsForDefaultValues' => 'competition,date,round,round_name',
        'dividers2tabs' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-games-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,home,guest,competition,round,round_name,status, goals_home_1,goals_guest_1,
					goals_home_2,goals_guest_2,goals_home_3,goals_guest_3,goals_home_4,goals_guest_4,date,
					game_report,visitors,t3images,goals_home_et,goals_guest_et,goals_home_ap,goals_guest_ap',
        'maxDBListItems' => '5',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, home, guest, competition, round, round_name, status, coach_home,
			coach_guest, players_home, players_guest, substitutes_home, substitutes_guest, goals_home_1,
			goals_guest_1, goals_home_2, goals_guest_2, goals_home_3, goals_guest_3, goals_home_4, goals_guest_4,
			date, link_report, link_ticker, game_report, visitors, goals_home_et, goals_guest_et, goals_home_ap,
			goals_guest_ap',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => \Sys25\RnBase\Backend\Utility\TcaTool::buildGeneralLabel('hidden'),
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'match_no' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.match_no',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'trim',
            ],
        ],
        'home' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_teams',
                'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getTeams4Competition',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'guest' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_teams',
                'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getTeams4Competition',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'sets' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games_sets',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '254',
                'eval' => 'trim',
            ],
        ],
        'competition' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.competition',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [' ', '0'],
                ],
                'foreign_table' => 'tx_cfcleague_competition',
                'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            'onChange' => 'reload',
        ],
        'round' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.round',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'required,int',
//				'checkbox' => '0',
                'range' => [
                    'upper' => '1000',
                    'lower' => '1',
                ],
                'default' => 0,
            ],
        ],
        'round_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.round_name',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '100',
                'eval' => 'required,trim',
            ],
        ],
        'addinfo' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.addinfo',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '254',
                'eval' => 'trim',
            ],
        ],
        'status' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.status_scheduled', 0],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.status_running', 1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.status_finished', 2],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.status_invalid', -1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.status_rescheduled', -10],
                ],
                'default' => 0,
            ],
        ],
        'stadium' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.stadium',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'eval' => 'trim',
            ],
        ],
        'arena' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_stadiums',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [['', '0']],
                'size' => '1',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getStadium4Match',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'coach_home' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.coach_home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getCoachesHome4Match',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'coach_guest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.coach_guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getCoachesGuest4Match',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'players_home' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.players_home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayersHome4Match',
                'size' => 11,
                'minitems' => 0,
                'maxitems' => 11,
            ],
        ],
        'players_guest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.players_guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayersGuest4Match',
                'size' => 11,
                'minitems' => 0,
                'maxitems' => 11,
            ],
        ],
        'substitutes_home' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.substitutes_home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayersHome4Match',
                'size' => 9,
                'minitems' => 0,
                'maxitems' => 10,
            ],
        ],
        'substitutes_guest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.substitutes_guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayersGuest4Match',
                'size' => 9,
                'minitems' => 0,
                'maxitems' => 10,
            ],
        ],
        'players_home_stat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.players_home',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'players_guest_stat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.players_guest',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'substitutes_home_stat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.substitutes_home',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'substitutes_guest_stat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.substitutes_guest',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'scorer_home_stat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.scorer_home_stat',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'scorer_guest_stat' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.scorer_guest_stat',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'system_home' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.system_home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getFormations',
                'default' => 0,
            ],
        ],
        'system_guest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.system_guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getFormations',
                'default' => 0,
            ],
        ],
        'referee' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.referee',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 1,
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
                'wizards' => Tx_Rnbase_Utility_TcaTool::getWizards('tx_cfcleague_profiles', [
                    'suggest' => true,
                ]),
            ],
        ],
        'assists' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.assists',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 5,
                'wizards' => Tx_Rnbase_Utility_TcaTool::getWizards('tx_cfcleague_profiles', [
                    'suggest' => true,
                ]),
            ],
        ],

        'goals_home_1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_home_1',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_guest_1' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_guest_1',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_home_2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_home_2',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_guest_2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_guest_2',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_home_3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_home_3',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_guest_3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_guest_3',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_home_4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_home_4',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_guest_4' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_guest_4',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],

        'date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.date',
            'config' => [
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'link_report' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.link_report',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'link_ticker' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.link_ticker',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'game_report_author' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.game_report_author',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'trim',
            ],
        ],
        'liveticker_author' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.liveticker_author',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'trim',
            ],
        ],
        'game_report' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.game_report',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => [
                    '_PADDING' => 2,
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                    ],
                ],
            ],
        ],
        'visitors' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.visitors',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '6',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '500000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],

        'is_extratime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.extratime',
            'config' => [
                'type' => 'check',
            ],
        ],

        'goals_home_et' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_home_et',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
//				'checkbox' => '0',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_guest_et' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_guest_et',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
//				'checkbox' => '0',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],

        'is_penalty' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.penalty',
            'config' => [
                'type' => 'check',
            ],
        ],

        'goals_home_ap' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_home_ap',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
//				'checkbox' => '0',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'goals_guest_ap' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.goals_guest_ap',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
//				'checkbox' => '0',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'extid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games_extid',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
    // goals_home_1, goals_guest_1, goals_home_2, goals_guest_2,
        '0' => [
            'showitem' => 'hidden,match_no,competition,home,guest,round,round_name,date,addinfo,status,--palette--;;6,sets,arena,stadium,visitors,extid,
			--div--;LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.tab_lineup,coach_home, players_home, substitutes_home, system_home, system_guest, coach_guest, players_guest, substitutes_guest, referee, assists,
			--div--;LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.tab_lineup_stat,players_home_stat, substitutes_home_stat, players_guest_stat, substitutes_guest_stat, scorer_home_stat, scorer_guest_stat,
			--div--;LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.tab_score, is_extratime,--palette--;;2, is_penalty,--palette--;;3,
			--div--;LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_games.game_report, game_report,--palette--;;4, game_report_author,--palette--;;5, t3images, dam_media, dam_media2, video, videoimg',
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
        '2' => ['showitem' => 'goals_home_et, goals_guest_et'],
        '3' => ['showitem' => 'goals_home_ap, goals_guest_ap'],
        '4' => ['showitem' => 'link_report, link_ticker'],
        '5' => ['showitem' => 'liveticker_author'],
        '6' => ['showitem' => 'goals_home_2, goals_guest_2, goals_home_1, goals_guest_1'],
    ],
];

if (!\Sys25\RnBase\Utility\TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_games['ctrl']['requestUpdate'] = 'competition';
}

if (\Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_games['interface']['showRecordFieldList']);
}

\Sys25\RnBase\Backend\Utility\TcaTool::configureWizards($tx_cfcleague_games, [
    'game_report' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'referee' => ['targettable' => 'tx_cfcleague_profiles', 'suggest' => true],
    'assists' => ['targettable' => 'tx_cfcleague_profiles', 'suggest' => true],
]);

$tx_cfcleague_games['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', [
    'label' => \Sys25\RnBase\Backend\Utility\TcaTool::buildGeneralLabel('images'),
]);
$tx_cfcleague_games['columns']['dam_media'] = tx_rnbase_util_TSFAL::getMediaTCA('dam_media', [
    'type' => 'media',
    'label' => 'LLL:EXT:cms/locallang_ttc.xml:media',
]);
$tx_cfcleague_games['columns']['dam_media2'] = tx_rnbase_util_TSFAL::getMediaTCA('dam_media2', [
    'type' => 'media',
    'label' => 'LLL:EXT:cms/locallang_ttc.xml:media',
]);

return $tx_cfcleague_games;
