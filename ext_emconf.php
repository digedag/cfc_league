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
    'version' => '1.11.3',
    'dependencies' => 'rn_base',
    'module' => 'mod1',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => 'uploads/tx_cfcleague/',
    'clearcacheonload' => 1,
    'author' => 'Rene Nitzsche',
    'author_email' => 'rene@system25.de',
    'author_company' => 'System 25',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-12.4.99',
            'php' => '7.1.0-8.9.99',
            'rn_base' => '1.17.1-0.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
