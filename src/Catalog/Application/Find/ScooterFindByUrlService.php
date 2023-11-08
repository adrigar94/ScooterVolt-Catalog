<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Find;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;

class ScooterFindByUrlService
{
    public function __construct(
        private readonly ScooterRepository $repository
    ) {
    }

    public function __invoke(AdUrl $url): ?Scooter
    {
        return $this->repository->findByUrl($url);
    }
}
