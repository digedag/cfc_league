<?php

namespace System25\T3sports\Service;

use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use System25\T3sports\Model\Repository\StadiumRepository;
use System25\T3sports\Model\Stadium;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2021 Rene Nitzsche (rene@system25.de)
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
 * Service for accessing stadiums.
 *
 * @author Rene Nitzsche
 */
class StadiumService extends AbstractService
{
    private $repo;

    public function __construct(StadiumRepository $repo = null)
    {
        $this->repo = $repo ?: new StadiumRepository();
    }

    /**
     * Search database for stadiums.
     *
     * @param array $fields
     * @param array $options
     *
     * @return Stadium[]
     */
    public function search($fields, $options)
    {
        return $this->repo->search($fields, $options);
    }
}
