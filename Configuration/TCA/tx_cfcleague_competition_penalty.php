<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tx_cfcleague_competition_penalty = [
    'ctrl' => [
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty',
        'label' => 'comment',
        'label_alt' => 'team,competition',
        'label_alt_force' => 1,
        'searchFields' => 'uid,name,short_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => array(
                'disabled' => 'hidden',
        ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-competition-penalty-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_competition_penalty.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,competition,team,game,comment,points_pos,points_neg,goals_pos,goals_neg,static_position',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, comment, team, game, points_pos, points_neg, goals_pos, goals_neg, static_position',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => \Sys25\RnBase\Backend\Utility\TcaTool::buildGeneralLabel('hidden'),
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'competition' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.competition',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [' ', '0'],
                ],
                'foreign_table' => 'tx_cfcleague_competition',
                'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.sorting ',

                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            'onChange' => 'reload',
        ],
        'team' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.team',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_teams',
                'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getTeams4Competition',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
            ],
        ],
        'game' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.game',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_games',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
            ],
        ],
        'comment' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.comment',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                    ),
                ),
            ),
        ),
        'matches' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.matches',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'wins' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.wins',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'loses' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.loses',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'draws' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.draws',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),

        'goals_pos' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.goals_pos',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'goals_neg' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.goals_neg',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'points_pos' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.points_pos',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'points_neg' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.points_neg',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '-1000',
                ),
                'default' => 0,
            ),
        ),
        'static_position' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.static_position',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'range' => array(
                    'upper' => '100',
                    'lower' => '-1',
                ),
                'default' => 0,
            ),
        ),
        'correction' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.correction',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden, competition, team, game, comment, points_pos, points_neg, goals_pos, goals_neg, matches, wins, draws, loses, static_position, correction'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];

if (\Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher()) {
    unset($tx_cfcleague_competition_penalty['interface']['showRecordFieldList']);
}

if (!\Sys25\RnBase\Utility\TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_competition_penalty['ctrl']['requestUpdate'] = 'competition';
}

$tx_cfcleague_competition_penalty['columns']['comment']['config']['wizards'] = Tx_Rnbase_Utility_TcaTool::getWizards('', ['RTE' => true]);

return $tx_cfcleague_competition_penalty;
