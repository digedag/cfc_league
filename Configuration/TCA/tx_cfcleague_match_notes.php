<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_match_notes = [
    'ctrl' => array(
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
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-matchnotes-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,game,minute,extra_time,type,player_home,player_guest,comment',
        'maxDBListItems' => '5',
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, game, minute, extra_time, type, player_home, player_guest, comment',
    ),
    'columns' => array(
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'game' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.match',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_games',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'minute' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.minute',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '200',
                    'lower' => '-1',
                ),
                'default' => 0,
            ),
        ),
        'extra_time' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.extra_time',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '20',
                    'lower' => '0',
                ),
                'default' => 0,
            ),
        ),
        'second' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.second',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '3',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '59',
                    'lower' => '0',
                ),
                'default' => 0,
            ),
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getMatchNoteTypes',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ),
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
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
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
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'comment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.comment',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
    ),
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

return $tx_cfcleague_match_notes;
