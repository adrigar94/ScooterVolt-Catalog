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

    private readonly Database $db;
    private readonly Collection $collection;

    public function __construct(MongoDBConnection $connection)
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
        $cursor = $this->collection->find([
            'status' => 'published',
        ]);
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

        $currency = null;

        foreach ($criteria->filters() as $filter) {
            $field = $filter->field();
            $operator = $filter->operator()->value();
            $value = $filter->value();

            if ('search' === $field) {
                // TODO fuzzy search
                $query['$text']['$search'] = $value;
                continue;
            }

            if ('coords' === $field) {
                $coords = explode(',', $value);
                continue;
            }
            if ('max_km' === $field) {
                $max_km = $value;
                continue;
            }

            if ('currency' === $field) {
                $currency = $value;
                continue;
            }

            if ('price' === $field) {
                $value = (int) ($value * 100);
            }

            switch ($operator) {
                case FilterOperator::EQUAL:
                    $query[$field][] = is_numeric($value) ? [
                        '$eq' => (int) $value,
                    ] : [
                        '$regex' => $value,
                        '$options' => 'i',
                    ];

                    if (! in_array($field, $andConditions)) {
                        $andConditions[] = $field;
                    }
                    break;
                case FilterOperator::NOT_EQUAL:
                    $query[$field][]['$ne'] = $value;
                    if (! in_array($field, $andConditions)) {
                        $andConditions[] = $field;
                    }
                    break;
                case FilterOperator::GT:
                    $query[$field][]['$gte'] = (int) $value;
                    if (! in_array($field, $andConditions)) {
                        $andConditions[] = $field;
                    }
                    break;
                case FilterOperator::LT:
                    $query[$field][]['$lte'] = (int) $value;
                    if (! in_array($field, $andConditions)) {
                        $andConditions[] = $field;
                    }
                    break;
                case FilterOperator::CONTAINS:
                    $query[$field][] = [
                        '$regex' => $value,
                        '$options' => 'i',
                    ];
                    if (! in_array($field, $andConditions)) {
                        $andConditions[] = $field;
                    }
                    break;
                case FilterOperator::NOT_CONTAINS:
                    $query[$field][] = [
                        '$regex' => "^((?!$value).)*$",
                        '$options' => 'i',
                    ];
                    if (! in_array($field, $andConditions)) {
                        $andConditions[] = $field;
                    }
                    break;
                default:
                    break;
            }
        }

        foreach ($andConditions as $field) {
            $and = [];
            if ('price' === $field) {
                $newName = 'price.price_conversions.' . strtoupper($currency);
                $query[$newName] = $query[$field];
                unset($query[$field]);
                $field = $newName;
            }
            foreach ($query[$field] as $filter) {
                $and[][$field] = $filter;
            }
            $separatedFilters = $this->separateFiltersGTnLTofOthers($and);
            $query['$and'] = array_merge(array_key_exists('$and', $query) ? $query['$and'] : [], $separatedFilters['gtLtArray']);
            if ($separatedFilters['restArray']) {
                $query['$and'][]['$or'] = $separatedFilters['restArray'];
            }
            unset($query[$field]);
        }

        if (! isset($query['$and'])) {
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
                                (float) $coords[0],
                            ],
                        ],
                        '$minDistance' => 0,
                        '$maxDistance' => $max_km ? $max_km * 1000 : 20000,
                    ],
                ],
            ];
        }

        // dd(json_encode($query));

        $options = [];
        if ($criteria->hasOrder()) {
            foreach ($criteria->order() as $order) {
                $options['sort'][$order->orderBy()] = 'desc' === $order->orderType()->value() ? -1 : 1;
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
        $restArray = [];

        foreach ($filters as $filtro) {
            $operador = key($filtro[key($filtro)]);
            if (in_array($operador, ['$gte', '$lte', '$gt', '$lt'])) {
                $gtLtArray[] = $filtro;
            } else {
                $restArray[] = $filtro;
            }
        }

        return [
            'gtLtArray' => $gtLtArray,
            'restArray' => $restArray,
        ];
    }

    public function findById(AdId $id): ?Scooter
    {
        $document = $this->collection->findOne([
            'id' => $id->toNative(),
        ]);

        return $document ? $this->createScooterFromDocument($document) : null;
    }

    public function findByUrl(AdUrl $url): ?Scooter
    {
        $document = $this->collection->findOne([
            'url' => $url->toNative(),
        ]);

        return $document ? $this->createScooterFromDocument($document) : null;
    }

    public function save(Scooter $scooter): void
    {
        $document = $scooter->toNative();
        if (isset($document['location']['coords']['coordinates'])) {
            $lat = $document['location']['coords']['coordinates'][0];
            $long = $document['location']['coords']['coordinates'][1];
            $document['location']['coords']['coordinates'] = [$long, $lat];
        }
        $this->collection->updateOne(
            [
                'id' => $scooter->getId()->toNative(),
            ],
            [
                '$set' => $document,
            ],
            [
                'upsert' => true,
            ]
        );
    }

    public function delete(AdId $id): void
    {
        $this->collection->deleteOne([
            'id' => $id->toNative(),
        ]);
    }

    private function createScooterFromDocument(BSONDocument $document): Scooter
    {
        $json = json_encode($document->jsonSerialize(), JSON_THROW_ON_ERROR);
        $json = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (isset($json['location']['coords']['coordinates'])) {
            $lat = $json['location']['coords']['coordinates'][1];
            $long = $json['location']['coords']['coordinates'][0];
            $json['location']['coords']['coordinates'] = [$lat, $long];
        }

        return Scooter::fromNative($json);
    }

    public function deleteAndImportDatabase(string $path_json): void
    {
        $this->deleteDatabase();
        $this->collection->insertMany(json_decode(file_get_contents($path_json), true, 512, JSON_THROW_ON_ERROR));
        $this->createIndex();
    }

    private function deleteDatabase(): void
    {
        $this->db->drop();
    }

    private function createIndex(): void
    {
        $this->collection->createIndex([
            'brand' => 'text',
            'model' => 'text',
            'description' => 'text',
            'condition' => 'text',
        ]);
        $this->collection->createIndex([
            'location.coords' => '2dsphere',
        ]);
        $this->collection->createIndex([
            'id' => 1,
        ]);
        $this->collection->createIndex([
            'url' => 1,
        ]);
    }
}
