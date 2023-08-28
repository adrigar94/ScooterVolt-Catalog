<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Upsert;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Upsert\ScooterUpsertService;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterUpsertServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
    }

    public function testScooterUpsertService(): void
    {
        $scooterDTO = ScooterMother::randomScooterDTO();

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Scooter::class));

        $service = new ScooterUpsertService($this->repositoryMock);

        $service->__invoke($scooterDTO);
    }
}
