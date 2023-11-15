<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Search;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Search\ScooterSearchService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Filter;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\FilterOperator;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Order;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\OrderType;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;

class ScooterSearchServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
    }

    public function testSearchScooters(): void
    {
        $criteria = new Criteria(
            [
                new Filter('brand', FilterOperator::EQUAL(), 'Leuschke-Blanda'),
                new Filter('status', FilterOperator::EQUAL(), 'published'),
            ],
            [new Order('price', OrderType::ASC())],
            0,
            10
        );

        $expectedScooters = [
            ScooterMother::random(),
            ScooterMother::random(),
        ];

        $this->repositoryMock->expects($this->once())
            ->method('search')
            ->with($criteria)
            ->willReturn($expectedScooters);

        $service = new ScooterSearchService($this->repositoryMock);

        $result = $service->__invoke($criteria);

        $this->assertSame($expectedScooters, $result);
    }
}
