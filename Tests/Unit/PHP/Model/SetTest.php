<?php

namespace System25\T3sports\Tests\Model;

use Sys25\RnBase\Tests\BaseTestCase;
use System25\T3sports\Model\Set;

/***************************************************************
*  Copyright notice
*
*  (c) 2012-2021 Rene Nitzsche (rene@system25.de)
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

class SetTest extends BaseTestCase
{
    /**
     * @group unit
     */
    public function testBuildFromStringEmpty()
    {
        $sets = Set::buildFromString('');
        $this->assertFalse($sets);
    }

    /**
     * @dataProvider getStrings
     * @group unit
     */
    public function testBuildFromString($setString, $setCnt, $setResult)
    {
        $sets = Set::buildFromString($setString);

        $this->assertEquals($setCnt, count($sets));
        $i = 0;
        foreach ($sets as $set) {
            $expResult = $setResult[$i];
            $this->assertEquals($i + 1, $set->getSet());
            $this->assertEquals($expResult[0], $set->getPointsHome());
            $this->assertEquals($expResult[1], $set->getPointsGuest());
            $this->assertEquals($expResult[0], $set->getProperty('pointshome'));
            $this->assertEquals($expResult[1], $set->getProperty('pointsguest'));
            ++$i;
        }
    }

    public function getStrings()
    {
        return [
            ['20:11;10:8', 2, [[20, 11], [10, 8]]],
            ['20:11', 1, [[20, 11]]],
            ['b:11', 1, [[0, 11]]],
        ];
    }
}
