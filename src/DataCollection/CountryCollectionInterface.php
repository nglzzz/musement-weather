<?php

namespace App\DataCollection;

use App\Entity\Country;
use Doctrine\Common\Collections\Collection;

interface CountryCollectionInterface
{
    public function find(string $isoCode): ?Country;
    public function getAll(): Collection;
}
