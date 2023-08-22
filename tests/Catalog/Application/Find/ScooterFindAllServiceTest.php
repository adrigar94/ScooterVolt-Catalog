<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Find;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindAllService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterFindAllServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;


    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
    }

    public function testFindAllReturnsScooters(): void
    {
        $scooters = [
            ScooterMother::randomPublished(),
            ScooterMother::randomPublished(),
        ];
        $this->repositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($scooters);

        $service = new ScooterFindAllService($this->repositoryMock);

        $result = $service->__invoke();

        $this->assertSame($scooters, $result);
    }
}
