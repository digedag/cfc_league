<?php

namespace System25\T3sports\Model\Repository;

use Sys25\RnBase\Domain\Repository\PersistenceRepository;
use System25\T3sports\Model\Profile;
use System25\T3sports\Model\Team;
use System25\T3sports\Model\TeamNote;
use System25\T3sports\Search\TeamNoteSearch;

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

/**
 * @author Rene Nitzsche
 */
class TeamNoteRepository extends PersistenceRepository
{
    public function getSearchClass()
    {
        return TeamNoteSearch::class;
    }

    /**
     * Find team notes for a profile.
     *
     * @param Profile $profile
     * @param Team $team
     *
     * @return TeamNote[]
     */
    public function getTeamNotes(Profile $profile, Team $team)
    {
        $fields = [
            'TEAMNOTE.PLAYER' => [OP_EQ_INT => $profile->getUid()],
            'TEAMNOTE.TEAM' => [OP_EQ_INT => $team->getUid()],
        ];
        $options = [];

        return $this->search($fields, $options);
    }
}
