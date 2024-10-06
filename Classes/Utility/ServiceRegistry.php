<?php

namespace System25\T3sports\Utility;

use System25\T3sports\Service\CompetitionService;
use System25\T3sports\Service\GroupService;
use System25\T3sports\Service\MatchService;
use System25\T3sports\Service\ProfileService;
use System25\T3sports\Service\SaisonService;
use System25\T3sports\Service\StadiumService;
use System25\T3sports\Service\TeamService;
use tx_rnbase;

/***************************************************************
*  Copyright notice
*
*  (c) 2009-2024 Rene Nitzsche (rene@system25.de)
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

class ServiceRegistry implements \TYPO3\CMS\Core\SingletonInterface
{
    private $competitionSrv;
    private $groupSrv;
    private $profileSrv;
    private $matchSrv;
    private $saisonSrv;
    private $stadiumSrv;
    private $teamSrv;

    public function __construct(
        ?CompetitionService $competitionService = null,
        ?GroupService $groupService = null,
        ?MatchService $matchService = null,
        ?ProfileService $profileService = null,
        ?SaisonService $saisonService = null,
        ?StadiumService $stadiumService = null,
        ?TeamService $teamService = null
    ) {
        $this->competitionSrv = $competitionService ?: new CompetitionService();
        $this->groupSrv = $groupService ?: new GroupService();
        $this->matchSrv = $matchService ?: new MatchService();
        $this->profileSrv = $profileService ?: new ProfileService();
        $this->saisonSrv = $saisonService ?: new SaisonService();
        $this->stadiumSrv = $stadiumService ?: new StadiumService();
        $this->teamSrv = $teamService ?: new TeamService();
    }

    /**
     * @return self
     */
    private static function getInstance()
    {
        return tx_rnbase::makeInstance(ServiceRegistry::class);
    }

    /**
     * Liefert den Competition-Service.
     *
     * @return CompetitionService
     */
    public static function getCompetitionService()
    {
        return self::getInstance()->competitionSrv;
    }

    /**
     * Liefert den Match/Fixture-Service.
     *
     * @return MatchService
     */
    public static function getMatchService()
    {
        return self::getInstance()->matchSrv;
    }

    /**
     * Liefert den Stadium-Service.
     *
     * @return StadiumService
     */
    public static function getStadiumService()
    {
        return self::getInstance()->stadiumSrv;
    }

    /**
     * Liefert den Saison-Service.
     *
     * @return SaisonService
     */
    public static function getSaisonService()
    {
        return self::getInstance()->saisonSrv;
    }

    /**
     * Liefert den Profile-Service.
     *
     * @return ProfileService
     */
    public static function getProfileService()
    {
        return self::getInstance()->profileSrv;
    }

    /**
     * Return den Team-Service.
     *
     * @return TeamService
     */
    public static function getTeamService()
    {
        return self::getInstance()->teamSrv;
    }

    /**
     * Returns Group-Service.
     *
     * @return GroupService
     */
    public static function getGroupService()
    {
        return self::getInstance()->groupSrv;
    }
}
