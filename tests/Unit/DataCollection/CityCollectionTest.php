<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataCollection;

use App\DataCollection\CityCollection;
use App\DataCollection\DataCollection;
use App\Entity\City;
use App\Repository\CityRepository;
use Prophecy\PhpUnit\ProphecyTrait;

class CityCollectionTest extends DataCollectionTestCase
{
    use ProphecyTrait;

    public function setUp(): void
    {
        $this->repository = $this->prophesize(CityRepository::class);

        $this->dataCollection = new CityCollection($this->repository->reveal());
    }

    public function testFindReturnsNullWhenCollectionEmpty(): void
    {
        $code = 'code';

        $result = $this->dataCollection->find($code);

        self::assertNull($result);
    }

    public function testFindReturnsNullWhenCollectionDoesNotHaveCity(): void
    {
        $code = 'amsterdam';

        $existedCity = $this->prophesize(City::class);
        $existedCity->getCode()->willReturn('paris');

        $this->repository->findAll()->willReturn([
            $existedCity->reveal(),
        ]);

        $this->dataCollection->getAll();

        $result = $this->dataCollection->find($code);
        self::assertEquals(null, $result);
    }

    public function testFindReturnsCity(): void
    {
        $code = 'amsterdam';

        $existedCity = $this->prophesize(City::class);
        $existedCity->getCode()->willReturn('amsterdam');

        $this->repository->findAll()->willReturn([
            $existedCity->reveal(),
        ]);

        $this->dataCollection->getAll();

        $result = $this->dataCollection->find($code);
        self::assertEquals($existedCity->reveal(), $result);
    }
}
