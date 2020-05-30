<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "cfc_league".
 *
 * Auto generated 03-02-2015 22:01
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'T3sports',
    'description' => 'Umfangreiche Extension zur Verwaltung von Sportvereinen und -wettbewerben. Extensive extension to manage sports clubs and competitions. https://github.com/digedag/cfc_league',
    'category' => 'module',
    'shy' => 0,
    'version' => '1.4.2',
    'dependencies' => 'rn_base',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => 'mod1',
    'state' => 'stable',
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
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-10.4.99',
            'php' => '5.6.0-8.9.99',
            'rn_base' => '1.11.4-0.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    '_md5_values_when_last_written' => 'a:137:{s:12:"CHANGELOG.md";s:4:"2a40";s:22:"class.tx_cfcleague.php";s:4:"223e";s:25:"class.tx_cfcleague_db.php";s:4:"a75d";s:32:"class.tx_cfcleague_form_tool.php";s:4:"4e98";s:29:"class.tx_cfcleague_league.php";s:4:"7bd3";s:28:"class.tx_cfcleague_match.php";s:4:"e9ec";s:30:"class.tx_cfcleague_profile.php";s:4:"8383";s:29:"class.tx_cfcleague_saison.php";s:4:"8e24";s:31:"class.tx_cfcleague_showItem.php";s:4:"a454";s:27:"class.tx_cfcleague_team.php";s:4:"bfa1";s:21:"ext_conf_template.txt";s:4:"9850";s:12:"ext_icon.gif";s:4:"0eb4";s:29:"ext_icon_cfcleague_folder.gif";s:4:"9882";s:17:"ext_localconf.php";s:4:"02cb";s:14:"ext_tables.php";s:4:"7d68";s:14:"ext_tables.sql";s:4:"7173";s:14:"icon_table.gif";s:4:"5632";s:27:"icon_tx_cfcleague_clubs.gif";s:4:"5ddc";s:33:"icon_tx_cfcleague_competition.gif";s:4:"bb43";s:41:"icon_tx_cfcleague_competition_penalty.gif";s:4:"bb43";s:27:"icon_tx_cfcleague_games.gif";s:4:"475a";s:27:"icon_tx_cfcleague_group.gif";s:4:"4ad7";s:32:"icon_tx_cfcleague_match_note.gif";s:4:"475a";s:30:"icon_tx_cfcleague_profiles.gif";s:4:"05b2";s:28:"icon_tx_cfcleague_saison.gif";s:4:"6f20";s:27:"icon_tx_cfcleague_teams.gif";s:4:"487c";s:22:"locallang_csh_club.php";s:4:"0921";s:29:"locallang_csh_competition.php";s:4:"0f36";s:37:"locallang_csh_competition_penalty.php";s:4:"4998";s:23:"locallang_csh_games.php";s:4:"a552";s:26:"locallang_csh_profiles.php";s:4:"a615";s:16:"locallang_db.xml";s:4:"c78e";s:9:"README.md";s:4:"71da";s:10:"README.txt";s:4:"4a45";s:7:"tca.php";s:4:"ff3b";s:26:"Configuration/TCA/Club.php";s:4:"c05d";s:33:"Configuration/TCA/Competition.php";s:4:"40cd";s:27:"Configuration/TCA/Group.php";s:4:"184f";s:27:"Configuration/TCA/Match.php";s:4:"e83d";s:29:"Configuration/TCA/Profile.php";s:4:"3a76";s:29:"Configuration/TCA/Stadium.php";s:4:"769f";s:26:"Configuration/TCA/Team.php";s:4:"3a60";s:30:"Configuration/TCA/TeamNote.php";s:4:"fe09";s:40:"Configuration/TypoScript/video/setup.txt";s:4:"1955";s:14:"doc/manual.sxw";s:4:"b3b6";s:19:"doc/wizard_form.dat";s:4:"29b0";s:20:"doc/wizard_form.html";s:4:"972e";s:42:"hooks/class.tx_cfcleague_hooks_cmhooks.php";s:4:"81c8";s:45:"hooks/class.tx_cfcleague_hooks_tceAfterDB.php";s:4:"7e00";s:42:"hooks/class.tx_cfcleague_hooks_tcehook.php";s:4:"7ca6";s:20:"mod1/cfc_league.html";s:4:"9a95";s:37:"mod1/class.tx_cfcleague_generator.php";s:4:"f102";s:38:"mod1/class.tx_cfcleague_match_edit.php";s:4:"fad2";s:40:"mod1/class.tx_cfcleague_match_ticker.php";s:4:"80be";s:43:"mod1/class.tx_cfcleague_mod1_AjaxTicker.php";s:4:"b6a0";s:42:"mod1/class.tx_cfcleague_mod1_decorator.php";s:4:"ef6c";s:41:"mod1/class.tx_cfcleague_mod1_modClubs.php";s:4:"7702";s:56:"mod1/class.tx_cfcleague_mod1_modCompCreateMatchTable.php";s:4:"e80e";s:48:"mod1/class.tx_cfcleague_mod1_modCompetitions.php";s:4:"d3b5";s:45:"mod1/class.tx_cfcleague_mod1_modCompTeams.php";s:4:"acab";s:41:"mod1/class.tx_cfcleague_mod1_modTeams.php";s:4:"3973";s:54:"mod1/class.tx_cfcleague_mod1_modTeamsProfileCreate.php";s:4:"3c90";s:46:"mod1/class.tx_cfcleague_mod1_profileMerger.php";s:4:"77c5";s:48:"mod1/class.tx_cfcleague_mod1_profilesearcher.php";s:4:"b201";s:47:"mod1/class.tx_cfcleague_mod1_subAddProfiles.php";s:4:"f2ba";s:45:"mod1/class.tx_cfcleague_mod1_subTeamNotes.php";s:4:"9991";s:42:"mod1/class.tx_cfcleague_profile_search.php";s:4:"6861";s:36:"mod1/class.tx_cfcleague_selector.php";s:4:"bb52";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"e39a";s:14:"mod1/index.php";s:4:"6b51";s:18:"mod1/locallang.xml";s:4:"123c";s:22:"mod1/locallang_mod.xml";s:4:"247a";s:19:"mod1/moduleicon.gif";s:4:"8074";s:21:"mod1/pageTSconfig.txt";s:4:"4c7f";s:18:"mod1/template.html";s:4:"6936";s:60:"mod1/decorator/class.tx_cfcleague_mod1_decorator_Stadium.php";s:4:"2a65";s:61:"mod1/handler/class.tx_cfcleague_mod1_handler_ClubStadiums.php";s:4:"326a";s:61:"mod1/handler/class.tx_cfcleague_mod1_handler_MatchCreator.php";s:4:"9d96";s:17:"mod1/js/ticker.js";s:4:"7c7a";s:54:"mod1/linker/class.tx_cfcleague_mod1_linker_NewClub.php";s:4:"3584";s:58:"mod1/searcher/class.tx_cfcleague_mod1_searcher_Stadium.php";s:4:"df7e";s:36:"mod1/templates/funccompetitions.html";s:4:"1b09";s:44:"models/class.tx_cfcleague_models_Address.php";s:4:"0901";s:41:"models/class.tx_cfcleague_models_Club.php";s:4:"1723";s:48:"models/class.tx_cfcleague_models_Competition.php";s:4:"c1cc";s:55:"models/class.tx_cfcleague_models_CompetitionPenalty.php";s:4:"56d5";s:53:"models/class.tx_cfcleague_models_CompetitionRound.php";s:4:"05b2";s:42:"models/class.tx_cfcleague_models_Group.php";s:4:"6a6f";s:42:"models/class.tx_cfcleague_models_Match.php";s:4:"1ed1";s:46:"models/class.tx_cfcleague_models_MatchNote.php";s:4:"2235";s:44:"models/class.tx_cfcleague_models_Profile.php";s:4:"c78f";s:40:"models/class.tx_cfcleague_models_Set.php";s:4:"d9a4";s:44:"models/class.tx_cfcleague_models_Stadium.php";s:4:"a209";s:41:"models/class.tx_cfcleague_models_Team.php";s:4:"7956";s:45:"models/class.tx_cfcleague_models_TeamNote.php";s:4:"7127";s:49:"models/class.tx_cfcleague_models_TeamNoteType.php";s:4:"5e03";s:44:"search/class.tx_cfcleague_search_Builder.php";s:4:"c953";s:41:"search/class.tx_cfcleague_search_Club.php";s:4:"5a3a";s:48:"search/class.tx_cfcleague_search_Competition.php";s:4:"242b";s:42:"search/class.tx_cfcleague_search_Match.php";s:4:"ffc2";s:46:"search/class.tx_cfcleague_search_MatchNote.php";s:4:"f82a";s:47:"search/class.tx_cfcleague_search_MatchRound.php";s:4:"154c";s:42:"search/class.tx_cfcleague_search_Media.php";s:4:"f83f";s:44:"search/class.tx_cfcleague_search_Profile.php";s:4:"4576";s:44:"search/class.tx_cfcleague_search_Stadium.php";s:4:"8f82";s:41:"search/class.tx_cfcleague_search_Team.php";s:4:"47bf";s:45:"search/class.tx_cfcleague_search_TeamNote.php";s:4:"d057";s:52:"services/class.tx_cfcleague_services_Competition.php";s:4:"c13a";s:46:"services/class.tx_cfcleague_services_Group.php";s:4:"2531";s:46:"services/class.tx_cfcleague_services_Match.php";s:4:"2a91";s:49:"services/class.tx_cfcleague_services_Profiles.php";s:4:"b1d2";s:53:"services/class.tx_cfcleague_services_ProfileTypes.php";s:4:"a919";s:49:"services/class.tx_cfcleague_services_Stadiums.php";s:4:"c1c0";s:46:"services/class.tx_cfcleague_services_Teams.php";s:4:"e73c";s:26:"services/ext_localconf.php";s:4:"76a2";s:45:"sports/class.tx_cfcleague_sports_Football.php";s:4:"423d";s:46:"sports/class.tx_cfcleague_sports_IceHockey.php";s:4:"c80e";s:44:"sports/class.tx_cfcleague_sports_ISports.php";s:4:"68a2";s:47:"sports/class.tx_cfcleague_sports_Volleyball.php";s:4:"243e";s:24:"sports/ext_localconf.php";s:4:"c0f1";s:37:"tca/class.tx_cfcleague_tca_Lookup.php";s:4:"a2fb";s:53:"tests/class.tx_cfcleague_tests_modelsSet_testcase.php";s:4:"2bc5";s:38:"util/class.tx_cfcleague_util_Cache.php";s:4:"d222";s:36:"util/class.tx_cfcleague_util_DAM.php";s:4:"ca2c";s:42:"util/class.tx_cfcleague_util_Generator.php";s:4:"3b2f";s:47:"util/class.tx_cfcleague_util_GeneratorMatch.php";s:4:"eb80";s:42:"util/class.tx_cfcleague_util_MatchNote.php";s:4:"e84f";s:42:"util/class.tx_cfcleague_util_MatchSets.php";s:4:"dc0d";s:50:"util/class.tx_cfcleague_util_MatchTableBuilder.php";s:4:"8db8";s:40:"util/class.tx_cfcleague_util_Memento.php";s:4:"70c5";s:37:"util/class.tx_cfcleague_util_Misc.php";s:4:"01ee";s:49:"util/class.tx_cfcleague_util_ProfileDecorator.php";s:4:"a0f4";s:48:"util/class.tx_cfcleague_util_ProfileSearcher.php";s:4:"d507";s:48:"util/class.tx_cfcleague_util_ServiceRegistry.php";s:4:"8364";s:41:"util/class.tx_cfcleague_util_TeamInfo.php";s:4:"4ea1";s:50:"util/class.tx_cfcleague_util_TeamNoteDecorator.php";s:4:"bb39";}',
    'suggests' => [],
];
