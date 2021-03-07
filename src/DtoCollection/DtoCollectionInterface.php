<?php

namespace App\DtoCollection;

interface DtoCollectionInterface
{
    public function add($city): self;
    public function remove($city): self;
}
