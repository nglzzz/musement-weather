<?php

namespace App\CitySynchronization;

use Psr\Log\LoggerInterface;

interface CitySynchronization
{
    public function process(LoggerInterface $logger, bool $dryRun = false): array;
}
