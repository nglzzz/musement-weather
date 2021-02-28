<?php

declare(strict_types=1);

namespace App\Synchronization\City;

use App\Dal\CityList\CityListInterface;
use App\DataCollection\CityCollection;
use App\DataCollection\CityCollectionInterface;
use App\DataCollection\CountryCollection;
use App\DataCollection\CountryCollectionInterface;
use App\Entity\City;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MusementCitySynchronization implements CitySynchronization
{
    private const BATCH_SIZE = 25;

    private EntityManagerInterface $em;
    private CityListInterface $cityGetter;
    /** @var CityCollection|CityCollectionInterface */
    private CityCollectionInterface $cityCollection;
    /** @var CountryCollection|CountryCollectionInterface */
    private CountryCollectionInterface $countryCollection;

    public function __construct(
        EntityManagerInterface $em,
        CityListInterface $cityGetter,
        CityCollectionInterface $cityCollection,
        CountryCollectionInterface $countryCollection
    ) {
        $this->em = $em;
        $this->cityGetter = $cityGetter;
        $this->cityCollection = $cityCollection;
        $this->countryCollection = $countryCollection;
    }

    public function process(LoggerInterface $logger, bool $dryRun = false): array
    {
        $logger->info($dryRun ? 'Running in dry mode' : 'Synchronization started normally');

        $cities = $this->cityGetter->getAll();

        $logger->info(\sprintf('Musement API returned %d cities', \count($cities)));
        $logger->info(\sprintf('In the database we have %s cities', $this->cityCollection->getAll()->count()));
        $logger->info(\sprintf('In the database we have %s countries', $this->countryCollection->getAll()->count()));

        $batches = \array_chunk($cities, self::BATCH_SIZE);

        $createdCities = $updatedCities = $createdCountries = $skippedCities = 0;

        foreach ($batches as $batch) {
            $batchResult = $this->processBatch($batch, $logger);

            if (!$dryRun) {
                $this->em->flush();

                $createdCities += $batchResult['createdCities'];
                $updatedCities += $batchResult['updatedCities'];
                $createdCountries += $batchResult['createdCountries'];
            } else {
                $skippedCities += $batchResult['createdCities'] + $batchResult['updatedCities'];
            }
        }

        return [
            'createdCities' => $createdCities,
            'updatedCities' => $updatedCities,
            'createdCountries' => $createdCountries,
            'skippedCities' => $skippedCities,
        ];
    }

    private function processBatch(array $batch, LoggerInterface $logger): array
    {
        $createdCities = $updatedCities = $createdCountries = 0;

        foreach ($batch as $cityItem) {
            if (($country = $this->countryCollection->find($cityItem['country']['iso_code'])) === null) {
                $country = new Country(
                    $cityItem['country']['name'],
                    $cityItem['country']['iso_code']
                );
                $this->countryCollection->add($country);

                ++$createdCountries;

                $logger->info(\sprintf(
                    'New country for saving in the database: %s (%s)',
                    $cityItem['country']['name'],
                    $cityItem['country']['iso_code'],
                ));
            } else {
                $logger->debug(\sprintf(
                    'Country to update: %s (%s)',
                    $cityItem['name'],
                    $cityItem['code'],
                ));
                $country->setName($cityItem['country']['name']);
                $country->setIsoCode($cityItem['country']['iso_code']);
            }

            // Update or create if exists
            if (($city = $this->cityCollection->find($cityItem['code'])) === null) {
                $city = new City($cityItem['name'], $cityItem['code'], $country);
                $this->cityCollection->add($city);

                ++$createdCities;

                $logger->info(\sprintf(
                    'New city for saving in the database: %s (%s)',
                    $cityItem['name'],
                    $cityItem['code'],
                ));
            } else {
                ++$updatedCities;

                $logger->debug(\sprintf(
                    'City to update: %s (%s)',
                    $cityItem['name'],
                    $cityItem['code'],
                ));
            }

            $city->setLatitude($cityItem['latitude']);
            $city->setLongitude($cityItem['longitude']);
            $city->setSourceId($cityItem['sourceId']);

            $this->em->persist($city);
        }

        return [
            'createdCities' => $createdCities,
            'updatedCities' => $updatedCities,
            'createdCountries' => $createdCountries,
        ];
    }
}
