<?php

namespace System25\T3sports\Utility;

/***************************************************************
*  Copyright notice
*
*  (c) 2010-2021 Rene Nitzsche (rene@system25.de)
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
 * Kleine Hilfsmethoden für MatchNotes.
 */
class MatchNotes
{
    public static function isChangeIn($note)
    {
        return self::isType($note, 81);
    }

    public static function isChangeOut($note)
    {
        return self::isType($note, 80);
    }

    public static function isCardYellow($note)
    {
        return self::isType($note, 70);
    }

    public static function isCardYellowRed($note)
    {
        return self::isType($note, 71);
    }

    public static function isCardRed($note)
    {
        return self::isType($note, 72);
    }

    public static function isType($note, $type)
    {
        return $note->getType() == $type;
    }
}
