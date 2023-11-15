<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterFindByIdControllerTest extends WebTestCase
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

    public function testFindById(): void
    {
        $id = '60c08215-d243-46d7-b9ff-14d4a4d00d46';
        $this->client->request('GET', "/api/catalog/scooter/$id");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooter = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($id, $scooter['id']);
    }

    public function testFindByIdNotFound(): void
    {
        $id = '77261de2-b236-4b71-8974-d4601908120b';
        $this->client->request('GET', "/api/catalog/scooter/$id");

        $this->assertResponseStatusCodeSame(404);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}
