<?php

########################################################################
# Extension Manager/Repository config file for ext "cfc_league".
#
# Auto generated 04-07-2010 17:24
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'T3sports',
	'description' => 'Umfangreiche Extension zur Verwaltung von Sportvereinen und -wettbewerben. Funktioniert nur mit PHP5! Extensive extension to manage sports clubs and competitions. Requires PHP5! http://cfcleague.sf.net/',
	'category' => 'module',
	'shy' => 0,
	'version' => '0.7.4',
	'dependencies' => 'cms,rn_memento,rn_base,div,lib',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_cfcleague/',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Rene Nitzsche',
	'author_email' => 'rene@system25.de',
	'author_company' => 'System 25',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.0.0-0.0.0',
			'cms' => '',
			'rn_memento' => '',
			'rn_base' => '0.9.0-0.0.0',
			'div' => '0.1.0-0.0.0',
			'lib' => '0.1.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'dam' => '1.0.11-0.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:104:{s:9:"ChangeLog";s:4:"1d6d";s:10:"README.txt";s:4:"4a45";s:22:"class.tx_cfcleague.php";s:4:"ff7c";s:25:"class.tx_cfcleague_db.php";s:4:"f6c7";s:32:"class.tx_cfcleague_form_tool.php";s:4:"70f6";s:29:"class.tx_cfcleague_league.php";s:4:"e78d";s:28:"class.tx_cfcleague_match.php";s:4:"cabf";s:30:"class.tx_cfcleague_profile.php";s:4:"8d6a";s:29:"class.tx_cfcleague_saison.php";s:4:"ecc4";s:31:"class.tx_cfcleague_showItem.php";s:4:"0456";s:27:"class.tx_cfcleague_team.php";s:4:"eb53";s:21:"ext_conf_template.txt";s:4:"5407";s:12:"ext_icon.gif";s:4:"0eb4";s:29:"ext_icon_cfcleague_folder.gif";s:4:"9882";s:17:"ext_localconf.php";s:4:"33d1";s:14:"ext_tables.php";s:4:"7558";s:14:"ext_tables.sql";s:4:"f95b";s:14:"icon_table.gif";s:4:"5632";s:27:"icon_tx_cfcleague_clubs.gif";s:4:"5ddc";s:33:"icon_tx_cfcleague_competition.gif";s:4:"bb43";s:41:"icon_tx_cfcleague_competition_penalty.gif";s:4:"bb43";s:27:"icon_tx_cfcleague_games.gif";s:4:"475a";s:27:"icon_tx_cfcleague_group.gif";s:4:"4ad7";s:32:"icon_tx_cfcleague_match_note.gif";s:4:"475a";s:30:"icon_tx_cfcleague_profiles.gif";s:4:"05b2";s:28:"icon_tx_cfcleague_saison.gif";s:4:"6f20";s:27:"icon_tx_cfcleague_teams.gif";s:4:"487c";s:22:"locallang_csh_club.php";s:4:"0921";s:29:"locallang_csh_competition.php";s:4:"ac49";s:37:"locallang_csh_competition_penalty.php";s:4:"4998";s:23:"locallang_csh_games.php";s:4:"8e14";s:26:"locallang_csh_profiles.php";s:4:"a615";s:16:"locallang_db.xml";s:4:"fc63";s:7:"tca.php";s:4:"5fad";s:14:"doc/manual.sxw";s:4:"43e3";s:19:"doc/wizard_form.dat";s:4:"29b0";s:20:"doc/wizard_form.html";s:4:"972e";s:42:"hooks/class.tx_cfcleague_hooks_cmhooks.php";s:4:"ff60";s:45:"hooks/class.tx_cfcleague_hooks_tceAfterDB.php";s:4:"6759";s:42:"hooks/class.tx_cfcleague_hooks_tcehook.php";s:4:"7fdf";s:20:"mod1/cfc_league.html";s:4:"9a95";s:37:"mod1/class.tx_cfcleague_generator.php";s:4:"57af";s:38:"mod1/class.tx_cfcleague_match_edit.php";s:4:"7de2";s:40:"mod1/class.tx_cfcleague_match_ticker.php";s:4:"d03e";s:43:"mod1/class.tx_cfcleague_mod1_AjaxTicker.php";s:4:"20ee";s:42:"mod1/class.tx_cfcleague_mod1_decorator.php";s:4:"ad53";s:56:"mod1/class.tx_cfcleague_mod1_modCompCreateMatchTable.php";s:4:"a9d1";s:45:"mod1/class.tx_cfcleague_mod1_modCompTeams.php";s:4:"c868";s:48:"mod1/class.tx_cfcleague_mod1_modCompetitions.php";s:4:"f30f";s:41:"mod1/class.tx_cfcleague_mod1_modTeams.php";s:4:"b92e";s:54:"mod1/class.tx_cfcleague_mod1_modTeamsProfileCreate.php";s:4:"c492";s:46:"mod1/class.tx_cfcleague_mod1_profileMerger.php";s:4:"8823";s:48:"mod1/class.tx_cfcleague_mod1_profilesearcher.php";s:4:"b3d4";s:47:"mod1/class.tx_cfcleague_mod1_subAddProfiles.php";s:4:"4576";s:45:"mod1/class.tx_cfcleague_mod1_subTeamNotes.php";s:4:"3b55";s:42:"mod1/class.tx_cfcleague_profile_search.php";s:4:"012f";s:36:"mod1/class.tx_cfcleague_selector.php";s:4:"e927";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"e39a";s:14:"mod1/index.php";s:4:"c48d";s:18:"mod1/locallang.xml";s:4:"7581";s:22:"mod1/locallang_mod.xml";s:4:"247a";s:19:"mod1/moduleicon.gif";s:4:"8074";s:17:"mod1/js/ticker.js";s:4:"7c7a";s:44:"models/class.tx_cfcleague_models_Address.php";s:4:"0901";s:41:"models/class.tx_cfcleague_models_Club.php";s:4:"a70f";s:48:"models/class.tx_cfcleague_models_Competition.php";s:4:"41fa";s:53:"models/class.tx_cfcleague_models_CompetitionRound.php";s:4:"05b2";s:42:"models/class.tx_cfcleague_models_Group.php";s:4:"1470";s:44:"models/class.tx_cfcleague_models_Profile.php";s:4:"5680";s:44:"models/class.tx_cfcleague_models_Stadium.php";s:4:"a209";s:41:"models/class.tx_cfcleague_models_Team.php";s:4:"1de2";s:45:"models/class.tx_cfcleague_models_TeamNote.php";s:4:"7127";s:49:"models/class.tx_cfcleague_models_TeamNoteType.php";s:4:"d689";s:44:"search/class.tx_cfcleague_search_Builder.php";s:4:"e05e";s:41:"search/class.tx_cfcleague_search_Club.php";s:4:"06b3";s:48:"search/class.tx_cfcleague_search_Competition.php";s:4:"ce6e";s:42:"search/class.tx_cfcleague_search_Match.php";s:4:"3da0";s:47:"search/class.tx_cfcleague_search_MatchRound.php";s:4:"4df4";s:42:"search/class.tx_cfcleague_search_Media.php";s:4:"1b1e";s:44:"search/class.tx_cfcleague_search_Profile.php";s:4:"a101";s:44:"search/class.tx_cfcleague_search_Stadium.php";s:4:"6ba6";s:41:"search/class.tx_cfcleague_search_Team.php";s:4:"a90a";s:45:"search/class.tx_cfcleague_search_TeamNote.php";s:4:"9504";s:52:"services/class.tx_cfcleague_services_Competition.php";s:4:"86db";s:46:"services/class.tx_cfcleague_services_Group.php";s:4:"2907";s:46:"services/class.tx_cfcleague_services_Match.php";s:4:"ffa8";s:53:"services/class.tx_cfcleague_services_ProfileTypes.php";s:4:"8e89";s:49:"services/class.tx_cfcleague_services_Profiles.php";s:4:"40ff";s:49:"services/class.tx_cfcleague_services_Stadiums.php";s:4:"a185";s:46:"services/class.tx_cfcleague_services_Teams.php";s:4:"3196";s:26:"services/ext_localconf.php";s:4:"76a2";s:37:"tca/class.tx_cfcleague_tca_Lookup.php";s:4:"f029";s:38:"util/class.tx_cfcleague_util_Cache.php";s:4:"9e1f";s:36:"util/class.tx_cfcleague_util_DAM.php";s:4:"ca2c";s:42:"util/class.tx_cfcleague_util_Generator.php";s:4:"e3cd";s:47:"util/class.tx_cfcleague_util_GeneratorMatch.php";s:4:"d95c";s:40:"util/class.tx_cfcleague_util_Memento.php";s:4:"70c5";s:37:"util/class.tx_cfcleague_util_Misc.php";s:4:"7149";s:49:"util/class.tx_cfcleague_util_ProfileDecorator.php";s:4:"a0f4";s:48:"util/class.tx_cfcleague_util_ProfileSearcher.php";s:4:"6c72";s:48:"util/class.tx_cfcleague_util_ServiceRegistry.php";s:4:"8364";s:41:"util/class.tx_cfcleague_util_TeamInfo.php";s:4:"30f0";s:50:"util/class.tx_cfcleague_util_TeamNoteDecorator.php";s:4:"592d";}',
	'suggests' => array(
	),
);

?>