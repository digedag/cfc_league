<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2020 Rene Nitzsche (rene@system25.de)
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
 * Die Klasse stellt den MatchTicker bereit.
 */
class Tx_Cfcleague_Controller_MatchTicker extends tx_rnbase_mod_BaseModFunc
{
    const TABLE_NOTES = 'tx_cfcleague_match_notes';

    public $doc;

    public $MCONF;

    public $playerNames = [];

    /**
     * Method getFuncId.
     *
     * @return string
     */
    public function getFuncId()
    {
        return 'functicker';
    }

    /**
     * Bearbeitung von Spielen.
     * Es werden die Paaren je Spieltag angezeigt.
     */
    protected function getContent($template, &$configurations, &$formatter, $formTool)
    {
        global $LANG;

        $this->doc = $this->getModule()->getDoc();

        // Selector-Instanz bereitstellen
        $this->selector = tx_rnbase::makeInstance('tx_cfcleague_selector');
        $this->selector->init($this->getModule()
            ->getDoc(), $this->getModule());

        // Zuerst mal müssen wir die passende Liga auswählen lassen:
        $content = '';

        $selector = '';
        // Anzeige der vorhandenen Ligen
        /* @var $current_league tx_cfcleague_models_Competition */
        $current_league = $this->getSelector()->showLeagueSelector($selector, $this->getModule()
            ->getPid());
        if (!$current_league) {
            return $this->doc->section('Info:', $LANG->getLL('no_league_in_page'), 0, 1, self::ICON_WARN);
        }

        if (!count($current_league->getRounds())) {
            $this->setSelectorMenu($selector);
            $content .= $LANG->getLL('no_round_in_league');

            return $content;
        }
        // Jetzt den Spieltag wählen lassen
        $current_round = $this->getSelector()->showRoundSelector($selector, $this->getModule()
            ->getPid(), $current_league);

        // Und nun das Spiel wählen
        $matchData = tx_cfcleague_util_ServiceRegistry::getMatchService()->searchMatchesByRound($current_league, $current_round, true);
        $match = $this->getSelector()->showMatchSelector($selector, $this->getModule()
            ->getPid(), $matchData);
        $this->setSelectorMenu($selector);

        $modContent = '<div id="editform">';
        $update = Tx_Rnbase_Utility_T3General::_GP('update');
        $data = Tx_Rnbase_Utility_T3General::_GP('data');
        // Haben wir Daten im Request?
        if ($update && is_array($data['tx_cfcleague_match_notes'])) {
            $this->insertNotes($data);
            $modContent .= '<i>'.$LANG->getLL('msg_data_saved').'</i>';
            // Jetzt das Spiel nochmal laden, da sich Daten geändert haben könnten
            $match->reset();
        }

        // Wir zeigen die bisherigen Meldungen
        // Dann zeigen wir die FORM für die nächste Meldung
        $modContent .= $this->getInstantMessageField();
        $modContent .= $this->getFormHeadline();
        $arr = $this->createFormArray($match);

        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');
        $modContent .= $tables->buildTable($arr, $this->_getTableLayoutForm());
        $modContent .= '<br />';

        // Das Form für den aktuellen Spielstand
        $modContent .= $this->createStandingForm($match, $current_league);
        $modContent .= '<br />';
        // Den Update-Button einfügen
        $modContent .= $this->getModule()
            ->getFormTool()
            ->createSubmit('update', $LANG->getLL('btn_save'));
        // Jetzt listen wir noch die zum Spiel vorhandenen Tickermeldungen auf
        $modContent .= $this->doc->divider(5);
        $arr = $this->createTickerArray($match, Tx_Rnbase_Utility_T3General::_GP('showAll'));
        if ($arr) {
            $tickerContent = $formTool->createModuleLink(
                ['showAll' => '1'],
                $this->getModule()->getPid(),
                $LANG->getLL('label_showAllTickers')
            );
            $tableLayout = $this->_getTableLayoutForm();
            $tableLayout['defRowEven']['defCol'] = $tableLayout['defRowOdd']['defCol']; // Array('<td valign="top" style="padding:5px 5px;">', '</td>');
            $tickerContent .= $tables->buildTable($arr, $tableLayout);
        } else {
            $tickerContent .= $LANG->getLL('msg_NoTicker');
        }

        // Den JS-Code für Validierung einbinden
        $content .= $formTool->form->printNeededJSFunctions_top();
        $content .= $modContent.'</div>';
        // $content .= $this->getModule()->getFormTool()->form->JSbottom('editform');
        $content .= $this->doc->section($LANG->getLL('title_recent_tickers'), $tickerContent);

        $content .= $formTool->form->printNeededJSFunctions();

        return $content;
    }

