<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Search;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;

class ScooterSearchService
{
    public function __construct(
        private ScooterRepository $repository
    ) {
    }

    /**
     * @return Scooter[]
     */
    public function __invoke(Criteria $criteria): array
    {
        return $this->repository->search($criteria);
    }
}
