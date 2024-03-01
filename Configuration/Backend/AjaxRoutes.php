<?php

/**
 * Definitions for routes provided by EXT:cfc_league.
 */
return [
    't3sports_ticker' => [
        'path' => '/t3sports/ticker',
        'target' => System25\T3sports\Controller\Ajax\AjaxTicker::class.'::dispatch',
    ],
];
