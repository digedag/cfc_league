<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tx_cfcleague_team_notes = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes',
        'label' => 'uid',
        'label_alt' => 'type,player,team',
        'label_alt_force' => 1,
        'searchFields' => 'uid,comment',
        'tstamp' => 'tstamp',
        'type' => 'mediatype',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-teamnotes-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_teams.gif',
    ],
    'interface' => [
        'showRecordFieldList' => '',
        'maxDBListItems' => '5',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, team, player, type, comment',
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
        'team' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_teams',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', ''],
                ],
                'foreign_table' => 'tx_cfcleague_teams',
                'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.sorting ',
                'eval' => 'required',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            'onChange' => 'reload',
        ],
        'type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_note_types',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_note_types',
                'foreign_table_where' => 'ORDER BY tx_cfcleague_note_types.sorting',
                'eval' => 'required',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'mediatype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes.mediatype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes.mediatype.text', '0'],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes.mediatype.number', '2'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'player' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_profiles',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getPlayers4Team',
                'eval' => 'required',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'comment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_match_notes.comment',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'number' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes.number',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden, mediatype, team, player, type, comment'],
        '1' => ['showitem' => 'hidden, mediatype, team, player, type, media'],
        '2' => ['showitem' => 'hidden, mediatype, team, player, type, number'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];

if (\Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_team_notes['interface']['showRecordFieldList']);
}

if (!\Sys25\RnBase\Utility\TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_team_notes['ctrl']['requestUpdate'] = 'team';
}

$tx_cfcleague_team_notes['columns']['mediatype']['config']['items'][] =
                ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes.mediatype.media', '1'];
$tx_cfcleague_team_notes['columns']['media'] = tx_rnbase_util_TSFAL::getMediaTCA('media', [
    'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.images',
    'config' => ['size' => 1, 'maxitems' => 1],
]);
$tx_cfcleague_team_notes['columns']['media']['label'] = 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xml:tx_cfcleague_team_notes.media';

return $tx_cfcleague_team_notes;
