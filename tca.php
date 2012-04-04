<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_configurations');
// Zur Sicherheit einbinden, da die Funktion schon einmal nicht gefunden wurde...
if(t3lib_extMgm::isLoaded('dam')) {
	require_once(t3lib_extMgm::extPath('dam').'tca_media_field.php');
}
tx_rnbase::load('tx_cfcleague_tca_Lookup');

$globalClubs = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
$clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

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

if(t3lib_extMgm::isLoaded('dam')) {
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


$TCA['tx_cfcleague_saison'] = Array (
	'ctrl' => $TCA['tx_cfcleague_saison']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,name'
	),
	'feInterface' => $TCA['tx_cfcleague_saison']['feInterface'],
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
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name, halftime')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_cfcleague_competition_penalty'] = Array (
	'ctrl' => $TCA['tx_cfcleague_competition_penalty']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,competition,team,game,comment,points_pos,points_neg,goals_pos,goals_neg,static_position'
	),
	'feInterface' => $TCA['tx_cfcleague_competition_penalty']['feInterface'],
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
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_teams',
				'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getTeams4Competition',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'game' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.game',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_games',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'comment' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.comment',
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
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.goals_neg',
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
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.points_pos',
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
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.points_neg',
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
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.static_position',
			'config' => Array (
				'type' => 'check',
				'default' => 0
			)
		),
		'correction' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.correction',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, competition, team, game, comment, points_pos, points_neg, goals_pos, goals_neg, matches, wins, draws, loses, static_position, correction')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$clubArr = $globalClubs ? 
		Array (
				'type' => 'select',
				'items' => Array (
					Array(' ', '0'),
				),
				'foreign_table' => 'tx_cfcleague_club',
				'foreign_table_where' => 'ORDER BY ' . ($clubOrdering ? 'tx_cfcleague_club.city,' : '').' tx_cfcleague_club.name',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			) : Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_club',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			);

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
			'config' => $clubArr
		),
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
		'short_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.short_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'required,trim',
			)
		),
		'agegroup' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (Array('', ''),),
				'foreign_table' => 'tx_cfcleague_group',
				'foreign_table_where' => 'ORDER BY tx_cfcleague_group.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
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
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, club,logo, t3logo, name, short_name, agegroup, dam_images, t3images, dam_logo, link_report, dummy,
		--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_tab_members,coaches, players, supporters, players_comment, coaches_comment, supporters_comment, comment;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

if(t3lib_extMgm::isLoaded('dam')) {
	$TCA['tx_cfcleague_teams']['columns']['logo'] = tx_cfcleague_tca_Lookup::getTeamLogoField();
	$TCA['tx_cfcleague_teams']['columns']['dam_images'] = txdam_getMediaTCA('image_field', 'dam_images');
	$TCA['tx_cfcleague_teams']['columns']['dam_logo'] = txdam_getMediaTCA('image_field');
	$TCA['tx_cfcleague_teams']['columns']['dam_logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.logo';
	$TCA['tx_cfcleague_teams']['columns']['dam_logo']['config']['size'] = 1;
	$TCA['tx_cfcleague_teams']['columns']['dam_logo']['config']['maxitems'] = 1;
}
else {
	$TCA['tx_cfcleague_teams']['columns']['t3logo'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 900,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	);
	$TCA['tx_cfcleague_teams']['columns']['t3images'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.pictures',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 900,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 4,
			'minitems' => 0,
			'maxitems' => 10,
		)
	);
}



