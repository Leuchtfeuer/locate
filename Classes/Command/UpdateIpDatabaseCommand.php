<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Command;

use Doctrine\DBAL\Exception as DBALException;
use Leuchtfeuer\Locate\Utility\TypeCaster;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory;

/*
 * This command updates the ip tables in the database.
 *
 * Example: vendor/bin/typo3 locate:updateIpDatabase TOKEN static_ip2country_v4
 *
 * IP data is available at https://lite.ip2location.com/
 */
class UpdateIpDatabaseCommand extends Command
{
    protected const string DOWNLOAD_LINK = 'https://www.ip2location.com/download/?token=%s&file=%s';

    protected SymfonyStyle $io;

    protected string $token = '';

    protected string $table = '';

    protected string $databaseCode = '';

    protected string $path = '';

    protected string $source = '';

    protected string $downloadFile = '';

    public function __construct(
        protected readonly ConnectionPool $connectionPool,
        protected readonly RequestFactory $requestFactory,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Imports and updates the static IP tables')
            ->setHelp('This command is a helper command to update the static IP tables.')
            ->addArgument('token', InputArgument::REQUIRED, 'Download Token from your IP2Location account.')
            ->addArgument('table', InputArgument::OPTIONAL, 'The data table to update (valid arguments \'static_ip2country_v4\' and \'static_ip2country_v6\')', 'static_ip2country_v4');
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->validateArguments($input)) {
            return 1;
        }

        try {
            $this->io->writeln('Truncate database table...');
            $this->truncateTable();

            $this->io->writeln('Fetch CSV file...');
            $this->fetchCsv();

            $this->io->writeln('Load CSV file...');
            $data = $this->loadCsv();

            $this->io->writeln('Write data into database...');
            $this->importData($data);

            $this->io->writeln('Cleanup...');
            $this->cleanup();

            $this->io->writeln(sprintf('<info>%d recoreds imported.</info>', count($data)));
        } catch (\Exception $exception) {
            /** @extensionScannerIgnoreLine */
            $this->io->error($exception->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function validateArguments(InputInterface $input): bool
    {
        $this->token = TypeCaster::toString($input->getArgument('token'));
        $this->table = TypeCaster::toString($input->getArgument('table'));

        if ($this->token === '') {
            /** @extensionScannerIgnoreLine */
            $this->io->error(sprintf('Download Token %s is required. Exit.', $this->token));
            return false;
        }

        if ($this->table !== 'static_ip2country_v4' && $this->table !== 'static_ip2country_v6') {
            /** @extensionScannerIgnoreLine */
            $this->io->error(sprintf('Could not update table "%s". Valid options are: "static_ip2country_v4" or "static_ip2country_v6"', $this->table));
            return false;
        }

        $this->path = Environment::getVarPath() . '/transient';
        if (!is_dir($this->path)) {
            mkdir($this->path, 0775);
        }
        if (is_writable($this->path)) {
            if ($this->table === 'static_ip2country_v4') {
                $this->databaseCode = 'DB1LITECSV';
                $this->source = $this->path . '/' . 'IP2LOCATION-LITE-DB1.CSV';
            } else {
                $this->databaseCode = 'DB1LITECSVIPV6';
                $this->source = $this->path . '/' . 'IP2LOCATION-LITE-DB1.IPV6.CSV';
            }

            $this->downloadFile = $this->path . '/' . $this->databaseCode . '.zip';
        } else {
            /** @extensionScannerIgnoreLine */
            $this->io->error(sprintf('Filepath %s for download is not writeable!', $this->path));

            return false;
        }

        return true;
    }

    /**
     * @throws DBALException
     */
    private function truncateTable(): void
    {
        $connection = $this->connectionPool->getConnectionForTable($this->table);
        $databasePlatform = $connection->getDatabasePlatform();
        $connection->executeStatement($databasePlatform->getTruncateTableSQL($this->table, true));
    }

    private function fetchCsv(): void
    {
        $response = $this->requestFactory->request(sprintf(self::DOWNLOAD_LINK, $this->token, $this->databaseCode), 'GET', ['sink' => $this->downloadFile]);
        if ($response->getStatusCode() === 200) {
            $zip = new \ZipArchive();
            if ($zip->open($this->downloadFile) === true) {
                $zip->extractTo($this->path);
                $zip->close();
            } else {
                throw new \RuntimeException('Could not unzip download file. Maybe the token is not valid.', 1_667_409_547);
            }
        } else {
            throw new \RuntimeException('Could not download file. Maybe the token is not valid.', 1_667_409_548);
        }
    }

    /**
     * @return array<array<string, mixed>>
     */
    private function loadCsv(): array
    {
        if (($resource = fopen($this->source, 'r')) === false) {
            throw new \RuntimeException('Could not read source file.', 1_667_409_546);
        }

        $data = [];

        while (($row = fgetcsv($resource)) !== false) {
            if ($row !== []) {
                $data[] = [
                    'ip_from' => $row[0],
                    'ip_to' => $row[1],
                    'country_code' => $row[2],
                ];
            }
        }

        fclose($resource);

        return $data;
    }

    /**
     * @param array<array<string, mixed>> $data
     */
    private function importData(array $data): void
    {
        $progressBar = $this->io->createProgressBar(count($data));
        $progressBar->start(0);

        foreach ($data as $row) {
            $this->connectionPool->getQueryBuilderForTable($this->table)
                ->insert($this->table)
                ->values($row)
                ->executeStatement();
            $progressBar->advance();
        }

        /** @extensionScannerIgnoreLine */
        $progressBar->finish();
        $this->io->writeln('');
    }

    private function cleanup(): void
    {
        unlink($this->downloadFile);
        unlink($this->source);
    }
}
