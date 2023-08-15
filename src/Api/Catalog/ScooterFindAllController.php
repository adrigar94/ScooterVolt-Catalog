<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindAllService;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/catalog', name: 'catalog_find_all', methods: ['GET'])]
#[OA\Tag("Catalog")]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: "Ads Found",
    content: new OA\JsonContent(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                // new OA\Property(property: "id", type: "string", example: "062714f2-3916-4924-81fc-5ef985d19f5d"),
                // new OA\Property(property: "fullname", type: "json", example: "{\"name\":\"John\",\"surname\":\"Doe\"}"),
                // new OA\Property(property: "email", type: "string", example: "john@email.com"),
                // new OA\Property(property: "created_at", type: "string", format: "date-time"),
                // new OA\Property(property: "updated_at", type: "string", format: "date-time"),
            ]
        )
    )
)]
class ScooterFindAllController
{
    public function __construct(private ScooterFindAllService $finder)
    {
    }

    public function __invoke(Request $request): Response
    {
        $ads = ($this->finder)();

        $responseData = [];
        foreach ($ads as $ad) {
            $responseData[] = [
                // TODO
            ];
        }

        return new JsonResponse($responseData, JsonResponse::HTTP_OK);
    }
}
