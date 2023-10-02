<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Upsert;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Upsert\ScooterUpsertService;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\EventBus;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterUpsertServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;
    private EventBus|MockObject $eventBus;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
        $this->eventBus = $this->createMock(EventBus::class);
    }

    public function testScooterUpsertService(): void
    {
        $scooterDTO = ScooterMother::randomScooterDTO();

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Scooter::class));

        $this->eventBus->expects($this->once())
            ->method('publish');

        $service = new ScooterUpsertService($this->repositoryMock, $this->eventBus);

        $service->__invoke($scooterDTO);
    }
}