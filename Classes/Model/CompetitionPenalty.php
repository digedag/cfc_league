<?php

namespace System25\T3sports\Model;

use Sys25\RnBase\Domain\Model\BaseModel;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2021 Rene Nitzsche (rene@system25.de)
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
 * Model für eine Wettbewerbsstrafe.
 */
class CompetitionPenalty extends BaseModel
{
    public function getTableName()
    {
        return 'tx_cfcleague_competition_penalty';
    }

    public function isCorrection()
    {
        return $this->getProperty('correction') > 0;
    }
}
