<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$globalClubs = intval(Tx_Rnbase_Configuration_Processor::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
$clubOrdering = intval(Tx_Rnbase_Configuration_Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

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

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_teams = [
    'ctrl' => array(
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams',
        'label' => 'name',
        'searchFields' => 'uid,name,short_name,tlc',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-teams-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_teams.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,club,name,short_name',
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, name, short_name',
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'club' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.club',
            'config' => $clubArr,
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'required,trim',
            ),
        ),
        'short_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.short_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'required,trim',
            ),
        ),
        'tlc' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_tlc',
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'max' => '3',
                'eval' => 'trim',
            ),
        ),
        'agegroup' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group',
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
        'coaches' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.coaches',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 4,
                'minitems' => 0,
                'maxitems' => 10,
            ),
        ),
        'players' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.players',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 20,
                'minitems' => 0,
                'maxitems' => 60,
            ),
        ),
        'supporters' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.supporters',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_profiles',
                'size' => 4,
                'minitems' => 0,
                'maxitems' => 10,
            ),
        ),

        'coaches_comment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.coaches_comment',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
        'supporters_comment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.supporters_comment',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
        'players_comment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.players_comment',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
        'link_report' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.link_report',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'comment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.comment',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                    ),
                ),
            ),
        ),
        'dummy' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.dummy',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'extid' => array(
                'exclude' => 1,
                'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_extid',
                'config' => array(
                        'type' => 'input',
                        'size' => '10',
                        'max' => '255',
                        'eval' => 'trim',
                ),
        ),
    ),
    'types' => [
        '0' => [
        'showitem' => 'hidden, club,logo, t3logo, name, short_name, tlc, agegroup, t3images, link_report, dummy, extid,
            --div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_tab_members,coaches, players, supporters, players_comment, coaches_comment, supporters_comment, comment',
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];

Tx_Rnbase_Utility_TcaTool::configureWizards($tx_cfcleague_teams, [
    'comment' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);

// Auswahlbox Vereinslogos
$tx_cfcleague_teams['columns']['logo'] = tx_cfcleague_tca_Lookup::getTeamLogoField();

$tx_cfcleague_teams['columns']['t3logo'] = tx_rnbase_util_TSFAL::getMediaTCA('t3logo', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.altlogo',
    'config' => ['size' => 1, 'maxitems' => 1],
]);
$tx_cfcleague_teams['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.pictures',
]);

return $tx_cfcleague_teams;
