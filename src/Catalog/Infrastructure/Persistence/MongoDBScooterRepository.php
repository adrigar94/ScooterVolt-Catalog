<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence;

use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use Psr\Log\LoggerInterface;
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
        $andConditions = [];

        $coords = null;
        $max_km = null;

        foreach ($criteria->filters() as $filter) {
            $field = $filter->field();
            $operator = $filter->operator()->value();
            $value = $filter->value();

            if ($field === 'search') {
                //TODO fuzzy search
                $query['$text']['$search'] = $value;
                continue;
            }

            if ($field === 'coords') {
                $coords = explode(',', $value);
                continue;
            }
            if ($field === 'max_km') {
                $max_km = $value;
                continue;
            }

            switch ($operator) {
                case FilterOperator::EQUAL:
                    if (is_numeric($value))
                        $query[$field][] = ['$eq' => (int) $value];
                    else
                        $query[$field][] = ['$regex' => $value, '$options' => 'i'];

                    if (!in_array($field, $andConditions))
                        $andConditions[] = $field;
                    break;
                case FilterOperator::NOT_EQUAL:
                    $query[$field][]['$ne'] = $value;
                    if (!in_array($field, $andConditions))
                        $andConditions[] = $field;
                    break;
                case FilterOperator::GT:
                    $query[$field][]['$gte'] = (int) $value;
                    if (!in_array($field, $andConditions))
                        $andConditions[] = $field;
                    break;
                case FilterOperator::LT:
                    $query[$field][]['$lte'] = (int) $value;
                    if (!in_array($field, $andConditions))
                        $andConditions[] = $field;
                    break;
                case FilterOperator::CONTAINS:
                    $query[$field][] = ['$regex' => $value, '$options' => 'i'];
                    if (!in_array($field, $andConditions))
                        $andConditions[] = $field;
                    break;
                case FilterOperator::NOT_CONTAINS:
                    $query[$field][] = ['$regex' => "^((?!$value).)*$", '$options' => 'i'];
                    if (!in_array($field, $andConditions))
                        $andConditions[] = $field;
                    break;
                default:
                    break;
            }
        }

        foreach ($andConditions as $field) {
            $and = [];
            foreach ($query[$field] as $filter) {
                $and[][$field] = $filter;
            }
            $separatedFilters = $this->separateFiltersGTnLTofOthers($and);
            $query['$and'] = array_merge(array_key_exists('$and', $query) ? $query['$and'] : [], $separatedFilters['gtLtArray']);
            if ($separatedFilters['restoArray']) {
                $query['$and'][]['$or'] = $separatedFilters['restoArray'];
            }
            unset($query[$field]);
        }

        if (!isset($query['$and'])) {
            unset($query['$and']);
        }

        if ($coords) {
            $query['$and'][] = [
                'location.coords' => [
                    '$near' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [
                                (float) $coords[1],
                                (float) $coords[0]
                            ]
                        ],
                        '$minDistance' => 0,
                        '$maxDistance' => $max_km ? $max_km * 1000 : 20000,
                    ]
                ]
            ];
        }

        //dd(json_encode($query));

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

    private function separateFiltersGTnLTofOthers(array $filters): array
    {
        $gtLtArray = [];
        $restoArray = [];

        foreach ($filters as $filtro) {
            $operador = key($filtro[key($filtro)]);
            $campo = key($filtro);
            if (in_array($operador, ['$gte', '$lte', '$gt', '$lt'])) {
                $gtLtArray[] = $filtro;
            } else {
                $restoArray[] = $filtro;
            }
        }
        return ['gtLtArray' => $gtLtArray, 'restoArray' => $restoArray];
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
        $this->collection->createIndex(["location.coords" => "2dsphere"]);
        $this->collection->createIndex(["id" => 1]);
        $this->collection->createIndex(["url" => 1]);
    }
}