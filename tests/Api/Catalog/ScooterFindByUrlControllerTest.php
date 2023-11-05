<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterFindByUrlControllerTest extends WebTestCase
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


    public function testFindByUrl(): void
    {
        $url = 'xiaomi-scooter-323ce288';
        $this->client->request('GET', "/api/catalog/scooters/url/$url");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooter = json_decode($data, true);
        $this->assertSame($url, $scooter['url']);
    }

    public function testFindByUrlNotFound(): void
    {
        $url = 'xiaomi-not-found';
        $this->client->request('GET', "/api/catalog/scooters/$url");

        $this->assertResponseStatusCodeSame(404);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}