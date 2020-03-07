<?php
if (! defined('TYPO3_MODE'))
    die('Access denied.');

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_group = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group',
        'label' => 'name',
        'searchFields' => 'uid,name,shortname',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'useColumnsForDefaultValues' => 'hidden',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-group-default'
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_group.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,starttime,fe_group,name'
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, starttime, fe_group, name'
    ],
    'columns' => Array(
        'hidden' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/' . $sysLangFile . ':LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ),
        'starttime' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/' . $sysLangFile . ':LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            ]
        ),
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim'
            ]
        ],
        'shortname' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group.shortname',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '8',
                'eval' => 'trim'
            ]
        ]
    ),
    'types' => [
        '0' => [
            'showitem' => 'hidden,--palette--;;1, name, shortname, logo, t3logo'
        ]
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'starttime'
        ]
    ]
];

tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_group['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', array(
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',
    'size' => 1,
    'maxitems' => 1
));

return $tx_cfcleague_group;
