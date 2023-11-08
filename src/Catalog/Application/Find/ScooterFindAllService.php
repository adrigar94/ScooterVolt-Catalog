<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Find;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;

class ScooterFindAllService
{
    public function __construct(
        private readonly ScooterRepository $repository
    ) {
    }

    /**
     * @return Scooter[]
     */
    public function __invoke(): array
    {
        return $this->repository->findAll();
    }
}
