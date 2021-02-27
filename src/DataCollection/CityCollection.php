<?php

declare(strict_types=1);

namespace App\DataCollection;

use App\Entity\City;
use App\Repository\CityRepository;

class CityCollection extends DataCollection
{
    public function __construct(CityRepository $cityRepository)
    {
        parent::__construct();

        $this->repository = $cityRepository;
    }

    public function find($code): ?City
    {
        $data = $this->collection->filter(fn (City $city) => $code === $city->getCode());

        return !$data->isEmpty() ? $data->first() : null;
    }
}
