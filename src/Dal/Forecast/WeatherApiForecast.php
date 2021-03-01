<?php

declare(strict_types=1);

namespace App\Dal\Forecast;

use App\Entity\City;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WeatherApiForecast implements ForecastInterface
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $weatherApiClient, LoggerInterface $logger)
    {
        $this->httpClient = $weatherApiClient;
        $this->logger = $logger;
    }

    public function getForCity(City $city, int $days): array
    {
        try {
            $response = $this->makeCityRequest($city, $days);

            return [$this->prepareResultData($response->toArray())];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException('Invalid request for getting forecast in weatherapi.');
        }
    }

    public function getForCities(array $cities, int $days): array
    {
        $responses = [];

        foreach ($cities as $city) {
            $responses[] = $this->makeCityRequest($city, $days);
        }

        $data = [];

        // Async concurrent requests
        $stream = $this->httpClient->stream($responses, 0);

        foreach ($stream as $response => $chunk) {
            try {
                $data[] = $this->prepareResultData($response->toArray());
            } catch (\Exception $e) {
                $this->logger->error('Cannot to get forecast from weatherapi', [
                    'exception' => $e,
                    'response' => $response,
                ]);
            }
        }

        return $data;
    }

    private function makeCityRequest(City $city, int $days): ResponseInterface
    {
        return $this->httpClient->request('GET', 'v1/forecast.json', [
            'query' => [
                'q' => $city->getName(),
                'days' => $days,
                'aqi' => 'no',
                'alerts' => 'no',
            ],
        ]);
    }

    private function prepareResultData(array $responseData): array
    {
        $data = [
            'location' => $responseData['location']['name'],
            'forecast' => [],
        ];

        foreach ($responseData['forecast']['forecastday'] as $forecastDay) {
            $date = $forecastDay['date'];

            $data['forecast'][$date] = $forecastDay['day']['condition']['text'];
        }

        return $data;
    }
}
