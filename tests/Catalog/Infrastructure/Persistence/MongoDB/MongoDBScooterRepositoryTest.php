<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Infrastructure\Persistence;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterCondition;
use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Filter;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\FilterOperator;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Order;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\OrderType;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MongoDBScooterRepositoryTest extends KernelTestCase
{
    private MongoDBScooterRepository $repository;

    protected function setUp(): void
    {
        $this->repository = static::getContainer()->get(MongoDBScooterRepository::class);

        $this->setUpDatabase();
    }

    public function testFindAll(): void
    {
        $scooters = $this->repository->findAll();

        $this->assertIsArray($scooters);
        $this->assertCount(5, $scooters);
        $this->assertInstanceOf(Scooter::class, $scooters[0]);
        foreach ($scooters as $scooter) {
            $this->assertEquals("published", $scooter->getStatus()->value());
        }
    }

    public function testSearchByOneField(): void
    {
        $criteria = new Criteria(
            [new Filter("brand", FilterOperator::EQUAL(), "Leuschke-Blanda")],
            []
        );

        $scooters = $this->repository->search($criteria);

        $this->assertCount(1, $scooters);
        $this->assertEquals("Leuschke-Blanda", $scooters[0]->getBrand());
    }

    public function testSearchByTwoFields(): void
    {
        $criteria = new Criteria(
            [
                new Filter("status", FilterOperator::EQUAL(), "published"),
                new Filter("condition", FilterOperator::EQUAL(), "used"),
            ],
            [new Order("price.price", OrderType::DESC())]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertGreaterThanOrEqual(1, count($scooters));
        foreach ($scooters as $scooter) {
            $this->assertEquals("published", $scooter->getStatus()->value());
            $this->assertEquals("used", $scooter->getCondition()->value());
        }
    }

    public function testSearchWithNotEqual(): void
    {
        $criteria = new Criteria(
            [
                new Filter("status", FilterOperator::NOT_EQUAL(), "published"),
            ],
            []
        );

        $scooters = $this->repository->search($criteria);

        $this->assertGreaterThanOrEqual(1, count($scooters));
        foreach ($scooters as $scooter) {
            $this->assertNotEquals("published", $scooter->getStatus()->value());
        }
    }

    public function testSearchWithContains(): void
    {
        $criteria = new Criteria(
            [
                new Filter("model", FilterOperator::CONTAINS(), "scooter"),
            ]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertGreaterThanOrEqual(2, count($scooters));
        foreach ($scooters as $scooter) {
            $this->assertStringContainsStringIgnoringCase("scooter", $scooter->getModel()->value());
        }
    }

    public function testSearchWithNotContains(): void
    {
        $criteria = new Criteria(
            [
                new Filter("model", FilterOperator::NOT_CONTAINS(), "scooter"),
            ]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertGreaterThanOrEqual(2, count($scooters));
        foreach ($scooters as $scooter) {
            $this->assertStringNotContainsStringIgnoringCase("scooter", $scooter->getModel()->value());
        }
    }

    public function testSearchByPriceRange(): void
    {

        $criteria = new Criteria(
            [
                new Filter("price.price", FilterOperator::GT(), 50000),
                new Filter("price.price", FilterOperator::LT(), 70000),
            ]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertGreaterThanOrEqual(1, count($scooters));
        foreach ($scooters as $scooter) {
            $price = $scooter->getPrice()->getPrice();
            $this->assertGreaterThanOrEqual(500, $price);
            $this->assertLessThanOrEqual(700, $price);
        }
    }

    public function testSearchWithoutResults(): void
    {

        $criteria = new Criteria(
            [
                new Filter("price.price", FilterOperator::GT(), 200),
                new Filter("price.price", FilterOperator::LT(), 100),
            ]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertIsArray($scooters);
        $this->assertEmpty($scooters);
    }

    public function testSearchWithPagination(): void
    {
        $sccoterPerPage = 5;
        $criteriaPage1 = new Criteria(
            [],
            [],
            0,
            $sccoterPerPage
        );
        $criteriaPage2 = new Criteria(
            [],
            [],
            5,
            $sccoterPerPage
        );

        $scootersPage1 = $this->repository->search($criteriaPage1);
        $scootersPage2 = $this->repository->search($criteriaPage2);

        $this->assertIsArray($scootersPage1);
        $this->assertIsArray($scootersPage2);
        $this->assertCount($sccoterPerPage, $scootersPage1);
        $this->assertCount($sccoterPerPage, $scootersPage2);

        $scootersPage1Id = [];
        foreach ($scootersPage1 as $scooter) {
            $scootersPage1Id[] = $scooter->getId()->value();
        }
        foreach ($scootersPage2 as $scooter) {
            $this->assertNotContains($scooter->getId()->value(), $scootersPage1Id);
        }
    }

    public function testSearchWithOrderAsc(): void
    {
        $criteria = new Criteria(
            [],
            [new Order("price.price", OrderType::ASC())]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertCount(10, $scooters);
        for ($i = 1; $i < count($scooters); $i++) {
            $prevPrice = $scooters[$i - 1]->getPrice()->getPrice();
            $currentPrice = $scooters[$i]->getPrice()->getPrice();
            $this->assertGreaterThanOrEqual($prevPrice, $currentPrice);
        }
    }

    public function testSearchWithOrderDesc(): void
    {
        $criteria = new Criteria(
            [],
            [new Order("price.price", OrderType::DESC())]
        );

        $scooters = $this->repository->search($criteria);

        $this->assertCount(10, $scooters);
        for ($i = 1; $i < count($scooters); $i++) {
            $prevPrice = $scooters[$i - 1]->getPrice()->getPrice();
            $currentPrice = $scooters[$i]->getPrice()->getPrice();
            $this->assertLessThanOrEqual($prevPrice, $currentPrice);
        }
    }

    public function testFindById(): void
    {
        $adId = AdId::random();
        $foundScooter = $this->repository->findById($adId);
        $this->assertNull($foundScooter);


        $adId = new AdId('60c08215-d243-46d7-b9ff-14d4a4d00d46');
        $foundScooter = $this->repository->findById($adId);

        $this->assertInstanceOf(Scooter::class, $foundScooter);
        $this->assertEquals($adId, $foundScooter->getId());
    }

    public function testFindByUrl(): void
    {
        $url = new AdUrl('not-exists-9999');
        $foundScooter = $this->repository->findByUrl($url);
        $this->assertNull($foundScooter);

        $url = new AdUrl('hamill-similique-64e5bdf686ac3');
        $foundScooter = $this->repository->findByUrl($url);

        $this->assertInstanceOf(Scooter::class, $foundScooter);
        $this->assertEquals('4e009fd0-f03d-4754-a222-495ff88e622f', $foundScooter->getId()->value());
    }

    public function testSaveNew(): void
    {
        $scooter = ScooterMother::random();

        $this->repository->save($scooter);

        $foundScooter = $this->repository->findById($scooter->getId());
        $this->assertInstanceOf(Scooter::class, $foundScooter);
        $this->assertEquals($scooter->getId()->value(), $foundScooter->getId()->value());
    }

    public function testSaveModification(): void
    {
        $adId = new AdId('60c08215-d243-46d7-b9ff-14d4a4d00d46');
        $scooter = $this->repository->findById($adId);
        $scooter->setCondition(ScooterCondition::USED());

        $this->repository->save($scooter);

        $foundScooter = $this->repository->findById($scooter->getId());
        $this->assertInstanceOf(Scooter::class, $foundScooter);
        $this->assertEquals($scooter->getId()->value(), $foundScooter->getId()->value());
        $this->assertEquals($scooter->getCondition()->value(), $foundScooter->getCondition()->value());
    }


    public function testDelete(): void
    {
        $adId = new AdId('60c08215-d243-46d7-b9ff-14d4a4d00d46');
        $foundScooter = $this->repository->findById($adId);
        $this->assertInstanceOf(Scooter::class, $foundScooter);
        $this->assertEquals($adId->value(), $foundScooter->getId()->value());

        $this->repository->delete($adId);
        $foundScooter = $this->repository->findById($adId);
        $this->assertNull($foundScooter);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}
