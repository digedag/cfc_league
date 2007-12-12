<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Zur Sicherheit einbinden, da die Funktion schon einmal nicht gefunden wurde...
//require_once(t3lib_extMgm::extPath('dam').'tca_media_field.php');


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
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, name")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, fe_group")
	)
);



$TCA['tx_cfcleague_saison'] = Array (
	'ctrl' => $TCA['tx_cfcleague_saison']['ctrl'],
	'interface' => Array (
		"showRecordFieldList" => "hidden,name"
	),
	'feInterface' => $TCA['tx_cfcleague_saison']['feInterface'],
	"columns" => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		"name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison.name",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required,trim",
			)
		),
		'halftime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison.halftime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, name, halftime")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_cfcleague_competition"] = Array (
	"ctrl" => $TCA["tx_cfcleague_competition"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,name,agegroup,saison,type,teams"
	),
	"feInterface" => $TCA["tx_cfcleague_competition"]["feInterface"],
	"columns" => Array (
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
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.match_keys",
			"config" => Array (
				"type" => "input",
				"size" => "50",
				'max' => '2000',
				"eval" => "trim",
			)
		),
		"table_marks" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.table_marks",
			"config" => Array (
				"type" => "input",
				"size" => "50",
				'max' => '2000',
				"eval" => "trim",
			)
		),
		"agegroup" => Array (		
			'exclude' => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.agegroup",		
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_cfcleague_group",	
				"foreign_table_where" => "ORDER BY tx_cfcleague_group.sorting",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"saison" => Array (		
			'exclude' => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.saison",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_cfcleague_saison",	
				"foreign_table_where" => "ORDER BY tx_cfcleague_saison.name desc",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
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
		'point_system' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system',
			'config' => Array (
				'type' => 'radio',
				'items' => Array(
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_2',1),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition.point_system_3',0)
				),
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
		'0' => Array('showitem' => "hidden;;1;;1-1-1, name, internal_name, short_name, agegroup, saison, type, point_system, teams, match_keys, table_marks, match_parts")
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_cfcleague_competition_penalty'] = Array (
	'ctrl' => $TCA['tx_cfcleague_competition_penalty']['ctrl'],
	'interface' => Array (
		"showRecordFieldList" => "hidden,competition,team,game,comment,points_pos,points_neg,goals_pos,goals_neg,static_position"
	),
	'feInterface' => $TCA["tx_cfcleague_competition_penalty"]["feInterface"],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'competition' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.competition',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array(' ', '0'),
				),
		    'foreign_table' => 'tx_cfcleague_competition',
				'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.sorting ',

				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'team' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.team',
			'config' => Array (
				"type" => "select",
				"foreign_table" => "tx_cfcleague_teams",
				"foreign_table_where" => "AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid",
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getTeams4Competition',
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		'game' => Array (
			'exclude' => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.game",
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "tx_cfcleague_games",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"comment" => Array (
			'exclude' => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.comment",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		'matches' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.matches',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'wins' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.wins',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'loses' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.loses',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'draws' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.draws',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),

		'goals_pos' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.goals_pos',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'goals_neg' => Array (
			'exclude' => 0,
			'label' => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.goals_neg",
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'points_pos' => Array (
			'exclude' => 0,
			'label' => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.points_pos",
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'points_neg' => Array (
			'exclude' => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.points_neg",
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '-1000'
				),
				'default' => 0
			)
		),
		'static_position' => Array (
			'exclude' => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.static_position",
			"config" => Array (
				"type" => "check",
/*				"size" => "4",
				"max" => "4",
				"eval" => "int",
*/				"default" => 0
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, competition, team, game, comment, points_pos, points_neg, goals_pos, goals_neg, matches, wins, draws, loses, static_position")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);


$TCA['tx_cfcleague_club'] = Array (
	'ctrl' => $TCA['tx_cfcleague_club']['ctrl'],
	'interface' => Array (
		"showRecordFieldList" => "hidden,name,short_name,dam_logo"
	),
	'feInterface' => $TCA['tx_cfcleague_club']['feInterface'],
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
			'label' => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.short_name",
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max' => '100',	
				'eval' => 'required,trim',
			)
		),
                'dam_logo' => txdam_getMediaTCA('image_field', 'dam_images'),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name, short_name, dam_logo')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
$TCA['tx_cfcleague_club']['columns']['dam_logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo';
$TCA['tx_cfcleague_club']['columns']['dam_logo']['config']['size'] = 1;
$TCA['tx_cfcleague_club']['columns']['dam_logo']['config']['maxitems'] = 1;


$TCA['tx_cfcleague_teams'] = Array (
	'ctrl' => $TCA['tx_cfcleague_teams']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,club,name,short_name'
	),
	'feInterface' => $TCA['tx_cfcleague_teams']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'club' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.club',
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "tx_cfcleague_club",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
                'dam_logo' => txdam_getMediaTCA('image_field'),
		'name' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'required,trim',
			)
		),
		"short_name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.short_name",
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
				"eval" => "required,trim",
			)
		),
		'coaches' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.coaches',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_profiles',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 5,
			)
		),
		'players' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.players',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_profiles',
				'size' => 20,
				'minitems' => 0,
				'maxitems' => 40,
			)
		),
		'supporters' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.supporters',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_profiles',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 5,
			)
		),
                'dam_images' => txdam_getMediaTCA('image_field', 'dam_images'),

		'coaches_comment' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.coaches_comment',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'supporters_comment' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.supporters_comment',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'players_comment' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.players_comment',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'link_report' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.link_report',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'comment' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.comment',
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
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'dummy' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.dummy',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),

	),
	"types" => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, club, name, short_name, coaches, players, supporters, dam_images, players_comment, coaches_comment, supporters_comment, comment;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], dam_logo, link_report, dummy')
	),
	"palettes" => Array (
		'1' => Array('showitem' => '')
	)
);
$TCA['tx_cfcleague_teams']['columns']['dam_logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.logo';
$TCA['tx_cfcleague_teams']['columns']['dam_logo']['config']['size'] = 1;
$TCA['tx_cfcleague_teams']['columns']['dam_logo']['config']['maxitems'] = 1;



$TCA['tx_cfcleague_games'] = Array (
	"ctrl" => $TCA["tx_cfcleague_games"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,home,guest,competition,round,round_name,status, goals_home_1,goals_guest_1,goals_home_2,goals_guest_2,goals_home_3,goals_guest_3,goals_home_4,goals_guest_4,date,game_report,visitors,dam_images,goals_home_et,goals_guest_et,goals_home_ap,goals_guest_ap",
		"maxDBListItems" => "5"
	),
	'feInterface' => $TCA['tx_cfcleague_games']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),

		'match_no' => Array (
			'exclude' => 1,
			'label' => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.match_no",
			'config' => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "trim",
			)
		),
		"home" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.home",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_cfcleague_teams",	
				"foreign_table_where" => "AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid",	
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getTeams4Competition',
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"guest" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.guest",		
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_cfcleague_teams",
				"foreign_table_where" => "AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid",
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getTeams4Competition',
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"competition" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.competition",
			"config" => Array (
				'type' => 'select',
				'items' => Array (
					Array(' ', '0'),
				),
				'foreign_table' => 'tx_cfcleague_competition',
				"foreign_table_where" => "AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.uid",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"round" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.round",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => 'required,int',
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "1"
				),
				"default" => 0
			)
		),
		"round_name" => Array (
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.round_name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
				"eval" => 'required,trim',
			)
		),
		"status" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status",
			"config" => Array (
				'type' => 'select',
				'items' => Array(
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_scheduled",0),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_running",1),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_finished",2),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_invalid",-1)
				),
				'default' => 0
			)
		),
		"stadium" => Array (
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.stadium",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "200",	
				"eval" => "trim",
			)
		),
		"coach_home" => Array (
			"exclude" => 0,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.coach_home",
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_cfcleague_profiles",
				"foreign_table_where" => "AND tx_cfcleague_profiles.uid = 0",	
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getCoachesHome4Match',
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"coach_guest" => Array (
			"exclude" => 0,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.coach_guest",
			"config" => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_profiles',
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getCoachesGuest4Match',
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"players_home" => Array (
			"exclude" => 0,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_home",
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_cfcleague_profiles",
/*
Die Daten kommen auch hier aus der ItemProcFunc
Eine Einschränkung der ausgangsdaten funktioniert aber nicht, weil dann die
ausgewählten Spieler im T3-Formular nicht mher angezeigt werden...
Beim Trainer funktioniert es allerdings.
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
*/
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersHome4Match',
				"size" => 11,
				"minitems" => 0,
				"maxitems" => 11,
			)
		),
		"players_guest" => Array (
			"exclude" => 0,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_guest",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_cfcleague_profiles",
/*
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
*/
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersGuest4Match',
				"size" => 11,
				"minitems" => 0,
				"maxitems" => 11,
			)
		),
		"substitutes_home" => Array (
			"exclude" => 0,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_home",
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_cfcleague_profiles",
/*
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
*/
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersHome4Match',
				"size" => 5,
				"minitems" => 0,
				"maxitems" => 10,
			)
		),
		"substitutes_guest" => Array (
			"exclude" => 0,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_guest",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_cfcleague_profiles",
/*
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
*/
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersGuest4Match',
				"size" => 5,
				"minitems" => 0,
				"maxitems" => 10,
			)
		),
		"system_home" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.system_home",
			"config" => Array (
				'type' => 'select',
				'items' => Array(
					Array('','0'),
					Array("3-5-2","1-3-5-2"),
					Array("3-4-3","1-3-4-3"),
					Array("4-4-2","1-4-4-2"),
					Array("4-3-3","1-4-3-3"),
					Array("4-5-1","1-4-5-1"),
					Array("5-3-2","1-5-3-2"),
					Array("5-4-1","1-5-4-1")
				),
				'default' => 0
			)
		),
		"system_guest" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.system_guest",
			"config" => Array (
				'type' => 'select',
				'items' => Array(
					Array('','0'),
					Array("3-5-2","1-3-5-2"),
					Array("3-4-3","1-3-4-3"),
					Array("4-4-2","1-4-4-2"),
					Array("4-3-3","1-4-3-3"),
					Array("4-5-1","1-4-5-1"),
					Array("5-3-2","1-5-3-2"),
					Array("5-4-1","1-5-4-1")
				),
				'default' => 0
			)
		),
		"referee" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.referee",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "tx_cfcleague_profiles",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"assists" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.assists",
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "tx_cfcleague_profiles",
				"size" => 3,
				"minitems" => 0,
				"maxitems" => 5,
			)
		),

		'goals_home_1' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_1',		
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
		"goals_guest_1" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_1",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),
		"goals_home_2" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_2",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),
		"goals_guest_2" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_2",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),
		'goals_home_3' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_3',		
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
		'goals_guest_3' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_3',		
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
		'goals_home_4' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_4',		
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
		'goals_guest_4' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_4',		
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
		
		'date' => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.date",		
			"config" => Array (
				"type" => "input",
				"size" => "12",
				"max" => "20",
				"eval" => "datetime",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		'link_report' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.link_report',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'link_ticker' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.link_ticker',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'game_report_author' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.game_report_author',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'trim',
			)
		),
		'game_report' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.game_report',
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
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		"visitors" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.visitors",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "6",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "500000",
					"lower" => "0"
				),
				"default" => 0
			)
		),

    'dam_images' => txdam_getMediaTCA('image_field', 'dam_images'),
    'dam_media' => txdam_getMediaTCA('media_field', 'dam_media'),

		'is_extratime' => Array (
			'exclude' => 1,
			'label' => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.extratime",
			'config' => Array (
				'type' => 'check',
			)
		),

		"goals_home_et" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_et",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),
		"goals_guest_et" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_et",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),

		'is_penalty' => Array (
			'exclude' => 1,
			'label' => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.penalty",
			'config' => Array (
				'type' => 'check',
			)
		),

		"goals_home_ap" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_ap",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),
		"goals_guest_ap" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_ap",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "0"
				),
				"default" => 0
			)
		),
	),
	"types" => Array (
	// goals_home_1, goals_guest_1, goals_home_2, goals_guest_2, 
		"0" => Array("showitem" => "hidden;;1;;1-1-1, match_no, competition, home, guest, round, round_name, stadium, status, date, coach_home, players_home, substitutes_home, system_home, system_guest, coach_guest, players_guest, substitutes_guest, referee, assists, game_report_author, game_report;;4;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], visitors, dam_images, dam_media, is_extratime;;2, is_penalty;;3;;1-1-1")
	),
	"palettes" => Array (
		'1' => Array('showitem' => ''),
		'2' => Array('showitem' => 'goals_home_et, goals_guest_et'),
		'3' => Array('showitem' => 'goals_home_ap, goals_guest_ap'),
		'4' => Array('showitem' => 'link_report, link_ticker')
	)
);

