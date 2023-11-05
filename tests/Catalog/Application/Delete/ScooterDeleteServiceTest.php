<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Delete;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Delete\ScooterDeleteService;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindByIdService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterDeleteServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;


    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
    }

    public function testDelete(): void
    {
        $scooter = ScooterMother::randomPublished();
        $this->repositoryMock->expects($this->once())
            ->method('delete');

        $service = new ScooterDeleteService($this->repositoryMock);

        $service->__invoke($scooter->getId());
    }
}
