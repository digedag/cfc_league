<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (rene@system25.de)
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

class tx_cfcleague_util_GeneratorMatch
{
    public $home;

    public $guest;

    public $nr;

    public $nr2;

    public $noMatch;

    public function __construct($n, $n2, $h, $g, $noMatch)
    {
        $this->nr = $n; // ID
        $this->nr2 = $n2; // Spielnummer
        $this->home = $h;
        $this->guest = $g;
        $this->noMatch = $noMatch;
    }

    public function toString()
    {
        return 'SNr '.$this->nr.': '.$this->home.' - '.$this->guest."\n";
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_GeneratorMatch.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_GeneratorMatch.php'];
}
