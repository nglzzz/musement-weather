<?php

declare(strict_types=1);

namespace App\Dal\CityList;

use App\Dto\MusementCity;
use App\DtoCollection\MusementCityCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusementCityList implements CityListInterface
{
    private const GET_CITY_URI = '/api/v3/cities';

    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $musementApiClient, LoggerInterface $logger)
    {
        $this->httpClient = $musementApiClient;
        $this->logger = $logger;
    }

    public function getAll(): MusementCityCollection
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, self::GET_CITY_URI);

            return $this->prepareResultData($response->toArray());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException('Invalid request for getting musement cities.');
        }
    }

    private function prepareResultData(array $responseData): MusementCityCollection
    {
        $collection = new MusementCityCollection();

        foreach ($responseData as $item) {
            $musementCity = new MusementCity();
            $musementCity->setSourceId((int) $item['id']);
            $musementCity->setName($item['name']);
            $musementCity->setCode($item['code']);
            $musementCity->setLatitude((float) $item['latitude']);
            $musementCity->setLongitude((float) $item['longitude']);
            $musementCity->setCountryName($item['country']['name']);
            $musementCity->setCountryCode($item['country']['iso_code']);
            $musementCity->setTimeZone($item['time_zone']);
            $musementCity->setWeight((int) $item['weight']);

            $collection->add($musementCity);
        }

        return $collection;
    }
}
