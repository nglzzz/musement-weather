<?php

declare(strict_types=1);

namespace App\Dal\CityList;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusementCityList implements CityListInterface
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $musementApiClient, LoggerInterface $logger)
    {
        $this->httpClient = $musementApiClient;
        $this->logger = $logger;
    }

    public function getAll(): array
    {
        try {
            $response = $this->httpClient->request('GET', '/api/v3/cities');

            return $this->prepareResultData($response->toArray());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException('Invalid request for getting musement cities.');
        }
    }

    private function prepareResultData(array $responseData): array
    {
        return \array_map(fn (array $item) => [
            'sourceId' => (int) $item['id'],
            'name' => (string) $item['name'],
            'code' => (string) $item['code'],
            'latitude' => (float) $item['latitude'],
            'longitude' => (float) $item['longitude'],
            'country' => $item['country'],
            'time_zone' => $item['time_zone'],
        ], $responseData);
    }
}
