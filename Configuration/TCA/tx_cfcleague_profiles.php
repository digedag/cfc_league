<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('tx_cfcleague_tca_Lookup');
tx_rnbase::load('tx_rnbase_util_TYPO3');

$rteConfig = 'richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/]';
$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_profiles = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles',
		'label' => 'last_name',
		'label_alt' => 'first_name',
		'label_alt_force' => 1,
		'searchFields' => 'uid,first_name,last_name,stage_name,email,nickname',
		'dividers2tabs' => TRUE,
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
            'default' => 'ext-cfcleague-profiles-default'
        ],
		'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_profiles.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,first_name,last_name,dam_images,birthday,nationality,height,
			weight,position,duration_of_contract,start_of_contract,email,nickname,summary,description'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, first_name, last_name, image, birthday, nationality, height,
			weight, position, duration_of_contract, start_of_contract, email,
			stations, nickname, family, hobbies, prosperities, summary, description',
	),
	'columns' => Array (
		'hidden' => [
			'exclude' => 1,
		    'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.hidden',
			'config' => [
				'type' => 'check',
				'default' => '0'
			]
		],
		'first_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.first_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'trim',
			)
		),
		'last_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.last_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '70',
				'eval' => 'required,trim',
			)
		),
		'stage_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.stage_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '70',
				'eval' => 'trim',
			)
		),
		'link_report' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.link_report',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'birthday' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.birthday',
			'config' => Array (
				'type' => 'input',
			    'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
			    'size' => '8',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'native_town' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.native_town',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'home_town' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.home_town',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'nationality' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.nationality',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'height' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.height',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'weight' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.weight',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'position' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.position',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'duration_of_contract' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.duration_of_contract',
			'config' => Array (
				'type' => 'input',
			    'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
			    'size' => '8',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'start_of_contract' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.start_of_contract',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'email' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.email',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'nickname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.nickname',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'summary' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.summary',
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
					),
				),
			)
		),
		'types' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.type',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getProfileTypes',
				'size' => 5,
				'autoSizeMax' => 10,
				'minitems' => 0,
				'maxitems' => 20,
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description',
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
					),
				),
			)
		),
		'extid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles_extid',
				'config' => Array (
						'type' => 'input',
						'size' => '10',
						'max' => '255',
						'eval' => 'trim',
				)
		),
	),
	'types' => Array (
		'0' => [
		    'showitem' => 'hidden, first_name, last_name, stage_name, home_town, birthday, native_town, nationality, height, weight, position, duration_of_contract, start_of_contract, email, nickname, extid,
		         --div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description,link_report,dam_images,t3images,types, summary, description'
		    ],
    	    'columnsOverrides' => [
    	        'description' => ['defaultExtras' => $rteConfig],
    	        'summary' => ['defaultExtras' => $rteConfig],
    	    ]
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
Tx_Rnbase_Utility_TcaTool::configureWizards($tx_cfcleague_profiles, [
    'description' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'summary' => ['RTE' => ['defaultExtras' => $rteConfig]],
]);

if(!tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
    $tx_cfcleague_profiles['types'][0]['showitem'] = 'hidden, first_name, last_name, stage_name, home_town, birthday, native_town, nationality, height, weight, position, duration_of_contract, start_of_contract, email, nickname, extid,
		--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description,link_report,dam_images,t3images,types, summary;;;'.$rteConfig.', description;;;'.$rteConfig;
}


tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_profiles['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', array(
	'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_pictures',
));

return $tx_cfcleague_profiles;
