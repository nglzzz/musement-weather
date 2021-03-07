<?php

namespace App\Dal\CityList;

use App\DtoCollection\MusementCityCollection;

interface CityListInterface
{
    public function getAll(): MusementCityCollection;
}
