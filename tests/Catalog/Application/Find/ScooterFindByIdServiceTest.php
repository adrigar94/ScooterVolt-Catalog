<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Find;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindByIdService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterFindByIdServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;


    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
    }

    public function testFindById(): void
    {
        $scooter = ScooterMother::randomPublished();
        $this->repositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn($scooter);

        $service = new ScooterFindByIdService($this->repositoryMock);

        $result = $service->__invoke($scooter->getId());

        $this->assertSame($scooter, $result);
    }
}
