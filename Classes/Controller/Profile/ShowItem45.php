<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2016 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('Tx_Rnbase_Utility_Strings');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');

/**
 * This class is a modified copy of SC_show_item. It works until 6.2.
 */
class Tx_Cfcleague_Controller_Profile_ShowItem45
{
    // GET vars:
    public $table;			// Record table (or filename)

    public $uid;			// Record uid  (or '' when filename)

    // Internal, static:
    public $perms_clause;	// Page select clause

    public $access;		// If true, access to element is granted

    public $type;			// Which type of element: "file" or "db"

    public $doc;			// Document Template Object

    // Internal, dynamic:
    public $content;		// Content Accumulation

    public $file;			// For type "file": Filename

    public $pageinfo;		// For type "db": Set to page record of the parent page of the item set (if type="db")

    public $row;			// For type "db": The database record row.

    public $shortInfoTable = true; // By default show short info

    /**
     * Initialization of the class
     * Will determine if table/uid GET vars are database record or a file and if the user has access to view information about the item.
     */
    public function init($table = '', $uid = 0)
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA;

        $this->content = '';
        // Setting input variables.
        $this->table = $table ? $table : Tx_Rnbase_Utility_T3General::_GET('table');
        $this->uid = $uid ? $uid : Tx_Rnbase_Utility_T3General::_GET('uid');

        // Initialize:
        $this->perms_clause = $BE_USER->getPagePermsClause(1);
        $this->access = 0;	// Set to true if there is access to the record / file.
        $this->type = '';	// Sets the type, "db" or "file". If blank, nothing can be shown.

            // Checking if the $table value is really a table and if the user has access to it.
        if (isset($TCA[$this->table])) {
            if (!tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
                Tx_Rnbase_Utility_T3General::loadTCA($this->table);
            }
            $this->type = 'db';
            $this->uid = intval($this->uid);

            // Check permissions and uid value:
            if ($this->uid && $BE_USER->check('tables_select', $this->table)) {
                if ('pages' == (string) $this->table) {
                    $this->pageinfo = Tx_Rnbase_Backend_Utility::readPageAccess($this->uid, $this->perms_clause);
                    $this->access = is_array($this->pageinfo) ? 1 : 0;
                    $this->row = $this->pageinfo;
                } else {
                    $this->row = Tx_Rnbase_Backend_Utility::getRecord($this->table, $this->uid);
                    if ($this->row) {
                        $this->pageinfo = Tx_Rnbase_Backend_Utility::readPageAccess($this->row['pid'], $this->perms_clause);
                        $this->access = is_array($this->pageinfo) ? 1 : 0;
                    }
                }

                // FIXME: Implement in 7.6
                $treatData = Tx_Rnbase_Utility_T3General::makeInstance('t3lib_transferData');
                $treatData->renderRecord($this->table, $this->uid, 0, $this->row);
                $cRow = $treatData->theRecord;
            }
        } else {
            // if the filereference $this->file is relative, we correct the path
            if ('../' == substr($this->table, 0, 3)) {
                $this->file = PATH_site.ereg_replace('^\.\./', '', $this->table);
            } else {
                $this->file = $this->table;
            }
            if (@is_file($this->file) && Tx_Rnbase_Utility_T3General::isAllowedAbsPath($this->file)) {
                $this->type = 'file';
                $this->access = 1;
            }
        }

        // Initialize document template object:
        $this->doc = tx_rnbase::makeInstance('smallDoc');
        $this->doc->backPath = $BACK_PATH;
        $this->doc->docType = 'xhtml_trans';

