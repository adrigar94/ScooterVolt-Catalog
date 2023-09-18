<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterSearchControllerTest extends WebTestCase
{

    //TODO this tests
    private MongoDBScooterRepository $repository;

    private $client;

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

        $scooters = json_decode($data, true);
        $this->assertIsArray($scooters);
        $this->assertCount(10, $scooters);
    }

    public function testSearchByBrand(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?brand=xiaomi');
        $data = $this->client->getResponse()->getContent();


        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode($data, true);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('brand', $scooter);
            $this->assertEqualsIgnoringCase('xiaomi', $scooter['brand']);
        }
    }

    public function testSearchByModel(): void
    {
        $this->client->request('GET', '/api/catalog/scooters/search?brand=xiaomi');
        $data = $this->client->getResponse()->getContent();


        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode($data, true);
        $this->assertIsArray($scooters);
        $this->assertCount(2, $scooters);

        foreach ($scooters as $scooter) {
            $this->assertArrayHasKey('brand', $scooter);
            $this->assertEqualsIgnoringCase('xiaomi', $scooter['brand']);
        }
    }


    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}
