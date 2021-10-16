<?php

namespace System25\T3sports\Service;

use Sys25\RnBase\Cache\CacheManager;
use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use System25\T3sports\Model\Repository\GroupRepository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2021 Rene Nitzsche (rene@system25.de)
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
 * Service for accessing age groups.
 *
 * @author Rene Nitzsche
 */
class GroupService extends AbstractService
{
    private $repo;

    public function __construct(GroupRepository $repo = null)
    {
        $this->repo = $repo ?: new GroupRepository();
    }

    /**
     * Returns a group instance by its uid.
     *
     * @param int $uid
     *
     * @return GroupService
     */
    public function getGroupByUid($uid)
    {
        $cache = CacheManager::getCache('t3sports');
        $group = $cache->get('group_'.$uid);
        if (!$group) {
            $group = $this->repo->findByUid($uid);
            $cache->set('group_'.$uid, $group, 3600);
        }

        return $group;
    }
}
