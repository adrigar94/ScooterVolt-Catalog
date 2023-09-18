<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation as NOA;
use Psr\Log\LoggerInterface;
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
#[OA\Tag("Catalog")]
#[OA\Parameter(
    name: "search",
    in: "query",
    description: "Search in any text field of Scooter",
    schema: new OA\Schema(type: "string")
)]
#[OA\Parameter(
    name: "brand",
    in: "query",
    description: "Brand of Scooter",
    schema: new OA\Schema(
        type: "array",
        items: new OA\Items(
            type: "string"
        )
    )
)]
#[OA\Parameter(
    name: "model",
    in: "query",
    description: "Model of Scooter",
    schema: new OA\Schema(
        type: "array",
        items: new OA\Items(
            type: "string"
        )
    )
)]
#[OA\Parameter(
    name: "condition",
    in: "query",
    description: "Contition of Scooter [new, used, broken]",
    schema: new OA\Schema(
        type: "array",
        items: new OA\Items(
            type: "string"
        )
    )
)]
#[OA\Parameter(
    name: "status[]",
    in: "query",
    description: "Status of Scooter [draft, published, sold] default=published",
    schema: new OA\Schema(
        type: "array",
        items: new OA\Items(
            type: "string"
        )
    )
)]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: "Scooters Found",
    content: new OA\JsonContent(
        type: "array",
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
        'search', 'brand', 'model', 'condition', 'status'
    ];

    public function __construct(
        private ScooterSearchService $searcher
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $data = $request->query->all();

        $filters = [
            //new Filter('status', FilterOperator::EQUAL(), 'published')
        ];
        foreach ($data as $param => $value_filter) {
            if (in_array($param, self::FILTERS_ALLOWED)) {
                if (is_array($value_filter)) {
                    foreach ($value_filter as $value) {
                        $filters[] = new Filter($param, FilterOperator::EQUAL(), $value);
                    }
                    continue;
                }
                $filters[] = new Filter($param, FilterOperator::EQUAL(), $value_filter);
            }
        }

        $criteria = new Criteria($filters);

        $scooters = ($this->searcher)($criteria);

        return new JsonResponse($scooters, JsonResponse::HTTP_OK);
    }
}
