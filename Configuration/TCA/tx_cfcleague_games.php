<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('tx_cfcleague_tca_Lookup');

$rteConfig = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';
$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_games = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games',
		'label' => 'round_name',
		'label_alt' => 'competition,home,guest',
		'label_alt_force' => 1,
		'searchFields' => 'uid,round_name,addinfo,stadium,game_report',
		// configure fields for NeighborRow initialization
		'useColumnsForDefaultValues' => 'competition,date,round,round_name',
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-games-default'
        ],
		'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_table.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,home,guest,competition,round,round_name,status, goals_home_1,goals_guest_1,
					goals_home_2,goals_guest_2,goals_home_3,goals_guest_3,goals_home_4,goals_guest_4,date,
					game_report,visitors,dam_images,goals_home_et,goals_guest_et,goals_home_ap,goals_guest_ap',
		'maxDBListItems' => '5'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, home, guest, competition, round, round_name, status, coach_home,
			coach_guest, players_home, players_guest, substitutes_home, substitutes_guest, goals_home_1,
			goals_guest_1, goals_home_2, goals_guest_2, goals_home_3, goals_guest_3, goals_home_4, goals_guest_4,
			date, link_report, link_ticker, game_report, visitors, goals_home_et, goals_guest_et, goals_home_ap,
			goals_guest_ap',
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
			    'renderType' => 'selectSingle',
			    'foreign_table' => 'tx_cfcleague_teams',
				'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getTeams4Competition',
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
			    'renderType' => 'selectSingle',
			    'foreign_table' => 'tx_cfcleague_teams',
				'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getTeams4Competition',
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
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.competition',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectSingle',
			    'items' => Array (
					Array(' ', '0'),
				),
				'foreign_table' => 'tx_cfcleague_competition',
				'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			),
			'onChange' => 'reload',
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
			    'renderType' => 'selectSingle',
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
			    'renderType' => 'selectSingle',
			    'items' => Array(Array('','0')),
				'size' => '1',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getStadium4Match',
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'coach_home' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.coach_home',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectSingle',
			    'foreign_table' => 'tx_cfcleague_profiles',
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getCoachesHome4Match',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'coach_guest' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.coach_guest',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectSingle',
			    'foreign_table' => 'tx_cfcleague_profiles',
				'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getCoachesGuest4Match',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'players_home' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_home',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersHome4Match',
				'size' => 11,
				'minitems' => 0,
				'maxitems' => 11,
			)
		),
		'players_guest' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.players_guest',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersGuest4Match',
				'size' => 11,
				'minitems' => 0,
				'maxitems' => 11,
			)
		),
		'substitutes_home' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_home',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersHome4Match',
				'size' => 9,
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'substitutes_guest' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.substitutes_guest',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersGuest4Match',
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
			    'renderType' => 'selectSingle',
			    'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getFormations',
				'default' => 0
			)
		),
		'system_guest' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.system_guest',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectSingle',
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
			    'wizards' => Tx_Rnbase_Utility_TcaTool::getWizards('tx_cfcleague_profiles', [
			        'suggest' => true,
			    ])
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
			    'wizards' => Tx_Rnbase_Utility_TcaTool::getWizards('tx_cfcleague_profiles', [
			        'suggest' => true,
			    ])
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
				'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
			    'size' => '8',
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
		'extid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games_extid',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '255',
				'eval' => 'trim',
			)
		),
	),
	'types' => Array (
	// goals_home_1, goals_guest_1, goals_home_2, goals_guest_2,
		'0' => [
		    'showitem' => 'hidden,match_no,competition,home,guest,round,round_name,date,addinfo,status,--palette--;;6,sets,arena,stadium,visitors,extid,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_lineup,coach_home, players_home, substitutes_home, system_home, system_guest, coach_guest, players_guest, substitutes_guest, referee, assists,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_lineup_stat,players_home_stat, substitutes_home_stat, players_guest_stat, substitutes_guest_stat, scorer_home_stat, scorer_guest_stat,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_score, is_extratime,--palette--;;2, is_penalty,--palette--;;3,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.game_report, game_report,--palette--;;4, game_report_author,--palette--;;5, dam_images, t3images, dam_media, dam_media2, video, videoimg'
		]
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

if (!tx_rnbase_util_TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_games['ctrl']['requestUpdate'] = 'competition';
}

if(!tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
    $tx_cfcleague_games['types'][0]['showitem'] = 'hidden,match_no,competition,home,guest,round,round_name,date,addinfo,status;;6,sets,arena,stadium,visitors,extid,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_lineup,coach_home, players_home, substitutes_home, system_home, system_guest, coach_guest, players_guest, substitutes_guest, referee, assists,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_lineup_stat,players_home_stat, substitutes_home_stat, players_guest_stat, substitutes_guest_stat, scorer_home_stat, scorer_guest_stat,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.tab_score, is_extratime;;2, is_penalty;;3,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.game_report, game_report;;4;'.$rteConfig.', game_report_author;;5, dam_images, t3images, dam_media, dam_media2, video, videoimg';
}
tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
Tx_Rnbase_Utility_TcaTool::configureWizards($tx_cfcleague_games, [
    'game_report' => ['RTE' => ['defaultExtras' => $rteConfig]],
    'referee' => ['targettable' => 'tx_cfcleague_profiles', 'suggest' => true],
    'assists' => ['targettable' => 'tx_cfcleague_profiles', 'suggest' => true],
]);



if(tx_rnbase_util_Extensions::isLoaded('rgmediaimages') && !tx_rnbase_util_TYPO3::isTYPO70OrHigher()) {
	$tx_cfcleague_games['columns']['video'] = Array (
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
	$tx_cfcleague_games['columns']['videoimg'] = Array (
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
						'icon' => tx_rnbase_util_Extensions::extRelPath('rgmediaimages').'wizard/icon.png',
						'script' => tx_rnbase_util_Extensions::extRelPath('rgmediaimages').'wizard/index.php?table=tx_cfcleague_games&config=videoimg&internal=video',
						'JSopenParams' => 'height=750,width=900,status=0,menubar=0,scrollbars=0',
						'notNewRecords' => 1,
				),
        ),
		)
	);
	// TODO: Fix for 7.6
// 	$tca->addWizard($tx_cfcleague_games, 'videoimg', 'RTE', 'wizard_rte',
// 			array('table'=>'tx_cfcleague_games','config'=>'videoimg', 'internal'=>'video'));
}


tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_games['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', array(
	'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.images',
));
$tx_cfcleague_games['columns']['dam_media'] = tx_rnbase_util_TSFAL::getMediaTCA('dam_media', array(
	'type' => 'media',
	'label' => 'LLL:EXT:cms/locallang_ttc.xml:media',
));
$tx_cfcleague_games['columns']['dam_media2'] = tx_rnbase_util_TSFAL::getMediaTCA('dam_media2', array(
	'type' => 'media',
	'label' => 'LLL:EXT:cms/locallang_ttc.xml:media',
));

return $tx_cfcleague_games;
