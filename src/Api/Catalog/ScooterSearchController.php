<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use Nelmio\ApiDocBundle\Annotation as NOA;
use OpenApi\Attributes as OA;
use ScooterVolt\CatalogService\Catalog\Application\Search\ScooterSearchService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Filter;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\FilterOperator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog/scooters/search', name: 'scooters_search', methods: ['GET'])]
#[OA\Tag('Catalog')]
#[OA\Parameter(
    name: 'search',
    in: 'query',
    description: 'Search in any text field of Scooter',
    schema: new OA\Schema(type: 'string')
)]
#[OA\Parameter(
    name: 'brand[]',
    in: 'query',
    description: 'Brand of Scooter',
    schema: new OA\Schema(
        type: 'array',
        items: new OA\Items(
            type: 'string'
        )
    )
)]
#[OA\Parameter(
    name: 'model[]',
    in: 'query',
    description: 'Model of Scooter',
    schema: new OA\Schema(
        type: 'array',
        items: new OA\Items(
            type: 'string'
        )
    )
)]
#[OA\Parameter(
    name: 'condition[]',
    in: 'query',
    description: 'Contition of Scooter',
    schema: new OA\Schema(
        type: 'array',
        items: new OA\Items(
            type: 'string'
        )
    ),
    example: ['new', 'used', 'broken']
)]
#[OA\Parameter(
    name: 'status[]',
    in: 'query',
    description: 'Status of Scooter default=published',
    schema: new OA\Schema(
        type: 'array',
        items: new OA\Items(
            type: 'string',
        ),
    ),
    example: ['draft', 'published', 'sold']
)]
#[OA\Parameter(
    name: 'year_gt',
    in: 'query',
    description: 'Year greater than',
    schema: new OA\Schema(type: 'integer'),
    example: 2000
)]
#[OA\Parameter(
    name: 'year_lt',
    in: 'query',
    description: 'Year less than',
    schema: new OA\Schema(type: 'integer'),
    example: 2023
)]
#[OA\Parameter(
    name: 'price_gt',
    in: 'query',
    description: 'Price greater than',
    schema: new OA\Schema(type: 'float'),
    example: 100
)]
#[OA\Parameter(
    name: 'price_lt',
    in: 'query',
    description: 'Price less than',
    schema: new OA\Schema(type: 'float'),
    example: 200
)]
#[OA\Parameter(
    name: 'currency',
    in: 'query',
    description: 'Currency code of price',
    schema: new OA\Schema(type: 'string'),
    example: 'EUR'
)]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: 'Scooters Found',
    content: new OA\JsonContent(
        type: 'array',
        items: new OA\Items(
            ref: new NOA\Model(
                type: ScooterDTO::class
            ),
        )
    )
)]
class ScooterSearchController
{
    private const FILTERS_ALLOWED = [
        'search',
        'brand',
        'model',
        'condition',
        'status',
        'year_gt',
        'year_lt',
        'max_speed_gt',
        'max_speed_lt',
        'power_gt',
        'power_lt',
        'travel_range_gt',
        'travel_range_lt',
        'coords',
        'max_km',
        'price_gt',
        'price_lt',
        'currency',
    ];

    private const FILTERS_OPERATOR_MAPPING = [
        'year_gt' => FilterOperator::GT,
        'year_lt' => FilterOperator::LT,
        'max_speed_gt' => FilterOperator::GT,
        'max_speed_lt' => FilterOperator::LT,
        'power_gt' => FilterOperator::GT,
        'power_lt' => FilterOperator::LT,
        'travel_range_gt' => FilterOperator::GT,
        'travel_range_lt' => FilterOperator::LT,
        'price_gt' => FilterOperator::GT,
        'price_lt' => FilterOperator::LT,
    ];

    private const FILTERS_NAME_MAPPING = [
        'year_gt' => 'year',
        'year_lt' => 'year',
        'max_speed_gt' => 'max_speed',
        'max_speed_lt' => 'max_speed',
        'power_gt' => 'power',
        'power_lt' => 'power',
        'travel_range_gt' => 'travel_range',
        'travel_range_lt' => 'travel_range',
        'price_gt' => 'price',
        'price_lt' => 'price',
    ];

    public function __construct(
        private readonly ScooterSearchService $searcher
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $filters = $this->getFilters($request);

        $criteria = new Criteria($filters);

        $scooters = ($this->searcher)($criteria);

        return new JsonResponse($scooters, JsonResponse::HTTP_OK);
    }

    private function getFilters(Request $request): array
    {
        $data = $request->query->all();

        $filters = [];
        foreach ($data as $param => $value_filter) {
            if (in_array($param, self::FILTERS_ALLOWED)) {
                if (is_array($value_filter)) {
                    foreach ($value_filter as $value) {
                        $filters[] = new Filter($this->getNameFilterMapping($param), $this->getOperatorMapping($param), $value);
                    }
                    continue;
                }
                $filters[] = new Filter($this->getNameFilterMapping($param), $this->getOperatorMapping($param), $value_filter);
            }
        }

        return $filters;
    }

    private function getOperatorMapping(string $filter): FilterOperator
    {
        if (array_key_exists($filter, self::FILTERS_OPERATOR_MAPPING)) {
            return new FilterOperator(self::FILTERS_OPERATOR_MAPPING[$filter]);
        }

        return FilterOperator::EQUAL();
    }

    private function getNameFilterMapping(string $filter): string
    {
        if (array_key_exists($filter, self::FILTERS_NAME_MAPPING)) {
            return self::FILTERS_NAME_MAPPING[$filter];
        }

        return $filter;
    }
}
