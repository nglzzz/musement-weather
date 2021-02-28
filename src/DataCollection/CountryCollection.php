<?php

declare(strict_types=1);

namespace App\DataCollection;

use App\Entity\Country;
use App\Repository\CountryRepository;

class CountryCollection extends DataCollection implements CountryCollectionInterface
{
    public function __construct(CountryRepository $countryRepository)
    {
        parent::__construct($countryRepository);
    }

    public function find(string $isoCode): ?Country
    {
        $data = $this->collection->filter(fn (Country $country) => $isoCode === $country->getIsoCode());

        return !$data->isEmpty() ? $data->first() : null;
    }
}
