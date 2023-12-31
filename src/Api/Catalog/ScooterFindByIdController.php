<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use Nelmio\ApiDocBundle\Annotation as NOA;
use OpenApi\Attributes as OA;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindByIdService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog/scooter/{id}', name: 'scooter_find_by_id', methods: ['GET'])]
#[OA\Tag('Catalog')]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: 'Scooters Found',
    content: new OA\JsonContent(
        ref: new NOA\Model(
            type: ScooterDTO::class
        )
    )
)]
class ScooterFindByIdController
{
    public function __construct(
        private readonly ScooterFindByIdService $findByIdService
    ) {
    }

    public function __invoke(string $id): Response
    {
        $adId = new AdId($id);
        $scooter = ($this->findByIdService)($adId);

        if ($scooter instanceof \ScooterVolt\CatalogService\Catalog\Domain\Scooter) {
            return new JsonResponse($scooter, JsonResponse::HTTP_OK);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
    }
}
