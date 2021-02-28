<?php

declare(strict_types=1);

namespace App\Tests\Unit\Handler\Api\City;

use App\Entity\City;
use App\Handler\Api\City\CityListHandler;
use App\Repository\CityRepository;
use App\Service\CityService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CityListHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $repository;
    private ObjectProphecy $service;
    private CityListHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->prophesize(CityRepository::class);
        $this->service = $this->prophesize(CityService::class);

        $this->handler = new CityListHandler($this->repository->reveal(), $this->service->reveal());
    }

    public function testHandle(): void
    {
        $citiesData = $this->getCities();

        $this->repository->findAll()->shouldBeCalledOnce()->willReturn($citiesData['raw']);

        foreach ($citiesData['raw'] as $key => $city) {
            $this->service
                ->normalizeCity($city)
                ->shouldBeCalledOnce()
                ->willReturn($citiesData['serialized'][$key]);
        }

        $result = $this->handler->handle();

        self::assertEquals($citiesData['serialized'], $result);
    }

    private function getCities(): array
    {
        return [
            'raw' => [
                $this->prophesize(City::class),
                $this->prophesize(City::class),
                $this->prophesize(City::class),
            ],
            'serialized' => [
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
            ],
        ];
    }
}
