<?php

namespace System25\T3sports\Module\Searcher;

use Sys25\RnBase\Backend\Module\IModule;
use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Backend\Utility\BEPager;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Utility\T3General;
use System25\T3sports\Module\Decorator\StadiumDecorator;
use System25\T3sports\Utility\ServiceRegistry;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2023 Rene Nitzsche (rene@system25.de)
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
 * Search stadiums.
 */
class StadiumSearcher
{
    private $mod;

    private $data;

    private $SEARCH_SETTINGS;

    private $currentClub;
    private $options;
    private $formTool;
    private $resultSize;
    private $currentShowHidden = true;

    /**
     * Constructor.
     *
     * @param IModule $mod
     * @param array $options
     */
    public function __construct(IModule $mod, $options = [])
    {
        $this->init($mod, $options);
    }

    /**
     * Init object.
     *
     * @param IModule $mod
     * @param array $options
     */
    private function init(IModule $mod, $options)
    {
        $this->options = $options;
        $this->mod = $mod;
        $this->formTool = $mod->getFormTool();
        $this->resultSize = 0;
        $this->data = T3General::_GP('searchdata');

        if (!isset($options['nopersist'])) {
            $this->SEARCH_SETTINGS = BackendUtility::getModuleData([
                'searchterm' => '',
            ], $this->data, $mod->getName());
        } else {
            $this->SEARCH_SETTINGS = $this->data;
        }
    }

    /**
     * Returns the complete search form.
     */
    public function getSearchForm()
    {
        $out = '';

        return $out;
    }

    /**
     * @return IModule
     */
    private function getModule()
    {
        return $this->mod;
    }

    public function getResultList()
    {
        /** @var BEPager $pager */
        $pager = tx_rnbase::makeInstance(BEPager::class, 'stadiumPager', $this->getModule()->getName(), 0);
        // Get stadium service
        $srv = ServiceRegistry::getStadiumService();

        // Set options
        $options = [
            'count' => 1,
            'distinct' => 1,
        ];

        $fields = [];
        // Filter companies according to filter selections
        if ($this->currentClub) {
            $fields['CLUB.UID'] = [
                OP_EQ_INT => $this->currentClub,
            ];
        }

        if (!$this->currentShowHidden) {
            $options['enablefieldsfe'] = 1;
        } else {
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
        $ret = [];
        $content = '';
        $this->showStadiums($content, $items);
        $ret['table'] = $content;
        $ret['totalsize'] = $cnt;
        $pagerData = $pager->render();
        $ret['pager'] = sprintf('<div class="pager">%s - %s</div>',
            $pagerData['limits'] ?? '',
            $pagerData['pages'] ?? ''
        );

        return $ret;
    }

    /**
     * Start creation of result list.
     *
     * @param string $content
     * @param array $items
     */
    private function showStadiums(&$content, $items)
    {
        $decor = tx_rnbase::makeInstance(StadiumDecorator::class, $this->getModule());
        $columns = [
            'uid' => [
                'title' => 'label_uid',
                'decorator' => $decor,
            ],
            'name' => [
                'title' => 'label_name',
            ],
            'capacity' => [
                'title' => 'label_capacity',
            ],
            'address' => [
                'title' => 'label_address',
                'decorator' => $decor,
            ],
            'longlat' => [
                'title' => 'label_longlat',
                'decorator' => $decor,
            ],
        ];

        if ($items) {
            /** @var Tables $tables */
            $tables = tx_rnbase::makeInstance(Tables::class);
            $arr = $tables->prepareTable($items, $columns, $this->getModule()->getFormTool(), $this->options);

            $out = $tables->buildTable($arr[0]);
        } else {
            $out = '<p><strong>###LABEL_MSG_NOSTADIUMSFOUND###</strong></p><br/>';
        }
        $content .= $out;
    }

    /**
     * Method to get the number of data records
     * Works only if the result list has been retrieved.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->resultSize;
    }

    /**
     * Set club filter.
     *
     * @param int $clubUid
     */
    public function setClub($clubUid)
    {
        $this->currentClub = $clubUid;
    }
}
