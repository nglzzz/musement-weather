<?php

declare(strict_types=1);

namespace App\Handler\Api\City;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Service\CityService;

class CityListHandler
{
    private CityRepository $repository;
    private CityService $cityService;

    public function __construct(CityRepository $repository, CityService $cityService)
    {
        $this->repository = $repository;
        $this->cityService = $cityService;
    }

    public function handle(): array
    {
        return \array_map(
            fn (City $city): array => $this->cityService->normalizeCity($city),
            $this->repository->findAll(),
        );
    }
}