    protected function setSelectorMenu($selector)
    {
        $this->getModule()->selector = '<div id="selector">'.$selector.'</div>';
    }

    /**
     * Liefert ein Textfeld für eine SofortMeldung per Ajax.
     *
     * @return string
     */
    protected function getInstantMessageField()
    {
        $jsPath = \TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cfc_league', 'Resources/Public/JavaScript/')
        ).'jeditable.min'; // .js

        $pageRenderer = $this->doc->getPageRenderer();
        $pageRenderer->addRequireJsConfiguration(
            [
                'paths' => [
                    'jquery' => 'typo3/sysext/core/Resources/Public/JavaScript/Contrib/jquery/',
                    'jeditable' => $jsPath,
                ],
                'shim' => [
                     'jeditable' => [
                         'deps' => ['jquery'],
                         'exports' => 'jeditable',
                     ],
                ],
            ]
        );
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/CfcLeague/InstantMessage',
            'function (instantMessage) {instantMessage.init("test")}'
        );
        $ret = '';
        $ret .= '<p id="instant" style="background:yellow; margin-bottom:10px; padding:3px"></p>';

        return $ret;
    }

    protected function toTime($time)
    {
        return (new \DateTime('@'.((int) ($time / 1000))))->format('Y-m-d H:i:s').' - '.$time;
    }

    protected function getFormHeadline()
    {
        $stop = Tx_Rnbase_Utility_T3General::_GP('btn_watch_stop');
        $pause = Tx_Rnbase_Utility_T3General::_GP('btn_watch_pause');
        $start = Tx_Rnbase_Utility_T3General::_GP('btn_watch_start');
        $pauseTime = (int) Tx_Rnbase_Utility_T3General::_GP('watch_pausetime');
        $startTime = (int) Tx_Rnbase_Utility_T3General::_GP('watch_starttime');
        $clickTime = (int) Tx_Rnbase_Utility_T3General::_GP('watch_localtime');

        // Daten: Startuhrzeit auf dem Client und gewünschtes offset
        $currentValues = [];
        if ($stop) {
            $currentValues['watch_starttime'] = 0;
            $currentValues['watch_paused'] = 0;
        } elseif ($start) {
            if ($pauseTime > 0) {
                // Restart nach der Pause
                $currentValues['watch_starttime'] = $clickTime - ($pauseTime - $startTime);
            } else {
                $currentValues['watch_starttime'] = $clickTime;
            }
            $currentValues['watch_paused'] = 0;
        } else {
            // Zeitpunkt sichern, an dem Pause gedrückt wurde
            $currentValues['watch_paused'] = $clickTime;
        }

        $modValues = Tx_Rnbase_Backend_Utility::getModuleData(
            ['watch_starttime' => 0, 'watch_paused' => 0],
            $start || $stop || $pause ? $currentValues : [],
            $this->getModule()->getName()
        );
        $startTime = isset($modValues['watch_starttime']) ? $modValues['watch_starttime'] : '0';
        $pauseTime = isset($modValues['watch_paused']) ? $modValues['watch_paused'] : 0;
        $isPaused = $pauseTime > 0;

        // Der übergebene Offset wird immer gespeichert
        $offset = [
            'watch_offset' => (int) Tx_Rnbase_Utility_T3General::_GP('watch_offset'),
        ];
        $modValues = Tx_Rnbase_Backend_Utility::getModuleData(
            ['watch_offset' => 0],
            $offset,
            $this->getModule()->getName()
        );
        $offset = isset($modValues['watch_offset']) ? $modValues['watch_offset'] : '0';

        $out = '<table width="100%"><tr><td style="text-align:left">';
        $out .= $this->getModule()
            ->getFormTool()
            ->createSubmit('update', $GLOBALS['LANG']->getLL('btn_save'));
        $out .= '</td><td style="text-align:left">';

        $out .= '###LABEL_TICKEROFFSET###: ';
        $out .= $this->getModule()
            ->getFormTool()
            ->createTxtInput('watch_offset', $offset, tx_rnbase_util_TYPO3::isTYPO70OrHigher() ? 3 : 2).' ';
        $out .= '###LABEL_TICKERMINUTE###: ';
        $out .= $this->getModule()
            ->getFormTool()
            ->createTxtInput('watch', '00:00', '5');
        $out .= $this->getModule()
            ->getFormTool()
            ->createHidden('watch_starttime', $startTime);
        $out .= $this->getModule()
            ->getFormTool()
            ->createHidden('watch_localtime', 0);
        $out .= $this->getModule()
            ->getFormTool()
            ->createHidden('watch_minute', 0);
        $out .= $this->getModule()
            ->getFormTool()
            ->createHidden('watch_pausetime', $pauseTime);

        if ($isPaused) {
            $out .= $this->getModule()
                ->getFormTool()
                ->createSubmit('btn_watch_start', '###LABEL_BTN_WATCHSTART###');
            $out .= $this->getModule()
                ->getFormTool()
                ->createSubmit('btn_watch_stop', '###LABEL_BTN_WATCHSTOP###');
        } elseif ($startTime > 0) {
            $out .= $this->getModule()
                ->getFormTool()
                ->createSubmit('btn_watch_pause', '###LABEL_BTN_WATCHPAUSE###');
            $out .= $this->getModule()
                ->getFormTool()
                ->createSubmit('btn_watch_stop', '###LABEL_BTN_WATCHSTOP###');
        } else {
            $out .= $this->getModule()
                ->getFormTool()
                ->createSubmit('btn_watch_start', '###LABEL_BTN_WATCHSTART###');
        }
        $out .= '</td></tr></table>';

        $out .= '<script>
	function pause() {
		var form = document.forms[0];
		var now = (new Date()).getTime();
		form.watch_localtime.value = now;
		setTimeout("pause()", 1000);
	}
    function toTime(tstamp) {
    	return new Date(tstamp).toLocaleString() + " (" + tstamp +")";
    }
	function ticker() {
		var form = document.forms[0];
		var now = (new Date()).getTime();
		form.watch_localtime.value = now;

		var paused = parseInt(form.watch_pausetime.value);
		var start = parseInt(form.watch_starttime.value);
		if(start > 0) {
			offset = trim(form.watch_offset.value);
			offset = parseInt(isNaN(offset) || offset == "" ? 0 : offset);
			diff = new Date((paused > 0 ? paused : now) - start);
			std = diff.getHours();
			min = diff.getMinutes() + ((std - 1) * 60) + offset;
			sec = diff.getSeconds();
			form.watch_minute.value = min + 1;
			form.watch.value = ((min>9) ? min : "0" + min) + ":" + ((sec>9) ? sec : "0" + sec);
		}
		if (paused == 0) {
			setTimeout("ticker()", 1000);
		}
		else {
			setTimeout("pause()", 1000);
		}
	}
	function trim(str) {
		return str ? str.replace(/\s+/,"") : "";
	}
	ticker();
</script>
';

        $out .= $this->getMatchMinuteJsCode();

        return $out;
    }

    protected function getMatchMinuteJsCode()
    {
        // use jQuery for 7.6. or higher
        if (tx_rnbase_util_TYPO3::isTYPO76OrHigher()) {
            $jsCode = '<script>
            function setMatchMinute(elem) {
                var form = elem.form;
                min = form.watch_minute.value;
                if(min == 0) return;
                line = elem.name.match(/NEW(\d+)/)[1];
                var elements = jQuery(elem.form).find(":input");
                for (var i = 0; i < elements.length; i++) {

                    if(elements[i].name == "data[tx_cfcleague_match_notes][NEW"+line+"][minute]") {

                        if(jQuery(elements[i]).val() == "") {
                            jQuery(elements[i]).val(min);
                            jQuery(elem.form).find("[data-formengine-input-name=\'"+elements[i].name+"\']").val(min);
                        }
                    }
                }
            }
            </script>
                    ';
        } else {
            $jsCode = '<script>
            function setMatchMinute(elem) {
                var form = elem.form;
                min = form.watch_minute.value;
                if(min == 0) return;
                line = elem.name.match(/NEW(\d+)/)[1];
                var elements = Form.getInputs(elem.form);
                for (var i = 0; i < elements.length; i++) {
                    if(elements[i].name == "data[tx_cfcleague_match_notes][NEW"+line+"][minute]_hr") {
                        if(Field.getValue(elements[i]) == "") {
                            elements[i].value = min;
                            typo3FormFieldGet("data[tx_cfcleague_match_notes][NEW"+line+"][minute]", "int", "", 0,0);
                        }
                    }
                }
            }
            </script>';
        }

        return $jsCode;
    }

    /**
     * Erstellt die Eingabemaske für den Spielstand.
     *
     * @param \tx_cfcleague_models_Match $match
     * @param \tx_cfcleague_models_Competition $competition
     */
    private function createStandingForm($match, $competition)
    {
        global $LANG;

        $standingWidth = tx_rnbase_util_TYPO3::isTYPO70OrHigher() ? 3 : 2;
        $out = '';

        $parts = $competition->getNumberOfMatchParts();
        for ($i = $parts; $i > 0; --$i) {
            $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_'.$i);
            if (!$label) {
                // Prüfen ob ein default gesetzt ist
                $label = $LANG->getLL('tx_cfcleague_games.parts_'.$parts.'_default');
                if ($label) {
                    $label = $i.'. '.$label;
                }
            }
            $out .= $label ? $label : $i.'. part';
            $out .= ': ';
            $out .= $this->getModule()
                ->getFormTool()
                ->createIntInput('data[tx_cfcleague_games]['.$match->getUid().'][goals_home_'.$i.']', $match->getProperty('goals_home_'.$i), $standingWidth);
            $out .= ':';
            $out .= $this->getModule()
                ->getFormTool()
                ->createIntInput('data[tx_cfcleague_games]['.$match->getUid().'][goals_guest_'.$i.']', $match->getProperty('goals_guest_'.$i), $standingWidth);
        }
        $out .= $this->getModule()
            ->getFormTool()
            ->createSelectSingle('data[tx_cfcleague_games]['.$match->getUid().'][status]', intval($match->getProperty('status')), 'tx_cfcleague_games', 'status');
        $out .= $LANG->getLL('tx_cfcleague_games.visitors').': ';
        $out .= $this->getModule()
            ->getFormTool()
            ->createIntInput('data[tx_cfcleague_games]['.$match->getUid().'][visitors]', $match->getProperty('visitors'), tx_rnbase_util_TYPO3::isTYPO70OrHigher() ? 6 : 4);
        $out .= $this->getModule()
            ->getFormTool()
            ->createHidden('t3matchid', $match->getUid());
        $out .= '<br />';

        return $out;
    }

    /**
     * Für das Formular benötigen wir ein spezielles Layout.
     */
    protected function _getTableLayoutForm()
    {
        $arr = [
            'table' => array(
                '<table class="typo3-dblist table" width="100%" cellspacing="0" cellpadding="0" border="0">',
                '</table><br/>',
            ),
            '0' => [ // Format für 1. Zeile
                'tr' => [
                    '<tr class="c-headLineTable">',
                    '</tr>',
                ],
                'defCol' => [
                    '<td valign="top" class="t3-row-header c-headLineTable" style="font-weight:bold;padding:2px 5px;">',
                    '</td>',
                ], // Format für jede Spalte in der 1. Zeile
            ],
            'defRowOdd' => array( // Formate für alle geraden Zeilen
                'tr' => array(
                    '<tr class="db_list_normal">',
                    '</tr>',
                ),
                'defCol' => array(
                    '<td valign="top" style="padding:5px 5px;">',
                    '</td>',
                ), // Format für jede Spalte in jeder Zeile
            ),
            'defRowEven' => [ // Formate für alle ungeraden Zeilen (die Textbox)
                'tr' => [
                    '<tr class="db_list_alt">',
                    '</tr>',
                ],
                'defCol' => [
                    '<td colspan="2" style="border-bottom:solid 1px #A2AAB8;">&nbsp;</td><td valign="top" align="left" colspan="2" style="padding:2px 5px;border-bottom:solid 1px #A2AAB8;">',
                    '</td>',
                ], // Format für jede Spalte in jeder Zeile
            ],
        ];

        return $arr;
    }

    /**
     * Wir listen die Tickermeldungen des Spiels auf.
     *
     * @param tx_cfcleague_models_Match $match
     */
    protected function createTickerArray($match, $showAll)
    {
        global $LANG;
        $notes = $match->getMatchNotes('desc', $showAll ? false : 5);
        if (!count($notes)) {
            return 0;
        }

        $arr = [
            [
                $LANG->getLL('tx_cfcleague_match_notes.minute'),
                $LANG->getLL('tx_cfcleague_match_notes.type'),
                $LANG->getLL('tx_cfcleague_match_notes.player_home'),
                $LANG->getLL('tx_cfcleague_match_notes.player_guest'),
                $LANG->getLL('tx_cfcleague_match_notes.comment'),
                '',
            ],
        ];

        // Die NotesTypen laden
        $types = $this->getTickerTypes();
        $playersHome = $this->getPlayerNames($match, 'home');
        $playersGuest = $this->getPlayerNames($match, 'guest');

        foreach ($notes as $noteObj) {
            $note = $noteObj->getProperty();
            $row = [];

            $min = $note['minute'].($note['extra_time'] ? '+'.$note['extra_time'] : '');
            $min .= $note['hidden'] ? '*' : '';
            $row[] = $min;
            $row[] = $types[$note['type']];

            $row[] = -1 == intval($note['player_home']) ? $LANG->getLL('tx_cfcleague.unknown') : $playersHome[$note['player_home']];
            $row[] = -1 == intval($note['player_guest']) ? $LANG->getLL('tx_cfcleague.unknown') : $playersGuest[$note['player_guest']];

            $row[] = $note['comment'];
            $row[] = $this->getModule()
                ->getFormTool()
                ->createEditLink('tx_cfcleague_match_notes', $note['uid']);
            $row[] = $this->getModule()
                ->getFormTool()
                ->createHideLink('tx_cfcleague_match_notes', $note['uid']);
            $arr[] = $row;
        }

        return $arr;
    }

    protected function getTickerTypes()
    {
        $srv = tx_cfcleague_util_ServiceRegistry::getMatchService();
        $tcaTypes = $srv->getMatchNoteTypes4TCA();
        $types = [];
        foreach ($tcaTypes as $typeDef) {
            $types[$typeDef[1]] = $typeDef[0];
        }

        return $types;
    }

    /**
     * @param tx_cfcleague_models_Match $match
     * @param string $team
     */
    protected function getPlayerNames($match, $team)
    {
        if (isset($this->playerNames[$team])) {
            return $this->playerNames[$team];
        }

        $profileSrv = tx_cfcleague_util_ServiceRegistry::getProfileService();
        if ('home' == $team) {
            $players = $profileSrv->loadProfiles($match->getPlayersHome(true));
        } else {
            $players = $profileSrv->loadProfiles($match->getPlayersGuest(true));
        }
        $this->playerNames = [
            $team => [],
        ];
        foreach ($players as $player) {
            $this->playerNames[$team][$player->getUid()] = $player->getName(true);
        }

        return $this->playerNames[$team];
    }

    /**
     * Erstellt das Formular für die Eingabe der Tickermeldungen.
     *
     * @param tx_cfcleague_models_Match $match
     */
    protected function createFormArray($match)
    {
        global $LANG;

        $arr = [
            [
                $LANG->getLL('tx_cfcleague_match_notes.minute'),
                $LANG->getLL('tx_cfcleague_match_notes.type'),
                $LANG->getLL('tx_cfcleague_match_notes.player_home'),
                $LANG->getLL('tx_cfcleague_match_notes.player_guest'),
            ],
        ];

        $minuteWidth = tx_rnbase_util_TYPO3::isTYPO70OrHigher() ? 3 : 2;

        // TS-Config der aktuellen Seite laden, um die Anzahl der Felder zu ermitteln
        $pageTSconfig = Tx_Rnbase_Backend_Utility::getPagesTSconfig($this->getModule()->getPid());
        $inputFields = (is_array($pageTSconfig) && is_array($pageTSconfig['tx_cfcleague.']['matchTickerCfg.'])) ? intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['numberOfInputFields']) : 4;
        $cols = (is_array($pageTSconfig) && is_array($pageTSconfig['tx_cfcleague.']['matchTickerCfg.'])) ? intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['commentFieldCols']) : 35;
        $rows = (is_array($pageTSconfig) && is_array($pageTSconfig['tx_cfcleague.']['matchTickerCfg.'])) ? intval($pageTSconfig['tx_cfcleague.']['matchTickerCfg.']['commentFieldRows']) : 3;

        $playersHome = $playersGuest = [
            0 => '',
        ]; // Erster Eintrag bleibt leer
        $players = tx_cfcleague_util_ServiceRegistry::getProfileService()->loadProfiles($match->getPlayersHome(true));
        foreach ($players as $player) {
            $playersHome[$player->getUid()] = $player->getName(true);
        }
        asort($playersHome);
        $players = tx_cfcleague_util_ServiceRegistry::getProfileService()->loadProfiles($match->getPlayersGuest(true));
        foreach ($players as $player) {
            $playersGuest[$player->getUid()] = $player->getName(true);
        }
        asort($playersGuest);
        // Jetzt noch den Dummy-Player anhängen
        $playersHome[-1] = $playersGuest[-1] = $LANG->getLL('tx_cfcleague.unknown');

        $types = $this->getTickerTypes();
        // Wenn kein sinnvoller Wert vorhanden ist, bleibt der Standard bei 4
        $inputFields = $inputFields ? $inputFields : 4;
        for ($i = 0; $i < $inputFields; ++$i) {
            $row = [];

            $row[] = $this->getModule()
                ->getFormTool()
                ->createIntInput('data[tx_cfcleague_match_notes][NEW'.$i.'][minute]', '', $minuteWidth, 3).'+'.$this->getModule()
                ->getFormTool()
                ->createIntInput('data[tx_cfcleague_match_notes][NEW'.$i.'][extra_time]', '', $minuteWidth, 2).$this->getModule()
                ->getFormTool()
                ->createHidden('data[tx_cfcleague_match_notes][NEW'.$i.'][game]', $match->getUid());
            $row[] = $this->getModule()
                ->getFormTool()
                ->createSelectByArray('data[tx_cfcleague_match_notes][NEW'.$i.'][type]', '0', $types, [
                'onchange' => 'setMatchMinute(this);',
            ]);
            $row[] = $this->getModule()
                ->getFormTool()
                ->createSelectByArray('data[tx_cfcleague_match_notes][NEW'.$i.'][player_home]', '0', $playersHome, [
                'onchange' => 'setMatchMinute(this);',
            ]);
            $row[] = $this->getModule()
                ->getFormTool()
                ->createSelectByArray('data[tx_cfcleague_match_notes][NEW'.$i.'][player_guest]', '0', $playersGuest, [
                'onchange' => 'setMatchMinute(this);',
            ]);
            $arr[] = $row;

            // Das Bemerkungsfeld kommt in die nächste Zeile
            $row = [];
            $row[] = $this->getModule()
                ->getFormTool()
                ->createTextArea('data[tx_cfcleague_match_notes][NEW'.$i.'][comment]', '', $cols, $rows, [
                'onchange' => 'setMatchMinute(this);',
            ]);
            $arr[] = $row;
        }

        return $arr;
    }

    /**
     * Erstellt eine neue Spielaktion mit den Daten aus dem Request.
     */
    public function insertNotes($data)
    {
        $notes = $data[self::TABLE_NOTES];
        foreach ($notes as $noteId => $note) {
            $data[self::TABLE_NOTES][$noteId]['player_home'] = (int) $data[self::TABLE_NOTES][$noteId]['player_home'];
            $data[self::TABLE_NOTES][$noteId]['player_guest'] = (int) $data[self::TABLE_NOTES][$noteId]['player_guest'];
            $data[self::TABLE_NOTES][$noteId]['player_guest'] = (int) $data[self::TABLE_NOTES][$noteId]['player_guest'];

            $playerOk = !(0 != (int) $note['player_home'] && 0 != (int) $note['player_guest']);

            // Ohne Minute (Feld ist leer) wird nix gespeichert
            // kleinste Minute ist -1 für versteckte Meldungen
            if (strlen($note['minute']) > 0 && intval($note['minute']) >= -1 && $playerOk) { // Minute ist Pflichtfeld
                $data[self::TABLE_NOTES][$noteId]['pid'] = $this->getModule()->getPid();
                $data[self::TABLE_NOTES][$noteId]['comment'] = nl2br($data[self::TABLE_NOTES][$noteId]['comment']);
            } else {
                unset($data['tx_cfcleague_match_notes'][$noteId]);
            }
        }
        if (!count($data['tx_cfcleague_match_notes'])) {
            unset($data['tx_cfcleague_match_notes']);
        }
        // Die neuen Notes werden jetzt gespeichert
        reset($data);
        $tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
    }

    /**
     * Liefert die Selector Instanz.
     *
     * @return tx_cfcleague_selector
     */
    private function getSelector()
    {
        return $this->selector;
    }
}
