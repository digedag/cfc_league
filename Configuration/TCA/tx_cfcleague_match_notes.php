<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('tx_rnbase_configurations');
// Zur Sicherheit einbinden, da die Funktion schon einmal nicht gefunden wurde...
if(tx_rnbase_util_Extensions::isLoaded('dam')) {
	require_once(tx_rnbase_util_Extensions::extPath('dam').'tca_media_field.php');
}

$tx_cfcleague_match_notes = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes',
		'label' => 'uid',
		'label_alt' => 'minute,comment',
		'label_alt_force' => 1,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'iconfile' => tx_rnbase_util_Extensions::extRelPath('cfc_league').'Resources/Public/Icons/icon_table.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,game,minute,extra_time,type,player_home,player_guest,comment',
		'maxDBListItems' => '5'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, game, minute, extra_time, type, player_home, player_guest, comment',
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

		'player_home' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.player_home',
				'config' => Array (
						'type' => 'select',
						'foreign_table' => 'tx_cfcleague_profiles',
						'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
						'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersHome4Match',
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
						'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayersGuest4Match',
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

return $tx_cfcleague_match_notes;