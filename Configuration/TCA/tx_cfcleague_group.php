<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_group = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group',
		'label' => 'name',
		'searchFields' => 'uid,name,shortname',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'fe_group' => 'fe_group',
		),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-group-default'
        ],
		'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_group.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,starttime,fe_group,name'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, starttime, fe_group, name',
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
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.starttime',
			'config' => Array (
				'type' => 'input',
			    'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
			    'size' => '8',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.fe_group',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'shortname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group.shortname',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '8',
				'eval' => 'trim',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden,--palette--;;1, name, shortname, logo, t3logo')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'starttime, fe_group')
	)
);

tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_group['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', array(
	'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',
	'size' => 1,
	'maxitems' => 1
));

return $tx_cfcleague_group;
