<?php

namespace System25\T3sports\Sports;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2020 Rene Nitzsche (rene@system25.de)
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
 * Additional information about matches.
 */
class MatchInfo
{
    public const MATCH_TIME = 'MATCH_TIME';

    public const MATCH_EXTRA_TIME = 'MATCH_EXTRA_TIME';

    public const MATCH_PARTS = 'MATCH_PARTS';

    private $info = [];

    public function __construct($info)
    {
        $this->info = $info;
    }

    /**
     * @param string $key
     */
    public function getInfo($key)
    {
        return isset($this->info[$key]) ? $this->info[$key] : null;
    }
}
