<?php

declare(strict_types=1);

namespace App\Tests\Unit\Synchronization\City;

use App\Dal\CityList\CityListInterface;
use App\DataCollection\CityCollection;
use App\DataCollection\CountryCollection;
use App\Entity\City;
use App\Entity\Country;
use App\Synchronization\City\MusementCitySynchronization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class MusementCitySynchronizationTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $em;
    private ObjectProphecy $cityList;
    private ObjectProphecy $cityCollection;
    private ObjectProphecy $countryCollection;
    private MusementCitySynchronization $citySynchronization;

    public function setUp(): void
    {
        $this->em             = $this->prophesize(EntityManagerInterface::class);
        $this->cityList       = $this->prophesize(CityListInterface::class);
        $this->cityCollection = $this->prophesize(CityCollection::class);
        $this->countryCollection = $this->prophesize(CountryCollection::class);

        $this->citySynchronization = new MusementCitySynchronization(
            $this->em->reveal(),
            $this->cityList->reveal(),
            $this->cityCollection->reveal(),
            $this->countryCollection->reveal(),
        );
    }

    public function testProcessInDryRunMode(): void
    {
        $logger = $this->givenLogger(true);

        $this->countryCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn(null);
        $this->countryCollection->add(Argument::type(Country::class))->shouldBeCalled();

        $this->cityCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn(null);
        $this->cityCollection->add(Argument::type(City::class))->shouldBeCalled();

        $this->givenCities();
        $this->givenCollections();

        $this->em->persist(Argument::type(City::class))->shouldBeCalled();
        $this->em->flush()->shouldNotBeCalled();

        $result = $this->citySynchronization->process($logger->reveal(), true);

        self::assertArrayHasKey('createdCities', $result);
        self::assertArrayHasKey('updatedCities', $result);
        self::assertArrayHasKey('createdCountries', $result);
        self::assertArrayHasKey('skippedCities', $result);
        self::assertEquals(\count($this->getCitiesArray()), $result['skippedCities']);
    }

    public function testProcessInNonDryMode(): void
    {
        $logger = $this->givenLogger();

        $this->countryCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn(null);
        $this->countryCollection->add(Argument::type(Country::class))->shouldBeCalled();

        $this->cityCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn(null);
        $this->cityCollection->add(Argument::type(City::class))->shouldBeCalled();

        $this->givenCities();
        $this->givenCollections();

        $this->em->persist(Argument::type(City::class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $result = $this->citySynchronization->process($logger->reveal(), false);

        self::assertArrayHasKey('createdCities', $result);
        self::assertArrayHasKey('updatedCities', $result);
        self::assertArrayHasKey('createdCountries', $result);
        self::assertArrayHasKey('skippedCities', $result);

        self::assertEquals(\count($this->getCitiesArray()), $result['createdCities']);
        self::assertEquals(0, $result['skippedCities']);
    }

    public function testProcessInNonDryModeWhenInTheDatabaseExist(): void
    {
        $logger = $this->givenLogger();

        $existedCountry = $this->prophesize(Country::class);
        $existedCountry->setName(Argument::type('string'))->shouldBeCalledOnce();
        $existedCountry->setIsoCode(Argument::type('string'))->shouldBeCalledOnce();

        $this->countryCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn($existedCountry);
        $logger->debug('Country to update: Netherlands (NL)');

        $existedCity = $this->prophesize(City::class);
        $existedCity->setLatitude(Argument::type('float'))->shouldBeCalledOnce();
        $existedCity->setLongitude(Argument::type('float'))->shouldBeCalledOnce();
        $existedCity->setSourceId(Argument::type('int'))->shouldBeCalledOnce();
        $this->cityCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn($existedCity);

        $this->givenCities([$this->getCitiesArray()[0]]);
        $this->givenCollections([$existedCountry], [$existedCity]);

        $this->em->persist($existedCity)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $result = $this->citySynchronization->process($logger->reveal(), false);

        self::assertArrayHasKey('createdCities', $result);
        self::assertArrayHasKey('updatedCities', $result);
        self::assertArrayHasKey('createdCountries', $result);
        self::assertArrayHasKey('skippedCities', $result);

        self::assertEquals(0, $result['createdCities']);
        self::assertEquals(0, $result['createdCountries']);
        self::assertEquals(1, $result['updatedCities']);
        self::assertEquals(0, $result['skippedCities']);
    }

    private function givenLogger(bool $dryRun = false): ObjectProphecy
    {
        /** @var LoggerInterface|ObjectProphecy $logger */
        $logger = $this->prophesize(LoggerInterface::class);

        $logger->info($dryRun ? 'Running in dry mode' : 'Synchronization started normally');
        $logger->info(\sprintf('Musement API returned % cities', \count($this->getCitiesArray())));
        $logger->info('In the database we have 0 cities');
        $logger->info('In the database we have 0 countries');

        return $logger;
    }

    private function givenCities(array $cities = []): void
    {
        $this->cityList->getAll()->shouldBeCalledOnce()->willReturn($cities ?: $this->getCitiesArray());
    }

    private function givenCollections(array $cities = [], array $countries = []): void
    {
        $this->cityCollection->getAll()->willReturn(new ArrayCollection($cities));
        $this->countryCollection->getAll()->willReturn(new ArrayCollection($countries));
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
                'country' => [
                    'name' => 'Netherlands',
                    'iso_code' => 'NL',
                ],
                'createdAt' => \date('c'),
                'updatedAt' => \date('c'),
            ],
            [
                'name' => 'Paris',
                'code' => 'paris',
                'sourceId' => 40,
                'latitude' => 48.866,
                'longitude' => 2.355,
                'country' => [
                    'name' => 'France',
                    'iso_code' => 'FR',
                ],
                'createdAt' => \date('c'),
                'updatedAt' => \date('c'),
            ],
            [
                'name' => 'Rome',
                'code' => 'rome',
                'sourceId' => 2,
                'latitude' => 41.898,
                'longitude' => 12.483,
                'country' => [
                    'name' => 'Italy',
                    'iso_code' => 'IT',
                ],
                'createdAt' => \date('c'),
                'updatedAt' => \date('c'),
            ],
        ];
    }
}
