<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

tx_rnbase::load('tx_cfcleague_tca_Lookup');
tx_rnbase::load('tx_rnbase_util_TYPO3');

$rteConfig = 'richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/]';
$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_profiles = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles',
        'label' => 'last_name',
        'label_alt' => 'first_name',
        'label_alt_force' => 1,
        'searchFields' => 'uid,first_name,last_name,stage_name,email,nickname,extid',
        'dividers2tabs' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
//		'sortby' => 'sorting',
        'default_sortby' => 'ORDER BY last_name, first_name',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-profiles-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_profiles.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,first_name,last_name,t3images,birthday,nationality,height,
			weight,position,duration_of_contract,start_of_contract,email,nickname,summary,description',
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, first_name, last_name, image, birthday, nationality, height,
			weight, position, duration_of_contract, start_of_contract, email,
			stations, nickname, family, hobbies, prosperities, summary, description',
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
        'first_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.first_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'trim',
            ),
        ),
        'last_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.last_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '70',
                'eval' => 'required,trim',
            ),
        ),
        'stage_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.stage_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '70',
                'eval' => 'trim',
            ),
        ),
        'link_report' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.link_report',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'gdpr' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gdpr',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gdpr_ok', 0],
                    ['LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gdpr_nameonly', 1],
                    ['LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gdpr_anonymize', 2],
                ],
                'default' => 0,
            ],
        ],
        'birthday' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.birthday',
            'config' => array(
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ),
        ),
        'dayofdeath' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.dayofdeath',
            'config' => [
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'died' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.died',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'gender' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gender',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gender_male', 0],
                    ['LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gender_female', 1],
                    ['LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.gender_unspecified', 2],
                ],
                'default' => 0,
            ],
        ],
        'native_town' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.native_town',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'home_town' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.home_town',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'nationality' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.nationality',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'height' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.height',
            'config' => [
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '1000',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'weight' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.weight',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '0',
                ),
                'default' => 0,
            ),
        ),
        'position' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.position',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'duration_of_contract' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.duration_of_contract',
            'config' => array(
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ),
        ),
        'start_of_contract' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.start_of_contract',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'email' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.email',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'nickname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.nickname',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ),
        ),
        'summary' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.summary',
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
        'types' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getProfileTypes',
                'size' => 5,
                'autoSizeMax' => 10,
                'minitems' => 0,
                'maxitems' => 20,
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description',
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
        'extid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles_extid',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '255',
                'eval' => 'trim',
            ],
        ],
    ),
    'types' => [
        '0' => [
            'showitem' => 'hidden, first_name, last_name, stage_name, home_town, birthday, died, dayofdeath, native_town, nationality, gender, height, weight, position, duration_of_contract, start_of_contract, email, nickname, extid,
		         --div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description,gdpr,link_report,t3images,types, summary, description',
        ],
        'columnsOverrides' => [
            'description' => ['defaultExtras' => $rteConfig],
            'summary' => ['defaultExtras' => $rteConfig],
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
);

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
Tx_Rnbase_Utility_TcaTool::configureWizards($tx_cfcleague_profiles, [
    'description' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'summary' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);

if (!tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
    $tx_cfcleague_profiles['types'][0]['showitem'] = 'hidden, first_name, last_name, stage_name, home_town, birthday, died, dayofdeath, native_town, nationality, gender, height, weight, position, duration_of_contract, start_of_contract, email, nickname, extid,
		--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description,gdpr,link_report,t3images,types, summary;;;'.$rteConfig.', description;;;'.$rteConfig;
}

tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_profiles['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', [
    'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_pictures',
]);

return $tx_cfcleague_profiles;
