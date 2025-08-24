<?php

namespace System25\T3sports\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Sys25\RnBase\Database\Connection;
use System25\T3sports\Utility\SlugModifier;
use Throwable;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2025 Rene Nitzsche
 *  Contact: rene@system25.de
 *  All rights reserved
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ***************************************************************/

/**
 * @author Rene Nitzsche
 */
class MaintenanceCommand extends Command
{
    /** @var OutputInterface */
    private $output;
    private $slugModifier;
    private $slugTableMap = [
        'fixtures' => ['table' => 'tx_cfcleague_games', 'slugMethod' => 'handleFixture'],
        'profiles' => ['table' => 'tx_cfcleague_profiles', 'slugMethod' => 'handleProfile'],
    ];

    public function __construct(SlugModifier $slugModifier)
    {
        parent::__construct(null);
        $this->slugModifier = $slugModifier;
    }

    protected function configure()
    {
        $this->addArgument('task', InputOption::VALUE_REQUIRED, 'Task to execute');
        $this->addOption('table', null, InputOption::VALUE_REQUIRED, 'Table to process.');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Really execute command.');
        $this->setHelp('Maintenance operations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $style = new SymfonyStyle($input, $output);
        $style->title('T3sports maintenance');

        $result = Command::SUCCESS;
        $force = $input->getOption('force') ?? false;
        $command = $input->getArgument('task');
        switch ($command) {
            case 'slug':
                $result = $this->updateSlug($input, $style, $force);
                break;
            default:
                $style->error('Unknown command');

                return Command::FAILURE;
        }
        $style->success('Done');

        return $result;
    }

    public function updateSlug(InputInterface $input, SymfonyStyle $style, bool $force): int
    {
        $tableData = $this->slugTableMap[$input->getOption('table')] ?? null;
        if (!$tableData) {
            $style->warning(sprintf('Unknown or missing table for option --table.'));

            return Command::FAILURE;
        }

        $table = $tableData['table'];
        $connection = Connection::getInstance();
        $rows = $connection->doSelect('f.*', [
            'table' => $table,
            'alias' => 'f',
        ], [
            'enablefieldsbe' => 1,
            'collection' => 'iterator',
            'debug' => 1,
            'where' => function (QueryBuilder $qb) {
                $qb->where(sprintf('(f.slug IS NULL OR f.slug = \'\')'));
            },
        ]);

        $style->info(sprintf('Found %d matches without slug', count($rows)));

        if (!$force) {
            $style->comment('use --force to really execute the command');

            return Command::SUCCESS;
        }

        $progress = $style->createProgressBar(count($rows));
        $progress->setFormat('debug');
        $progress->start();
        foreach ($rows as $row) {
            try {
                $slugMethod = $tableData['slugMethod'];
                $slug = $this->slugModifier->$slugMethod(['record' => $row], null);
                $connection->doUpdate($table, sprintf('uid = %d', $row['uid']), [
                    'slug' => $slug,
                ]);
            } catch (Throwable $e) {
                $this->output->writeln(sprintf('Error for match (%d): %s', $row['uid'], $e->getMessage()));
            }
            $progress->advance();
        }
        $progress->finish();

        return Command::SUCCESS;
    }
}