$TCA['tx_cfcleague_profiles'] = Array (
	'ctrl' => $TCA['tx_cfcleague_profiles']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => "hidden,first_name,last_name,dam_images,birthday,nationality,height,weight,position,duration_of_contract,start_of_contract,email,nickname,summary,description"
	),
	'feInterface' => $TCA["tx_cfcleague_profiles"]["feInterface"],
	'columns' => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"first_name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.first_name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "50",	
				"eval" => "trim",
			)
		),
		"last_name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.last_name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "70",	
				"eval" => "required,trim",
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
                'dam_images' => txdam_getMediaTCA('image_field', 'dam_images'),
		'birthday' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.birthday',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
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
		"nationality" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.nationality",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"height" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.height",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"weight" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.weight",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"position" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.position",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"duration_of_contract" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.duration_of_contract",		
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"start_of_contract" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.start_of_contract",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"email" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.email",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"nickname" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.nickname",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"summary" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.summary",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, first_name, last_name, home_town, dam_images, birthday, native_town, nationality, height, weight, position, duration_of_contract, start_of_contract, email, nickname, link_report, summary;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/], description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/]")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA['tx_cfcleague_match_notes'] = Array (
	'ctrl' => $TCA['tx_cfcleague_match_notes']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,game,minute,extra_time,type,player_home,player_guest,comment',
		'maxDBListItems' => '5'
	),
	'feInterface' => $TCA['tx_cfcleague_match_notes']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		'game' => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.match",
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "tx_cfcleague_games",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		'minute' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.minute',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '3',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '200',
					'lower' => '-1'
				),
				'default' => 0
			)
		),
		'extra_time' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.extra_time',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '3',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '20',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'type' => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.ticker", '100'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal", '10'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.header", '11'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.penalty", '12'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.own", '30'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.assist", '31'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.penalty.forgiven", '32'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.corner", '33'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.yellow", '70'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.yellowred", '71'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.red", '72'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.changeout", '80'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.changein", '81'),
					Array("LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.captain", '200'),
				),
				"size" => 1,
				"maxitems" => 1,
			)
		),

        'player_home' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.player_home',        
            'config' => Array (
                'type' => 'select',
                'foreign_table' => 'tx_cfcleague_profiles',
		'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',	
                'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersHome4Match',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'player_guest' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.player_guest',        
            'config' => Array (
                'type' => 'select',
                'foreign_table' => 'tx_cfcleague_profiles',
		'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',	
                'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersGuest4Match',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'comment' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.comment',
            'config' => Array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
    ),
    'types' => Array (
        '0' => Array('showitem' => "hidden;;1;;1-1-1, game, minute, extra_time, type, player_home, player_guest, comment")
    ),
    'palettes' => Array (
        '1' => Array('showitem' => '')
    )
);

?>