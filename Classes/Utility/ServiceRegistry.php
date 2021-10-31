<?php

namespace System25\T3sports\Utility;

use Sys25\RnBase\Utility\Misc;
use System25\T3sports\Service\CompetitionService;
use System25\T3sports\Service\GroupService;
use System25\T3sports\Service\MatchService;
use System25\T3sports\Service\ProfileService;
use System25\T3sports\Service\SaisonService;
use System25\T3sports\Service\StadiumService;
use System25\T3sports\Service\TeamService;

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

class ServiceRegistry
{
    /**
     * Liefert den Competition-Service.
     *
     * @return CompetitionService
     */
    public static function getCompetitionService()
    {
        return Misc::getService('t3sports_srv', 'competition');
    }

    /**
     * Liefert den Match-Service.
     *
     * @return MatchService
     */
    public static function getMatchService()
    {
        return Misc::getService('t3sports_srv', 'match');
    }

    /**
     * Liefert den Stadium-Service.
     *
     * @return StadiumService
     */
    public static function getStadiumService()
    {
        return Misc::getService('t3sports_srv', 'stadiums');
    }

    /**
     * Liefert den Saison-Service.
     *
     * @return SaisonService
     */
    public static function getSaisonService()
    {
        return Misc::getService('t3sports_srv', 'saison');
    }

    /**
     * Liefert den Profile-Service.
     *
     * @return ProfileService
     */
    public static function getProfileService()
    {
        return Misc::getService('t3sports_srv', 'profiles');
    }

    /**
     * Return den Team-Service.
     *
     * @return TeamService
     */
    public static function getTeamService()
    {
        return Misc::getService('t3sports_srv', 'teams');
    }

    /**
     * Returns Group-Service.
     *
     * @return GroupService
     */
    public static function getGroupService()
    {
        return Misc::getService('t3sports_srv', 'group');
    }
}
