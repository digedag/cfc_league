<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$globalClubs = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'useGlobalClubs')) > 0;
$clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;

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

$tx_cfcleague_teams = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams',
		'label' => 'name',
		'searchFields' => 'uid,name,short_name,tlc',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'iconfile' => tx_rnbase_util_Extensions::extRelPath('cfc_league').'icon_tx_cfcleague_teams.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,club,name,short_name'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name',
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
		'tlc' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_tlc',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => 'trim',
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
				'size' => 4,
				'minitems' => 0,
				'maxitems' => 10,
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
				'maxitems' => 60,
			)
		),
		'supporters' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.supporters',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_cfcleague_profiles',
				'size' => 4,
				'minitems' => 0,
				'maxitems' => 10,
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
		'extid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_extid',
				'config' => Array (
						'type' => 'input',
						'size' => '10',
						'max' => '255',
						'eval' => 'trim',
				)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, club,logo, t3logo, name, short_name, tlc, agegroup, dam_images, t3images, dam_logo, link_report, dummy, extid,
		--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams_tab_members,coaches, players, supporters, players_comment, coaches_comment, supporters_comment, comment;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$tca = tx_rnbase::makeInstance('Tx_Rnbase_Util_TCATool');
$tca->addWizard($tx_cfcleague_teams, 'comment', 'RTE', 'wizard_rte', array());

tx_rnbase::load('tx_cfcleague_tca_Lookup');
tx_rnbase::load('tx_rnbase_util_TYPO3');
if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
	tx_rnbase::load('tx_rnbase_util_TSFAL');
	// Auswahlbox Vereinslogos
	$tx_cfcleague_teams['columns']['logo'] = tx_cfcleague_tca_Lookup::getTeamLogoField();

	$tx_cfcleague_teams['columns']['t3logo'] = tx_rnbase_util_TSFAL::getMediaTCA('t3logo', array(
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.logo',
		'config' => array('size' => 1, 'maxitems' => 1),
	));
	$tx_cfcleague_teams['columns']['t3images'] = tx_rnbase_util_TSFAL::getMediaTCA('t3images', array(
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.pictures',
	));
}
else if(tx_rnbase_util_Extensions::isLoaded('dam')) {
	require_once(tx_rnbase_util_Extensions::extPath('dam').'tca_media_field.php');
	$tx_cfcleague_teams['columns']['logo'] = tx_cfcleague_tca_Lookup::getTeamLogoField();
	$tx_cfcleague_teams['columns']['dam_images'] = txdam_getMediaTCA('image_field', 'dam_images');
	$tx_cfcleague_teams['columns']['dam_logo'] = txdam_getMediaTCA('image_field');
	$tx_cfcleague_teams['columns']['dam_logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams.logo';
	$tx_cfcleague_teams['columns']['dam_logo']['config']['size'] = 1;
	$tx_cfcleague_teams['columns']['dam_logo']['config']['maxitems'] = 1;
}
else {
	$tx_cfcleague_teams['columns']['t3logo'] = Array (
		'exclude' => 1,
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
	$tx_cfcleague_teams['columns']['t3images'] = Array (
		'exclude' => 1,
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

return $tx_cfcleague_teams;
