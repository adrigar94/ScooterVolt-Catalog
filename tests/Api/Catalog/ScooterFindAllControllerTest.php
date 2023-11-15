<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterFindAllControllerTest extends WebTestCase
{
    use AuthenticatedJwt;
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


    public function testFindAll(): void
    {
        $this->setAuthToken($this->client, "john@email.com", ['ROLE_ADMIN']);

        $this->client->request('GET', "/api/catalog/scooters");
        $data = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($data);

        $scooters = json_decode((string) $data, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($scooters);
        $this->assertCount(10, $scooters);
    }

    public function testFindAllUnauthenticated(): void
    {
        $this->client->request('GET', "/api/catalog/scooters");
        $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(401);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}