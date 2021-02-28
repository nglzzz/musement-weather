<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\Api\V3\CityController;
use App\Entity\City;
use App\Handler\Api\City\CityGetterHandler;
use App\Handler\Api\City\CityListHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CityControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $logger;
    private ObjectProphecy $container;
    private CityController $controller;

    public function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);

        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->controller = new CityController($this->logger->reveal());
        $this->controller->setContainer($this->container->reveal());
    }

    public function testGetCitiesReturnsServerErrorWhenHandleThrowsException(): void
    {
        $handler = $this->prophesize(CityListHandler::class);

        $handler->handle()->shouldBeCalledOnce()->willThrow(\Exception::class);

        $this->logger->error(Argument::type('string'), Argument::type('array'));

        $response = $this->controller->getListAction($handler->reveal());

        self::assertEquals(500, $response->getStatusCode());
    }

    public function testGetCitiesWithSuccessfulResult(): void
    {
        $handler = $this->prophesize(CityListHandler::class);

        $cities = $this->getCitiesArray();
        $handler->handle()->willReturn($cities);

        $response = $this->controller->getListAction($handler->reveal());

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($cities, \json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testGetCityReturnsServerErrorWhenHandleThrowsError(): void
    {
        $city = $this->prophesize(City::class);
        $handler = $this->prophesize(CityGetterHandler::class);

        $handler->handle($city)->shouldBeCalledOnce()->willThrow(\Exception::class);
        $this->logger->error(Argument::type('string'), Argument::type('array'));

        $response = $this->controller->getCityAction($city->reveal(), $handler->reveal());

        self::assertEquals(500, $response->getStatusCode());
    }

    public function testGetCityReturnsCorrectCityData(): void
    {
        $city = $this->prophesize(City::class);
        $handler = $this->prophesize(CityGetterHandler::class);

        $cityResponseData = $this->getCitiesArray()[0];

        $handler->handle($city)->willReturn($cityResponseData);

        $response = $this->controller->getCityAction($city->reveal(), $handler->reveal());

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($cityResponseData, \json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    private function getCitiesArray(): array
    {
        return [
            [
                'name' => 'Amsterdam',
                'code' => 'amsterdam',
                'sourceId' => 57,
                'latitude' => 52.374,
                'longitude' => 4.9,
                'createdAt' => \date('c'),
                'updatedAt' => \date('c'),
            ],
            [
                'name' => 'Paris',
                'code' => 'paris',
                'sourceId' => 40,
                'latitude' => 48.866,
                'longitude' => 2.355,
                'createdAt' => \date('c'),
                'updatedAt' => \date('c'),
            ],
            [
                'name' => 'Rome',
                'code' => 'rome',
                'sourceId' => 2,
                'latitude' => 41.898,
                'longitude' => 12.483,
                'createdAt' => \date('c'),
                'updatedAt' => \date('c'),
            ],
        ];
    }
}
