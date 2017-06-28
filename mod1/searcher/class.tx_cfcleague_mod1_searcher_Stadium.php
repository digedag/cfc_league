<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_mod_IModule');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');

/**
 * Search stadiums
 */
class tx_cfcleague_mod1_searcher_Stadium
{

    private $mod;

    private $data;

    private $SEARCH_SETTINGS;

    private $currentClub;

    /**
     * Constructor
     *
     * @param tx_rnbase_mod_IModule $mod
     * @param unknown_type $options
     * @return unknown_type
     */
    public function __construct(tx_rnbase_mod_IModule $mod, $options = array())
    {
        $this->init($mod, $options);
    }

    /**
     * Init object
     *
     * @param tx_rnbase_mod_IModule $mod
     * @param array $options
     */
    private function init(tx_rnbase_mod_IModule $mod, $options)
    {
        $this->options = $options;
        $this->mod = $mod;
        $this->formTool = $mod->getFormTool();
        $this->resultSize = 0;
        $this->data = Tx_Rnbase_Utility_T3General::_GP('searchdata');

        if (! isset($options['nopersist']))
            $this->SEARCH_SETTINGS = Tx_Rnbase_Backend_Utility::getModuleData(array(
                'searchterm' => ''
            ), $this->data, $mod->getName());
        else
            $this->SEARCH_SETTINGS = $this->data;
    }

    /**
     * Returns the complete search form
     */
    public function getSearchForm()
    {
        $out = '';

        return $out;
    }

    /**
     *
     * @return tx_rnbase_mod_IModule
     */
    private function getModule()
    {
        return $this->mod;
    }

    /**
     */
    public function getResultList()
    {
        $pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'stadiumPager', $this->getModule()->getName(), 0);
        // Get stadium service
        $srv = tx_cfcleague_util_ServiceRegistry::getStadiumService();

        // Set options
        $options = array(
            'count' => 1,
            'distinct' => 1
        );

        $fields = array();
        // Filter companies according to filter selections
        if ($this->currentClub) {
            $fields['CLUB.UID'] = array(
                OP_EQ_INT => $this->currentClub
            );
        }

        if (! $this->currentShowHidden) {
            $options['enablefieldsfe'] = 1;
        }
        else {
            $options['enablefieldsbe'] = 1;
        }

        // Set more options
        $options['orderby']['STADIUM.NAME'] = 'ASC';

        // Get counted data
        $cnt = $srv->search($fields, $options);
        unset($options['count']);
        $pager->setListSize($cnt);
        $pager->setOptions($options);

        // Get data
        $items = $srv->search($fields, $options);
        $ret = array();
        $content = '';
        $this->showStadiums($content, $items);
        $ret['table'] = $content;
        $ret['totalsize'] = $cnt;
        $pagerData = $pager->render();
        $ret['pager'] .= '<div class="pager">' . $pagerData['limits'] . ' - ' . $pagerData['pages'] . '</div>';
        return $ret;
    }

    /**
     * Start creation of result list
     *
     * @param string $content
     * @param array $items
     */
    private function showStadiums(&$content, $items)
    {
        $decor = tx_rnbase::makeInstance('tx_cfcleague_mod1_decorator_Stadium', $this->getModule());
        $columns = array(
            'uid' => array(
                'title' => 'label_uid',
                'decorator' => $decor
            ),
            'name' => array(
                'title' => 'label_name'
            ),
            'capacity' => array(
                'title' => 'label_capacity'
            ),
            'address' => array(
                'title' => 'label_address',
                'decorator' => $decor
            ),
            'longlat' => array(
                'title' => 'label_longlat',
                'decorator' => $decor
            )
        );

        if ($items) {
            $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
            $arr = $tables->prepareTable($items, $columns, $this->getModule()->getFormTool(), $this->options);
            $out = $this->getModule()
                ->getDoc()
                ->table($arr[0]);
        } else {
            $out = '<p><strong>###LABEL_MSG_NOSTADIUMSFOUND###</strong></p><br/>';
        }
        $content .= $out;
    }

    /**
     * Method to get the number of data records
     * Works only if the result list has been retrieved
     *
     * @return int
     */
    public function getSize()
    {
        return $this->resultSize;
    }

    /**
     * Set club filter
     *
     * @param int $clubUid
     */
    public function setClub($clubUid)
    {
        $this->currentClub = $clubUid;
    }
}