$TCA['tx_cfcleague_games'] = Array (
	'ctrl' => $TCA['tx_cfcleague_games']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,home,guest,competition,round,round_name,status, goals_home_1,goals_guest_1,goals_home_2,goals_guest_2,goals_home_3,goals_guest_3,goals_home_4,goals_guest_4,date,game_report,visitors,dam_images,goals_home_et,goals_guest_et,goals_home_ap,goals_guest_ap',
		'maxDBListItems' => '5'
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
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.match_no',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'trim',
			)
		),
		'home' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.home',		
			'config' => Array (
				'type' => 'select',	
				'foreign_table' => 'tx_cfcleague_teams',	
				'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',	
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getTeams4Competition',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'guest' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.guest',		
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_teams',
				'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getTeams4Competition',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'sets' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games_sets',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '254',
				'eval' => 'trim',
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
				'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'round' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.round',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'required,int',
//				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '1'
				),
				'default' => 0
			)
		),
		'round_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.round_name',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '100',
				'eval' => 'required,trim',
			)
		),
		'addinfo' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.addinfo',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '254',
				'eval' => 'trim',
			)
		),
		'status' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status',
			'config' => Array (
				'type' => 'select',
				'items' => Array(
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_scheduled',0),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_running',1),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_finished',2),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_invalid',-1),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.status_rescheduled',-10)
				),
				'default' => 0
			)
		),
		'stadium' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.stadium',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '200',
				'eval' => 'trim',
			)
		),
		'arena' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums',
			'config' => Array (
				'type' => 'select',
				'items' => Array(Array('','0')),
				'size' => '1',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getStadium4Match',
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'coach_home' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.coach_home',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_profiles',
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getCoachesHome4Match',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'coach_guest' => Array (
			'exclude' => 0,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.coach_guest',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_profiles',
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getCoachesGuest4Match',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'players_home' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_home',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersHome4Match',
				'size' => 11,
				'minitems' => 0,
				'maxitems' => 11,
			)
		),
		'players_guest' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_guest',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersGuest4Match',
				'size' => 11,
				'minitems' => 0,
				'maxitems' => 11,
			)
		),
		'substitutes_home' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_home',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersHome4Match',
				'size' => 9,
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'substitutes_guest' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_guest',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayersGuest4Match',
				'size' => 9,
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'players_home_stat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_home',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'players_guest_stat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_guest',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'substitutes_home_stat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_home',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'substitutes_guest_stat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_guest',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'scorer_home_stat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.scorer_home_stat',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'scorer_guest_stat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.scorer_guest_stat',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'system_home' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.system_home',
			'config' => Array (
				'type' => 'select',
				'items' => Array(
					Array('','0'),
					Array('3-5-2','1-3-5-2'),
					Array('3-4-3','1-3-4-3'),
					Array('4-4-2','1-4-4-2'),
					Array('4-3-3','1-4-3-3'),
					Array('4-5-1','1-4-5-1'),
					Array('5-3-2','1-5-3-2'),
					Array('5-4-1','1-5-4-1')
				),
				'default' => 0
			)
		),
		'system_guest' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.system_guest',
			'config' => Array (
				'type' => 'select',
				'items' => Array(
					Array('','0'),
					Array('3-5-2','1-3-5-2'),
					Array('3-4-3','1-3-4-3'),
					Array('4-4-2','1-4-4-2'),
					Array('4-3-3','1-4-3-3'),
					Array('4-5-1','1-4-5-1'),
					Array('5-3-2','1-5-3-2'),
					Array('5-4-1','1-5-4-1')
				),
				'default' => 0
			)
		),
		'referee' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.referee',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_profiles',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'assists' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.assists',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_profiles',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 5,
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
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'goals_guest_1' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_1',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'goals_home_2' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_2',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'goals_guest_2' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_2',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
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
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		
		'date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.date',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0'
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
		'liveticker_author' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.liveticker_author',
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
		'visitors' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.visitors',		
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '6',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '500000',
					'lower' => '0'
				),
				'default' => 0
			)
		),

		'is_extratime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.extratime',
			'config' => Array (
				'type' => 'check',
			)
		),

		'goals_home_et' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_et',		
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
//				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'goals_guest_et' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_et',		
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
//				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),

		'is_penalty' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.penalty',
			'config' => Array (
				'type' => 'check',
			)
		),

		'goals_home_ap' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_home_ap',		
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
//				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'goals_guest_ap' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.goals_guest_ap',		
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
//				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
	),
	'types' => Array (
	// goals_home_1, goals_guest_1, goals_home_2, goals_guest_2, 
		'0' => Array('showitem' => 
			'hidden;;1;;1-1-1,match_no,competition,home,guest,round,round_name,date,addinfo,status;;6,sets,arena,stadium,visitors, 
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_lineup,coach_home, players_home, substitutes_home, system_home, system_guest, coach_guest, players_guest, substitutes_guest, referee, assists,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_lineup_stat,players_home_stat, substitutes_home_stat, players_guest_stat, substitutes_guest_stat, scorer_home_stat, scorer_guest_stat,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_score, is_extratime;;2, is_penalty;;3;;1-1-1,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.game_report, game_report;;4;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], game_report_author;;5, dam_images, t3images, dam_media, dam_media2, video, videoimg')
	),
	'palettes' => Array (
		'1' => Array('showitem' => ''),
		'2' => Array('showitem' => 'goals_home_et, goals_guest_et'),
		'3' => Array('showitem' => 'goals_home_ap, goals_guest_ap'),
		'4' => Array('showitem' => 'link_report, link_ticker'),
		'5' => Array('showitem' => 'liveticker_author'),
		'6' => Array('showitem' => 'goals_home_2, goals_guest_2, goals_home_1, goals_guest_1'),
	)
);

