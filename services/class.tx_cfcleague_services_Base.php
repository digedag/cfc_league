<?php

use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;

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
 * Base service.
 * Can be removed after refactoring to repository structure.
 *
 * @author Rene Nitzsche
 */
class tx_cfcleague_services_Base extends AbstractService
{
    /**
     * Create or update model.
     *
     * @param Tx_Rnbase_Domain_Model_RecordInterface $model
     */
    public function persist($model)
    {
        if ($model->isPersisted()) {
            $this->update($model);
        } else {
            $this->create($model);
        }
    }

    /**
     * @param Tx_Rnbase_Domain_Model_RecordInterface $model
     *
     * @return Tx_Rnbase_Domain_Model_RecordInterface
     */
    protected function update($model)
    {
        $model->setProperty('tstamp', time());
        $data = $model->getProperty();
        $table = $model->getTableName();
        $uid = (int) $model->getUid();

        $where = '1=1 AND `'.$table.'`.`uid`='.$uid;

        // remove uid if exists
        if (array_key_exists('uid', $data)) {
            unset($data['uid']);
        }

        Tx_Rnbase_Database_Connection::getInstance()->doUpdate($table, $where, $data);

        return $model;
    }

    /**
     * Create a new record
     * TODO: remove after migration to repository.
     *
     * @param Tx_Rnbase_Domain_Model_RecordInterface $model
     * @param string $table
     *
     * @return int UID of just created record
     */
    protected function create($model)
    {
        $model->setProperty('crdate', time());
        $model->setProperty('tstamp', time());
        $newUid = Tx_Rnbase_Database_Connection::getInstance()->doInsert($model->getTableName(), $model->getProperty());
        $model->uid = $newUid;
        $model->setUid($newUid);

        return $newUid;
    }
}
