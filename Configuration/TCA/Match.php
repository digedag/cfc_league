<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_configurations');
// Zur Sicherheit einbinden, da die Funktion schon einmal nicht gefunden wurde...
if(t3lib_extMgm::isLoaded('dam')) {
	require_once(t3lib_extMgm::extPath('dam').'tca_media_field.php');
}
tx_rnbase::load('tx_cfcleague_tca_Lookup');


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
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getFormations',
				'default' => 0
			)
		),
		'system_guest' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.system_guest',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getFormations',
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


if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
	tx_rnbase::load('tx_rnbase_util_TSFAL');
	$TCA['tx_cfcleague_games']['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', array(
		'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.images',
	));
	$TCA['tx_cfcleague_games']['columns']['dam_media'] = tx_rnbase_util_TSFAL::getMediaTCA('dam_media', array(
		'type' => 'media',
		'label' => 'LLL:EXT:cms/locallang_ttc.xml:media',
	));
	$TCA['tx_cfcleague_games']['columns']['dam_media2'] = tx_rnbase_util_TSFAL::getMediaTCA('dam_media2', array(
		'type' => 'media',
		'label' => 'LLL:EXT:cms/locallang_ttc.xml:media',
	));
}
elseif(t3lib_extMgm::isLoaded('dam')) {
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

