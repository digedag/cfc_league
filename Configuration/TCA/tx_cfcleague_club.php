<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('Tx_Rnbase_Configuration_Processor');
tx_rnbase::load('tx_cfcleague_tca_Lookup');

$clubOrdering = intval(Tx_Rnbase_Configuration_Processor::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

$labelClub = $clubOrdering ? 'city' : 'name';
$altLabelClub = $clubOrdering ? 'name' : 'city';
$rteConfig = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_club = Array (
    'ctrl' => Array (
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',
        'label' => $labelClub,
        'label_alt' => $altLabelClub,
        'label_alt_force' => 1,
        'searchFields' => 'uid,name,short_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'dividers2tabs' => TRUE,
        'delete' => 'deleted',
        'enablecolumns' => Array (
            'disabled' => 'hidden',
            ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-clubs-default'
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_clubs.gif',
        ),
    'interface' => Array (
        'showRecordFieldList' => 'hidden,name,short_name'
        ),
    'feInterface' => Array (
        'fe_admin_fieldList' => 'hidden, name, short_name',
        ),
    'columns' => Array (
        'hidden' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.hidden',
            'config' => Array (
                'type' => 'check',
                'default' => '0'
            )
        ),
        'name' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.name',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'required,trim',
            )
        ),
        'short_name' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.short_name',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '100',
                'eval' => 'required,trim',
            )
        ),
        'address' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.address',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tt_address',
                'default' => 0,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'favorite' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.favorite',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'www' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.www',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'eval' => 'trim',
            )
        ),
        'email' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.email',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'eval' => 'trim',
            )
        ),
        'street' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.street',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'eval' => 'trim',
            )
        ),
        'zip' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.zip',
            'config' => Array (
                'type' => 'input',
                'size' => '10',
                'max' => '10',
                'eval' => 'trim',
            )
        ),
        'city' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.city',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'eval' => 'trim',
            )
        ),
        'countrycode' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_countrycode',
            'config' => Array (
                'type' => 'input',
                'size' => '10',
                'max' => '20',
                'eval' => 'trim',
            )
        ),
        'lng' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lng',
            'config' => Array (
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'trim',
            )
        ),
        'lat' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lat',
            'config' => Array (
                'type' => 'input',
                'size' => '20',
                'max' => '50',
                'eval' => 'trim',
            )
        ),
        'shortinfo' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.shortinfo',
            'config' => Array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
        'info' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_info',
            'config' => Array (
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
                        'icon' => 'wizard_rte2.gif',
                        //						'script' => 'wizard_rte.php',
                    ),
                ),
            )
        ),
        'info2' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_info2',
            'config' => Array (
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
                        'icon' => 'wizard_rte2.gif',
                        //						'script' => 'wizard_rte.php',
                    ),
                ),
            )
        ),

        'established' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_established',
            'config' => Array (
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '10',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            )
        ),
        'yearestablished' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_yearestablished',
            'config' => Array (
                'type' => 'input',
                'size' => '10',
                'max' => '4',
                'eval' => 'int',
            )
        ),
        'colors' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_colors',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'eval' => 'trim',
                )
            ),
        'members' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_members',
            'config' => Array (
                'type' => 'input',
                'size' => '10',
                'max' => '10s',
                'eval' => 'int',
            )
        ),
        'stadiums' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums',
            'config' => Array (
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 10,
                'autoSizeMax' => 50,
                'minitems' => 0,
                'maxitems' => 100,
                'foreign_table' => 'tx_cfcleague_stadiums',
                'foreign_table_where' => 'ORDER BY name',
                'MM' => 'tx_cfcleague_stadiums_mm',
                'MM_foreign_select' => 1,
                'MM_opposite_field' => 'clubs',
                'MM_match_fields' => [
                    'tablenames' => 'tx_cfcleague_club',
                ],
            )
        ),
    ),
    'types' => [
        '0' => [
            'showitem' => 'hidden, name,short_name,logo,favorite,stadiums,
                --div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.tab_contact,www,email,street,zip,city,country,countrycode,address,lng,lat,
                --div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.tab_info,established,yearestablished,colors,members,shortinfo,info,info2',
        ]
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ]
);

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
Tx_Rnbase_Utility_TcaTool::configureWizards($tx_cfcleague_club, [
    'stadiums'=> ['targettable' => 'tx_cfcleague_stadiums', 'add' => true, 'edit'=> true],
    'info' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'info2' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);



if(tx_rnbase_util_Extensions::isLoaded('static_info_tables')) {
    $tx_cfcleague_club['columns']['country'] = tx_cfcleague_tca_Lookup::getCountryField();
}

tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_club['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',
]);

return $tx_cfcleague_club;
