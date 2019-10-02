<?php
if (! defined('TYPO3_MODE'))
    die('Access denied.');

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_match_notes = [
    'ctrl' => Array(
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes',
        'label' => 'uid',
        'label_alt' => 'minute,comment',
        'label_alt_force' => 1,
        'searchFields' => 'uid,comment',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => Array(
            'disabled' => 'hidden'
        ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-matchnotes-default'
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif'
    ),
    'interface' => Array(
        'showRecordFieldList' => 'hidden,game,minute,extra_time,type,player_home,player_guest,comment',
        'maxDBListItems' => '5'
    ),
    'feInterface' => Array(
        'fe_admin_fieldList' => 'hidden, game, minute, extra_time, type, player_home, player_guest, comment'
    ),
    'columns' => Array(
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'game' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.match',
            'config' => Array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_games',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1
            )
        ),
        'minute' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.minute',
            'config' => Array(
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => Array(
                    'upper' => '200',
                    'lower' => '-1'
                ),
                'default' => 0
            )
        ),
        'extra_time' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.extra_time',
            'config' => Array(
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => Array(
                    'upper' => '20',
                    'lower' => '0'
                ),
                'default' => 0
            )
        ),
        'second' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.second',
            'config' => Array(
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => Array(
                    'upper' => '59',
                    'lower' => '0'
                ),
                'default' => 0
            )
        ),
        'type' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type',
            'config' => Array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getMatchNoteTypes',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1
            )
        ),

        'player_home' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.player_home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersHome4Match',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1
            ]
        ],
        'player_guest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.player_guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersGuest4Match',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1
            ]
        ],
        'comment' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.comment',
            'config' => Array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            )
        )
    ),
    'types' => [
        '0' => [
            'showitem' => 'hidden, game, minute, second, extra_time, type, player_home, player_guest, comment'
        ]
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ]
    ]
];

return $tx_cfcleague_match_notes;
