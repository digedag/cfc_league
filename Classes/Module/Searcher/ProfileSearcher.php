<?php

namespace System25\T3sports\Module\Searcher;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModFunc;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\T3General;
use System25\T3sports\Module\Decorator\ProfileDecorator;
use System25\T3sports\Utility\ServiceRegistry;
use tx_rnbase;

/*
 * *************************************************************
 * Copyright notice
 *
 * (c) 2008-2021 Rene Nitzsche (rene@system25.de)
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * *************************************************************
 */

/**
 * Search matches from competitions
 * We to it by showing to select boxes: one for competition and the other for round.
 * tx_cfcleague_mod1_profilesearcher.
 */
class ProfileSearcher
{
    private $mod;

    private $data;

    private $SEARCH_SETTINGS;

    public function __construct(IModule $mod, $options = [])
    {
        $this->init($mod, $options);
    }

    /**
     * @param IModule $mod
     * @param array $options
     */
    private function init(IModule $mod, $options)
    {
        $this->options = $options;
        $this->doc = $mod->getDoc();
        $this->pid = $mod->getPid();
        $this->options['pid'] = $mod->getPid();
        $this->formTool = $mod->getFormTool();
        $this->resultSize = 0;
        $this->data = T3General::_GP('data');

        if (!isset($options['nopersist'])) {
            $this->SEARCH_SETTINGS = BackendUtility::getModuleData([
                'searchterm' => '',
            ], $this->data, $mod->getName());
        } else {
            $this->SEARCH_SETTINGS = $this->data;
        }
    }

    /**
     * Liefert das Suchformular.
     * Hier die beiden Selectboxen anzeigen.
     *
     * @param string $label
     *            Alternatives Label
     *
     * @return string
     */
    public function getSearchForm($label = '')
    {
        $out = '';
        $out .= '###LABEL_SEARCHTERM###: ';
        $out .= $this->getFormTool()->createTxtInput('data[searchterm]', $this->SEARCH_SETTINGS['searchterm'], 20);
        // Jetzt noch zusätzlichen JavaScriptcode für Buttons auf der Seite
        // $out .= $this->getFormTool()->getJSCode($this->id);
        // Den Update-Button einfügen
        $out .= $this->getFormTool()->createSubmit('searchProfile', $GLOBALS['LANG']->getLL('btn_search'));

        return $out;
    }

    public function getResultList()
    {
        $content = '';
        $searchTerm = Misc::validateSearchString($this->SEARCH_SETTINGS['searchterm']);
        if (!$searchTerm) {
            return $this->doc->section($GLOBALS['LANG']->getLL('message').':', $GLOBALS['LANG']->getLL('msg_searchhelp'), 0, 1, \tx_rnbase_mod_IModFunc::ICON_INFO);
        }

        $profiles = $this->searchProfiles($searchTerm);
        $content .= $this->showProfiles($GLOBALS['LANG']->getLL('label_searchresults'), $profiles);

        return $content;
    }

    private function searchProfiles($searchterm)
    {
        $joined = $fields = [];
        if (strlen($searchterm)) {
            $joined['value'] = trim($searchterm);
            $joined['cols'] = [
                'PROFILE.LAST_NAME',
                'PROFILE.FIRST_NAME',
                'PROFILE.UID',
            ];
            $joined['operator'] = OP_LIKE;
            $fields[SEARCH_FIELD_JOINED][] = $joined;
        }

        $options = [];
        $options['orderby']['PROFILE.LAST_NAME'] = 'ASC';
        $options['orderby']['PROFILE.FIRST_NAME'] = 'ASC';
        $srv = ServiceRegistry::getProfileService();
        $profiles = $srv->search($fields, $options);
        $this->resultSize = count($profiles);

        return $profiles;
    }

    /**
     * Liefert die Anzahl der gefunden Datensätze.
     * Funktioniert natürlich erst, nachdem die Ergebnisliste abgerufen wurde.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->resultSize;
    }

    private function showProfiles($headline, $profiles)
    {
        $this->options['tablename'] = 'tx_cfcleague_profiles';
        $decor = tx_rnbase::makeInstance(ProfileDecorator::class, $this->formTool);
        $columns = [
            'uid' => [],
            'last_name' => [
                'decorator' => $decor,
                'title' => 'label_name',
            ],
            'birthday' => [
                'decorator' => $decor,
            ],
        ];

        if ($profiles) {
            /* @var $tables Tables */
            $tables = tx_rnbase::makeInstance(Tables::class);
            $arr = $tables->prepareTable($profiles, $columns, $this->formTool, $this->options);
            $out .= $tables->buildTable($arr[0]);
        } else {
            $out = '<p><strong>'.$GLOBALS['LANG']->getLL('msg_no_matches_in_betset').'</strong></p><br/>';
        }

        return $this->doc->section($headline.':', $out, 0, 1, IModFunc::ICON_INFO);
    }

    /**
     * Returns the formTool.
     *
     * @return ToolBox
     */
    private function getFormTool()
    {
        return $this->formTool;
    }
}
