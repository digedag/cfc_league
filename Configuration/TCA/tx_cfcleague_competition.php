<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$tx_cfcleague_competition = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition',
        'label' => 'name',
        'label_alt' => 'internal_name',
        'label_alt_force' => 1,
        'searchFields' => 'uid,name',
        'tstamp' => 'tstamp',
        'type' => 'tournament',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-competition-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_competition.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,name,agegroup,saison,type,teams',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, name, short_name, internal_name, agegroup, saison, type, teams, match_keys, table_marks',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => Sys25\RnBase\Backend\Utility\TcaTool::buildGeneralLabel('hidden'),
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'tournament' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition_tournament',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ],
        ],
        'internal_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.internal_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'short_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.short_name',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'match_keys' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_keys',
            'config' => [
                'type' => 'input',
                'size' => '50',
                'max' => '2000',
                'eval' => 'trim',
            ],
        ],
        'table_marks' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.table_marks',
            'config' => [
                'type' => 'input',
                'size' => '50',
                'max' => '2000',
                'eval' => 'trim',
            ],
        ],
        'agegroup' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_cfcleague_group',
                'foreign_table_where' => 'ORDER BY tx_cfcleague_group.sorting',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 5,
            ],
        ],
        'saison' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.saison',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_saison',
                'foreign_table_where' => 'ORDER BY tx_cfcleague_saison.name desc',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'sports' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.sports',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getSportsTypes',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            'onChange' => 'reload',
        ],
        'type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.type',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.type_league', 1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.type_ko', 2],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.type_other', 0],
                    // ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.type_combined',100]
                ],
                'default' => 1,
            ],
        ],
        'obligation' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.obligation',
            'config' => [
                'type' => 'check',
                'default' => '1',
            ],
        ],
        'match_parts' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_parts',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_parts_0', 0],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_parts_1', 1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_parts_2', 2],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_parts_3', 3],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.match_parts_4', 4],
                ],
                'default' => 0,
            ],
        ],
        'addparts' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition_addparts',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'point_system' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.point_system',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPointSystems',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
            ],
        ],
        'tablestrategy' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition_tablestrategy',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getTableStrategies',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'teams' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.teams',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_teams',
                'size' => 20,
                'minitems' => 0,
                'maxitems' => 100,
            ],
        ],
        /* used for combined competitions later...
        'parent' => Array (
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition.parent',
            'config' => Array (
                'type' => 'select',
                'foreign_table' => 'tx_cfcleague_competition',
                'foreign_table_where' => 'tx_cfcleague_competition.type = 100 ORDER BY tx_cfcleague_competition.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
*/
        'extid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_competition_extid',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '255',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden, name, tournament, sports, internal_name, short_name, agegroup, saison, type,--palette--;;1, tablestrategy, point_system, logo, teams, match_keys, table_marks, match_parts, addparts,extid'],
        '1' => ['showitem' => 'hidden, name, tournament'],
        // 'icehockey' => ['showitem' => 'hidden, name, sports, internal_name, short_name, agegroup, saison, type;;2, point_system, logo, teams, match_keys, table_marks, match_parts, addparts'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'obligation'],
    ],
];

if (Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_competition['interface']['showRecordFieldList']);
}

Sys25\RnBase\Backend\Utility\TcaTool::configureWizards($tx_cfcleague_competition, [
    'teams' => ['targettable' => 'tx_cfcleague_teams', 'add' => true],
]);

$tx_cfcleague_competition['columns']['logo'] = Sys25\RnBase\Utility\TSFAL::getMediaTCA('logo', [
    'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_club.logo',
    'config' => ['size' => 1, 'maxitems' => 1],
]);

return $tx_cfcleague_competition;
