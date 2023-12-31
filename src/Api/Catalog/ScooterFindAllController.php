<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use Nelmio\ApiDocBundle\Annotation as NOA;
use OpenApi\Attributes as OA;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindAllService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog/scooters', name: 'scooters_find_all', methods: ['GET'])]
#[OA\Tag('Catalog')]
#[OA\Get(description: 'Returns all scooters (only admins)')]
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
class ScooterFindAllController
{
    public function __construct(
        private readonly ScooterFindAllService $finder
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $scooters = ($this->finder)();

        return new JsonResponse($scooters, JsonResponse::HTTP_OK);
    }
}
