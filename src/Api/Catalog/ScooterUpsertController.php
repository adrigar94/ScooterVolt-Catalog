<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation as NOA;
use ScooterVolt\CatalogService\Catalog\Application\Upsert\ScooterUpsertService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/catalog/scooter/{id}', name: 'scooter_upsert', methods: ['PUT'])]
#[OA\Tag("Catalog")]
#[OA\RequestBody(content: new OA\JsonContent(
    type: "object",
    ref: new NOA\Model(
        type: ScooterDTO::class
    )
)
)]
#[OA\Response(
    response: JsonResponse::HTTP_CREATED,
    description: "Scooter Upsert"
)]
class ScooterUpsertController
{
    public function __construct(private readonly ScooterUpsertService $upsertService)
    {
    }

    public function __invoke(Request $request, string $id): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $scooterDTO = new ScooterDTO(
            $id,
            $data['url'],
            $data['created_at'],
            $data['updated_at'],
            $data['status'],
            $data['user_id'],
            $data['user_contact_info'],
            $data['brand'] ?? null,
            $data['model'] ?? null,
            $data['price'] ?? null,
            $data['location'] ?? null,
            $data['gallery'] ?? null,
            $data['year'] ?? null,
            $data['condition'] ?? null,
            $data['travel_range'] ?? null,
            $data['max_speed'] ?? null,
            $data['power'] ?? null,
            $data['description'] ?? null
        );


        ($this->upsertService)($scooterDTO);

        return new JsonResponse("Scooter Upsert", JsonResponse::HTTP_CREATED);
    }
}