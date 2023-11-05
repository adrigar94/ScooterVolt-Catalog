<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterFindAllControllerTest extends WebTestCase
{
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


    public function testFindById(): void
    {
        $this->client->request('GET', "/api/catalog/scooters");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode($data, true);
        $this->assertIsArray($scooters);
        $this->assertCount(10, $scooters);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}