<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Find;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindByUrlService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterFindByUrlServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
    }

    public function testFindByUrl(): void
    {
        $scooter = ScooterMother::randomPublished();
        $this->repositoryMock->expects($this->once())
            ->method('findByUrl')
            ->willReturn($scooter);

        $service = new ScooterFindByUrlService($this->repositoryMock);

        $result = $service->__invoke($scooter->getUrl());

        $this->assertSame($scooter, $result);
    }
}
