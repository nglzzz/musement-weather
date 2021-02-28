<?php

declare(strict_types=1);

namespace App\Tests\Unit\Handler\Api\City;

use App\Entity\City;
use App\Handler\Api\City\CityGetterHandler;
use App\Service\CityService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CityGetterHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $cityService;
    private CityGetterHandler $handler;

    public function setUp(): void
    {
        $this->cityService = $this->prophesize(CityService::class);

        $this->handler = new CityGetterHandler($this->cityService->reveal());
    }

    public function testHandleReturnsArray(): void
    {
        $city = $this->prophesize(City::class);

        $cityArray = [
            'name' => 'Amsterdam',
            'code' => 'amsterdam',
            'sourceId' => 57,
            'latitude' => 52.374,
            'longitude' => 4.9,
            'createdAt' => \date('c'),
            'updatedAt' => \date('c'),
        ];

        $this->cityService->normalizeCity($city, ['Default'])->shouldBeCalledOnce()->willReturn($cityArray);

        $result = $this->handler->handle($city->reveal());

        self::assertEquals($cityArray, $result);
    }
}
