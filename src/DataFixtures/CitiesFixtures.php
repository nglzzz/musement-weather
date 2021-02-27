<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CitiesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getCities() as $data) {
            /** @var Country $country */
            $country = $this->getReference($data['countryReference']);

            $city = new City($data['name'], $data['code'], $country);
            $city->setSourceId($data['sourceId']);
            $city->setLatitude($data['latitude']);
            $city->setLongitude($data['longitude']);

            $manager->persist($city);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CountiesFixtures::class,
        ];
    }

    private function getCities(): array
    {
        return [
            [
                'name' => 'Amsterdam',
                'code' => 'amsterdam',
                'sourceId' => 1,
                'latitude' => 52.374,
                'longitude' => 4.9,
                'countryReference' => \sprintf('%s_%s', CountiesFixtures::REFERENCE_PREFIX, 'NL'),
            ],
            [
                'name' => 'Paris',
                'code' => 'paris',
                'sourceId' => 2,
                'latitude' => 48.866,
                'longitude' => 2.355,
                'countryReference' => \sprintf('%s_%s', CountiesFixtures::REFERENCE_PREFIX, 'FR'),
            ],
            [
                'name' => 'Rome',
                'code' => 'rome',
                'sourceId' => 3,
                'latitude' => 41.898,
                'longitude' => 12.483,
                'countryReference' => \sprintf('%s_%s', CountiesFixtures::REFERENCE_PREFIX, 'IT'),
            ],
        ];
    }
}
