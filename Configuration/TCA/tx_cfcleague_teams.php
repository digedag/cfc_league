<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$globalClubs = intval(\Sys25\RnBase\Configuration\Processor::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
$clubOrdering = intval(\Sys25\RnBase\Configuration\Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

$rteConfig = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';

$clubArr = $globalClubs ?
    [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            [' ', '0'],
        ],
        'foreign_table' => 'tx_cfcleague_club',
        'foreign_table_where' => 'ORDER BY '.($clubOrdering ? 'tx_cfcleague_club.city,' : '').' tx_cfcleague_club.name',
        'size' => 1,
        'minitems' => 0,
        'maxitems' => 1,
        'default' => 0,
    ] : [
        'type' => 'group',
        'internal_type' => 'db',
        'allowed' => 'tx_cfcleague_club',
        'size' => 1,
        'minitems' => 0,
        'maxitems' => 1,
        'default' => 0,
    ];

$tx_cfcleague_teams = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams',
        'label' => 'name',
        'searchFields' => 'uid,name,short_name,tlc',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-teams-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_teams.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,club,name,short_name',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, name, short_name',
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
        'club' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.club',
            'config' => $clubArr,
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'required,trim',
            ],
        ],
        'short_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.short_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'required,trim',
            ],
        ],
        'tlc' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams_tlc',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '3',
                'eval' => 'trim',
            ],
        ],
        'agegroup' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [['', 0]],
                'foreign_table' => 'tx_cfcleague_group',
                'foreign_table_where' => 'ORDER BY tx_cfcleague_group.sorting',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
            ],
        ],
        'coaches' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.coaches',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 4,
                'minitems' => 0,
                'maxitems' => 10,
            ],
        ],
        'players' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.players',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 20,
                'minitems' => 0,
                'maxitems' => 60,
            ],
        ],
        'supporters' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.supporters',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 4,
                'minitems' => 0,
                'maxitems' => 10,
            ],
        ],

        'coaches_comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.coaches_comment',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'supporters_comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.supporters_comment',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'players_comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.players_comment',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'link_report' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.link_report',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.comment',
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
        'dummy' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.dummy',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams_dummy_none', 0],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams_dummy_simple', 1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams_dummy_outofcompetition', 2],
                ],
                'default' => '0',
            ],
        ],
        'extid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams_extid',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '255',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => [
        'showitem' => 'hidden, club,logo, t3logo, name, short_name, tlc, agegroup, t3images, link_report, dummy, extid,
            --div--;LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams_tab_members,coaches, players, supporters, players_comment, coaches_comment, supporters_comment, comment',
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];

if (\Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_teams['interface']['showRecordFieldList']);
}

\Sys25\RnBase\Backend\Utility\TcaTool::configureWizards($tx_cfcleague_teams, [
    'comment' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);

// Auswahlbox Vereinslogos
$tx_cfcleague_teams['columns']['logo'] = tx_cfcleague_tca_Lookup::getTeamLogoField();

$tx_cfcleague_teams['columns']['t3logo'] = tx_rnbase_util_TSFAL::getMediaTCA('t3logo', [
    'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.altlogo',
    'config' => ['size' => 1, 'maxitems' => 1],
]);
$tx_cfcleague_teams['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', [
    'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams.pictures',
]);

return $tx_cfcleague_teams;