        // Starting the page by creating page header stuff:
        //		$this->content.=$this->doc->startPage($LANG->sL('LLL:EXT:lang/locallang_core.php:show_item.php.viewItem'));
        //		$this->content.=$this->doc->header($LANG->sL('LLL:EXT:lang/locallang_core.php:show_item.php.viewItem'));
        $this->content .= $this->doc->spacer(5);
    }

    /**
     * Main function. Will generate the information to display for the item set internally.
     */
    public function main()
    {
        global $LANG;

        if ($this->access) {
            $returnLinkTag = Tx_Rnbase_Utility_T3General::_GP('returnUrl') ? '<a href="'.Tx_Rnbase_Utility_T3General::_GP('returnUrl').'" class="typo3-goBack">' : '<a href="#" onclick="window.close();">';

            // render type by user func
            $typeRendered = false;
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/show_item.php']['typeRendering'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/show_item.php']['typeRendering'] as $classRef) {
                    $typeRenderObj = Tx_Rnbase_Utility_T3General::getUserObj($classRef);
                    if (is_object($typeRenderObj) && method_exists($typeRenderObj, 'isValid') && method_exists($typeRenderObj, 'render')) {
                        if ($typeRenderObj->isValid($this->type, $this)) {
                            $this->content .= $typeRenderObj->render($this->type, $this);
                            $typeRendered = true;

                            break;
                        }
                    }
                }
            }

            // if type was not rendered use default rendering functions
            if (!$typeRendered) {
                // Branch out based on type:
                switch ($this->type) {
                    case 'db':
                        $this->renderDBInfo();

                    break;
                    case 'file':
                        $this->renderFileInfo($returnLinkTag);

                    break;
                }
            }

            // If return Url is set, output link to go back:
            if (Tx_Rnbase_Utility_T3General::_GP('returnUrl')) {
                $this->content = $this->doc->section('', $returnLinkTag.'<strong>'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack', 1).'</strong></a><br /><br />').$this->content;

                $this->content .= $this->doc->section('', '<br />'.$returnLinkTag.'<strong>'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack', 1).'</strong></a>');
            }
        }

        return $this->content;
    }

    /**
     * Main function. Will generate the information to display for the item set internally.
     */
    public function renderDBInfo()
    {
        global $LANG, $TCA;

        // Print header, path etc:
        $code = $this->doc->getHeader($this->table, $this->row, $this->pageinfo['_thePath'], 1).'<br />';
        $this->content .= $this->doc->section('', $code);

        // Initialize variables:
        $tableRows = array();
        $i = 0;

        // Traverse the list of fields to display for the record:
        $fieldList = Tx_Rnbase_Utility_Strings::trimExplode(',', $TCA[$this->table]['interface']['showRecordFieldList'], 1);
        foreach ($fieldList as $name) {
            $name = trim($name);
            if ($TCA[$this->table]['columns'][$name]) {
                if (!$TCA[$this->table]['columns'][$name]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields', $this->table.':'.$name)) {
                    ++$i;
                    $tableRows[] = '
						<tr>
							<td class="bgColor5">'.$LANG->sL(Tx_Rnbase_Backend_Utility::getItemLabel($this->table, $name), 1).'</td>
							<td class="bgColor4">'.htmlspecialchars(Tx_Rnbase_Backend_Utility::getProcessedValue($this->table, $name, $this->row[$name])).'</td>
						</tr>';
                }
            }
        }

        // Create table from the information:
        $tableCode = '
					<table border="0" cellpadding="1" cellspacing="1" id="typo3-showitem">
						'.implode('', $tableRows).'
					</table>';
        $this->content .= $this->doc->section('', $tableCode);
        $this->content .= $this->doc->divider(2);

        // Add path and table information in the bottom:
        $code = '';
        $code .= $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.Tx_Rnbase_Utility_T3General::fixed_lgd_cs($this->pageinfo['_thePath'], -48).'<br />';
        $code .= $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.table').': '.$LANG->sL($TCA[$this->table]['ctrl']['title']).' ('.$this->table.') - UID: '.$this->uid.'<br />';
        $this->content .= $this->doc->section('', $code);

        // References:
        $this->content .= $this->doc->section('References to this item:', $this->makeRef($this->table, $this->row['uid']));

        // References:
        $this->content .= $this->doc->section('References from this item:', $this->makeRefFrom($this->table, $this->row['uid']));
    }

    /**
     * Main function. Will generate the information to display for the item set internally.
     *
     * @param	string		<a> tag closing/returning
     */
    public function renderFileInfo($returnLinkTag)
    {
        global $LANG;

        // Initialize object to work on the image:
        $imgObj = tx_rnbase::makeInstance('t3lib_stdGraphic');
        $imgObj->init();
        $imgObj->mayScaleUp = 0;
        $imgObj->absPrefix = PATH_site;

        // Read Image Dimensions (returns false if file was not an image type, otherwise dimensions in an array)
        $imgInfo = '';
        $imgInfo = $imgObj->getImageDimensions($this->file);

        // File information
        $fI = Tx_Rnbase_Utility_T3General::split_fileref($this->file);
        $ext = $fI['fileext'];

        $code = '';

        // Setting header:
        $icon = Tx_Rnbase_Backend_Utility::getFileIcon($ext);
        $url = 'gfx/fileicons/'.$icon;
        $fileName = '<img src="'.$url.'" width="18" height="16" align="top" alt="" /><b>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:show_item.php.file', 1).':</b> '.$fI['file'];
        if (Tx_Rnbase_Utility_Strings::isFirstPartOfStr($this->file, PATH_site)) {
            $code .= '<a href="../'.substr($this->file, strlen(PATH_site)).'" target="_blank">'.$fileName.'</a>';
        } else {
            $code .= $fileName;
        }
        $code .= ' &nbsp;&nbsp;<b>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:show_item.php.filesize').':</b> '.Tx_Rnbase_Utility_T3General::formatSize(@filesize($this->file)).'<br />
			';
        if (is_array($imgInfo)) {
            $code .= '<b>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:show_item.php.dimensions').':</b> '.$imgInfo[0].'x'.$imgInfo[1].' pixels';
        }
        $this->content .= $this->doc->section('', $code);
        $this->content .= $this->doc->divider(2);

        // If the file was an image...:
        if (is_array($imgInfo)) {
            $imgInfo = $imgObj->imageMagickConvert($this->file, 'web', '346', '200m', '', '', '', 1);
            $imgInfo[3] = '../'.substr($imgInfo[3], strlen(PATH_site));
            $code = '<br />
				<div align="center">'.$returnLinkTag.$imgObj->imgTag($imgInfo).'</a></div>';
            $this->content .= $this->doc->section('', $code);
        } else {
            $this->content .= $this->doc->spacer(10);
            $lowerFilename = strtolower($this->file);

            // Archive files:
            if (TYPO3_OS != 'WIN' && !$GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']) {
                if ('zip' == $ext) {
                    $code = '';
                    $t = array();
                    exec('unzip -l '.$this->file, $t);
                    if (is_array($t)) {
                        reset($t);
                        next($t);
                        next($t);
                        next($t);
                        while (list(, $val) = each($t)) {
                            $parts = explode(' ', trim($val), 7);
                            $code .= '
								'.$parts[6].'<br />';
                        }
                        $code = '
							<span class="nobr">'.$code.'
							</span>
							<br /><br />';
                    }
                    $this->content .= $this->doc->section('', $code);
                } elseif ('tar' == $ext || 'tgz' == $ext || 'tar.gz' == substr($lowerFilename, -6) || 'tar.z' == substr($lowerFilename, -5)) {
                    $code = '';
                    if ('tar' == $ext) {
                        $compr = '';
                    } else {
                        $compr = 'z';
                    }
                    $t = array();
                    exec('tar t'.$compr.'f '.$this->file, $t);
                    if (is_array($t)) {
                        foreach ($t as $val) {
                            $code .= '
								'.$val.'<br />';
                        }

                        $code .= '
								 -------<br/>
								 '.count($t).' files';

                        $code = '
							<span class="nobr">'.$code.'
							</span>
							<br /><br />';
                    }
                    $this->content .= $this->doc->section('', $code);
                }
            } elseif ($GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']) {
                $this->content .= $this->doc->section('', 'Sorry, TYPO3_CONF_VARS[BE][disable_exec_function] was set, so cannot display content of archive file.');
            }

            // Font files:
            if ('ttf' == $ext) {
                $thumbScript = 'thumbs.php';
                $check = basename($this->file).':'.filemtime($this->file).':'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
                $params = '&file='.rawurlencode($this->file);
                $params .= '&md5sum='.Tx_Rnbase_Utility_T3General::shortMD5($check);
                $url = $thumbScript.'?&dummy='.$GLOBALS['EXEC_TIME'].$params;
                $thumb = '<br />
					<div align="center">'.$returnLinkTag.'<img src="'.htmlspecialchars($url).'" border="0" title="'.htmlspecialchars(trim($this->file)).'" alt="" /></a></div>';
                $this->content .= $this->doc->section('', $thumb);
            }
        }

        // References:
        $this->content .= $this->doc->section('References to this item:', $this->makeRef('_FILE', $this->file));
    }

    /**
     * End page and print content.
     */
    public function printContent()
    {
        $this->content .= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);
        echo $this->content;
    }

    /**
     * Make reference display.
     *
     * @param	string		Table name
     * @param	string		Filename or uid
     *
     * @return	string		HTML
     */
    public function makeRef($table, $ref)
    {
        if ('_FILE' === $table) {
            // First, fit path to match what is stored in the refindex:
            $fullIdent = $ref;

            if (Tx_Rnbase_Utility_Strings::isFirstPartOfStr($fullIdent, PATH_site)) {
                $fullIdent = substr($fullIdent, strlen(PATH_site));
            }

            // Look up the path:
            $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                '*',
                'sys_refindex',
                'ref_table='.$GLOBALS['TYPO3_DB']->fullQuoteStr('_FILE', 'sys_refindex').
                    ' AND ref_string='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fullIdent, 'sys_refindex').
                    ' AND deleted=0'
            );
        } else {
            // Look up the path:
            $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                '*',
                'sys_refindex',
                'ref_table='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table, 'sys_refindex').
                    ' AND ref_uid='.intval($ref).
                    ' AND deleted=0'
            );
        }

        // Compile information for title tag:
        $infoData = array();
        if (count($rows)) {
            $infoData[] = $this->shortInfoTable ? '<tr class="bgColor5 tableheader">'.
                    '<td>Table:</td>'.
                    '<td>Uid:</td>'.
                    '<td>Field:</td>'.
                    '</tr>' : '<tr class="bgColor5 tableheader">'.
                    '<td>Table:</td>'.
                    '<td>Uid:</td>'.
                    '<td>Field:</td>'.
                    '<td>Flexpointer:</td>'.
                    '<td>Softref Key:</td>'.
                    '<td>Sorting:</td>'.
                    '</tr>';
        }
        foreach ($rows as $row) {
            $infoData[] = $this->shortInfoTable ? '<tr class="bgColor4"">'.
                    '<td>'.$row['tablename'].'</td>'.
                    '<td>'.$row['recuid'].'</td>'.
                    '<td>'.$row['field'].'</td>'.
                    '</tr>' : '<tr class="bgColor4"">'.
                    '<td>'.$row['tablename'].'</td>'.
                    '<td>'.$row['recuid'].'</td>'.
                    '<td>'.$row['field'].'</td>'.
                    '<td>'.$row['flexpointer'].'</td>'.
                    '<td>'.$row['softref_key'].'</td>'.
                    '<td>'.$row['sorting'].'</td>'.
                    '</tr>';
        }

        return count($infoData) ? '<table border="0" cellpadding="1" cellspacing="1">'.implode('', $infoData).'</table>' : '';
    }

    /**
     * Make reference display (what this elements points to).
     *
     * @param	string		Table name
     * @param	string		Filename or uid
     *
     * @return	string		HTML
     */
    public function makeRefFrom($table, $ref)
    {
        // Look up the path:
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'sys_refindex',
            'tablename='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table, 'sys_refindex').
                ' AND recuid='.intval($ref)
        );

        // Compile information for title tag:
        $infoData = array();
        if (count($rows)) {
            $infoData[] = '<tr class="bgColor5 tableheader">'.
                    '<td>Field:</td>'.
                    '<td>Flexpointer:</td>'.
                    '<td>Softref Key:</td>'.
                    '<td>Sorting:</td>'.
                    '<td>Ref Table:</td>'.
                    '<td>Ref Uid:</td>'.
                    '<td>Ref String:</td>'.
                    '</tr>';
        }
        foreach ($rows as $row) {
            $infoData[] = '<tr class="bgColor4"">'.
                    '<td>'.$row['field'].'</td>'.
                    '<td>'.$row['flexpointer'].'</td>'.
                    '<td>'.$row['softref_key'].'</td>'.
                    '<td>'.$row['sorting'].'</td>'.
                    '<td>'.$row['ref_table'].'</td>'.
                    '<td>'.$row['ref_uid'].'</td>'.
                    '<td>'.$row['ref_string'].'</td>'.
                    '</tr>';
        }

        return count($infoData) ? '<table border="0" cellpadding="1" cellspacing="1">'.implode('', $infoData).'</table>' : '<strong>-</strong>';
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_showItem.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/class.tx_cfcleague_showItem.php'];
}
