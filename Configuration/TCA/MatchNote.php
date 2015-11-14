<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('tx_rnbase_configurations');
// Zur Sicherheit einbinden, da die Funktion schon einmal nicht gefunden wurde...
if(tx_rnbase_util_Extensions::isLoaded('dam')) {
	require_once(tx_rnbase_util_Extensions::extPath('dam').'tca_media_field.php');
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
				'second' => Array (
						'exclude' => 1,
						'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.second',
						'config' => Array (
								'type' => 'input',
								'size' => '4',
								'max' => '3',
								'eval' => 'int',
								'checkbox' => '0',
								'range' => Array (
										'upper' => '59',
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
				'0' => Array('showitem' => 'hidden;;1;;1-1-1, game, minute, second, extra_time, type, player_home, player_guest, comment')
		),
		'palettes' => Array (
				'1' => Array('showitem' => '')
		)
);

