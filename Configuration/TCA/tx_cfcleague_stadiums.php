<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$rteConfig = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';

$wecmap = [];
$wecmap['wec_map']['isMappable'] = 1;
$wecmap['wec_map']['addressFields'] = [
    'street' => 'street',
    'city' => 'city',
    'zip' => 'zip',
];

$globalClubs = (int) Sys25\RnBase\Configuration\Processor::getExtensionCfgValue('cfc_league', 'useGlobalClubs') > 0;
// $clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

$stadiumClubArr = $globalClubs ? [
    'type' => 'select',
    'renderType' => 'selectSingle',
    'foreign_table' => 'tx_cfcleague_club',
    'foreign_table_where' => 'ORDER BY name',
    'size' => 10,
    'autoSizeMax' => 30,
    'minitems' => 0,
    'maxitems' => 100,
    'MM' => 'tx_cfcleague_stadiums_mm',
    'MM_match_fields' => [
        'tablenames' => 'tx_cfcleague_club',
    ],
] : [
    'type' => 'group',
    'internal_type' => 'db',
    'allowed' => 'tx_cfcleague_club',
    'size' => 10,
    'autoSizeMax' => 30,
    'minitems' => 0,
    'maxitems' => 100,
    'MM' => 'tx_cfcleague_stadiums_mm',
    'MM_match_fields' => [
        'tablenames' => 'tx_cfcleague_club',
    ],
];

$tx_cfcleague_stadiums = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums',
        'label' => 'name',
        'searchFields' => 'uid,name,altname,description,city,street',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'default_sortby' => 'ORDER BY name',
        'EXT' => $wecmap,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-statiums-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'name',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, starttime, fe_group, name',
    ],
    'columns' => [
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'required,trim',
            ],
        ],
        'altname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_altname',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'trim',
            ),
        ),
        'capacity' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_capacity',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'max' => '7',
                'eval' => 'int',
                'default' => 0,
            ),
        ),
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_description',
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
        'description2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_description2',
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
        'street' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_street',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'trim',
            ],
        ],
        'city' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_city',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'trim',
            ),
        ),
        'zip' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_zip',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'max' => '50',
                'eval' => 'trim',
            ),
        ),
        'countrycode' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_countrycode',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'max' => '20',
                'eval' => 'trim',
            ),
        ),
        'lng' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lng',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'trim',
            ),
        ),
        'lat' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lat',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'trim',
            ),
        ),
        'clubs' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',
            'config' => $stadiumClubArr,
        ],
        'address' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.address',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tt_address',
                'appearance' => array(
                    'collapseAll' => 0,
                    'expandSingle' => 1,
                ),
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'extid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_extid',
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'max' => '3',
                'eval' => 'trim',
            ),
        ),
    ],
    'types' => [
        '0' => [
            'showitem' => 'name,altname,capacity,logo,t3logo,pictures,t3pictures,clubs,extid,
						--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_tab_description,description,description2,
						--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_tab_location,street,city,zip,country,countrycode,lng,lat,address',
        ],
        'columnsOverrides' => [
            'description' => [
                'defaultExtras' => $rteConfig,
            ],
            'description2' => [
                'defaultExtras' => $rteConfig,
            ],
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
];

if (\Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_stadiums['interface']['showRecordFieldList']);
}

\Sys25\RnBase\Backend\Utility\TcaTool::configureWizards($tx_cfcleague_stadiums, [
    'description' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'description2' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);

if (tx_rnbase_util_Extensions::isLoaded('static_info_tables')) {
    $tx_cfcleague_stadiums['columns']['country'] = tx_cfcleague_tca_Lookup::getCountryField();
}

$tx_cfcleague_stadiums['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_logo',
    'config' => [
        'size' => 1,
        'maxitems' => 1,
    ],
]);
$tx_cfcleague_stadiums['columns']['pictures'] = tx_rnbase_util_TSFAL::getMediaTCA('pictures', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_pictures',
]);

return $tx_cfcleague_stadiums;
