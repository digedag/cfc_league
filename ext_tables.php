<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (TYPO3_MODE == 'BE') {
       include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_cfcleague.php');
       include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_cfcleague_league.php');
}

//require_once(t3lib_extMgm::extPath($_EXTKEY) . 'class.tx_cfcleague.php');


$TCA['tx_cfcleague_group'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_group',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_group.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, starttime, fe_group, name',
	)
);

$TCA['tx_cfcleague_saison'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_saison.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name',
	)
);

$TCA['tx_cfcleague_competition'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition',		
		'label' => 'name',
		'label_alt' => 'internal_name',
		'label_alt_force' => 1,
		'tstamp' => 'tstamp',
		'requestUpdate' => 'sports',
		'type' => 'tournament',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'Configuration/TCA/Competition.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_competition.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name, internal_name, agegroup, saison, type, teams, match_keys, table_marks',
	)
);

t3lib_extMgm::addLLrefForTCAdescr('tx_cfcleague_competition','EXT:cfc_league/locallang_csh_competition.php');

$TCA['tx_cfcleague_competition_penalty'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty',		
		'label' => 'comment',	
		'label_alt' => 'team,competition',
		'label_alt_force' => 1,
		'requestUpdate' => 'competition',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_competition.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, comment, team, game, points_pos, points_neg, goals_pos, goals_neg, static_position',
	)
);

$TCA['tx_cfcleague_profiles'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles',		
		'label' => 'last_name',	
		'label_alt' => 'first_name',
		'label_alt_force' => 1,
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
//		'sortby' => 'sorting',	
		'default_sortby' => 'ORDER BY last_name, first_name',
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',	
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_profiles.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, first_name, last_name, image, birthday, nationality, height, weight, position, duration_of_contract, start_of_contract, email, stations, nickname, family, hobbies, prosperities, summary, description',
	)
);
t3lib_extMgm::addLLrefForTCAdescr('tx_cfcleague_profiles','EXT:cfc_league/locallang_csh_profiles.php');

$clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;
$labelClub = $clubOrdering ? 'city' : 'name';
$altLabelClub = $clubOrdering ? 'name' : 'city';

$TCA['tx_cfcleague_club'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',
		'label' => $labelClub,
		'label_alt' => $altLabelClub,
		'label_alt_force' => 1,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'Configuration/TCA/Club.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_clubs.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name, dam_logo',
	)
);
t3lib_extMgm::addLLrefForTCAdescr('tx_cfcleague_club','EXT:cfc_league/locallang_csh_club.php');

$TCA['tx_cfcleague_teams'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'Configuration/TCA/Team.php',
		//'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_teams.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name',
	)
);

$TCA['tx_cfcleague_games'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games',		
		'label' => 'round_name',
		'label_alt' => 'competition,home,guest',
		'label_alt_force' => 1,
		'requestUpdate' => 'competition',
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, home, guest, competition, round, round_name, status, coach_home, coach_guest, players_home, players_guest, substitutes_home, substitutes_guest, goals_home_1, goals_guest_1, goals_home_2, goals_guest_2, goals_home_3, goals_guest_3, goals_home_4, goals_guest_4, date, link_report, link_ticker, game_report, visitors, goals_home_et, goals_guest_et, goals_home_ap, goals_guest_ap',
	)
);
t3lib_extMgm::addLLrefForTCAdescr('tx_cfcleague_games','EXT:cfc_league/locallang_csh_games.php');

$TCA['tx_cfcleague_match_notes'] = Array (
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, game, minute, extra_time, type, player_home, player_guest, comment',
	)
);

$TCA['tx_cfcleague_team_notes'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes',
		'label' => 'uid',
		'label_alt' => 'type,player,team',
		'label_alt_force' => 1,
		'requestUpdate' => 'team',
		'tstamp' => 'tstamp',
		'type' => 'mediatype',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cfcleague_teams.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, team, player, type, comment',
	)
);

$TCA['tx_cfcleague_note_types'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types',
		'label' => 'label',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'enablecolumns' => Array (
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'label, marker, description',
	)
);

$wecmap = array();
$wecmap['wec_map']['isMappable'] = 1;
$wecmap['wec_map']['addressFields'] = array(
	'street' => 'street',
	'city' => 'city',
	'zip' => 'zip',
);

$TCA['tx_cfcleague_stadiums'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_stadiums',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'default_sortby' => 'ORDER BY name',
		'EXT' => $wecmap,
		'delete' => 'deleted',
		'enablecolumns' => Array (
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'Configuration/TCA/Stadium.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, starttime, fe_group, name',
	)
);


if(t3lib_extMgm::isLoaded('rgmediaimages')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'Configurations/TypoScript/video', 'Ext: cfcleague video support');
}

if (TYPO3_MODE=='BE')	{
		
  t3lib_extMgm::addModule('web','txcfcleagueM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');

//'', ''

//  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_match_edit',
//		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_match_edit.php',
//		'LLL:EXT:cfc_league/mod1/locallang.xml:edit_games'
//	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_mod1_modCompetitions',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_mod1_modCompetitions.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_competition'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_match_ticker',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_match_ticker.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_mod1_modTeams',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_mod1_modTeams.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_team'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_mod1_modClubs',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_mod1_modClubs.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_club'
	);
	t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_profile_search',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_profile_search.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles'
	);


	require_once(t3lib_extMgm::extPath('rn_base').'class.tx_rnbase.php');
	tx_rnbase::load('tx_rnbase_util_TYPO3');
	// add folder icon
	if(tx_rnbase_util_TYPO3::isTYPO3VersionOrHigher(4004000)) {
		t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-cfcleague', '../typo3conf/ext/cfc_league/ext_icon_cfcleague_folder.gif');
	}
	else {
		$ICON_TYPES['cfcleague'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif');
	}

  $TCA['pages']['columns']['module']['config']['items'][] = array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_folder', 'cfcleague');
//  t3lib_div::debug($TCA['pages']['columns']['module']['config'], 'ext_tables');
/*
  $PAGES_TYPES['cfcleague'] = Array(
      'type' => 'sys',
      'icon' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif',
      'allowedTables' => '*'
    );
*/
}
?>