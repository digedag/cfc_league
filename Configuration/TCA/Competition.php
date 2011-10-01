<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if(t3lib_extMgm::isLoaded('dam')) {
	require_once(t3lib_extMgm::extPath('dam').'tca_media_field.php');
}

$TCA['tx_cfcleague_competition'] = Array (
	'ctrl' => $TCA['tx_cfcleague_competition']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,name,agegroup,saison,type,teams'
	),
	'feInterface' => $TCA['tx_cfcleague_competition']['feInterface'],
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
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'internal_name' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.internal_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'short_name' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.short_name',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
			)
		),
		'match_keys' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_keys',
			'config' => Array (
				'type' => 'input',
				'size' => '50',
				'max' => '2000',
				'eval' => 'trim',
			)
		),
		'table_marks' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.table_marks',
			'config' => Array (
				'type' => 'input',
				'size' => '50',
				'max' => '2000',
				'eval' => 'trim',
			)
		),
		'agegroup' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_group',
				'foreign_table_where' => 'ORDER BY tx_cfcleague_group.sorting',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 5,
			)
		),
		'saison' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.saison',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_saison',
				'foreign_table_where' => 'ORDER BY tx_cfcleague_saison.name desc',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'sports' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.sports',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getLeagueTableTypes',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'type' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.type',
			'config' => Array (
				'type' => 'radio',
				'items' => Array(
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.type_league',1),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.type_ko',2),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.type_other',0)
//					, Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.type_combined',100)
				),
				'default' => 1
			)
		),
		'obligation' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.obligation',
			'config' => Array (
				'type' => 'check',
				'default' => '1'
			)
		),
		'match_parts' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_parts',
			'config' => Array (
				'type' => 'radio',
				'items' => Array(
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_parts_0',0),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_parts_1',1),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_parts_2',2),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_parts_3',3),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_parts_4',4)
				),
				'default' => 0
			)
		),
		'addparts' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_addparts',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'point_system' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPointSystems',
//				'items' => Array(
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2',1),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_3',0)
//				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'default' => 0
			)
		),
		'teams' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.teams',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_teams',
				'size' => 20,
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
/* used for combined competitions later...
		'parent' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.parent',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_competition',
				'foreign_table_where' => 'tx_cfcleague_competition.type = 100 ORDER BY tx_cfcleague_competition.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
*/
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name, sports'),
		'football' => Array('showitem' => 'hidden;;1;;1-1-1, name, sports, internal_name, short_name, agegroup, saison, type;;2, point_system, logo, t3logo, teams, match_keys, table_marks, match_parts, addparts'),
		'icehockey' => Array('showitem' => 'hidden;;1;;1-1-1, name, sports, internal_name, short_name, agegroup, saison, type;;2, point_system, logo, t3logo, teams, match_keys, table_marks, match_parts, addparts'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => ''),
		'2' => Array('showitem' => 'obligation')
	)
);

if(t3lib_extMgm::isLoaded('dam')) {
	$TCA['tx_cfcleague_competition']['columns']['logo'] = txdam_getMediaTCA('image_field', 'logo');
	$TCA['tx_cfcleague_competition']['columns']['logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo';
	$TCA['tx_cfcleague_competition']['columns']['logo']['config']['size'] = 1;
	$TCA['tx_cfcleague_competition']['columns']['logo']['config']['maxitems'] = 1;
}
else {
	$TCA['tx_cfcleague_competition']['columns']['t3logo'] = Array (
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
