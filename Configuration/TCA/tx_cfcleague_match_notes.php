<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$tx_cfcleague_match_notes = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes',
        'label' => 'uid',
        'label_alt' => 'minute,comment',
        'label_alt_force' => 1,
        'searchFields' => 'uid,comment',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-matchnotes-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,game,minute,extra_time,type,player_home,player_guest,comment',
        'maxDBListItems' => '5',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, game, minute, extra_time, type, player_home, player_guest, comment',
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
        'game' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.match',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_games',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'minute' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.minute',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '200',
                    'lower' => '-1',
                ],
                'default' => 0,
            ],
        ],
        'extra_time' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.extra_time',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '30',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'second' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.second',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '59',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getMatchNoteTypes',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],

        'player_home' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.player_home',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayersHome4Match',
                'size' => 1,
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'player_guest' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.player_guest',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayersGuest4Match',
                'size' => 1,
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_match_notes.comment',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'hidden, game, minute, second, extra_time, type, player_home, player_guest, comment',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
];

if (Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_match_notes['interface']['showRecordFieldList']);
}

return $tx_cfcleague_match_notes;
