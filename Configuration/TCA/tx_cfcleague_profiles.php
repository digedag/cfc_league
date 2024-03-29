<?php

if (!(defined('TYPO3') || defined('TYPO3_MODE'))) {
    exit('Access denied.');
}

$rteConfig = 'richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/]';

$tx_cfcleague_profiles = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles',
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
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,first_name,last_name,t3images,birthday,nationality,height,
			weight,position,duration_of_contract,start_of_contract,email,nickname,summary,description',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, first_name, last_name, image, birthday, nationality, height,
			weight, position, duration_of_contract, start_of_contract, email,
			stations, nickname, family, hobbies, prosperities, summary, description',
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
        'first_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.first_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'trim',
            ],
        ],
        'last_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.last_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '70',
                'eval' => 'required,trim',
            ],
        ],
        'stage_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.stage_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '70',
                'eval' => 'trim',
            ],
        ],
        'link_report' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.link_report',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'gdpr' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gdpr',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gdpr_ok', 0],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gdpr_nameonly', 1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gdpr_anonymize', 2],
                ],
                'default' => 0,
            ],
        ],
        'birthday' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.birthday',
            'config' => [
                'type' => 'input',
                'renderType' => (Sys25\RnBase\Utility\TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'dayofdeath' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.dayofdeath',
            'config' => [
                'type' => 'input',
                'renderType' => (Sys25\RnBase\Utility\TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'died' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.died',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'gender' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gender',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gender_male', 0],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gender_female', 1],
                    ['LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.gender_unspecified', 2],
                ],
                'default' => 0,
            ],
        ],
        'native_town' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.native_town',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'home_town' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.home_town',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'nationality' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.nationality',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'height' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.height',
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
        'weight' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.weight',
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
        'position' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.position',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'duration_of_contract' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.duration_of_contract',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'start_of_contract' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.start_of_contract',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.email',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'nickname' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.nickname',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'summary' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.summary',
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
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => System25\T3sports\Utility\TcaLookup::class.'->getProfileTypes',
                'size' => 5,
                'autoSizeMax' => 10,
                'minitems' => 0,
                'maxitems' => 20,
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.description',
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
            'label' => 'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles_extid',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '255',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'hidden, first_name, last_name, stage_name, home_town, birthday, died, dayofdeath, native_town, nationality, gender, height, weight, position, duration_of_contract, start_of_contract, email, nickname, extid,
		         --div--;LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_profiles.description,gdpr,link_report,t3images,types, summary, description',
        ],
        'columnsOverrides' => [
            'description' => ['defaultExtras' => $rteConfig],
            'summary' => ['defaultExtras' => $rteConfig],
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];

if (Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_profiles['interface']['showRecordFieldList']);
}

Sys25\RnBase\Backend\Utility\TcaTool::configureWizards($tx_cfcleague_profiles, [
    'description' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'summary' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);

$tx_cfcleague_profiles['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', [
    'label' => Sys25\RnBase\Backend\Utility\TcaTool::buildGeneralLabel('images'),
]);

return $tx_cfcleague_profiles;
