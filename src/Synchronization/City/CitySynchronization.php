<?php

namespace App\Synchronization\City;

use Psr\Log\LoggerInterface;

interface CitySynchronization
{
    public function process(LoggerInterface $logger, bool $dryRun = false): array;
}
