<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (TYPO3_MODE == 'BE') {
       include_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'class.tx_cfcleague.php');
}
tx_rnbase::load('tx_cfcleague_tca_Lookup');

if(!tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
	// TCA registration for 4.5
	$TCA['tx_cfcleague_group'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_group.php';
	$TCA['tx_cfcleague_saison'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_saison.php';
	$TCA['tx_cfcleague_competition'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_competition.php';
	$TCA['tx_cfcleague_competition_penalty'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_competition_penalty.php';
	$TCA['tx_cfcleague_profiles'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_profiles.php';
	$TCA['tx_cfcleague_note_types'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_note_types.php';
	$TCA['tx_cfcleague_club'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_club.php';
	$TCA['tx_cfcleague_teams'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_teams.php';
	$TCA['tx_cfcleague_team_notes'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_team_notes.php';
	$TCA['tx_cfcleague_games'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_games.php';
	$TCA['tx_cfcleague_match_notes'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_match_notes.php';
	$TCA['tx_cfcleague_stadiums'] = require tx_rnbase_util_Extensions::extPath($_EXTKEY).'Configuration/TCA/tx_cfcleague_stadiums.php';
}


tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_competition', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_competition.php');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_profiles', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_profiles.php');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_club', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_club.php');
tx_rnbase_util_Extensions::addLLrefForTCAdescr('tx_cfcleague_games', 'EXT:cfc_league/Resources/Private/Language/locallang_csh_games.php');


if(tx_rnbase_util_Extensions::isLoaded('rgmediaimages')) {
	tx_rnbase_util_Extensions::addStaticFile($_EXTKEY, 'Configurations/TypoScript/video', 'Ext: cfcleague video support');
}

if (TYPO3_MODE=='BE')	{

  tx_rnbase_util_Extensions::addModule(
  	'web',
  	'txcfcleagueM1',
  	'',
  	tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod1/',
  	array(
  		'labels' => array(
	  		'tabs_images' => array(
	  				'tab' => 'EXT:cfc_league/Resources/Public/Icons/module-t3sports.svg',
	  		),
  		),
  	)
  );

  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'Tx_Cfcleague_Controller_Competition',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'Classes/Controller/Competition.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_competition'
	);
  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'Tx_Cfcleague_Controller_MatchTicker',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'Classes/Controller/MatchTicker.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:match_ticker'
	);
  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'Tx_Cfcleague_Controller_Team',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'Classes/Controller/Team.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_team'
	);
  tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'Tx_Cfcleague_Controller_Club',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'Classes/Controller/Club.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:mod_club'
	);
	tx_rnbase_util_Extensions::insertModuleFunction('web_txcfcleagueM1', 'Tx_Cfcleague_Controller_Profile',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'Classes/Controller/Profile.php',
		'LLL:EXT:cfc_league/mod1/locallang.xml:search_profiles'
	);


	tx_rnbase::load('tx_rnbase_util_TYPO3');
	// add folder icon
	if(tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $icons = [
            'ext-cfcleague-clubs-default' => 'icon_tx_cfcleague_clubs.gif',
            'ext-cfcleague-competition-default' => 'icon_tx_cfcleague_competition.gif',
            'ext-cfcleague-competition-penalty-default' => 'icon_tx_cfcleague_competition_penalty.gif',
            'ext-cfcleague-games-default' => 'icon_table.gif',
            'ext-cfcleague-group-default' => 'icon_tx_cfcleague_group.gif',
            'ext-cfcleague-matchnotes-default' => 'icon_table.gif',
            'ext-cfcleague-notetypes-default' => 'icon_table.gif',
            'ext-cfcleague-profiles-default' => 'icon_tx_cfcleague_profiles.gif',
            'ext-cfcleague-saison-default' => 'icon_tx_cfcleague_saison.gif',
            'ext-cfcleague-statiums-default' => 'icon_table.gif',
            'ext-cfcleague-teamnotes-default' => 'icon_tx_cfcleague_teams.gif',
            'ext-cfcleague-teams-default' => 'icon_tx_cfcleague_teams.gif',
         ];
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
                ['source' => 'EXT:cfc_league/Resources/Public/Icons/' . $path]
            );
        }
	}
	elseif(tx_rnbase_util_TYPO3::isTYPO3VersionOrHigher(4004000)) {
		t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-cfcleague', '../typo3conf/ext/cfc_league/ext_icon_cfcleague_folder.gif');
	}
	else {
		$ICON_TYPES['cfcleague'] = array('icon' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif');
	}

  $TCA['pages']['columns']['module']['config']['items'][] = array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_folder', 'cfcleague');
/*
  $PAGES_TYPES['cfcleague'] = Array(
      'type' => 'sys',
      'icon' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'ext_icon_cfcleague_folder.gif',
      'allowedTables' => '*'
    );
*/
}
