<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Command\MusementSynchronizationCommand;
use App\Synchronization\City\MusementCitySynchronization;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class MusementSynchronizationCommandTest extends KernelTestCase
{
    private MusementSynchronizationCommand $command;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        /** @var MusementCitySynchronization $synchronization */
        $synchronization = self::$container->get(MusementCitySynchronization::class);

        $this->command = new MusementSynchronizationCommand($synchronization);
    }

    public function testExecuteReturnsSuccessWithDryRunMode(): void
    {
        $commandTester = $this->getCommandTester();

        $result = $commandTester->execute([
            'command' => MusementSynchronizationCommand::NAME,
            '--dry-run' => true,
        ]);

        $output = $commandTester->getDisplay();

        $infoOutputCount = \substr_count($output, '[INFO]');

        self::assertEquals(Command::SUCCESS, $result);
        self::assertEquals(1, $infoOutputCount);
    }

    public function testExecuteReturnsSuccessInStandardMode(): void
    {
        $commandTester = $this->getCommandTester();

        $result = $commandTester->execute([
            'command' => MusementSynchronizationCommand::NAME,
            '--dry-run' => false,
        ]);

        $output = $commandTester->getDisplay();

        $infoOutputCount = \substr_count($output, '[INFO]');

        self::assertEquals(Command::SUCCESS, $result);
        self::assertEquals(1, $infoOutputCount);
    }

    private function getCommandTester(): CommandTester
    {
        $application = new Application();
        $application->add($this->command);

        $command = $application->find(MusementSynchronizationCommand::NAME);
        return new CommandTester($command);
    }
}
