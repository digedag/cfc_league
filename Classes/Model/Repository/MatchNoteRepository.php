<?php

namespace System25\T3sports\Model\Repository;

use Sys25\RnBase\Domain\Model\RecordInterface;
use Sys25\RnBase\Search\SearchBase;
use System25\T3sports\Search\MatchNoteSearch;
use tx_rnbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017-2021 Rene Nitzsche (rene@system25.de)
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

// FIXME: Nach Umstellung MatchNote auf Sys25\RnBase\Domain\Model\BaseModel das
// Sys25\RnBase\Domain\Repository\PersistenceRepositoryeinsetzen!!

/**
 * @author Rene Nitzsche
 */
class MatchNoteRepository extends \Sys25\RnBase\Typo3Wrapper\Service\AbstractService
{
    public function getSearchClass()
    {
        return MatchNoteSearch::class;
    }

    /**
     * FIXME: Methode nach Umstellung auf Repo entfernen!
     *
     * @param array $record
     */
    public function createNewModel(array $record = [])
    {
        return $this->getDomainModel()->setProperty($record);
    }

    /**
     * FIXME: Methode nach Umstellung auf Repo entfernen!
     *
     * @param object $model
     * @param array $options
     */
    public function persist($model, $options = null)
    {
        if ($model->isPersisted()) {
            $this->handleUpdate($model, $model->getProperty());
        } else {
            $this->create($model->getProperty());
        }
    }

    private $dummyModel;

    /**
     * FIXME: Methode nach Umstellung auf Repo entfernen!
     *
     * @return RecordInterface
     */
    protected function getDomainModel()
    {
        if (!$this->dummyModel) {
            $searcher = SearchBase::getInstance($this->getSearchClass());
            $this->dummyModel = tx_rnbase::makeInstance($searcher->getWrapperClass(), ['uid' => 0]);
        }

        return $this->dummyModel;
    }
}
