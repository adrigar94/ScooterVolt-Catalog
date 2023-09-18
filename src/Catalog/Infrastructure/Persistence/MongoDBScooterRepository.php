<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence;

use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\FilterOperator;
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
        $this->createIndex();
    }


    /**
     * @return Scooter[]
     */
    public function findAll(): array
    {
        $cursor = $this->collection->find(['status' => 'published']);
        $scooters = [];

        foreach ($cursor as $document) {
            $scooters[] = $this->createScooterFromDocument($document);
        }

        return $scooters;
    }

    public function search(Criteria $criteria): array
    {
        $query = [];

        foreach ($criteria->filters() as $filter) {
            $field = $filter->field();
            $operator = $filter->operator()->value();
            $value = $filter->value();

            if ($field === 'search') {
                //TODO fuzzy search
                $query['$text']['$search'] = $value;
                continue;
            }

            switch ($operator) {
                case FilterOperator::EQUAL:
                    $query[$field] = ['$regex' => $value, '$options' => 'i'];

                    if (is_numeric($value))
                        $query[$field] = ['$eq' => (int)$value];
                    break;
                case FilterOperator::NOT_EQUAL:
                    $query[$field]['$ne'] = $value;
                    break;
                case FilterOperator::GT:
                    $query[$field]['$gt'] = $value;
                    break;
                case FilterOperator::LT:
                    $query[$field]['$lt'] = $value;
                    break;
                case FilterOperator::CONTAINS:
                    $query[$field] = ['$regex' => $value, '$options' => 'i'];
                    break;
                case FilterOperator::NOT_CONTAINS:
                    $query[$field] = ['$regex' => "^((?!$value).)*$", '$options' => 'i'];
                    break;
                default:
                    break;
            }
        }
        $options = [];

        if ($criteria->hasOrder()) {
            foreach ($criteria->order() as $order) {
                $options['sort'][$order->orderBy()] = $order->orderType()->value() === 'desc' ? -1 : 1;
            }
        }

        if ($criteria->hasLimit()) {
            $options['limit'] = $criteria->limit();
        }

        if ($criteria->hasOffset()) {
            $options['skip'] = $criteria->offset();
        }

        $cursor = $this->collection->find($query, $options);

        $scooters = [];

        foreach ($cursor as $document) {
            $scooters[] = $this->createScooterFromDocument($document);
        }

        return $scooters;
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
        $json = json_encode($document->jsonSerialize());
        return Scooter::fromNative(json_decode($json, true));
    }

    public function deleteAndImportDatabase(string $path_json): void
    {
        $this->deleteDatabase();
        $this->collection->insertMany(json_decode(file_get_contents($path_json), true));
        $this->createIndex();
    }

    private function deleteDatabase(): void
    {
        $this->db->drop();
    }

    private function createIndex(): void
    {
        $this->collection->createIndex(["brand" => "text", "model" => "text", "description" => "text", "condition" => "text"]);
    }
}
