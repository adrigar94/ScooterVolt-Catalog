<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterDeleteControllerTest extends WebTestCase
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


    public function testDelete(): void
    {
        $this->setAuthToken($this->client, "john@email.com", ['ROLE_USER']);
        $id = '60c08215-d243-46d7-b9ff-14d4a4d00d46';


        $adId = new AdId($id);
        $this->assertNotNull($this->repository->findById($adId));


        $this->client->request('DELETE', "/api/catalog/scooter/$id");
        $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();


        $adId = new AdId($id);
        $this->assertNull($this->repository->findById($adId));
    }

    public function testDeleteUnauthorized(): void
    {
        $id = '60c08215-d243-46d7-b9ff-14d4a4d00d46';

        $adId = new AdId($id);
        $this->assertNotNull($this->repository->findById($adId));


        $this->client->request('DELETE', "/api/catalog/scooter/$id");
        $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(401);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}