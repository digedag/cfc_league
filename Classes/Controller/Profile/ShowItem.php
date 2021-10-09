<?php

namespace System25\T3sports\Controller\Profile;

use Sys25\RnBase\Utility\TYPO3;
use tx_rnbase;
use TYPO3\CMS\Backend\Controller\ContentElement\ElementInformationController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Type\Bitmask\Permission;

/*
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

class ShowItem extends ElementInformationController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Der parent-Konstruktor darf nicht aufgerufen werden.
        $this->iconFactory = tx_rnbase::makeInstance(IconFactory::class);
    }

    public function getInfoScreen($table, $uid)
    {
        $this->initByParams($table, $uid);

        if (TYPO3::isTYPO104OrHigher()) {
            $request = ServerRequestFactory::fromGlobals();
            $normalizedParams = NormalizedParams::createFromRequest($request);
            $request = $request->withAttribute('normalizedParams', $normalizedParams);
            $this->main($request);
        } else {
            $this->main();
        }

        if (TYPO3::isTYPO87OrHigher()) {
            $content = $this->moduleTemplate->getView()->getRenderingContext()->getVariableProvider()->get('content');
        } else {
            $content = $this->content;
        }

        return $content;
    }

    protected function initByParams($table, $uid)
    {
        $this->table = $table;
        $this->uid = $uid;
        $this->permsClause = $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW);
        $this->moduleTemplate = tx_rnbase::makeInstance(ModuleTemplate::class);
        $this->moduleTemplate->getDocHeaderComponent()->disable();
        if (!TYPO3::isTYPO87OrHigher()) {
            $this->doc = tx_rnbase::makeInstance(\TYPO3\CMS\Backend\Template\DocumentTemplate::class);
            $this->doc->divClass = 'container';
        }

        if (isset($GLOBALS['TCA'][$this->table])) {
            $this->initDatabaseRecord();
        } elseif ('_FILE' == $this->table || '_FOLDER' == $this->table || 'sys_file' == $this->table) {
            $this->initFileOrFolderRecord();
        }
    }
}
