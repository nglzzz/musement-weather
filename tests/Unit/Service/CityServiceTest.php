<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\City;
use App\Service\CityService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CityServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $serializer;
    private CityService $service;

    public function setUp(): void
    {
        $this->serializer = $this->prophesize(NormalizerInterface::class);

        $this->service = new CityService($this->serializer->reveal());
    }

    public function testNormalizeCity(): void
    {
        $city = $this->prophesize(City::class);

        $this->serializer->normalize($city, null, [
            'groups' => ['some group'],
            'format' => 'long',
        ])->shouldBeCalledOnce()->willReturn([]);

        $result = $this->service->normalizeCity($city->reveal(), ['some group']);

        self::assertEquals([], $result);
    }
}
