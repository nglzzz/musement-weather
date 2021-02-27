<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountiesFixtures extends Fixture
{
    public const REFERENCE_PREFIX = 'country';

    private const COUNTRY_LIST = [
        [
            'name' => 'Netherlands',
            'isoCode' => 'NL',
        ],
        [
            'name' => 'France',
            'isoCode' => 'FR',
        ],
        [
            'name' => 'Italy',
            'isoCode' => 'IT',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::COUNTRY_LIST as $countryItem) {
            $country = new Country($countryItem['name'], $countryItem['isoCode']);

            $manager->persist($country);

            $this->addReference(
                \sprintf('%s_%s', self::REFERENCE_PREFIX, $countryItem['isoCode']),
                $country,
            );
        }

        $manager->flush();
    }
}
