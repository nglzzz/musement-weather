<?php

declare(strict_types=1);

namespace App\DtoCollection;

use App\Dto\MusementCity;
use Doctrine\Common\Collections\ArrayCollection;

class MusementCityCollection implements \IteratorAggregate, DtoCollectionInterface
{
    private ArrayCollection $list;

    public function __construct(array $list = [])
    {
        $this->list = new ArrayCollection($list);
    }

    public function getIterator(): \Traversable
    {
        return $this->list;
    }

    public function add($city): self
    {
        \assert($city instanceof MusementCity);

        $this->list->add($city);

        return $this;
    }

    public function remove($city): self
    {
        \assert($city instanceof MusementCity);

        $this->list->removeElement($city);

        return $this;
    }

    public function getBatches(int $batchSize): array
    {
        $batches = [];
        $arrayBatches = \array_chunk($this->list->getValues(), $batchSize);

        foreach ($arrayBatches as $arrayBatch) {
            $batches[] = new self($arrayBatch);
        }

        return $batches;
    }

    public function count(): int
    {
        return $this->list->count();
    }
}
