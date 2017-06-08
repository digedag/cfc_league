<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$tx_cfcleague_team_notes = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes',
		'label' => 'uid',
		'label_alt' => 'type,player,team',
		'label_alt_force' => 1,
		'searchFields' => 'uid,comment',
		'requestUpdate' => 'team',
		'tstamp' => 'tstamp',
		'type' => 'mediatype',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-teamnotes-default'
        ],
		'iconfile' => tx_rnbase_util_Extensions::extRelPath('cfc_league').'Resources/Public/Icons/icon_tx_cfcleague_teams.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => '',
		'maxDBListItems' => '5'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, team, player, type, comment',
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
				'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayers4Team',
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


if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
	tx_rnbase::load('tx_rnbase_util_TSFAL');
	$tx_cfcleague_team_notes['columns']['mediatype']['config']['items'][] =
					Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.media', '1');
	$tx_cfcleague_team_notes['columns']['media'] = tx_rnbase_util_TSFAL::getMediaTCA('media', array(
		'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.images',
		'config' => array('size' => 1, 'maxitems' => 1),
	));
	$tx_cfcleague_team_notes['columns']['media']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.media';
}
elseif(tx_rnbase_util_Extensions::isLoaded('dam')) {
	// Type media is supported with DAM only!
	$tx_cfcleague_team_notes['columns']['mediatype']['config']['items'][] =
		Array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.media', '1');
	$tx_cfcleague_team_notes['columns']['media'] = txdam_getMediaTCA('image_field', 'media');
	$tx_cfcleague_team_notes['columns']['media']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.media';
	$tx_cfcleague_team_notes['columns']['media']['config']['size'] = 1;
	$tx_cfcleague_team_notes['columns']['media']['config']['maxitems'] = 1;
}

return $tx_cfcleague_team_notes;
