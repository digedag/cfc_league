<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tx_cfcleague_note_types = array(
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types',
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
    'columns' => array(
        'label' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types.label',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'trim',
            ],
        ),
        'marker' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types.marker',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'size' => '20',
                'eval' => 'trim',
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types.description',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
    ),
    'types' => array(
        '0' => [
            'showitem' => 'label, marker, description',
        ],
    ),
    'palettes' => array(
        '1' => [
            'showitem' => '',
        ],
    ),
);

return $tx_cfcleague_note_types;
