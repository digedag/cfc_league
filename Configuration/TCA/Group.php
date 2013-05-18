<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if(t3lib_extMgm::isLoaded('dam')) {
	require_once(t3lib_extMgm::extPath('dam').'tca_media_field.php');
}

$TCA['tx_cfcleague_group'] = Array (
	'ctrl' => $TCA['tx_cfcleague_group']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,starttime,fe_group,name'
	),
	'feInterface' => $TCA['tx_cfcleague_group']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'fe_group' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
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
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name, shortname, logo, t3logo')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'starttime, fe_group')
	)
);

if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
	tx_rnbase::load('tx_rnbase_util_TSFAL');
	$TCA['tx_cfcleague_group']['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', array(
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',
		'size' => 1,
		'maxitems' => 1
	));
}
elseif(t3lib_extMgm::isLoaded('dam')) {
	$TCA['tx_cfcleague_group']['columns']['logo'] = txdam_getMediaTCA('image_field', 'logo');
	$TCA['tx_cfcleague_group']['columns']['logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo';
	$TCA['tx_cfcleague_group']['columns']['logo']['config']['size'] = 1;
	$TCA['tx_cfcleague_group']['columns']['logo']['config']['maxitems'] = 1;
}
else {
	$TCA['tx_cfcleague_group']['columns']['t3logo'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',		
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 700,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	);
}
