<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataCollection;

use App\DataCollection\CountryCollection;
use App\Entity\Country;
use App\Repository\CountryRepository;
use Prophecy\PhpUnit\ProphecyTrait;

class CountryCollectionTest extends DataCollectionTestCase
{
    use ProphecyTrait;

    public function setUp(): void
    {
        $this->repository = $this->prophesize(CountryRepository::class);

        $this->dataCollection = new CountryCollection($this->repository->reveal());
    }

    public function testFindReturnsNullWhenCollectionEmpty(): void
    {
        $code = 'code';

        $result = $this->dataCollection->find($code);

        self::assertEquals(null, $result);
    }

    public function testFindReturnsNullWhenCollectionDoesNotHaveCity(): void
    {
        $code = 'FR';

        $existedCountry = $this->prophesize(Country::class);
        $existedCountry->getIsoCode()->willReturn('BY');

        $this->repository->findAll()->willReturn([
            $existedCountry->reveal(),
        ]);

        $this->dataCollection->getAll();

        $result = $this->dataCollection->find($code);
        self::assertEquals(null, $result);
    }

    public function testFindReturnsCity(): void
    {
        $code = 'FR';

        $existedCountry = $this->prophesize(Country::class);
        $existedCountry->getIsoCode()->willReturn('FR');

        $this->repository->findAll()->willReturn([
            $existedCountry->reveal(),
        ]);

        $this->dataCollection->getAll();

        $result = $this->dataCollection->find($code);
        self::assertEquals($existedCountry->reveal(), $result);
    }
}
