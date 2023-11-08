<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use ScooterVolt\CatalogService\Catalog\Application\Delete\ScooterDeleteService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation as NOA;

#[Route('/api/catalog/scooter/{id}', name: 'scooter_delete', methods: ['DELETE'])]
#[OA\Tag("Catalog")]
#[OA\Response(
    response: JsonResponse::HTTP_OK,
    description: "Scooters Found",
    content: new OA\JsonContent(
        ref: new NOA\Model(
            type: ScooterDTO::class
        )
    )
)]
class ScooterDeleteController
{
    public function __construct(private readonly ScooterDeleteService $DeleteService)
    {
    }

    public function __invoke(string $id): Response
    {
        $adId = new AdId($id);
        ($this->DeleteService)($adId);

        return new JsonResponse("Scooter $id deleted", JsonResponse::HTTP_OK);
    }
}
