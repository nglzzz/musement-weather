<?php

declare(strict_types=1);

namespace App\Tests\Unit\MusementApi;

use App\MusementApi\CityGetter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CityGetterTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $httpClient;
    private ObjectProphecy $logger;
    private CityGetter $cityGetter;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->cityGetter = new CityGetter($this->httpClient->reveal(), $this->logger->reveal());
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

        $response->toArray()->willReturn(['some data']);

        $this->cityGetter->getAll();
    }
}
