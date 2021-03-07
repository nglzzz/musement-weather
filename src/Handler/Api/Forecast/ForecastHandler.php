<?php

declare(strict_types=1);

namespace App\Handler\Api\Forecast;

use App\Dal\Forecast\WeatherApiForecast;
use App\Repository\CityRepository;
use App\Dto\CityForecast;

class ForecastHandler
{
    private WeatherApiForecast $weatherApiForecast;
    private CityRepository $repository;

    public function __construct(WeatherApiForecast $weatherApiForecast, CityRepository $repository)
    {
        $this->weatherApiForecast = $weatherApiForecast;
        $this->repository = $repository;
    }

    public function handle(CityForecast $forecastRequestData): array
    {
        $city = $forecastRequestData->getCity();
        $days = $forecastRequestData->getDays();

        if (null !== $city) {
            return $this->weatherApiForecast->getForCity($city, $days);
        }

        return $this->weatherApiForecast->getForCities($this->repository->findAll(), $days);
    }
}
