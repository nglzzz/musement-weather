<?php

namespace App\Command;

use App\Synchronization\City\MusementCitySynchronization;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MusementSynchronizationCommand extends Command
{
    public const NAME = 'app:musement-city-synchronization';

    private MusementCitySynchronization $synchronization;

    public function __construct(MusementCitySynchronization $synchronization)
    {
        parent::__construct();

        $this->synchronization = $synchronization;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Create or update cities from Musement API')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Execute the synchronization as a dry run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = $this->getLogger($output);

        $io = new SymfonyStyle($input, $output);

        try {
            $result = $this->synchronization->process($logger, $input->getOption('dry-run'));
        } catch (\Exception $e) {
            $io->error(\sprintf(
                'An error occurred during synchronization: %s',
                $e->getMessage(),
            ));

            return Command::FAILURE;
        }
        $io->info(\sprintf(
            'Created cities: %d. Updated cities: %d. Created countries: %d. Skipped cities: %d',
            $result['createdCities'],
            $result['updatedCities'],
            $result['createdCountries'],
            $result['skippedCities'],
        ));

        $io->success(
            'Synchronization has been successfully completed'
        );

        return Command::SUCCESS;
    }

    private function getLogger(OutputInterface $output): LoggerInterface
    {
        return new ConsoleLogger($output, $verbosityLevelMap = [
            LogLevel::INFO => OutputInterface::VERBOSITY_VERBOSE,
            LogLevel::DEBUG => OutputInterface::VERBOSITY_VERY_VERBOSE,
        ]);
    }
}
