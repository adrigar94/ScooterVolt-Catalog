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

#[Route('/api/catalog/scooter/{id}', name: 'catalog_upsert', methods: ['PUT'])]
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
    public function __construct(private ScooterUpsertService $upsertService)
    {
    }

    public function __invoke(Request $request, string $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $scooterDTO = new ScooterDTO(
            $id,
            $data['url'],
            $data['created_at'],
            $data['updated_at'],
            $data['status'],
            $data['user_id'],
            $data['user_contact_info'],
            array_key_exists('brand', $data) ? $data['brand'] : null,
            array_key_exists('model', $data) ? $data['model'] : null,
            array_key_exists('price', $data) ? $data['price'] : null,
            array_key_exists('location', $data) ? $data['location'] : null,
            array_key_exists('gallery', $data) ? $data['gallery'] : null,
            array_key_exists('year', $data) ? $data['year'] : null,
            array_key_exists('condition', $data) ? $data['condition'] : null,
            array_key_exists('travel_range', $data) ? $data['travel_range'] : null,
            array_key_exists('max_speed', $data) ? $data['max_speed'] : null,
            array_key_exists('power', $data) ? $data['power'] : null,
            array_key_exists('description', $data) ? $data['description'] : null
        );


        ($this->upsertService)($scooterDTO);

        return new JsonResponse("Scooter Upsert", JsonResponse::HTTP_CREATED);
    }
}