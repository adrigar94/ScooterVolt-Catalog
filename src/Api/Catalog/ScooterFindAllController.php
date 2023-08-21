<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation as NOA;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindAllService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/catalog/scooters', name: 'scooters_find_all', methods: ['GET'])]
#[OA\Tag("Catalog")]
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
class ScooterFindAllController
{
    public function __construct(private ScooterFindAllService $finder)
    {
    }

    public function __invoke(Request $request): Response
    {
        $ads = ($this->finder)();

        return new JsonResponse($ads, JsonResponse::HTTP_OK);
    }
}
