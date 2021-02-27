<?php

declare(strict_types=1);

namespace App\DataCollection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

abstract class DataCollection
{
    protected EntityRepository $repository;
    protected ArrayCollection $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function getAll(): Collection
    {
        if (!$this->collection->isEmpty()) {
            return $this->collection;
        }

        foreach ($this->repository->findAll() as $country) {
            $this->collection->add($country);
        }

        return $this->collection;
    }

    abstract public function find($searchColumn);

    public function add($item): self
    {
        $this->collection->add($item);

        return $this;
    }

    public function remove($element): self
    {
        $this->collection->removeElement($element);

        return $this;
    }
}
