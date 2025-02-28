<?php

namespace System25\T3sports\Module\Searcher;

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Utility\T3General;
use tx_rnbase_mod_IModFunc;

/***************************************************************
*  Copyright notice
*
*  (c) 2008-2023 Rene Nitzsche (rene@system25.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * TODO: Suche von Personen im BE. Wird wohl noch nicht verwendet...
 */
class ProfileSearcherNew
{
    private $mod;

    private $data;
    private $formTool;
    private $options;
    private $doc;
    private $pid;
    private $resultSize;

    private $SEARCH_SETTINGS;

    public function __construct(&$mod, $options = [])
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
        $this->data = T3General::_GP('searchdata');

        if (!isset($options['nopersist'])) {
            $this->SEARCH_SETTINGS = BackendUtility::getModuleData([
                'searchtermProfile' => '', ],
                $this->data, $mod->getName());
        } else {
            $this->SEARCH_SETTINGS = $this->data;
        }
    }

    /**
     * Liefert das Suchformular.
     *
     * @param string $label Alternatives Label
     *
     * @return string
     */
    public function getSearchForm($label = '')
    {
        $out = '';
        $out .= '###LABEL_SEARCHTERM###: ';
        //        $out .= (strlen($label) ? $label : $LANG->getLL('label_searchterm')).': ';
        $out .= $this->getFormTool()->createTxtInput('data[searchterm]', $this->SEARCH_SETTINGS['searchterm'], 20);
        //$out .= $this->formTool->createTxtInput('searchdata[searchterm]', $this->SEARCH_SETTINGS['searchterm'], 20);
        // Den Update-Button einfügen
        $out .= $this->getFormTool()->createSubmit('searchProfile', $this->formTool->getLanguageService->getLL('btn_search'));
        //        $out .= '<input type="submit" name="search" value="'.$LANG->getLL('btn_search').'" />';
        // Jetzt noch zusätzlichen JavaScriptcode für Buttons auf der Seite
        //        $out .= $this->formTool->getJSCode($this->mod->getPid());

        return $out;
    }

    public function getResultList()
    {
        $content = '';
        $searchterm = $this->SEARCH_SETTINGS['searchterm'];
        if (strlen($searchterm) > 0) {
            $searchterm = trim($this->validateSearchString($searchterm));
            if (strlen($searchterm) > 2) {
                $companies = $this->searchProfiles($searchterm);
                $this->resultSize = count($companies);
                $label = $this->resultSize.((1 == $this->resultSize) ? ' gefundene Person' : ' gefundene Personen');
                $this->showCompanies($content, $label, $companies);
            } else {
                // Suchbefriff zu kurz
                $content .= $this->mod->getDoc()->section('Hinweis:', 'Der Suchbegriff sollte mindestens drei Zeichen lang sein', 0, 1, tx_rnbase_mod_IModFunc::ICON_INFO);
            }
        }

        return $content;
    }

    /**
     * Liefert die Anzahl der gefunden Mitglieder.
     * Funktioniert natürlich erst, nachdem die Ergebnisliste abgerufen wurde.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->resultSize;
    }

    public function searchProfiles($searchterm)
    {
        // TODO
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
