<?php

declare(strict_types=1);

namespace App\Handler\Api\City;

use App\Entity\City;
use App\Service\CityService;

class CityGetterHandler
{
    private const CITY_DATA_GROUPS = ['Default'];

    private CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function handle(City $city): array
    {
        return $this->cityService->normalizeCity($city, self::CITY_DATA_GROUPS);
    }
}