if(t3lib_extMgm::isLoaded('rgmediaimages')) {
	$TCA['tx_cfcleague_games']['columns']['video'] = Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games_video',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'flv',
			'uploadfolder' => 'uploads/tx_cfcleague',
			'size' => 5,
			'minitems' => 0,
			'maxitems' => 20,
			'max_size' => 225280, // 220MB
		)
	);
	$TCA['tx_cfcleague_games']['columns']['videoimg'] = Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games_videoimg',
		'config' => Array (
			'type' => 'text',
			'cols' => '30',	
			'rows' => '5',
			'wizards' => Array(
				'_PADDING' => 2,
				'example' => Array(
						'title' => 'rgmediaimages Wizard:',
						'type' => 'script',
						'notNewRecords' => 1,
						'icon' => t3lib_extMgm::extRelPath('rgmediaimages').'wizard/icon.png',
						'script' => t3lib_extMgm::extRelPath('rgmediaimages').'wizard/index.php?table=tx_cfcleague_games&config=videoimg&internal=video',
						'JSopenParams' => 'height=750,width=900,status=0,menubar=0,scrollbars=0',
						'notNewRecords' => 1, 
				),
        ),
		)
	);
}


if(t3lib_extMgm::isLoaded('dam')) {
	$TCA['tx_cfcleague_games']['columns']['dam_images'] = txdam_getMediaTCA('image_field', 'dam_images');
	$TCA['tx_cfcleague_games']['columns']['dam_media'] = txdam_getMediaTCA('media_field', 'dam_media');
	$TCA['tx_cfcleague_games']['columns']['dam_media2'] = txdam_getMediaTCA('media_field', 'dam_media2');
}
else {
	$TCA['tx_cfcleague_games']['columns']['t3images'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.pictures',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 900,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 4,
			'minitems' => 0,
			'maxitems' => 10,
		)
	);
}


$TCA['tx_cfcleague_profiles'] = Array (
	'ctrl' => $TCA['tx_cfcleague_profiles']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,first_name,last_name,dam_images,birthday,nationality,height,weight,position,duration_of_contract,start_of_contract,email,nickname,summary,description'
	),
	'feInterface' => $TCA['tx_cfcleague_profiles']['feInterface'],
	'columns' => Array (
		'hidden' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
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
					'lower' => '10'
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
					'lower' => '10'
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
				'size' => '8',
				'max' => '20',
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
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'types' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.type',
			'config' => Array (
				'type' => 'select',
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
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, first_name, last_name, stage_name, home_town, birthday, native_town, nationality, height, weight, position, duration_of_contract, start_of_contract, email, nickname,
		--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.description,link_report,dam_images,t3images,types, summary;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/], description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cfcleague/rte/]')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

