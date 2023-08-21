<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence;

use DateTimeImmutable;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;
use ScooterVolt\CatalogService\Shared\Infrastructure\Persistence\MongoDB\MongoDBConnection;

final class MongoDBScooterRepository implements ScooterRepository
{
    private const COLLECTION_NAME = 'scooters';

    private Database $db;
    private Collection $collection;

    public function __construct(private MongoDBConnection $connection)
    {
        $this->db = $connection->getDatabase();
        $this->collection = $this->db->selectCollection(self::COLLECTION_NAME);
    }


    /**
     * @return Scooter[]
     */
    public function findAll(): array
    {
        $cursor = $this->collection->find();
        $scooters = [];

        foreach ($cursor as $document) {
            $scooters[] = $this->createScooterFromDocument($document);
        }

        return $scooters;
    }

    public function search(Criteria $criteria): array
    {
        //TODO
        return [];
    }

    public function findById(AdId $id): ?Scooter
    {
        $document = $this->collection->findOne(['id' => $id->toNative()]);
        return $document ? $this->createScooterFromDocument($document) : null;
    }

    public function findByUrl(AdUrl $url): ?Scooter
    {
        $document = $this->collection->findOne(['url' => $url->toNative()]);
        return $document ? $this->createScooterFromDocument($document) : null;
    }

    public function save(Scooter $scooter): void
    {
        $document = $scooter->toNative();

        $this->collection->updateOne(
            ['id' => $scooter->getId()->toNative()],
            ['$set' => $document],
            ['upsert' => true]
        );
    }

    public function delete(AdId $id): void
    {
        $this->collection->deleteOne(['id' => $id->toNative()]);
    }

    private function createScooterFromDocument(BSONDocument $document): Scooter
    {
        return Scooter::fromNative($document->getArrayCopy());
    }
}
