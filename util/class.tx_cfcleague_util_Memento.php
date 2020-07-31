<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (rene@system25.de)
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

require_once tx_rnbase_util_Extensions::extPath('rn_memento').'sv1/class.tx_rnmemento_sv1.php';
require_once tx_rnbase_util_Extensions::extPath('rn_base').'util/class.tx_rnbase_util_Spyc.php';

/**
 * Basisklasse für die Verwendung des internen Caches.
 * Den Cache verwenden wir für die Sammlung von statistischen Daten über
 * einen Wettbewerb. Diese Statistiken können sehr unterschiedlich sein und
 * und entsprechend über mehrere Mementos verfügen. Gemeinsam ist ihnen aber der
 * Wettbewerb. Daher wird auch der Wettbewerb als SuperKey verwendet. Im BE
 * wird es so möglich, bei Änderungen an einem Wettbewerb automatisch die Mementos
 * zu löschen.
 */
abstract class tx_cfcleaguefe_util_Memento implements IMemento
{
    private $data; // data object

    private $yamlStr; // serialized data

    /**
     * Child classes implement this method to convert data object to php array.
     */
    abstract public function data2Array($data);

    /**
     * Child classes implement this method to convert php array to data object.
     */
    abstract public function array2Data($arr);

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        $yamlArr = tx_rnbase_util_Spyc::YAMLLoad($this->yamlStr);

        return $this->array2Data($yamlArr);
    }

    public function setString($str)
    {
        $this->yamlStr = $str;
    }

    public function getString()
    {
        $arr = $this->data2Array($this->data);

        return tx_rnbase_util_Spyc::YAMLDump($arr);
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Memento.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_Memento.php'];
}