if(t3lib_extMgm::isLoaded('dam')) {
	$TCA['tx_cfcleague_profiles']['columns']['dam_images'] = txdam_getMediaTCA('image_field', 'dam_images');
}
else {
	$TCA['tx_cfcleague_profiles']['columns']['t3images'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.pictures',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 900,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 4,
			'minitems' => 0,
			'maxitems' => 10,
		)
	);
}


$TCA['tx_cfcleague_match_notes'] = Array (
	'ctrl' => $TCA['tx_cfcleague_match_notes']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,game,minute,extra_time,type,player_home,player_guest,comment',
		'maxDBListItems' => '5'
	),
	'feInterface' => $TCA['tx_cfcleague_match_notes']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'game' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.match',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_games',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
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
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles.type',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getMatchNoteTypes',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
//		'type' => Array (
//			'exclude' => 1,
//			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type',
//			'config' => Array (
//				'type' => 'select',
//				'items' => Array (
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.ticker', '100'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal', '10'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.header', '11'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.penalty', '12'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.own', '30'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.goal.assist', '31'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.penalty.forgiven', '32'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.corner', '33'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.yellow', '70'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.yellowred', '71'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.red', '72'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.changeout', '80'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.changein', '81'),
//					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.type.captain', '200'),
//				),
//				'size' => 1,
//				'maxitems' => 1,
//			)
//		),

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
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, game, minute, extra_time, type, player_home, player_guest, comment')
	),
	'palettes' => Array (
			'1' => Array('showitem' => '')
	)
);

$TCA['tx_cfcleague_team_notes'] = Array (
	'ctrl' => $TCA['tx_cfcleague_team_notes']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => '',
		'maxDBListItems' => '5'
	),
	'feInterface' => $TCA['tx_cfcleague_team_notes']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'team' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams',
			'config' => Array (
//				'type' => 'group',
//				'internal_type' => 'db',
//				'allowed' => 'tx_cfcleague_teams',
				'type' => 'select',
				'items' => Array (
					Array('', ''),
					),
				'foreign_table' => 'tx_cfcleague_teams',
				'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.sorting ',
				'eval' => 'required',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'type' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_note_types',
				'foreign_table_where' => 'ORDER BY tx_cfcleague_note_types.sorting',
				'eval' => 'required',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'mediatype' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.text', '0'),
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.number', '2'),
					),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'player' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles',        
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_profiles',
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
				'itemsProcFunc' => 'tx_cfcleague_handleDataInput->getPlayers4Team',
				'eval' => 'required',
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
		'number' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.number',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
			)
		),
	),
	'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, mediatype, team, player, type, comment'),
			'1' => Array('showitem' => 'hidden;;1;;1-1-1, mediatype, team, player, type, media'),
			'2' => Array('showitem' => 'hidden;;1;;1-1-1, mediatype, team, player, type, number')
	),
	'palettes' => Array (
			'1' => Array('showitem' => '')
	)
);


if(t3lib_extMgm::isLoaded('dam')) {
	// Type media is supported with DAM only!
	$TCA['tx_cfcleague_team_notes']['columns']['mediatype']['config']['items'][] =
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.media', '1');
	$TCA['tx_cfcleague_team_notes']['columns']['media'] = txdam_getMediaTCA('image_field', 'media');
	$TCA['tx_cfcleague_team_notes']['columns']['media']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.media';
	$TCA['tx_cfcleague_team_notes']['columns']['media']['config']['size'] = 1;
	$TCA['tx_cfcleague_team_notes']['columns']['media']['config']['maxitems'] = 1;
}

