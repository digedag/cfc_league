<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_util_SearchBase');

// FIXME: Nach Umstellung MatchNote auf Tx_Rnbase_Domain_Model_Base das Repo einsetzen!!
tx_rnbase::load('tx_rnbase_sv1_Base');
//tx_rnbase::load('Tx_Rnbase_Domain_Repository_PersistenceRepository');



/**
 * @author Rene Nitzsche
 */
class Tx_Cfcleague_Model_Repository_MatchNote extends tx_rnbase_sv1_Base {

	public function getSearchClass() {
		return 'tx_cfcleague_search_MatchNote';
	}

	/**
	 * FIXME: Methode nach Umstellung auf Repo entfernen!
	 * @param array $record
	 */
	public function createNewModel(
			array $record = array()
	) {
		return $this->getDomainModel()->setProperty($record);
	}

	/**
	 * FIXME: Methode nach Umstellung auf Repo entfernen!
	 * @param object $model
	 * @param array $options
	 */
	public function persist($model, $options = null) {
		if($model->isPersisted()) {
			$this->handleUpdate($model, $model->getProperty());
		}
		else {
			$this->create($model->getProperty());
		}
	}
	private $dummyModel;
	/**
	 * FIXME: Methode nach Umstellung auf Repo entfernen!
	 * @return Tx_Rnbase_Domain_Model_RecordInterface
	 */
	protected function getDomainModel() {
		if (!$this->dummyModel) {
			$searcher = tx_rnbase_util_SearchBase::getInstance($this->getSearchClass());
			$this->dummyModel = tx_rnbase::makeInstance($searcher->getWrapperClass(), array('uid' => 0));
		}
		return $this->dummyModel;
	}
}

