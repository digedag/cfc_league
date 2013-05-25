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

