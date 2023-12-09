<?php

namespace System25\T3sports\Module\Decorator;

use Sys25\RnBase\Backend\Module\IModule;
use System25\T3sports\Model\Stadium;

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

/**
 * Diese Klasse ist für die Darstellung von Stadien im Backend verantwortlich.
 */
class StadiumDecorator
{
    private $mod;

    public function __construct(IModule $mod)
    {
        $this->mod = $mod;
    }

    /**
     * Returns the module.
     *
     * @return IModule
     */
    private function getModule()
    {
        return $this->mod;
    }

    /**
     * @param string $value
     * @param string $colName
     * @param array $record
     * @param Stadium $item
     */
    public function format($value, $colName, $record, $item)
    {
        $ret = $value;
        $formTool = $this->getModule()->getFormTool();
        if ('uid' == $colName) {
            $ret = $formTool->createEditLink('tx_cfcleague_stadiums', $item->getUid(), 'Edit '.$item->getUid());
        } elseif ('address' == $colName) {
            $ret = self::getAddress($item);
        } elseif ('longlat' == $colName) {
            if ($item->getCoords()) {
                $ret = $item->getLongitute().'/'.$item->getLatitute();
            }
        }

        return $ret;
    }

    private static function getAddress(Stadium $item)
    {
        $ret = '';
        if ($item->getStreet()) {
            $ret .= $item->getStreet().'<br />';
        }
        if ($item->getZip()) {
            $ret .= $item->getZip().' ';
        }
        if ($item->getCity()) {
            $ret .= $item->getCity().'<br/>';
        }
        if ($item->getCountryCode()) {
            $ret .= $item->getCountryCode();
        }

        return $ret;
    }
}
