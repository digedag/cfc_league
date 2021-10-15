<?php

namespace System25\T3sports\Model;

use Sys25\RnBase\Domain\Model\BaseModel;
use tx_rnbase;

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
 * Model for a team note.
 */
class TeamNote extends BaseModel
{
    protected $profile;

    public function getTableName()
    {
        return 'tx_cfcleague_team_notes';
    }

    /**
     * Returns the value according to media type.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (0 == $this->getProperty('mediatype')) { // Text
            return $this->getProperty('comment');
        } elseif (1 == $this->getProperty('mediatype')) { // DAM-Media
            return $this->getProperty('media');
        } elseif (2 == $this->getProperty('mediatype')) { // Integer
            return $this->getProperty('number');
        }
    }

    /**
     * Returns the NoteType.
     *
     * @return TeamNoteType
     */
    public function getType()
    {
        return TeamNoteType::getInstance($this->getProperty('type'));
    }

    /**
     * Returns the media type.
     *
     * @return int
     */
    public function getMediaType()
    {
        return $this->getProperty('mediatype');
    }

    /**
     * Returns the player.
     *
     * @return Profile
     */
    public function getProfile()
    {
        if (!$this->profile) {
            $this->profile = tx_rnbase::makeInstance(Profile::class, $this->getProperty('player'));
        }

        return $this->profile;
    }
}
