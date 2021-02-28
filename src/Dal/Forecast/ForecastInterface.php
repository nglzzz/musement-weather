<?php

namespace App\Dal\Forecast;

use App\Entity\City;

interface ForecastInterface
{
    public function getForCity(City $city, int $days): array;

    public function getForCities(array $cities, int $days): array;
}
