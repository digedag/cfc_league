<?php

namespace System25\T3sports\Utility;

use System25\T3sports\Model\Repository\CompetitionRepository;
use System25\T3sports\Model\Repository\SaisonRepository;
use System25\T3sports\Model\Repository\TeamRepository;

class SlugModifier
{
    private $competitionRepo;
    private $saisonRepo;
    private $teamRepo;

    public function __construct(
        CompetitionRepository $competitionRepo,
        SaisonRepository $saisonRepo,
        TeamRepository $teamRepo
    ) {
        $this->competitionRepo = $competitionRepo;
        $this->saisonRepo = $saisonRepo;
        $this->teamRepo = $teamRepo;
    }

    public function handleProfile(array $hookParameters, $parent)
    {
        $record = $hookParameters['record'];
        $uid = (int) ($record['uid'] ?? 0);
        if ($uid <= 0) {
            return '';
        }
        $map = [
            'uid' => $record['uid'],
            'name' => trim(!empty($record['stage_name'] ?? '') ? $record['stage_name'] : ($record['first_name'] ?? '').' '.($record['last_name'] ?? '')),
        ];
        $template = '[uid]/[name]';
        $slug = $this->buildSlug($template, $map);

        return $slug;
    }

    public function handleFixture(array $hookParameters, $parent)
    {
        $record = $hookParameters['record'];
        $comp = $this->competitionRepo->findByUid($record['competition']);
        $saison = $this->saisonRepo->findByUid($comp->getProperty('saison'));
        $home = $this->teamRepo->findByUid($record['home']);
        $guest = $this->teamRepo->findByUid($record['guest']);

        $map = [
            'comp_name' => $comp->getProperty('name'),
            'comp_short' => $comp->getProperty('name_short') ?: $comp->getProperty('name'),
            'comp_slug' => $comp->getProperty('slug') ?: ($comp->getProperty('name_short') ?: $comp->getProperty('name')),
            'saison_name' => $saison->getProperty('name'),
            'guest_name' => $guest->getProperty('name'),
            'guest_short' => $guest->getProperty('short_name') ?: $guest->getProperty('name'),
            'guest_tlc' => $guest->getProperty('tlc') ?: ($guest->getProperty('short_name') ?: $guest->getProperty('name')),
            'home_name' => $home->getProperty('name'),
            'home_short' => $home->getProperty('short_name') ?: $home->getProperty('name'),
            'home_tlc' => $home->getProperty('tlc') ?: ($home->getProperty('short_name') ?: $home->getProperty('name')),
        ];

        $template = '[saison_name]/[comp_name]/[home_tlc]-[guest_tlc]';
        $slug = $this->buildSlug($template, $map);

        return $slug;
    }

    private function buildSlug($template, $map)
    {
        $slug = preg_replace_callback('/\[([^\]]+)\]/', function ($matches) use ($map) {
            $key = $matches[1];

            return isset($map[$key]) ? $map[$key] : '';
        }, $template);

        $slug = mb_strtolower($slug, 'utf-8');
        if (function_exists('iconv')) {
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        } else {
            $slug = strtr($slug, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        }
        $slug = preg_replace('/[^a-z0-9-\/]+/', '-', $slug); // Rest normalisieren. Also alles ausser a-z, 0-9 und / zu -
        $slug = preg_replace('/-+/', '-', $slug); // Mehrere - zu einem
        $slug = trim($slug, '-/');

        return $slug;
    }
}
