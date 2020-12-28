<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This command updates the ip tables in the database and should be used for development purposes only.
 *
 * Example (IPv4): vendor/bin/typo3 locate:update /path/to/ipv4.csv static_ip2country_v4 -vvv
 * Example (IPv6): vendor/bin/typo3 locate:update /path/to/ipv6.csv static_ip2country_v6 -vvv
 *
 * IP data is available at https://lite.ip2location.com/
 */
class UpdateIpDatabaseCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    protected $table = '';

    protected $source = '';

    protected function configure(): void
    {
        $this
            ->setDescription('Imports and updates the static IP tables')
            ->setHelp('This command is a helber command to update the static IP tables' . LF . 'and should only used by developers.')
            ->addArgument('source', InputArgument::REQUIRED, 'The path to the IP source CSV file')
            ->addArgument('table', InputArgument::OPTIONAL, 'The data table to update', 'static_ip2country_v4');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->table = $input->getArgument('table');
        $this->source = $input->getArgument('source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->validateArguments()) {
            return 1;
        }

        try {
            $this->io->writeln('Truncate database table...');
            $this->truncateTable();

            $this->io->writeln('Load CSV file...');
            $data = $this->loadCsv();

            $this->io->writeln('Write data into database...');
            $this->importData($data);

            $this->io->writeln(sprintf('<info>%d recoreds imported.</info>', count($data)));
        } catch (\Exception $exception) {
            $this->io->error($exception->getMessage());

            return 1;
        }

        $this->io->comment('You can now dump the data tables and update the "ext_tables_static+adt.sql" file.');
        return 0;
    }

    private function validateArguments(): bool
    {
        if (@file_exists($this->source) === false) {
            $this->io->error(sprintf('Could not find source in "%s". Exit.', $this->source));

            return false;
        }

        if ($this->table !== 'static_ip2country_v4' && $this->table !== 'static_ip2country_v6') {
            $this->io->error(sprintf('Could not update table "%s". Valid options are: "static_ip2country_v4" or "static_ip2country_v6"', $this->table));

            return false;
        }

        return true;
    }

    private function truncateTable(): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table);
        $connection->executeUpdate($connection->getDatabasePlatform()->getTruncateTableSQL($this->table, true));
    }

    /**
     * @throws \RuntimeException
     */
    private function loadCsv(): array
    {
        if (($resource = fopen($this->source, 'r')) === false) {
            throw new \RuntimeException('Could not read source file.', 1606812836);
        }

        $data = [];

        while (($row = fgetcsv($resource)) !== false) {
            $data[] = [
                'ip_from' => $row[0],
                'ip_to' => $row[1],
                'country_code' => $row[2]
            ];
        }

        fclose($resource);

        return $data;
    }

    private function importData(array $data): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class);
        $progressBar = $this->io->createProgressBar(count($data));
        $progressBar->start(0);

        foreach ($data as $row) {
            $connection->getQueryBuilderForTable($this->table)
                ->insert($this->table)
                ->values($row)
                ->execute();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->io->writeln('');
    }
}
