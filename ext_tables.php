<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (TYPO3_MODE == 'BE') {
       include_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'class.tx_cfcleague.php');
       include_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'class.tx_cfcleague_league.php');
}

if(!tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
	// TCA registration for 4.5
	$TCA['tx_cfcleague_group'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_group.php';
	$TCA['tx_cfcleague_saison'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_saison.php';
	$TCA['tx_cfcleague_competition'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_competition.php';
	$TCA['tx_cfcleague_competition_penalty'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_competition_penalty.php';
	$TCA['tx_cfcleague_profiles'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_profiles.php';
	$TCA['tx_cfcleague_note_types'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_note_types.php';
}


tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_competition', 'EXT:cfc_league/locallang_csh_competition.php');
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
	if(tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
		// TODO...
	}
	elseif(tx_rnbase_util_TYPO3::isTYPO3VersionOrHigher(4004000)) {
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