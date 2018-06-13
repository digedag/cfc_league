<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$tx_cfcleague_saison = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison',
		'label' => 'name',
		'searchFields' => 'uid,name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-saison-default'
        ],
		'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_saison.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,name'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name',
	),
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'halftime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison.halftime',
			'config' => Array (
				'type' => 'input',
			    'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
			    'size' => '8',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden, name, halftime')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);


return $tx_cfcleague_saison;
