<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Find;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;

class ScooterFindByIdService
{
    public function __construct(
        private ScooterRepository $repository
    ) {
    }


    public function __invoke(AdId $id): ?Scooter
    {
        return $this->repository->findById($id);
    }
}
