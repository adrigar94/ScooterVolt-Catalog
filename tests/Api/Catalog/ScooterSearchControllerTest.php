<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterSearchControllerTest extends WebTestCase
{
    private MongoDBScooterRepository $repository;

    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        $this->repository = static::getContainer()->get(MongoDBScooterRepository::class);
        $this->setUpDatabase();
    }

    public function testSearchWithoutParams(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(10, $scooters);
    }

    public function testSearchByText(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?search=xiaomi');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(3, $scooters);
    }

    public function testSearchByBrand(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?brand[]=xiaomi');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('brand', $scooter);
            $this->assertEqualsIgnoringCase('xiaomi', $scooter['brand']);
        }
    }

    public function testSearchByModel(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?model[]=scooter');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('model', $scooter);
            $this->assertStringContainsStringIgnoringCase('scooter', $scooter['model']);
        }
    }

    public function testSearchByCondition(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?condition[]=new');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(4, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('condition', $scooter);
            $this->assertEquals('new', $scooter['condition']);
        }
    }

    public function testSearchByStatus(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?status[]=draft');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('status', $scooter);
            $this->assertEquals('draft', $scooter['status']);
        }
    }

    public function testSearchByStatuses(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?status[]=draft&status[]=sold');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(3, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('status', $scooter);
            $this->assertContains($scooter['status'], ['draft', 'sold']);
        }
    }

    public function testSearchByStatusesAndCondition(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?status[]=draft&status[]=sold&condition[]=used');
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('status', $scooter);
            $this->assertContains($scooter['status'], ['draft', 'sold']);
            $this->assertArrayHasKey('condition', $scooter);
            $this->assertEquals('used', $scooter['condition']);
        }
    }

    public function testSearchByYearGt(): void
    {
        $year_gt = 2010;
        $this->client->request('GET', "/api/catalog/scooters/search?year_gt=$year_gt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(6, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('year', $scooter);
            $this->assertGreaterThanOrEqual($year_gt, $scooter['year']);
        }
    }

    public function testSearchByYearLt(): void
    {
        $year_lt = 2010;
        $this->client->request('GET', "/api/catalog/scooters/search?year_lt=$year_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(5, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('year', $scooter);
            $this->assertLessThanOrEqual($year_lt, $scooter['year']);
        }
    }

    public function testSearchByYearGtAndLt(): void
    {
        $year_gt = 2016;
        $year_lt = 2023;
        $this->client->request('GET', "/api/catalog/scooters/search?year_gt=$year_gt&year_lt=$year_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(3, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('year', $scooter);
            $this->assertLessThanOrEqual($year_lt, $scooter['year']);
            $this->assertGreaterThanOrEqual($year_gt, $scooter['year']);
        }
    }

    public function testSearchByMaxSpeedGt(): void
    {
        $max_speed_gt = 50;
        $this->client->request('GET', "/api/catalog/scooters/search?max_speed_gt=$max_speed_gt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(5, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('max_speed', $scooter);
            $this->assertGreaterThanOrEqual($max_speed_gt, $scooter['max_speed']);
        }
    }

    public function testSearchByMaxSpeedLt(): void
    {
        $max_speed_lt = 50;
        $this->client->request('GET', "/api/catalog/scooters/search?max_speed_lt=$max_speed_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(5, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('max_speed', $scooter);
            $this->assertLessThanOrEqual($max_speed_lt, $scooter['max_speed']);
        }
    }

    public function testSearchByMaxSpeedGtAndLt(): void
    {
        $max_speed_gt = 30;
        $max_speed_lt = 70;
        $this->client->request('GET', "/api/catalog/scooters/search?max_speed_gt=$max_speed_gt&max_speed_lt=$max_speed_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(4, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('max_speed', $scooter);
            $this->assertLessThanOrEqual($max_speed_lt, $scooter['max_speed']);
            $this->assertGreaterThanOrEqual($max_speed_gt, $scooter['max_speed']);
        }
    }

    public function testSearchByPowerGt(): void
    {
        $power_gt = 250000;
        $this->client->request('GET', "/api/catalog/scooters/search?power_gt=$power_gt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(7, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('power', $scooter);
            $this->assertGreaterThanOrEqual($power_gt, $scooter['power']);
        }
    }

    public function testSearchByPowerLt(): void
    {
        $power_lt = 250000;
        $this->client->request('GET', "/api/catalog/scooters/search?power_lt=$power_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(3, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('power', $scooter);
            $this->assertLessThanOrEqual($power_lt, $scooter['power']);
        }
    }

    public function testSearchByPowerGtAndLt(): void
    {
        $power_gt = 50000;
        $power_lt = 200000;
        $this->client->request('GET', "/api/catalog/scooters/search?power_gt=$power_gt&power_lt=$power_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('power', $scooter);
            $this->assertLessThanOrEqual($power_lt, $scooter['power']);
            $this->assertGreaterThanOrEqual($power_gt, $scooter['power']);
        }
    }

    public function testSearchByTravelRangeGt(): void
    {
        $travel_range_gt = 50;
        $this->client->request('GET', "/api/catalog/scooters/search?travel_range_gt=$travel_range_gt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(6, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('travel_range', $scooter);
            $this->assertGreaterThanOrEqual($travel_range_gt, $scooter['travel_range']);
        }
    }

    public function testSearchByTravelRangeLt(): void
    {
        $travel_range_lt = 50;
        $this->client->request('GET', "/api/catalog/scooters/search?travel_range_lt=$travel_range_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(4, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('travel_range', $scooter);
            $this->assertLessThanOrEqual($travel_range_lt, $scooter['travel_range']);
        }
    }

    public function testSearchByTravelRangeGtAndLt(): void
    {
        $travel_range_gt = 30;
        $travel_range_lt = 60;
        $this->client->request('GET', "/api/catalog/scooters/search?travel_range_gt=$travel_range_gt&travel_range_lt=$travel_range_lt");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(5, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('travel_range', $scooter);
            $this->assertLessThanOrEqual($travel_range_lt, $scooter['travel_range']);
            $this->assertGreaterThanOrEqual($travel_range_gt, $scooter['travel_range']);
        }
    }

    public function testSearchByCoords(): void
    {
        $coords = '-6.10,175.15';
        $max_km = '5000';
        $this->client->request('GET', "/api/catalog/scooters/search?coords=$coords&max_km=$max_km");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(4, $scooters);
    }

    public function testSearchByPriceGtConversion(): void
    {
        $price = 400;
        $currency = 'USD';
        $this->client->request('GET', "/api/catalog/scooters/search?price_gt=$price&currency=$currency");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(1, $scooters);

        $this->assertGreaterThanOrEqual($price, $scooters[0]['price']['price_conversions'][$currency] / 100);
    }

    public function testSearchByPriceGtConversionWithoutResults(): void
    {
        $price = 500;
        $currency = 'USD';
        $this->client->request('GET', "/api/catalog/scooters/search?price_gt=$price&currency=$currency");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(0, $scooters);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}
