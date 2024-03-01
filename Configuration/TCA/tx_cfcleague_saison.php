<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$tx_cfcleague_saison = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_saison',
        'label' => 'name',
        'searchFields' => 'uid,name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-saison-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_saison.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,name',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, name',
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
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_saison.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ],
        ],
        'halftime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_saison.halftime',
            'config' => [
                'type' => 'input',
                'renderType' => (Sys25\RnBase\Utility\TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden, name, halftime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];

if (Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_saison['interface']['showRecordFieldList']);
}

return $tx_cfcleague_saison;
