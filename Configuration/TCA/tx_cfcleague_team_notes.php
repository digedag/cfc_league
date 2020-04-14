<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_team_notes = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes',
        'label' => 'uid',
        'label_alt' => 'type,player,team',
        'label_alt_force' => 1,
        'searchFields' => 'uid,comment',
        'tstamp' => 'tstamp',
        'type' => 'mediatype',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-teamnotes-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_teams.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => '',
        'maxDBListItems' => '5',
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, team, player, type, comment',
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
        'team' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_teams',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', ''),
                    ),
                'foreign_table' => 'tx_cfcleague_teams',
                'foreign_table_where' => 'AND tx_cfcleague_teams.pid=###CURRENT_PID### ORDER BY tx_cfcleague_teams.sorting ',
                'eval' => 'required',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
            'onChange' => 'reload',
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_note_types',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_note_types',
                'foreign_table_where' => 'ORDER BY tx_cfcleague_note_types.sorting',
                'eval' => 'required',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'mediatype' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.text', '0'),
                    array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.number', '2'),
                    ),
                'size' => 1,
                'maxitems' => 1,
            ),
        ),
        'player' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_profiles',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cfcleague_profiles',
                'foreign_table_where' => 'AND tx_cfcleague_profiles.uid = 0',
                'itemsProcFunc' => 'tx_cfcleague_tca_Lookup->getPlayers4Team',
                'eval' => 'required',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'comment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_match_notes.comment',
            'config' => array(
                    'type' => 'text',
                    'cols' => '30',
                    'rows' => '5',
            ),
        ),
        'number' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.number',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
            ),
        ),
    ),
    'types' => array(
            '0' => array('showitem' => 'hidden, mediatype, team, player, type, comment'),
            '1' => array('showitem' => 'hidden, mediatype, team, player, type, media'),
            '2' => array('showitem' => 'hidden, mediatype, team, player, type, number'),
    ),
    'palettes' => array(
            '1' => array('showitem' => ''),
    ),
);

if (!tx_rnbase_util_TYPO3::isTYPO86OrHigher()) {
    $tx_cfcleague_team_notes['ctrl']['requestUpdate'] = 'team';
}

tx_rnbase::load('tx_rnbase_util_TSFAL');
$tx_cfcleague_team_notes['columns']['mediatype']['config']['items'][] =
                array('LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.mediatype.media', '1');
$tx_cfcleague_team_notes['columns']['media'] = tx_rnbase_util_TSFAL::getMediaTCA('media', array(
    'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.images',
    'config' => array('size' => 1, 'maxitems' => 1),
));
$tx_cfcleague_team_notes['columns']['media']['label'] = 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_team_notes.media';

return $tx_cfcleague_team_notes;
