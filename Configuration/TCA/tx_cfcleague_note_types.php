<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$tx_cfcleague_note_types = Array (
		'ctrl' => Array (
				'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types',
				'label' => 'label',
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'delete' => 'deleted',
				'enablecolumns' => Array (
				),
				'iconfile' => tx_rnbase_util_Extensions::extRelPath('cfc_league').'icon_table.gif',
		),
		'interface' => Array (
				'showRecordFieldList' => '',
				'maxDBListItems' => '15'
		),
		'feInterface' => Array (
				'fe_admin_fieldList' => 'label, marker, description',
		),
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
				'0' => Array('showitem' => 'label, marker, description')
		),
		'palettes' => Array (
				'1' => Array('showitem' => '')
		)
);

return $tx_cfcleague_note_types;
