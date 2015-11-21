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

$labelClub = $clubOrdering ? 'city' : 'name';
$altLabelClub = $clubOrdering ? 'name' : 'city';


$tx_cfcleague_club = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',
		'label' => $labelClub,
		'label_alt' => $altLabelClub,
		'label_alt_force' => 1,
		'searchFields' => 'uid,name,short_name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'iconfile' => tx_rnbase_util_Extensions::extRelPath('cfc_league').'icon_tx_cfcleague_clubs.gif',
	),
	'interface' => Array (
		'showRecordFieldList' => 'hidden,name,short_name,dam_logo'
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name, dam_logo',
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
		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'required,trim',
			)
		),
		'short_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.short_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'required,trim',
			)
		),
		'address' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.address',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tt_address',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'favorite' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.favorite',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'www' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.www',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '200',
				'eval' => 'trim',
			)
		),
		'email' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.email',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '200',
				'eval' => 'trim',
			)
		),
		'street' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.street',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '200',
				'eval' => 'trim',
			)
		),
		'zip' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.zip',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '10',
				'eval' => 'trim',
			)
		),
		'city' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.city',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '200',
				'eval' => 'trim',
			)
		),
		'countrycode' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_countrycode',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'trim',
			)
		),
		'lng' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lng',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'max' => '50',
				'eval' => 'trim',
			)
		),
		'lat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_common_lat',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'max' => '50',
				'eval' => 'trim',
			)
		),
		'shortinfo' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.shortinfo',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'info' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_info',
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
		'info2' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_info2',
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

		'established' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_established',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '10',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'yearestablished' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_yearestablished',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '4',
				'eval' => 'int',
			)
		),
		'colors' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_colors',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '200',
				'eval' => 'trim',
			)
		),
		'members' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club_members',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '10s',
				'eval' => 'int',
			)
		),

		'stadiums' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums',
			'config' => Array (
				'type' => 'select',
				'size' => 10,
				'autoSizeMax' => 50,
				'minitems' => 0,
				'maxitems' => 100,
				'foreign_table' => 'tx_cfcleague_stadiums',
				'foreign_table_where' => 'ORDER BY name',
				'MM' => 'tx_cfcleague_stadiums_mm',
				'MM_foreign_select' => 1,
				'MM_opposite_field' => 'clubs',
				'MM_match_fields' => Array (
					'tablenames' => 'tx_cfcleague_club',
				),
				'wizards' => Array(
//					'_PADDING' => 1,
//					'_VERTICAL' => 1,
					'edit' => array(
						'type' => 'popup',
						'title' => 'Edit',
						'script' => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon' => 'edit2.gif',
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new record',
						'icon' => 'add.gif',
						'params' => Array(
							'table'=>'tx_cfcleague_stadiums',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name,short_name,dam_logo, logo,favorite,stadiums,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.tab_contact,www,email,street,zip,city,country,countrycode,address,lng,lat,
			--div--;LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.tab_info,established,yearestablished,colors,members,shortinfo,info;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts],info2;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

if(tx_rnbase_util_Extensions::isLoaded('static_info_tables')) {
	$tx_cfcleague_club['columns']['country'] = tx_cfcleague_tca_Lookup::getCountryField();
}
if(tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
	tx_rnbase::load('tx_rnbase_util_TSFAL');
	$tx_cfcleague_club['columns']['logo'] = tx_rnbase_util_TSFAL::getMediaTCA('logo', array(
		'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo',
	));
}
elseif(tx_rnbase_util_Extensions::isLoaded('dam')) {
	$tx_cfcleague_club['columns']['dam_logo'] = txdam_getMediaTCA('image_field', 'dam_images');
	$tx_cfcleague_club['columns']['dam_logo']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club.logo';
}
else {
	$tx_cfcleague_club['columns']['logo'] = Array (
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
}

return $tx_cfcleague_club;
