<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('tx_rnbase_configurations');
// Zur Sicherheit einbinden, da die Funktion schon einmal nicht gefunden wurde...
if(tx_rnbase_util_Extensions::isLoaded('dam')) {
	require_once(tx_rnbase_util_Extensions::extPath('dam').'tca_media_field.php');
}
tx_rnbase::load('tx_cfcleague_tca_Lookup');

$globalClubs = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
$clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;



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

