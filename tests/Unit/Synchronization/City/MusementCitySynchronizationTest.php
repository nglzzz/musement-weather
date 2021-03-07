<?php

declare(strict_types=1);

namespace App\Tests\Unit\Synchronization\City;

use App\Dal\CityList\CityListInterface;
use App\DataCollection\CityCollection;
use App\DataCollection\CountryCollection;
use App\Dto\MusementCity;
use App\DtoCollection\MusementCityCollection;
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

        $this->givenCities($this->getCitiesList());
        $this->givenCollections();

        $this->em->persist(Argument::type(City::class))->shouldBeCalled();
        $this->em->flush()->shouldNotBeCalled();

        $result = $this->citySynchronization->process($logger->reveal(), true);

        self::assertArrayHasKey('createdCities', $result);
        self::assertArrayHasKey('updatedCities', $result);
        self::assertArrayHasKey('createdCountries', $result);
        self::assertArrayHasKey('skippedCities', $result);
        self::assertEquals(\count($this->getCitiesList()), $result['skippedCities']);
    }

    public function testProcessInNonDryMode(): void
    {
        $logger = $this->givenLogger();

        $this->countryCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn(null);
        $this->countryCollection->add(Argument::type(Country::class))->shouldBeCalled();

        $this->cityCollection->find(Argument::type('string'))->shouldBeCalled()->willReturn(null);
        $this->cityCollection->add(Argument::type(City::class))->shouldBeCalled();

        $this->givenCities($this->getCitiesList());
        $this->givenCollections();

        $this->em->persist(Argument::type(City::class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $result = $this->citySynchronization->process($logger->reveal(), false);

        self::assertArrayHasKey('createdCities', $result);
        self::assertArrayHasKey('updatedCities', $result);
        self::assertArrayHasKey('createdCountries', $result);
        self::assertArrayHasKey('skippedCities', $result);

        self::assertEquals(\count($this->getCitiesList()), $result['createdCities']);
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

        $this->givenCities([$this->getCitiesList()[0]]);
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
        $logger->info(\sprintf('Musement API returned % cities', \count($this->getCitiesList())));
        $logger->info('In the database we have 0 cities');
        $logger->info('In the database we have 0 countries');

        return $logger;
    }

    private function givenCities(array $cities = []): void
    {
        $collection = $this->prophesize(MusementCityCollection::class);
        $collection->count()->willReturn(\count($cities));

        $batchedCollection = new MusementCityCollection($cities);
        $collection->getBatches(25)->willReturn([
            $batchedCollection,
        ]);

        $this->cityList->getAll()->shouldBeCalledOnce()->willReturn($collection->reveal());
    }

    private function givenCollections(array $cities = [], array $countries = []): void
    {
        $this->cityCollection->getAll()->willReturn(new ArrayCollection($cities));
        $this->countryCollection->getAll()->willReturn(new ArrayCollection($countries));
    }

    private function getCitiesList(): array
    {
        $city1 = $this->prophesize(MusementCity::class);
        $city1->getName()->willReturn('Amsterdam');
        $city1->getCode()->willReturn('amsterdam');
        $city1->getSourceId()->willReturn(57);
        $city1->getLatitude()->willReturn(52.374);
        $city1->getLongitude()->willReturn(4.9);
        $city1->getCountryName()->willReturn('Netherlands');
        $city1->getCountryCode()->willReturn('NL');

        $city2 = $this->prophesize(MusementCity::class);
        $city2->getName()->willReturn('Paris');
        $city2->getCode()->willReturn('paris');
        $city2->getSourceId()->willReturn(40);
        $city2->getLatitude()->willReturn(48.866);
        $city2->getLongitude()->willReturn(2.355);
        $city2->getCountryName()->willReturn('France');
        $city2->getCountryCode()->willReturn('FR');

        $city3 = $this->prophesize(MusementCity::class);
        $city3->getName()->willReturn('Rome');
        $city3->getCode()->willReturn('rome');
        $city3->getSourceId()->willReturn(2);
        $city3->getLatitude()->willReturn(41.898);
        $city3->getLongitude()->willReturn(12.483);
        $city3->getCountryName()->willReturn('Italy');
        $city3->getCountryCode()->willReturn('IT');

        return [
            $city1->reveal(),
            $city2->reveal(),
            $city3->reveal(),
        ];
    }
}
