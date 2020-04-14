<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2018 Rene Nitzsche (rene@system25.de)
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
tx_rnbase::load('tx_rnbase_parameters');
tx_rnbase::load('tx_rnbase_util_Logger');

class Tx_Cfcleague_Dfb_Synchronizer
{
    const TABLE_GAMES = 'tx_cfcleague_games';

    const TABLE_TEAMS = 'tx_cfcleague_teams';

    const TABLE_STADIUMS = 'tx_cfcleague_stadiums';

    const TABLE_COMPETITION = 'tx_cfcleague_competition';

    /**
     * Key ist DFB-ID, value ist T3-UID.
     */
    private $teamMap = [];

    /**
     * Key ist DFB-ID, value ist T3-UID.
     */
    private $matchMap = [];

    private $stats = [];

    private $pageUid = 0;

    public function process(\TYPO3\CMS\Core\Resource\File $file, tx_cfcleague_models_Competition $competition)
    {
        $fileContent = $this->removeBOM($file->getContents());
        // There are some annoying null bytes...
        $fileContent = str_replace("\0", '', $fileContent);
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-8, ISO-8859-1');
        $lines = explode("\n", $fileContent);
        $headers = array_shift($lines);
        $headers = str_getcsv($this->prepareHeaderLine($headers), "\t");
        $structure = tx_rnbase::makeInstance('Tx_Cfcleague_Dfb_CsvStructure', $headers);
        $start = microtime(true);

        $this->pageUid = $competition->getPid();
        $this->initMatches($competition);

        $info = [
            'match' => ['new' => 0, 'updated' => 0, 'skipped' => 0],
            'team' => ['new' => 0, 'updated' => 0],
        ];
        $data = [
            self::TABLE_TEAMS => [],
            self::TABLE_STADIUMS => [],
            self::TABLE_GAMES => [],
            self::TABLE_COMPETITION => [],
        ];

        $cnt = 0;
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }
            $matchData = str_getcsv($line, "\t");
            if ($this->handleMatch($data, $competition, $matchData, $structure, $info)) {
                if (0 == $cnt % 50) {
                    // Speichern
                    $this->persist($data);
                    // Wettbewerb neu laden, da ggf. neue Teams drin stehen
                    $competition->reset();
                }
                $cnt += 1;
            } else {
                $info['match']['skipped'] += 1;
            }
        }
        // Die restlichen Spiele speichern
        $this->persist($data);
        $this->stats['total']['time'] = intval(microtime(true) - $start).'s';
        $this->stats['total']['matches'] = $cnt;

        tx_rnbase_util_Logger::info('Update match schedule finished!', 'cfc_league', [
            'stats' => $this->stats,
            'info' => $info,
        ]);

        return $info;
    }

    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param array $data
     * @param tx_cfcleague_models_Competition $competition
     * @param array $matchData
     * @param Tx_Cfcleague_Dfb_CsvStructure $structure
     * @param array $info
     *
     * @return bool true if line was processed
     */
    protected function handleMatch(array &$data, tx_cfcleague_models_Competition $competition, array $matchData, Tx_Cfcleague_Dfb_CsvStructure $structure, array &$info)
    {
        $extCompId = $structure->getCompetitionId($matchData);
        if ($competition->getExtId() && $competition->getExtId() != $extCompId) {
            // Wrong competition, skip match
            return false;
        } elseif (!$competition->getExtId()) {
            if (empty($this->matchMap)) {
                // Wettbewerb zuordnen
                $competition->setProperty('extid', $extCompId);
                tx_cfcleague_util_ServiceRegistry::getCompetitionService()->persist($competition);
            } else {
                // Automatische Zuordnung nicht mehr möglich
                return false;
            }
        }
        // sync match
        $extMatchId = $structure->getMatchId($matchData);

        $matchUid = 'NEW_'.$extMatchId;
        if (array_key_exists($extMatchId, $this->matchMap)) {
            $matchUid = $this->matchMap[$extMatchId];
            $info['match']['updated'] += 1;
        } else {
            $info['match']['new'] += 1;
        }

        $blobFields = [
            'assists', 'players_home', 'players_guest',
            'substitutes_home', 'substitutes_guest',
            'players_home_stat', 'players_guest_stat',
            'substitutes_home_stat', 'substitutes_guest_stat',
            'scorer_guest_stat', 'scorer_home_stat', 'game_report',
        ];
        foreach ($blobFields as $field) {
            $data[self::TABLE_GAMES][$matchUid][$field] = '';
        }

        $data[self::TABLE_GAMES][$matchUid]['pid'] = $this->pageUid;
        $data[self::TABLE_GAMES][$matchUid]['extid'] = $extMatchId;
        $data[self::TABLE_GAMES][$matchUid]['competition'] = $competition->getUid();
        $data[self::TABLE_GAMES][$matchUid]['round'] = $structure->getRound($matchData);
        $data[self::TABLE_GAMES][$matchUid]['round_name'] = $structure->getRound($matchData).'. Spieltag';
        // Es muss ein lokaler Timestamp gesetzt werden
        $data[self::TABLE_GAMES][$matchUid]['date'] = $structure->getKickoffDate($matchData);
        $data[self::TABLE_GAMES][$matchUid]['stadium'] = $structure->getStadium($matchData);
        $data[self::TABLE_GAMES][$matchUid]['home'] = $this->findTeam($structure->getHome($matchData), $data, $competition, $info);
        $data[self::TABLE_GAMES][$matchUid]['guest'] = $this->findTeam($structure->getGuest($matchData), $data, $competition, $info);

        return true;
    }

    protected function findTeam($extTeam, array &$data, tx_cfcleague_models_Competition $competition, array &$info)
    {
        $extTeamId = $this->buildKey($extTeam);
        $uid = 'NEW_'.$extTeamId;
        if (!array_key_exists($extTeamId, $this->teamMap)) {
            // Das Team ist noch nicht im Cache, also in der DB suchen
            /* @var $teamSrv tx_cfcleague_services_Teams */
            $teamSrv = tx_cfcleague_util_ServiceRegistry::getTeamService();
            $fields = array();
            $fields['TEAM.EXTID'][OP_EQ_NOCASE] = $extTeamId;
            $fields['TEAM.PID'][OP_EQ_INT] = $competition->getPid();

            $options = ['what' => 'uid'];
            $ret = $teamSrv->searchTeams($fields, $options);
            if (!empty($ret)) {
                $this->teamMap[$extTeamId] = $ret[0]['uid'];
                $uid = $this->teamMap[$extTeamId];
            } else {
                // In uid steht jetzt NEW_
                // Team anlegen, falls es noch nicht in der Data-Map liegt
                if (!array_key_exists($uid, $data[self::TABLE_TEAMS])) {
                    $data[self::TABLE_TEAMS][$uid] = $this->loadTeamData($extTeamId, $extTeam);
                    $info['team']['new'] += 1;
                }
            }
            // Sicherstellen, daß das Team im Wettbewerb ist
            $this->addTeamToCompetition($uid, $data, $competition);
        } else {
            $uid = $this->teamMap[$extTeamId];
        }

        return $uid;
    }

    /**
     * Stellt sicher, daß das Team im Wettbewerb gespeichert wird.
     * Hier gibt es aber noch ein Todo: es wird nicht geprüft, ob die neue ID schon
     * in den TCE-Data liegt. Dadurch wird so mehrfach hinzugefügt. Das hat aber praktisch
     * keine Auswirkung, da die TCE das selbst korrigiert. Das könnte sich zukünftig aber
     * ändern...
     *
     * @param mixed $teamUid
     * @param array $data
     * @param tx_cfcleague_models_Competition $competition
     */
    protected function addTeamToCompetition($teamUid, &$data, $competition)
    {
        $add = true;
        if ($competition->getProperty('teams')) {
            $teamUids = array_flip(Tx_Rnbase_Utility_Strings::trimExplode(',', $competition->getProperty('teams')));
            $add = !(array_key_exists($teamUid, $teamUids));
        }
        if (!$add) {
            return;
        }
        // Das geht bestimmt auch kürzer...
        // Das Team in den Wettbewerb legen
        if (isset($data[self::TABLE_COMPETITION][$competition->getUid()]['teams'])) {
            $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] .= ','.$teamUid;
        } else {
            // Das erste Team
            if ($competition->getProperty('teams')) {
                $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] = $competition->getProperty('teams');
                $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] .= ','.$teamUid;
            } else {
                $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] = $teamUid;
            }
        }
    }

    protected function loadTeamData($extTeamId, $teamName)
    {
        return [
            'pid' => $this->pageUid,
            'extid' => $extTeamId,
            'name' => $teamName,
            'short_name' => $teamName,
        ];
    }

    protected function buildKey($string)
    {
        return str_replace(' ', '_', strtolower($string));
    }

    protected function persist(&$data)
    {
        $start = microtime(true);

        $tce = Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
        $this->stats['chunks'][]['time'] = intval(microtime(true) - $start).'s';
        $this->stats['chunks'][]['matches'] = count($data[self::TABLE_GAMES]);

        $data[self::TABLE_TEAMS] = array();
        $data[self::TABLE_STADIUMS] = array();
        $data[self::TABLE_GAMES] = array();
        $data[self::TABLE_COMPETITION] = array();
    }

    /**
     * Sorgt dafür, daß die Header eindeutig sind. Der DFB liefert manche Spaltennamen doppelt...
     *
     * @param string $headers
     *
     * @return string
     */
    protected function prepareHeaderLine($headers)
    {
        \tx_rnbase::load('Tx_Cfcleague_Dfb_CsvStructure');
        $headers = str_replace(
            Tx_Cfcleague_Dfb_CsvStructure::COL_MATCH_DATE."\t".Tx_Cfcleague_Dfb_CsvStructure::COL_TIME,
            Tx_Cfcleague_Dfb_CsvStructure::COL_MATCH_DATE."\t".Tx_Cfcleague_Dfb_CsvStructure::COL_MATCH_TIME,
            $headers
        );

        return $headers;
    }

    protected function removeBOM($data)
    {
        if (false !== strpos($data, "\xef\xbb\xbf")) {
            $data = substr($data, 3);
        }
        // strip off BOM (LE UTF-16)
        elseif (false !== strpos($data, "\xff\xfe")) {
            $data = substr($data, 2);
        }
        // strip off BOM (BE UTF-16)
        elseif (false !== strpos($data, "\xfe\xff")) {
            $data = substr($data, 2);
        }

        return $data;
    }

    /**
     * Lädt die vorhandenen Spiele des Wettbewerbs in die matchMap.
     *
     * @param tx_cfcleague_models_Competition $competition
     */
    protected function initMatches(tx_cfcleague_models_Competition $competition)
    {
        $fields = array();
        $options = array();
        /* @var $matchSrv tx_cfcleague_services_Match */
        $matchSrv = tx_cfcleague_util_ServiceRegistry::getMatchService();
        $fields['MATCH.COMPETITION'][OP_EQ_INT] = $competition->getUid();
        $options['what'] = 'uid,extid';
        $options['orderby'] = 'uid asc';
        $options['callback'] = array(
            $this,
            'cbAddMatch',
        );
        $matchSrv->search($fields, $options);
    }

    public function cbAddMatch($record)
    {
        $this->matchMap[$record['extid']] = $record['uid'];
    }
}
