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
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
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

$TCA['tx_cfcleague_club'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_club',		
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
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
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

if (TYPO3_MODE=='BE')	{
		
  t3lib_extMgm::addModule('web','txcfcleagueM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');

//'', ''

  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_match_edit',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_match_edit.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:edit_games'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_generator',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_generator.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:create_games'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_match_ticker',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_match_ticker.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_profile_create',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_profile_create.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:create_players'
	);
  t3lib_extMgm::insertModuleFunction('web_txcfcleagueM1','tx_cfcleague_profile_search',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_cfcleague_profile_search.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles'
	);


  // add folder icon
  $ICON_TYPES['cfcleague'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif');
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