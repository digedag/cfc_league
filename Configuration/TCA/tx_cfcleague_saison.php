<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$sysLangFile = tx_rnbase_util_TYPO3::isTYPO87OrHigher() ? 'Resources/Private/Language/locallang_general.xlf' : 'locallang_general.xml';

$tx_cfcleague_saison = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison',
        'label' => 'name',
        'searchFields' => 'uid,name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'typeicon_classes' => [
            'default' => 'ext-cfcleague-saison-default',
        ],
        'iconfile' => 'EXT:cfc_league/Resources/Public/Icons/icon_tx_cfcleague_saison.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,name',
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, name',
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
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ),
        ),
        'halftime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cfc_league/locallang_db.xml:tx_cfcleague_saison.halftime',
            'config' => array(
                'type' => 'input',
                'renderType' => (tx_rnbase_util_TYPO3::isTYPO86OrHigher() ? 'inputDateTime' : ''),
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ),
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'hidden, name, halftime'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
);

return $tx_cfcleague_saison;