$TCA['tx_cfcleague_note_types'] = Array (
	'ctrl' => $TCA['tx_cfcleague_note_types']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => '',
		'maxDBListItems' => '15'
	),
	'feInterface' => $TCA['tx_cfcleague_note_types']['feInterface'],
	'columns' => Array (
		'label' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types.label',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'trim',
			)
		),
		'marker' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types.marker',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'size' => '20',
				'eval' => 'trim',
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types.description',
			'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
			)
		),
	),
	'types' => Array (
			'0' => Array('showitem' => 'ntype,label, marker, description')
	),
	'palettes' => Array (
			'1' => Array('showitem' => '')
	)
);

$stadiumClubArr = $globalClubs ? 
		Array (
				'type' => 'select',
				'foreign_table' => 'tx_cfcleague_club',
				'foreign_table_where' => 'ORDER BY name',
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 0,
				'maxitems' => 100,
				'MM' => 'tx_cfcleague_stadiums_mm',
				'MM_match_fields' => Array(
					'tablenames' => 'tx_cfcleague_club',
				),
			) : Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_club',
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 0,
				'maxitems' => 100,
				'MM' => 'tx_cfcleague_stadiums_mm',
				'MM_match_fields' => Array(
					'tablenames' => 'tx_cfcleague_club',
				),
			);
$TCA['tx_cfcleague_stadiums'] = Array (
	'ctrl' => $TCA['tx_cfcleague_stadiums']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'name'
	),
	'feInterface' => $TCA['tx_cfcleague_stadiums']['feInterface'],
	'columns' => Array (
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'required,trim',
			)
		),
		'altname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_altname',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'trim',
			)
		),
		'capacity' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_capacity',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '7',
				'eval' => 'int',
				'default' => 0
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_description',
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
		'description2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_description2',
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
		'street' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_street',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'trim',
			)
		),
		'city' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_city',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '250',
				'eval' => 'trim',
			)
		),
		'zip' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_zip',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '50',
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
		'clubs' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',
			'config' => $stadiumClubArr,
		),
		'address' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.address',
			'config' => Array (
        'type' => 'inline',
        'foreign_table' => 'tt_address',
        'appearance' => Array(
          'collapseAll' => 0,
          'expandSingle' => 1,
        ),
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => Array (
			'0' => Array('showitem' => 'name,altname,capacity,logo,t3logo,pictures,t3pictures,clubs,
						--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_tab_description,description;;4;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts],description2;;4;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts],
						--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_tab_location,street,city,zip,country,countrycode,lng,lat,address')
	),
	'palettes' => Array (
			'1' => Array('showitem' => '')
	)
);
if(t3lib_extMgm::isLoaded('static_info_tables')) {
	$TCA['tx_cfcleague_stadiums']['columns']['country'] = tx_cfcleague_tca_Lookup::getCountryField();
}
if(t3lib_extMgm::isLoaded('dam')) {
	$TCA['tx_cfcleague_stadiums']['columns']['logo'] = txdam_getMediaTCA('image_field', 'logo');
	$TCA['tx_cfcleague_stadiums']['columns']['logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_logo';
	$TCA['tx_cfcleague_stadiums']['columns']['logo']['config']['size'] = 1;
	$TCA['tx_cfcleague_stadiums']['columns']['logo']['config']['maxitems'] = 1;
	$TCA['tx_cfcleague_stadiums']['columns']['pictures'] = txdam_getMediaTCA('image_field', 'pictures');
}
else {
	$TCA['tx_cfcleague_stadiums']['columns']['t3logo'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_logo',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 900,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 1,
			'minitems' => 0,
			'maxitems' => 1,
		)
	);
	$TCA['tx_cfcleague_stadiums']['columns']['t3pictures'] = Array (
		'exclude' => 0,
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums_pictures',
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',
			'max_size' => 900,
			'uploadfolder' => 'uploads/tx_cfcleague',
			'show_thumbs' => 1,
			'size' => 4,
			'minitems' => 0,
			'maxitems' => 10,
		)
	);
}

?>