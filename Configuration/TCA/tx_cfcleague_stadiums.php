<?php


if (! defined('TYPO3_MODE'))
    die('Access denied.');

tx_rnbase::load('Tx_Rnbase_Configuration_Processor');
tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('tx_cfcleague_tca_Lookup');

$rteConfig = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';

$wecmap = [];
$wecmap['wec_map']['isMappable'] = 1;
$wecmap['wec_map']['addressFields'] = [
    'street' => 'street',
    'city' => 'city',
    'zip' => 'zip'
];

$globalClubs = (int)Sys25\RnBase\Configuration\Processor::getExtensionCfgValue('cfc_league', 'useGlobalClubs') > 0;
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
        'tablenames' => 'tx_cfcleague_club'
    ]
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
        'tablenames' => 'tx_cfcleague_club'
    ]
];

$tx_cfcleague_stadiums = Array(
    'ctrl' => Array(
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums',
        'label' => 'name',
        'searchFields' => 'uid,name,altname,description,city,street',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'default_sortby' => 'ORDER BY name',
        'EXT' => $wecmap,
        'delete' => 'deleted',
        'enablecolumns' => Array(),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-statiums-default'
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif'
    ),
    'interface' => Array(
        'showRecordFieldList' => 'name'
    ),
    'feInterface' => Array(
        'fe_admin_fieldList' => 'hidden, starttime, fe_group, name'
    ),
    'columns' => Array(
        'name' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_name',
            'config' => Array(
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'required,trim'
            )
        ),
        'altname' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_altname',
            'config' => Array(
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'trim'
            )
        ),
        'capacity' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_capacity',
            'config' => Array(
                'type' => 'input',
                'size' => '10',
                'max' => '7',
                'eval' => 'int',
                'default' => 0
            )
        ),
        'description' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_description',
            'config' => Array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => Array(
                    '_PADDING' => 2,
                    'RTE' => Array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif'
                    )
                )
            )
        ),
        'description2' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_description2',
            'config' => Array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => Array(
                    '_PADDING' => 2,
                    'RTE' => Array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif'
                    )
                )
            )
        ),
        'street' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_street',
            'config' => Array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'trim'
            )
        ),
        'city' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_city',
            'config' => Array(
                'type' => 'input',
                'size' => '30',
                'max' => '250',
                'eval' => 'trim'
            )
        ),
        'zip' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_zip',
            'config' => Array(
                'type' => 'input',
                'size' => '10',
                'max' => '50',
                'eval' => 'trim'
            )
        ),
        'countrycode' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_countrycode',
            'config' => Array(
                'type' => 'input',
                'size' => '10',
                'max' => '20',
                'eval' => 'trim'
            )
        ),
        'lng' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lng',
            'config' => Array(
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'trim'
            )
        ),
        'lat' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lat',
            'config' => Array(
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'trim'
            )
        ),
        'clubs' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',
            'config' => $stadiumClubArr
        ),
        'address' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.address',
            'config' => Array(
                'type' => 'inline',
                'foreign_table' => 'tt_address',
                'appearance' => Array(
                    'collapseAll' => 0,
                    'expandSingle' => 1
                ),
                'minitems' => 0,
                'maxitems' => 1
            )
        ),
        'extid' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_extid',
            'config' => Array(
                'type' => 'input',
                'size' => '5',
                'max' => '3',
                'eval' => 'trim'
            )
        )
    ),
    'types' => Array(
        '0' => [
            'showitem' => 'name,altname,capacity,logo,t3logo,pictures,t3pictures,clubs,extid,
						--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_tab_description,description,description2,
						--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_tab_location,street,city,zip,country,countrycode,lng,lat,address'
        ],
        'columnsOverrides' => [
            'description' => [
                'defaultExtras' => $rteConfig
            ],
            'description2' => [
                'defaultExtras' => $rteConfig
            ]
        ]
    ),
    'palettes' => [
        '1' => [
            'showitem' => ''
        ]
    ]
);

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
Tx_Rnbase_Utility_TcaTool::configureWizards($tx_cfcleague_stadiums, [
    'description' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'description2' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);


if (tx_rnbase_util_Extensions::isLoaded('static_info_tables')) {
    $tx_cfcleague_stadiums['columns']['country'] = tx_cfcleague_tca_Lookup::getCountryField();
}
tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_stadiums['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_logo',
    'config' => [
        'size' => 1,
        'maxitems' => 1
    ]
]);
$tx_cfcleague_stadiums['columns']['pictures'] = tx_rnbase_util_TSFAL::getMediaTCA('pictures', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_pictures'
]);

return $tx_cfcleague_stadiums;
