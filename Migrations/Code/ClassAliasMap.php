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

    'Tx_Cfcleague_Model_Repository_MatchNote' => \System25\T3sports\Model\Repository\MatchNoteRepository::class,
];
