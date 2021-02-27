<?php

declare(strict_types=1);

namespace App\MusementApi;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CityGetter implements CityGetterInterface
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

            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException('Invalid request for getting musement cities.');
        }
    }
}
