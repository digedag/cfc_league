<?php

declare(strict_types=1);

return [
    'tx_cfcleague_util_Misc' => \System25\T3sports\Utility\Misc::class,
    'tx_cfcleague_tca_Lookup' => \System25\T3sports\Utility\TcaLookup::class,

    'tx_cfcleague_sports_ISports' => \System25\T3sports\Sports\ISports::class,
    'tx_cfcleague_sports_Football' => \System25\T3sports\Sports\Football::class,
    'tx_cfcleague_sports_Handball' => \System25\T3sports\Sports\Handball::class,
    'tx_cfcleague_sports_IceHockey' => \System25\T3sports\Sports\IceHockey::class,
    'tx_cfcleague_sports_Volleyball' => \System25\T3sports\Sports\Volleyball::class,
    'tx_cfcleague_sports_MatchInfo' => \System25\T3sports\Sports\MatchInfo::class,

    'tx_cfcleague_models_Address' => \System25\T3sports\Model\Address::class,
    'tx_cfcleague_models_Club' => \System25\T3sports\Model\Club::class,
    'tx_cfcleague_models_Competition' => \System25\T3sports\Model\Competition::class,
    'tx_cfcleague_models_CompetitionPenalty' => \System25\T3sports\Model\CompetitionPenalty::class,
    'tx_cfcleague_models_CompetitionRound' => \System25\T3sports\Model\CompetitionRound::class,
    'tx_cfcleague_models_Group' => \System25\T3sports\Model\Group::class,
    'tx_cfcleague_models_Match' => \System25\T3sports\Model\Match::class,
    'tx_cfcleague_models_MatchNote' => \System25\T3sports\Model\MatchNote::class,
    'tx_cfcleague_models_Profile' => \System25\T3sports\Model\Profile::class,
    'tx_cfcleague_models_Saison' => \System25\T3sports\Model\Saison::class,
    'tx_cfcleague_models_Set' => \System25\T3sports\Model\Set::class,
    'tx_cfcleague_models_Stadium' => \System25\T3sports\Model\Stadium::class,
    'tx_cfcleague_models_Team' => \System25\T3sports\Model\Team::class,
    'tx_cfcleague_models_TeamNote' => \System25\T3sports\Model\TeamNote::class,
    'tx_cfcleague_models_TeamNoteType' => \System25\T3sports\Model\TeamNoteType::class,
    'tx_cfcleague_util_ServiceRegistry' => \System25\T3sports\Utility\ServiceRegistry::class,
    'tx_cfcleague_util_Generator' => System25\T3sports\MatchGeneration\Generator::class,
    'tx_cfcleague_util_TeamInfo' => System25\T3sports\Module\Utility\TeamInfo::class,
    'Tx_Cfcleague_Model_Repository_MatchNote' => \System25\T3sports\Model\Repository\MatchNoteRepository::class,
];
