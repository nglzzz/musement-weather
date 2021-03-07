<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dal\CityList;

use App\Dal\CityList\MusementCityList;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MusementCityListTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $httpClient;
    private ObjectProphecy $logger;
    private MusementCityList $cityGetter;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->cityGetter = new MusementCityList($this->httpClient->reveal(), $this->logger->reveal());
    }

    public function testGetAllThrowsRuntimeExceptionWhenRequestThrowsException(): void
    {
        $this->httpClient->request('GET', '/api/v3/cities')->shouldBeCalledOnce()->willThrow(\Exception::class);
        $this->logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalledOnce();

        $this->expectException(\RuntimeException::class);

        $this->cityGetter->getAll();
    }

    public function testGetAllThrowsRuntimeExceptionWHenToArrayThrowsException(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $this->httpClient->request('GET', '/api/v3/cities')->shouldBeCalledOnce()->willReturn($response->reveal());
        $this->logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalledOnce();

        $response->toArray()->willThrow(\Exception::class);

        $this->expectException(\RuntimeException::class);

        $this->cityGetter->getAll();
    }

    public function testGetAllWithSuccessfulResult(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $this->httpClient->request('GET', '/api/v3/cities')->shouldBeCalledOnce()->willReturn($response->reveal());
        $this->logger->error(Argument::type('string'), Argument::type('array'))->shouldNotBeCalled();

        $response->toArray()->willReturn([[
            'id' => 1,
            'name' => 'City name',
            'code' => 'city_code',
            'latitude' => 1.0,
            'longitude' => 2.0,
            'country' => [
                'name' => 'Country name',
                'iso_code' => 'AA',
            ],
            'time_zone' => 'Euroupe/Amsterdam',
            'weight' => 1,
        ]]);

        $this->cityGetter->getAll();
    }
}
