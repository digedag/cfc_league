<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (TYPO3_MODE == 'BE') {
       include_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'class.tx_cfcleague.php');
       include_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'class.tx_cfcleague_league.php');
}



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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Group.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_group.gif',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_saison.gif',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Competition.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_competition.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name, internal_name, agegroup, saison, type, teams, match_keys, table_marks',
	)
);

tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_competition', 'EXT:cfc_league/locallang_csh_competition.php');

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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_competition.gif',
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
		'searchFields' => 'uid,first_name,last_name,stage_name,email,nickname',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Profile.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_profiles.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, first_name, last_name, image, birthday, nationality, height, weight, position, duration_of_contract, start_of_contract, email, stations, nickname, family, hobbies, prosperities, summary, description',
	)
);
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_profiles', 'EXT:cfc_league/locallang_csh_profiles.php');

$clubOrdering = intval(tx_rnbase_configurations::getExtensionCfgValue('cfc_league', 'clubOrdering')) > 0;
$labelClub = $clubOrdering ? 'city' : 'name';
$altLabelClub = $clubOrdering ? 'name' : 'city';

$TCA['tx_cfcleague_club'] = Array (
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Club.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_clubs.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, short_name, dam_logo',
	)
);
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_club', 'EXT:cfc_league/locallang_csh_club.php');

$TCA['tx_cfcleague_teams'] = Array (
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Team.php',
		//'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_teams.gif',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Match.php',
//		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, home, guest, competition, round, round_name, status, coach_home,
			coach_guest, players_home, players_guest, substitutes_home, substitutes_guest, goals_home_1,
			goals_guest_1, goals_home_2, goals_guest_2, goals_home_3, goals_guest_3, goals_home_4, goals_guest_4,
			date, link_report, link_ticker, game_report, visitors, goals_home_et, goals_guest_et, goals_home_ap,
			goals_guest_ap',
	)
);
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_games', 'EXT:cfc_league/locallang_csh_games.php');

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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/MatchNote.php',
		//'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_table.gif',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/TeamNote.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_tx_cfcleague_teams.gif',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_table.gif',
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
		'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/Stadium.php',
		'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, starttime, fe_group, name',
	)
);


if(tx_rnbase_util_Extensions::isLoaded('rgmediaimages')) {
	tx_rnbase_util_Extensions::addStaticFile($_EXTKEY, 'Configurations/TypoScript/video', 'Ext: cfcleague video support');
}

if (TYPO3_MODE=='BE')	{

  tx_rnbase_util_Extensions::addModule('web', 'txcfcleagueM1', '', tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/');

  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'tx_cfcleague_mod1_modCompetitions',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/class.tx_cfcleague_mod1_modCompetitions.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_competition'
	);
  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'tx_cfcleague_match_ticker',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/class.tx_cfcleague_match_ticker.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker'
	);
  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'tx_cfcleague_mod1_modTeams',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/class.tx_cfcleague_mod1_modTeams.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_team'
	);
  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'tx_cfcleague_mod1_modClubs',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/class.tx_cfcleague_mod1_modClubs.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_club'
	);
	tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'tx_cfcleague_profile_search',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/class.tx_cfcleague_profile_search.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles'
	);


	tx_rnbase::load('tx_rnbase_util_TYPO3');
	// add folder icon
	if(tx_rnbase_util_TYPO3::isTYPO3VersionOrHigher(4004000)) {
		t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-cfcleague', '../typo3conf/ext/cfc_league/ext_icon_cfcleague_folder.gif');
	}
	else {
		$ICON_TYPES['cfcleague'] = array('icon' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif');
	}

  $TCA['pages']['columns']['module']['config']['items'][] = array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_folder', 'cfcleague');
//  t3lib_div::debug($TCA['pages']['columns']['module']['config'], 'ext_tables');
/*
  $PAGES_TYPES['cfcleague'] = Array(
      'type' => 'sys',
      'icon' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif',
      'allowedTables' => '*'
    );
*/
}
?>