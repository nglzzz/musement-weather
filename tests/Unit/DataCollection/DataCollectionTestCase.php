<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataCollection;

use App\DataCollection\DataCollection;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

abstract class DataCollectionTestCase extends TestCase
{
    protected ObjectProphecy $repository;
    protected DataCollection $dataCollection;

    public function testGetAllFromRepository(): void
    {

        $data = [
            Argument::type('object'),
            Argument::type('object'),
        ];

        // Get from repository. Should be called only once
        $this->repository->findAll()->shouldBeCalledOnce()->willReturn($data);
        $result = $this->dataCollection->getAll();

        self::assertEquals(new ArrayCollection($data), $result);

        // The second run we get data from object cache
        $result = $this->dataCollection->getAll();
        self::assertEquals(new ArrayCollection($data), $result);
    }

    public function testAdd(): void
    {
        $item = Argument::type('object');

        $this->repository->findAll()->willReturn([]);

        self::assertEmpty($this->dataCollection->getAll());
        $this->dataCollection->add($item);
        self::assertCount(1, $this->dataCollection->getAll());
    }

    public function testRemove(): void
    {
        $item = Argument::type('object');
        $item2 = Argument::type('object');
        $this->repository->findAll()->willReturn([$item, $item2]);

        self::assertCount(2, $this->dataCollection->getAll());

        $this->dataCollection->remove($item);

        self::assertCount(1, $this->dataCollection->getAll());
    }
}
