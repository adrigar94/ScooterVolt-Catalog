<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Infrastructure\Persistence;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MongoDBScooterRepositoryTest extends KernelTestCase
{
    private const INIT_NUM_SCOOTERS = 10;
    private MongoDBScooterRepository $repository;

    protected function setUp(): void
    {
        $this->repository = static::getContainer()->get(MongoDBScooterRepository::class);

        $this->setUpDatabase();
    }

    // TODO test rest of types

    public function testFindAllReturnsScooters(): void
    {
        $scooters = $this->repository->findAll();

        $this->assertIsArray($scooters);
        $this->assertCount(self::INIT_NUM_SCOOTERS, $scooters);
        $this->assertInstanceOf(Scooter::class, $scooters[0]);
    }

    private function setUpDatabase()
    {
        $this->repository->deleteDatabase();

        for ($i = 1; $i <= self::INIT_NUM_SCOOTERS; $i++) {
            $scooter = ScooterMother::random();
            $this->repository->save($scooter);
        }
    }
}
