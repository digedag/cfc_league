<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2017 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('Tx_Rnbase_Service_Base');

/**
 * Service for accessing stadiums
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Stadiums extends Tx_Rnbase_Service_Base
{

    /**
     * Search database for trades
     *
     * @param array $fields
     * @param array $options
     * @return array of tx_a4base_models_trade
     */
    public function search($fields, $options)
    {
        tx_rnbase::load('tx_rnbase_util_SearchBase');
        $searcher = tx_rnbase_util_SearchBase::getInstance('tx_cfcleague_search_Stadium');
        return $searcher->search($fields, $options);
    }
}
