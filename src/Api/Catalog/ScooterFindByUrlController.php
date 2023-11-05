<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Api\Catalog;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation as NOA;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindByUrlService;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog/scooters/url/{url}', name: 'scooters_find_by_url', methods: ['GET'])]
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
class ScooterFindByUrlController
{
    public function __construct(private ScooterFindByUrlService $findByUrlService)
    {
    }

    public function __invoke(string $url): Response
    {
        $adUrl = new AdUrl($url);
        $scooter = ($this->findByUrlService)($adUrl);

        if ($scooter) {
            return new JsonResponse($scooter, JsonResponse::HTTP_OK);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
    }
}
