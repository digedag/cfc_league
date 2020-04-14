<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_competition_penalty = array(
    'ctrl' => array(
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
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,competition,team,game,comment,points_pos,points_neg,goals_pos,goals_neg,static_position',
    ),
    'feInterface' => array(
            'fe_admin_fieldList' => 'hidden, comment, team, game, points_pos, points_neg, goals_pos, goals_neg, static_position',
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/'.$sysLangFile.':LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'competition' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_games.competition',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                        array(' ', '0'),
                ),
                'foreign_table' => 'tx_cfcleague_competition',
                'foreign_table_where' => 'AND tx_cfcleague_competition.pid=###CURRENT_PID### ORDER BY tx_cfcleague_competition.sorting ',

                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
            'onChange' => 'reload',
        ),
        'team' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.team',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_teams',
                'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.uid',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getTeams4Competition',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'game' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.game',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cfcleague_games',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
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
        'correction' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_competition_penalty.correction',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'hidden, competition, team, game, comment, points_pos, points_neg, goals_pos, goals_neg, matches, wins, draws, loses, static_position, correction'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
);

if (!tx_rnbase_util_TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_competition_penalty['ctrl']['requestUpdate'] = 'competition';
}

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
$tx_cfcleague_competition_penalty['columns']['comment']['config']['wizards'] = Tx_Rnbase_Utility_TcaTool::getWizards('', ['RTE' => true]);

return $tx_cfcleague_competition_penalty;
