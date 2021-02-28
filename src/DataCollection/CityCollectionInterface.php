<?php

declare(strict_types=1);

namespace App\DataCollection;

use App\Entity\City;
use Doctrine\Common\Collections\Collection;

interface CityCollectionInterface
{
    public function find(string $code): ?City;
    public function getAll(): Collection;
}
