<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_competition_penalty = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty',
		'label' => 'comment',
		'label_alt' => 'team,competition',
		'label_alt_force' => 1,
		'searchFields' => 'uid,name,short_name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
				'disabled' => 'hidden',
		),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-competition-penalty-default'
        ],
		'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_competition_penalty.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,competition,team,game,comment,points_pos,points_neg,goals_pos,goals_neg,static_position'
	),
	'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, comment, team, game, points_pos, points_neg, goals_pos, goals_neg, static_position',
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
		'competition' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.competition',
			'config' => Array (
				'type' => 'select',
			    'renderType' => 'selectSingle',
			    'items' => Array (
						Array(' ', '0'),
				),
			    'foreign_table' => 'tx_cfcleague_competition',
				'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.sorting ',

				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			),
		    'onChange' => 'reload',
		),
		'team' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.team',
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
				'type' => 'input',
			    'size' => '4',
			    'max' => '4',
			    'eval' => 'int',
			    'range' => Array (
			        'upper' => '100',
			        'lower' => '-1'
			    ),
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
		'0' => Array('showitem' => 'hidden, competition, team, game, comment, points_pos, points_neg, goals_pos, goals_neg, matches, wins, draws, loses, static_position, correction')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

if (!tx_rnbase_util_TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_competition_penalty['ctrl']['requestUpdate'] = 'competition';
}

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
$tx_cfcleague_competition_penalty['columns']['comment']['config']['wizards'] = Tx_Rnbase_Utility_TcaTool::getWizards('',['RTE' => true,]);


return $tx_cfcleague_competition_penalty;
