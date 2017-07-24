<?php
/* **************************************************************
*  Copyright notice
*
*  (c) 2008-2017 Rene Nitzsche (rene@system25.de)
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

require_once(tx_rnbase_util_Extensions::extPath('cfc_league').'mod1/class.tx_cfcleague_selector.php');
tx_rnbase::load('tx_rnbase_mod_BaseModule');
tx_rnbase::load('Tx_Rnbase_Backend_Utility');


/**
 * Module 'T3sports'
 *
 * @author	René Nitzsche rene@system25.de
 * @package	TYPO3
 */
class  tx_cfcleague_mod1_Module extends tx_rnbase_mod_BaseModule {
    var $pageinfo;
    var $tabs;

    /**
     * Initializes the backend module by setting internal variables, initializing the menu.
     *
     * @return void
     */
    public function init()
    {
        if (!$this->MCONF['name']) {
            $this->MCONF = array_merge(
                (array) $GLOBALS['MCONF'],
                array(
                    'name' => 'web_CfcLeagueM1',
                    'access' => 'user,group',
                    'default' => array(
                        'tabs_images' => array('tab' => 'moduleicon.gif'),
                        'll_ref' => 'LLL:EXT:mksearch/mod1/locallang_mod.xml',
                    ),
                )
                );
        }

        $GLOBALS['LANG']->includeLLFile('EXT:cfc_league/mod1/locallang.xml');
        $GLOBALS['LANG']->includeLLFile('EXT:cfc_league/locallang_db.xml');

        //$GLOBALS['BE_USER']->modAccess($GLOBALS['MCONF'], 1);	// This checks permissions and exits if the users has no permission for entry.
        $GLOBALS['BE_USER']->modAccess($this->MCONF, 1);
        parent::init();
    }
    /**
     * Method to get the extension key
     *
     * @return	string Extension key
     */
    function getExtensionKey() {
        return 'cfc_league';
    }

    protected function getFormTag() {
        $modUrl = Tx_Rnbase_Backend_Utility::getModuleUrl($this->MCONF['name'], array('id'=>$this->getPid()), '');
        return '<form action="' . $modUrl . '" method="POST" name="editform" id="editform">';
    }

    protected function getModuleTemplate() {
        return 'EXT:cfc_league/Resources/Private/Templates/module.html';
    }
}
