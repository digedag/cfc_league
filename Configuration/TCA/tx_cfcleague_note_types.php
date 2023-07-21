<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$tx_cfcleague_note_types = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_note_types',
        'label' => 'label',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-notetypes-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif',
    ],
    'interface' => [
        'showRecordFieldList' => '',
        'maxDBListItems' => '15',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'label, marker, description',
    ],
    'columns' => [
        'label' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_note_types.label',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'trim',
            ],
        ],
        'marker' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_note_types.marker',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_note_types.description',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'label, marker, description',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
];

if (\Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_note_types['interface']['showRecordFieldList']);
}

return $tx_cfcleague_note_types;
