<?php

if (class_exists('tx_cfcleague_util_Misc')) {
    return;
}

\class_alias(\System25\T3sports\Utility\Misc::class, 'tx_cfcleague_util_Misc');
\class_alias(\System25\T3sports\Utility\TcaLookup::class, 'tx_cfcleague_tca_Lookup');

\class_alias(\System25\T3sports\Sports\ISports::class, 'tx_cfcleague_sports_ISports');
\class_alias(\System25\T3sports\Sports\Football::class, 'tx_cfcleague_sports_Football');
\class_alias(\System25\T3sports\Sports\Handball::class, 'tx_cfcleague_sports_Handball');
\class_alias(\System25\T3sports\Sports\IceHockey::class, 'tx_cfcleague_sports_IceHockey');
\class_alias(\System25\T3sports\Sports\Volleyball::class, 'tx_cfcleague_sports_Volleyball');
\class_alias(\System25\T3sports\Sports\MatchInfo::class, 'tx_cfcleague_sports_MatchInfo');

if (false) {
    /** @deprecated */
    class tx_cfcleague_util_Misc
    {
    }
    /** @deprecated */
    class tx_cfcleague_tca_Lookup
    {
    }
    /** @deprecated */
    interface tx_cfcleague_sports_ISports
    {
    }
    /** @deprecated */
    class tx_cfcleague_sports_Football
    {
    }
    /** @deprecated */
    class tx_cfcleague_sports_Handball
    {
    }
    /** @deprecated */
    class tx_cfcleague_sports_IceHockey
    {
    }
    /** @deprecated */
    class tx_cfcleague_sports_Volleyball
    {
    }
    /** @deprecated */
    class tx_cfcleague_sports_MatchInfo
    {
    }
}
