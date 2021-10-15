<?php

namespace System25\T3sports\Module\Decorator;

use Sys25\RnBase\Backend\Form\ToolBox;

/***************************************************************
*  Copyright notice
*
*  (c) 2007-2021 Rene Nitzsche (rene@system25.de)
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
 * Diese Klasse ist für die Darstellung von Spielen im Backend verantwortlich.
 */
class ProfileDecorator
{
    public $formTool;

    public function __construct(ToolBox $formTool)
    {
        $this->formTool = $formTool;
    }

    public function format($value, $colName, $record = [])
    {
        $ret = $value;
        if ('birthday' == $colName) {
            $ret = intval($value) ? date('d.m.Y', $value) : '-';
        } elseif ('last_name' == $colName) {
            $ret = $record['last_name'].', '.$record['first_name'];
            $ret .= $this->formTool->createEditLink('tx_cfcleague_profiles', $record['uid']);
        }

        return $ret;
    }
}
