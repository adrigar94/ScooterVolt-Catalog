<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence\MongoDBScooterRepository;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterBrandMother;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScooterUpsertControllerTest extends WebTestCase
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

    public function testUpsert(): void
    {
        $scooter = ScooterMother::random();

        $this->setAuthToken($this->client, $scooter->getUserContactInfo()->getEmail(), ['ROLE_USER'], $scooter->getUserId()->value());

        $id = $scooter->getId()->value();

        $this->client->request('PUT', "/api/catalog/scooter/$id", [], [], [], json_encode($scooter->toNative(), JSON_THROW_ON_ERROR));
        $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();

        $adId = new AdId($id);
        $newScooter = $this->repository->findById($adId);
        $this->assertNotNull($newScooter);

        $this->assertEquals(
            $scooter->getId()->value(),
            $newScooter->getId()->value()
        );

        $this->assertEquals(
            $scooter->getTitle(),
            $newScooter->getTitle()
        );
    }

    public function testUpsertModify(): void
    {
        $scooter = ScooterMother::random();

        $this->setAuthToken($this->client, $scooter->getUserContactInfo()->getEmail(), ['ROLE_USER'], $scooter->getUserId()->value());

        $id = $scooter->getId()->value();

        $this->client->request('PUT', "/api/catalog/scooter/$id", [], [], [], json_encode($scooter->toNative(), JSON_THROW_ON_ERROR));
        $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();

        $adId = new AdId($id);
        $newScooter = $this->repository->findById($adId);
        $this->assertNotNull($newScooter);

        $this->assertEquals(
            $scooter->getId()->value(),
            $newScooter->getId()->value()
        );

        $this->assertEquals(
            $scooter->getTitle(),
            $newScooter->getTitle()
        );

        // Modify Scooter

        $scooter->setBrand(ScooterBrandMother::random());
        $id = $scooter->getId()->value();

        $this->client->request('PUT', "/api/catalog/scooter/$id", [], [], [], json_encode($scooter->toNative(), JSON_THROW_ON_ERROR));
        $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();

        $adId = new AdId($id);
        $newScooter = $this->repository->findById($adId);
        $this->assertNotNull($newScooter);

        $this->assertEquals(
            $scooter->getBrand()->value(),
            $newScooter->getBrand()->value()
        );

        $this->assertEquals(
            $scooter->getTitle(),
            $newScooter->getTitle()
        );
    }

    public function testUpsertUnauthenticated(): void
    {
        $scooter = ScooterMother::random();
        $id = $scooter->getId()->value();

        $this->client->request('PUT', "/api/catalog/scooter/$id", [], [], [], json_encode($scooter->toNative(), JSON_THROW_ON_ERROR));
        $this->client->getResponse()->getContent();
        $this->assertResponseStatusCodeSame(401);
    }

    private function setUpDatabase()
    {
        $path_json = __DIR__ . '/scooters.json';
        $this->repository->deleteAndImportDatabase($path_json);
    }
}
