<?php

defined('TYPO3') or exit;

// Folder contains
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    'LLL:EXT:cfc_league/Resources/Private/Language/locallang_db.xlf:tx_cfcleague_folder',
    'cfcleague',
    'ext-cfcleague-ext-default',
];
$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-cfcleague'] = 'ext-cfcleague-folder-default';
